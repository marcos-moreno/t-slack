<?php
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) {
    
    require_once "../postgres.php";
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array(); 

    if ($received_data->action == 'fetchallNotifications') {
        $query = "
        SELECT id_notification_detail, n.id_notification, id_employee, viewed, msg, description 
        ,display_start
        FROM refividrio.notification n
            INNER JOIN refividrio.notification_detail nd ON n.id_notification = nd.id_notification
        WHERE -- viewed = 'N' AND
          nd.id_employee =" . $_SESSION['id_empleado']  . " ORDER BY n.id_notification DESC"; 
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row; 
        }
        echo json_encode($data);
    }

    if ($received_data->action == 'updateViewed') { 
        $data = array(
            ':id_notification_detail' => $received_data->id_notification_detail ,
            ':id_empleado' => $_SESSION['id_empleado'], 
        );  
        $query = "UPDATE refividrio.notification_detail SET viewed = true 
                    WHERE id_notification_detail = :id_notification_detail
                    AND id_employee= :id_empleado; ";  
        $statement = $connect->prepare($query); 
        $statement->execute($data); 
        $output = array(
            'message' => 'Data Updated'
        ); 
        echo json_encode($output); 
    } 
    // =======================================ADMIN  
    if ($received_data->action == 'fetchallNotificationsAndmin') {
        $query = "
        SELECT * FROM refividrio.notification  ORDER BY id_notification DESC"; 
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row; 
        }
        echo json_encode($data);
    }

    if ($received_data->action == 'updateData') { 
        $data = array(
            ':msg' => $received_data->data->msg ,
            ':description' => $received_data->data->description,
            ':id_notification' => $received_data->data->id_notification, 
        );  
        $query = "UPDATE refividrio.notification
                    SET  msg=:msg, description=:description
                    WHERE id_notification= :id_notification ; ";  
        $statement = $connect->prepare($query); 
        $statement->execute($data); 
        $output = array(
            'message' => 'Data Updated'
        ); 
        echo json_encode($output); 
    } 

    if ($received_data->action == 'insertNotification') {
        $data = array( 
            ':type' => $received_data->type,
            ':id_notification' => $received_data->id_notification, 
        );  
        $query = " SELECT * FROM refividrio.create_notifications('".json_encode($received_data->filter)."',:type,:id_notification)"; 
        $statement = $connect->prepare($query);
        $statement->execute($data);
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row; 
        }
        echo json_encode($data);
    }

    if ($received_data->action == 'insertData') {
        try {
            $data = array(
                ':msg' => $received_data->data->msg ,
                ':description' => $received_data->data->description,  
                ':display_start' => $received_data->data->display_start,  
            ); 
            $query = "INSERT INTO refividrio.notification(msg, description,display_start)  VALUES (:msg, :description,:display_start) RETURNING id_notification;"; 
            $statement = $connect->prepare($query); 
            $statement->execute($data); 
            $id = 0;
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id_notification']; 
            }
            $output = array(
                'message' => 'Data Inserted',
                'id' =>  $id
            ); 
            echo json_encode($output);
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }
       
    } 

    if ($received_data->action == 'deleteData') { 
        $data = array( 
            ':id_notification' => $received_data->id_notification, 
        );  
        $query = "DELETE FROM refividrio.notification  WHERE id_notification= :id_notification ; ";  
        $statement = $connect->prepare($query); 
        $statement->execute($data); 
        $output = array(
            'message' => 'Data Deleteted'
        ); 
        echo json_encode($output); 
    }  
    if ($received_data->action == 'getUsersEmail') {
        $query = "SELECT id_empleado,CONCAT(nombre,' ',materno,' ',paterno)As epleado,correo , nt.msg,nt.description
                    FROM refividrio.empleado e
                    INNER JOIN notification_detail ntd ON e.id_empleado = ntd.id_employee
                    INNER JOIN notification nt ON nt.id_notification = ntd.id_notification
                    WHERE correo <> '' AND correo IS NOT NULL AND ntd.id_notification=". $received_data->id_notifiation;  
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row; 
        }
        echo json_encode($data);
    }
    if ($received_data->action == 'getAllData') { 
        $dataEmpresa = array();
        $query = " SELECT empresa_nombre As value,id_empresa As id FROM refividrio.empresa ORDER BY value ;"; 
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $dataEmpresa[] = $row; 
        }

        $datasegmento = array();
        $query = " SELECT CONCAT(nombre,' (',substring(empresa_nombre from 0 for 20),')') As value,id_segmento As id  
                    FROM refividrio.segmento s
                    INNER JOIN refividrio.empresa e ON e.id_empresa = s.id_empresa
                    ORDER BY e.id_empresa "; 
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $datasegmento[] = $row; 
        }

        $dataEmpleado = array();
        $query = "SELECT CONCAT(emp.paterno,' ',emp.materno,' ',emp.nombre,' ',' (',substring(empresa_nombre from 0 for 20),')') As value,emp.id_empleado As id FROM refividrio.empleado emp
                    INNER JOIN refividrio.segmento s ON s.id_segmento = emp.id_segmento
                    INNER JOIN refividrio.empresa e ON e.id_empresa = s.id_empresa
                    WHERE  emp.activo = true  ORDER BY e.id_empresa,emp.id_segmento,value  "; 
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $dataEmpleado[] = $row; 
        }

        $general = array(); 
        $general[] = $datasegmento;
        $general[] = $dataEmpleado;
        $general[] = $dataEmpresa; 
        echo json_encode($general); 
    } 
 
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
}
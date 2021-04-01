<?php
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) { 
    
    require_once "../postgres.php";
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array(); 

    if ($received_data->action == 'fetchall') {
        $query = " SELECT 
                    r.*
                    ,CASE WHEN re.id_empleado IS NOT NULL THEN true ELSE false END AS selected
                    FROM rol AS r
                        LEFT JOIN Empleado_Rol AS re
                            ON r.id_rol = re.id_rol
                            AND re.id_empleado = ".$received_data->id_empleado." ";  
                            
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }

    if ($received_data->action == 'insert') {
        $data = array(
            ':id_rol' => $received_data->id_rol,
            ':id_empleado' => $received_data->id_empleado 
        );
        $query = "INSERT INTO empleado_rol (id_rol, id_empleado) VALUES (:id_rol,:id_empleado)";
        $statement = $connect->prepare($query); 
        $statement->execute($data); 
        $output = array(
            'message' => 'Data Inserted'
        ); 
        echo json_encode($output); 
    } 

    if ($received_data->action == 'delete') {
        $query = "DELETE FROM empleado_rol WHERE id_empleado = '".$received_data->id_empleado."'  ";
        $statement = $connect->prepare($query);
        $statement->execute();
        $output = array(
            'message' => 'Data Deleted'
        );
        echo json_encode($output);
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 
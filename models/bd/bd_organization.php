<?php
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) {  
    require_once "../postgres.php";
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array();
    if ($received_data->action == 'fetchall') {
        $query = "SELECT s.*,e.empresa_nombre 
                    FROM segmento s 
                  INNER JOIN Empresa e ON e.id_empresa = s.id_empresa  
                  ORDER BY s.id_empresa,s.nombre DESC";
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }

    if ($received_data->action == 'Agregar') {
        $data = array(
            ':first_name' => $received_data->firstName,
            ':last_name' => $received_data->lastName,
            ':checked' => $received_data->checked,
            ':company' => $received_data->company,
            ':id_creadopor' =>  $_SESSION['id_empleado'],
            ':id_actualizadopor' =>  $_SESSION['id_empleado']

        ); 
        $query = "INSERT INTO segmento (id_empresa,id_creadopor,fecha_creado,nombre, observaciones,activo,id_actualizadopor,fecha_actualizado) 
                VALUES (:company,:id_creadopor,CURRENT_TIMESTAMP,:first_name, :last_name,:checked,:id_actualizadopor,CURRENT_TIMESTAMP)";
        $statement = $connect->prepare($query);
        $statement->execute($data);
        $output = array(
            'message' => 'Organización Registrada'
        );
        echo json_encode($output);
    }
    if ($received_data->action == 'fetchSingle') {
        $query = "SELECT * FROM segmento WHERE id_segmento = '" . $received_data->id . "' ";
        $statement = $connect->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        foreach ($result as $row) {
            $data['id'] = $row['id_segmento'];
            $data['first_name'] = $row['nombre'];
            $data['last_name'] = $row['observaciones'];
            $data['checked'] = $row['activo'];
            $data['company'] = $row['id_empresa'];
        }
        echo json_encode($data);
    }
    if ($received_data->action == 'filterOrganization') {
            
        $query = " SELECT s.*,e.empresa_nombre 
        FROM segmento s 
            INNER JOIN Empresa e ON e.id_empresa = s.id_empresa   
        WHERE 
            empresa_nombre ILIKE '%$received_data->filter%'
            OR  s.nombre ILIKE '%$received_data->filter%' 
        ORDER BY s.id_empresa,s.nombre DESC ";
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
    if ($received_data->action == 'Modificar') {
        $data = array(
            ':first_name' => $received_data->firstName,
            ':last_name' => $received_data->lastName,
            ':company' => $received_data->company,
            ':checked' => $received_data->checked,
            ':id_actualizadopor' =>  $_SESSION['id_empleado'],
            ':id'   => $received_data->hiddenId
        );
        $query = " UPDATE segmento SET nombre = :first_name
                                        , observaciones = :last_name 
                                        ,fecha_actualizado = CURRENT_TIMESTAMP 
                                        ,activo = :checked 
                                        ,id_empresa = :company 
                                        ,id_actualizadopor = :id_actualizadopor
                                        WHERE id_segmento= :id";
        $statement = $connect->prepare($query);
        $statement->execute($data);
        $output = array(
            'message' => 'Organización Actualizada'
        );
        echo json_encode($output);
    }
    if ($received_data->action == 'delete') {
        $query = "DELETE FROM segmento WHERE id_segmento = '" . $received_data->id . "' ";
        $statement = $connect->prepare($query);
        $statement->execute();
        $output = array(
            'message' => 'Organización Eliminada'
        );
        echo json_encode($output);
    }
    if ($received_data->action == 'fetchaByCompany') {
        $query = "SELECT s.* 
                    FROM segmento s 
                    WHERE  s.id_empresa = " . $received_data->id_empresa  ;
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 
?>
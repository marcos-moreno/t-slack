<?php
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) {
    
    require_once "../postgres.php";
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array(); 

    if ($received_data->action == 'fetchall') {
        try {
            $query = "SELECT p.*,t.tipo,t.descripcion tipoDesc,t.direct_data,t.opcion_multiple FROM pregunta p INNER JOIN tipo t ON t.id_tipo =  p.id_tipo
            WHERE id_encuesta = ". $received_data->id_encuesta ."
            ORDER BY numero_pregunta ASC";
            $statement = $connect->prepare($query);
            $statement->execute();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            echo json_encode($data);
        } catch (\Throwable $th) {
            echo json_encode($th->errorInfo);
        }
    }

    if ($received_data->action == 'getTipos') {
        try {
            $query = "SELECT * FROM tipo";
            $statement = $connect->prepare($query);
            $statement->execute();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            echo json_encode($data);
        } catch (\Throwable $th) {
            echo json_encode($th->errorInfo);
        }
    }

    if ($received_data->action == 'insert') {
        try {
            $data = array(
                ':nombre_pregunta' => $received_data->model->nombre_pregunta,
                ':activo' => $received_data->model->activo,
                ':id_encuesta' => $received_data->model->id_encuesta,
                ':id_creado' =>  $_SESSION['id_empleado'],
                ':id_tipo' => $received_data->model->id_tipo,
                ':id_actualizadopor' =>  $_SESSION['id_empleado'],
                ':numero_pregunta' =>   $received_data->model->numero_pregunta,
                ':obligatoria' =>   $received_data->model->obligatoria,
                
            ); 
            $query = "INSERT INTO pregunta ( id_encuesta, id_creado, fecha_creado, nombre_pregunta, activo, id_tipo, id_actualizadopor, fecha_actualizado, numero_pregunta, obligatoria)  
                    VALUES (:id_encuesta,:id_creado,CURRENT_TIMESTAMP,:nombre_pregunta,:activo,:id_tipo,:id_actualizadopor,CURRENT_TIMESTAMP,:numero_pregunta,:obligatoria)";

            $statement = $connect->prepare($query); 
            $statement->execute($data); 
            $output = array(
                'message' => 'Data Inserted'
            ); 
            echo json_encode($output);
        } catch (\Throwable $th) {
            echo json_encode($th->errorInfo);
        }
    }

    if ($received_data->action == 'fetchSingle') {
        try {
            $query = "SELECT * FROM pregunta WHERE id_pregunta = '" . $received_data->id . "' "; 
            $statement = $connect->prepare($query); 
            $statement->execute(); 
            $result = $statement->fetchAll(); 
            foreach ($result as $row) {
                $data['id'] = $row['id_pregunta'];
                $data['question_name'] = $row['nombre_pregunta'];
                $data['checked'] = $row['activo'];
            } 
            echo json_encode($data);
        } catch (\Throwable $th) {
            echo json_encode($th->errorInfo);
        }
    }

    if ($received_data->action == 'update') {
        try {
            $data = array(
                ':nombre_pregunta' => $received_data->model->nombre_pregunta, 
                ':activo' => $received_data->model->activo,  
                ':id_actualizadopor' =>  $_SESSION['id_empleado'],
                ':numero_pregunta' =>   $received_data->model->numero_pregunta,
                ':obligatoria' =>   $received_data->model->obligatoria, 
                ':id_tipo' => $received_data->model->id_tipo,
                ':id_pregunta'=>   $received_data->model->id_pregunta, 
            ); 
            $query = " UPDATE pregunta SET 
                        nombre_pregunta = :nombre_pregunta
                        ,fecha_actualizado = CURRENT_TIMESTAMP 
                        ,activo = :activo 
                        ,id_actualizadopor = :id_actualizadopor
                        ,numero_pregunta = :numero_pregunta
                        ,obligatoria = :obligatoria
                        ,id_tipo = :id_tipo
                        WHERE id_pregunta= :id_pregunta"; 
            $statement = $connect->prepare($query); 
            $statement->execute($data); 
            $output = array(
                'message' => 'Data Updated'
            ); 
            echo json_encode($output);
        } catch (\Throwable $th) {
            echo json_encode($th->errorInfo);
        }
    
    }

    if ($received_data->action == 'delete') {
        try {
            $query = "DELETE FROM pregunta WHERE id_pregunta = '" . $received_data->id . "' ";
            $statement = $connect->prepare($query);
            $statement->execute();
            $output = array(
                'message' => 'Data Deleted'
            );
            echo json_encode($output);
        } catch (\Throwable $th) {
            echo json_encode($th->errorInfo);
        }
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 
?>
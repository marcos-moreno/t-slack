<?php
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) { 
    
    require_once "../postgres.php";
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array();

    if ($received_data->action == 'fetchallOption') {
        $query = "
        SELECT  
        o.id_opcion,o.nombre As opcion
                ,o.activo As op_activo , o.id_pregunta, o.pocision ,'update' as action,o.respuesta_extra
                ,o.is_correct_answer
                ,t.*
                ,p.is_evaluated
        FROM refividrio.encuesta e
                INNER JOIN pregunta p ON p.id_encuesta = e.id_encuesta
                INNER JOIN opciones o ON o.id_pregunta = p.id_pregunta
                INNER JOIN tipo t ON t.id_tipo = p.id_tipo 
        WHERE p.id_pregunta =" . $received_data->idQuestion . " 
                ORDER BY  o.pocision ";
        $statement = $connect->prepare($query);
        $statement->execute(); 
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row; 
        }
        echo json_encode($data);
    }

    if ($received_data->action == 'update') {
        // $data = array(
        //     ':nombre' =>  $received_data->model->opcion,
        //     ':op_activo' => $received_data->model->op_activo,
        //     ':id_actualizadopor'   =>  $_SESSION['id_empleado'],
        //     ':pocision' => $received_data->model->pocision,
        //     ':id_opcion' => $received_data->model->id_opcion,
        //     ':respuesta_extra' => $received_data->model->respuesta_extra,
        // ); 
        // $query = "UPDATE refividrio.opciones
        //             SET nombre=:nombre, activo=:op_activo, id_actualizadopor=:id_actualizadopor, 
        //             fecha_actualizado=CURRENT_TIMESTAMP, pocision=:pocision
        //         WHERE id_opcion = :id_opcion  ";  
        // $statement = $connect->prepare($query); 
        try{
            $query = "UPDATE refividrio.opciones SET 
                        nombre='".$received_data->model->opcion . "'
                        ,activo='" . $received_data->model->op_activo ."' 
                        ,respuesta_extra='". $received_data->model->respuesta_extra. "' 
                        ,id_actualizadopor='".  $_SESSION['id_empleado'] . "' 
                        ,pocision='".  $received_data->model->pocision . "' 
                        ,is_correct_answer='". $received_data->model->is_correct_answer. "' 
                    WHERE  id_opcion =".$received_data->model->id_opcion;
            $statement = $connect->prepare($query); 
            $statement->execute(); 
            $output = array(
                'message' => 'Data Updated'
            ); 
            echo json_encode($output);
        } catch (PDOException $e) {
            $output = array(
                'messageError' => $e->getMessage()
            );  
            echo json_encode($output); 
        }
        //  echo $query ;
    }

    if ($received_data->action == 'delete') { 
        $id = $received_data->model->id_opcion;
        $query = "DELETE FROM opciones WHERE id_opcion = '" . $id . "' "; 
        $statement = $connect->prepare($query); 
        $statement->execute(); 
        $output = array(
            'message' => 'Data Deleted'
        ); 
        echo json_encode($output);
    }

    if ($received_data->action == 'insert') {
        try{
            $data = array(
                ':id_pregunta' => $received_data->model->id_pregunta,
                ':nombre' => $received_data->model->opcion,
                ':activo' => $received_data->model->op_activo,
                ':id_creado' => $_SESSION['id_empleado'],
                ':id_actualizadopor' => $_SESSION['id_empleado'],
                ':pocision' => $received_data->model->pocision,  
                ':respuesta_extra' => $received_data->model->respuesta_extra,  
                ':is_correct_answer' => $received_data->model->is_correct_answer,  
            ); 
            $query = "INSERT INTO opciones (id_pregunta, id_creado, fecha_creado, nombre, activo, id_actualizadopor, fecha_actualizado, pocision,respuesta_extra,is_correct_answer) 
            VALUES (:id_pregunta,:id_creado,CURRENT_TIMESTAMP, :nombre,:activo,:id_actualizadopor,CURRENT_TIMESTAMP,:pocision,:respuesta_extra,:is_correct_answer)";
            $statement = $connect->prepare($query);
            $statement->execute($data);
            $output = array(
                'message' => 'Data Inserted'
            ); 
            echo json_encode($output);
        } catch (PDOException $e) {
            $output = array(
                'messageError' => $e->getMessage()
            );  
            echo json_encode($output); 
        }
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 
?>
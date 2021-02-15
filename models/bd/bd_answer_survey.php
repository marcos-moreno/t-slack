<?php
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) {
    
    require_once "../postgres.php";
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array(); 

    if ($received_data->action == 'fetchallQuestion') {
        $query = " SELECT 
                        e.id_encuesta,p.id_pregunta,p.nombre_pregunta
                        ,p.activo,t.tipo, t.direct_data ,p.obligatoria
                    FROM refividrio.encuesta e
                        INNER JOIN pregunta p ON p.id_encuesta = e.id_encuesta 
                        INNER JOIN tipo t ON t.id_tipo = p.id_tipo 
                    WHERE p.id_encuesta =  " . $received_data->idEncuesta  . "
                    AND p.activo = true
                    ORDER BY p.numero_pregunta";
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
    if ($received_data->action == 'fetchallOption') {
        $query = " SELECT  
                        o.id_opcion,o.nombre As opcion
                        ,o.activo As op_activo , o.id_pregunta
                        ,o.pocision ,'update' as action,respuesta_extra
                    FROM refividrio.encuesta e
                            INNER JOIN pregunta p ON p.id_encuesta = e.id_encuesta
                            INNER JOIN opciones o ON o.id_pregunta = p.id_pregunta
                            INNER JOIN tipo t ON t.id_tipo = p.id_tipo 
                    WHERE p.id_pregunta =     " . $received_data->idQuestion . " AND o.activo = true ORDER BY  o.pocision ";
        $statement = $connect->prepare($query);
        $statement->execute(); 
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
     
    if ($received_data->action == 'insert_answer_extra') {
        $data = array(
            ':id_option' => $received_data->answer_extra->id_option,
            ':value' => $received_data->answer_extra->value,
            ':id_empleado' =>  $_SESSION['id_empleado'], 
        ); 
        $query = "INSERT INTO refividrio.option_answer_free(id_option, value, id_empleado)
                  VALUES (:id_option,:value,:id_empleado)"; 
        $statement = $connect->prepare($query); 
        $statement->execute($data); 
        $output = array(
            'message' => 'Data Inserted'
        ); 
        echo json_encode($output);
    } 

    if ($received_data->action == 'insertAnswer') {
        $data = array(
            ':id_pregunta' => $received_data->respuesta->id_pregunta,
            ':id_empleado' =>  $_SESSION['id_empleado'],// $received_data->respuesta->id_empleado,
            ':id_opcion' => $received_data->respuesta->id_opcion,
            ':id_encuesta' => $received_data->respuesta->id_encuesta,  
            ':respuesta' => $received_data->respuesta->respuesta,
            ':directa' => $received_data->respuesta->directa,  
        ); 
        $query = "INSERT INTO refividrio.res_encuesta_empleado(id_pregunta, id_empleado, id_opcion, id_encuesta, respuesta, directa)
                  VALUES (:id_pregunta,:id_empleado,:id_opcion,:id_encuesta,:respuesta,:directa)"; 
        $statement = $connect->prepare($query); 
        $statement->execute($data); 
        $output = array(
            'message' => 'Data Inserted'
        ); 
        echo json_encode($output);
    } 
    if ($received_data->action == 'inserEncuesta_empleado') {
        $data = array(
            ':id_empleado' => $_SESSION['id_empleado'], 
            ':id_encuesta' => $received_data->id_encuesta, 
        ); 
        $query = "INSERT INTO refividrio.empleado_encuesta(
                         id_empleado, id_encuesta, fechafin, activo, termino, fecha_creado) 
                VALUES (:id_empleado,:id_encuesta,NOW(),true,true,NOW())"; 
        $statement = $connect->prepare($query); 
        $statement->execute($data); 
        $output = array(
            'message' => 'inserEncuesta_empleado Success'
        ); 
        echo json_encode($output);
    }  
    if ($received_data->action == 'validPoll') {
        $query = "
        SELECT 
            CASE WHEN COUNT(*) > 0 THEN true ELSE false END as res
        FROM 
            encuesta enc 
        INNER JOIN empleado emp ON emp.id_empleado = ".$_SESSION['id_empleado']."
        INNER JOIN segmento seg ON seg.id_segmento = emp.id_segmento
        INNER JOIN empresa empres ON empres.id_empresa = seg.id_empresa
        WHERE enc.id_encuesta = ".$received_data->id_encuesta."  
        AND emp.activo = true
        AND (now() BETWEEN enc.validodesde AND validohasta 
                OR 
            (SELECT  COUNT(*) FROM enc_intentos_encuesta i 
            WHERE (now() BETWEEN i.inicio AND i.fin) 
            AND i.id_encuesta = enc.id_encuesta ) > 0) " ;
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
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
                        ,p.activo,t.tipo, t.direct_data ,p.obligatoria,p.id_tipo
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
            AND i.id_encuesta = enc.id_encuesta ) > 0)
            AND (SELECT COUNT(id_empleado_encuesta) FROM empleado_encuesta res 
            WHERE res.id_empleado = emp.id_empleado 
            AND enc.id_encuesta = res.id_encuesta) = 0
            " ;
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
    if ($received_data->action == 'getRespuestasWET') {
        $query = "
        SELECT 
            COALESCE(resss.valueres,'')  respuesta
        FROM encuesta e
            INNER JOIN empleado_encuesta emp_enc ON emp_enc.id_encuesta = e.id_encuesta
            INNER JOIN empleado empl ON empl.id_empleado = emp_enc.id_empleado 
            LEFT JOIN LATERAL( 
                SELECT 
                        CASE WHEN res.directa THEN respuesta 
                        ELSE 
                            CASE WHEN freeansware.value is NOT NULL THEN
                            COALESCE(CONCAT((SELECT nombre FROM opciones opt WHERE opt.id_opcion = res.id_opcion) ,' : ',freeansware.value),'')  
                            ELSE 	COALESCE((SELECT nombre FROM opciones opt WHERE opt.id_opcion = res.id_opcion) ,'') END
                        END As valueres
                    ,P.nombre_pregunta,P.id_pregunta,p.numero_pregunta,res.respuesta
                FROM pregunta p
                LEFT JOIN res_encuesta_empleado res ON res.id_pregunta = p.id_pregunta AND res.id_empleado = emp_enc.id_empleado
                LEFT JOIN option_answer_free freeansware ON freeansware.id_option = res.id_opcion
                WHERE  p.id_encuesta = e.id_encuesta AND p.id_pregunta = ". $received_data->id_pregunta ."
            )as resss ON true  
        WHERE 
        e.id_Encuesta = ". $received_data->idEncuesta ."
        AND emp_enc.id_empleado = ".$_SESSION['id_empleado']."
        ORDER BY resss.numero_pregunta   " ;
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
    if ($received_data->action == 'getRespuestasCorrectasCheckbox') {
        $query = "
        SELECT * FROM opciones WHERE id_pregunta = ". $received_data->id_pregunta ." AND is_correct_answer = true" ;
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
    
    if ($received_data->action == 'getQuestions') {
        $query = "
        SELECT 
        p.*,
        --	typep.*
            --,
            CASE WHEN is_evaluated THEN
            CASE WHEN typep.direct_data = true  THEN 
                CASE WHEN UPPER(TRIM(p.resp_direct_quest_value))  = 
                        (SELECT UPPER(TRIM(respuesta)) respuesta FROM res_encuesta_empleado resp 
                            WHERE resp.id_pregunta =  p.id_pregunta 
                            AND resp.id_empleado = emp_enc.id_empleado) 
                THEN 'Correcta' ELSE 'Incorrecta' END
            WHEN typep.id_tipo = 4 THEN  
                CASE WHEN UPPER(TRIM(p.resp_direct_quest_value)) = 
                        (SELECT UPPER(TRIM(opts.nombre)) FROM res_encuesta_empleado resp 
                         INNER JOIN opciones opts ON resp.id_opcion = opts.id_opcion
                             WHERE resp.id_pregunta =  p.id_pregunta 
                             AND resp.id_empleado = emp_enc.id_empleado LIMIT 1)
                THEN 'Correcta' ELSE 'Incorrecta' END  
            ELSE 
                CASE WHEN 
                    (SELECT COUNT(resp2.id_opcion) FROM res_encuesta_empleado resp2 
                            WHERE resp2.id_pregunta =  p.id_pregunta 
                            AND resp2.id_empleado = emp_enc.id_empleado
                            AND resp2.id_opcion IN (SELECT op.id_opcion FROM opciones op 
                                                        WHERE op.id_pregunta = p.id_pregunta 
                                                        AND op.is_correct_answer = true) 
                    ) = ( SELECT COUNT(*)
                            FROM opciones op 
                                WHERE op.id_pregunta = p.id_pregunta AND op.is_correct_answer = true 
                        ) 
                AND   
                    (SELECT COUNT(resp2.id_opcion) FROM res_encuesta_empleado resp2 
                            WHERE resp2.id_pregunta =  p.id_pregunta 
                            AND resp2.id_empleado = emp_enc.id_empleado 
                    ) = ( SELECT COUNT(*)
                            FROM opciones op 
                                WHERE op.id_pregunta = p.id_pregunta AND op.is_correct_answer = true 
                        ) 
                THEN 'Correcta' ELSE 'Incorrecta' END  
            END 
            ELSE 'NEE' END As estado_respuesta
        FROM encuesta e
            INNER JOIN empleado_encuesta emp_enc ON emp_enc.id_encuesta = e.id_encuesta 
            INNER JOIN pregunta p ON p.id_encuesta = e.id_encuesta 
            INNER JOIN tipo typep ON typep.id_tipo = p.id_tipo
        WHERE 
        e.id_Encuesta =  ". $received_data->idEncuesta ."
        AND emp_enc.id_empleado =  ".$_SESSION['id_empleado']." ORDER BY p.numero_pregunta " ;

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
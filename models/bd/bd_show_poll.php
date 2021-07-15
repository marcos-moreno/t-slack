<?php 
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) { 
    require_once "../postgres.php"; 
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array();

    if ($received_data->action == 'getPooll' && $received_data->filter == 'pending') {
        // $query ="SELECT 
        //             --e.*
        //             e.id_encuesta, e.id_creadopor, e.fecha_creado, e.nombre, e.observaciones, 
        //             e.activo, e.id_actualizado, e.fecha_actualizado, 
        //             TO_CHAR(e.validohasta, 'DD/MM/YYYY HH12:MI:SS AM') As validohasta,
        //             TO_CHAR(e.validodesde, 'DD/MM/YYYY HH12:MI:SS AM') As validodesde,
        //             coalesce((SELECT COUNT(*) as total FROM refividrio.enc_leccion lec WHERE lec.id_encuesta = e.id_encuesta ),0) As totallecciones 
        //             ,coalesce(esta_lecc.estado,'NO') As estado_leccion
        //         FROM refividrio.encuesta e  
        //         LEFT JOIN refividrio.empleado empl ON empl.id_empleado = " . $_SESSION['id_empleado'] ."
        //         INNER JOIN refividrio.segmento seg ON empl.id_segmento = seg.id_segmento
        //         LEFT JOIN refividrio.enc_encuesta_leccion_empleado esta_lecc ON esta_lecc.id_encuesta = e.id_encuesta AND esta_lecc.id_empleado = empl.id_empleado
        //         WHERE 
        //         e.id_encuesta NOT IN (SELECT id_encuesta FROM refividrio.empleado_encuesta WHERE id_empleado = empl.id_empleado )
        //         AND seg.id_empresa IN (SELECT id_empresa FROM empresa_encuesta WHERE  e.id_encuesta = id_encuesta)
        //         AND e.activo = true
        //         AND
        //         (	
        //             ( now() >= e.validodesde AND now() <=  e.validohasta)
        //          OR
        //             ((SELECT COALESCE(COUNT(*),0)  FROM enc_intentos_encuesta ie   WHERE ie.id_Encuesta = e.id_encuesta
        //                  AND now() >= ie.inicio  AND now() <= ie.fin  ) > 0 )
        //         )
        //     ORDER BY e.id_encuesta DESC";
        $query = "SELECT  
                    e.id_encuesta, e.id_creadopor, e.fecha_creado, e.nombre, e.observaciones, 
                    e.activo, e.id_actualizado, e.fecha_actualizado, 
                    TO_CHAR(e.validohasta, 'DD/MM/YYYY HH12:MI:SS AM') As validohasta,
                    TO_CHAR(e.validodesde, 'DD/MM/YYYY HH12:MI:SS AM') As validodesde,
                    coalesce((SELECT COUNT(*) as total FROM refividrio.enc_leccion lec WHERE lec.id_encuesta = e.id_encuesta ),0) As totallecciones 
                    ,coalesce(esta_lecc.estado,'NO') As estado_leccion 
            FROM refividrio.encuesta e 
            LEFT JOIN refividrio.empleado empl ON empl.id_empleado =  " . $_SESSION['id_empleado'] ."
                INNER JOIN refividrio.segmento seg ON empl.id_segmento = seg.id_segmento
                LEFT JOIN refividrio.enc_encuesta_leccion_empleado esta_lecc ON esta_lecc.id_encuesta = e.id_encuesta AND esta_lecc.id_empleado = empl.id_empleado
                WHERE 
                    --Valida que no se haya contestado
                    e.id_encuesta NOT IN (SELECT id_encuesta FROM refividrio.empleado_encuesta WHERE id_empleado = empl.id_empleado ) 
                AND (
                    seg.id_empresa IN (SELECT id_empresa FROM empresa_encuesta WHERE  e.id_encuesta = id_encuesta)
                    OR  
                    (
                        (
                            SELECT  
                                CASE WHEN COUNT(*) = 1 THEN true ELSE false END 
                            FROM acceso_encuesta 
                            WHERE entidad = 'empleado' AND id_entidad = empl.id_empleado AND id_encuesta = e.id_encuesta 
                        ) = true   
                        OR 
                        (
                            SELECT  
                                CASE WHEN COUNT(*) = 1 THEN true ELSE false END 
                            FROM acceso_encuesta 
                            WHERE entidad = 'segmento' AND id_entidad = empl.id_segmento AND id_encuesta = e.id_encuesta
                        )
                    ) = true   
                )
                AND e.activo = true

                AND(	
                    (now() >= e.validodesde AND now() <=  e.validohasta)
                    OR
                    ((SELECT COALESCE(COUNT(*),0)  FROM enc_intentos_encuesta ie   WHERE ie.id_Encuesta = e.id_encuesta
                            AND now() >= ie.inicio  AND now() <= ie.fin  ) > 0 )  
                )
            ORDER BY e.id_encuesta DESC";

        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }  

    if ($received_data->action == 'getPoollByID' ) {
        $query ="SELECT 
                    --e.*
                    e.id_encuesta, e.id_creadopor, e.fecha_creado, e.nombre, e.observaciones, 
                    e.activo, e.id_actualizado, e.fecha_actualizado, 
                    TO_CHAR(e.validohasta, 'DD/MM/YYYY HH12:MI:SS AM') As validohasta,
                    TO_CHAR(e.validodesde, 'DD/MM/YYYY HH12:MI:SS AM') As validodesde
                    ,coalesce((SELECT COUNT(*) as total FROM refividrio.enc_leccion lec WHERE lec.id_encuesta = e.id_encuesta ),0) As totallecciones 
                FROM refividrio.encuesta e    
                WHERE 
                e.id_encuesta = " . $received_data->id_encuesta . "  ORDER BY e.id_encuesta DESC";
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }  

    if ($received_data->action == 'getdatosdetermino' ) {
        $query =" 
        SELECT
            Concat(empleado.paterno,' ',empleado.materno,' ',empleado.nombre) as nomempleado
            ,ee.id_empleado_encuesta,ee.id_empleado,ee.id_encuesta,ee.fechafin,ee.activo,ee.termino, 
                            TO_CHAR(ee.fecha_creado, 'DD/MM/YYYY HH12:MI:SS AM') respuesta,encuesta.total,respuestas.respuestas_correctas
        FROM refividrio.empleado_encuesta ee
        LEFT JOIN LATERAL(
        SELECT COUNT(*) total 
            FROM refividrio.pregunta pre  
            WHERE pre.id_encuesta = ee.id_encuesta AND pre.is_evaluated = true
        )As encuesta ON true
        
        LEFT JOIN LATERAL(
                SELECT  
                    SUM(CASE WHEN (CASE WHEN is_evaluated THEN
                    CASE WHEN typep.direct_data = true  THEN 
                        CASE WHEN UPPER(p.resp_direct_quest_value) = 
                                (SELECT UPPER(respuesta) FROM refividrio.res_encuesta_empleado resp 
                                    WHERE resp.id_pregunta =  p.id_pregunta 
                                    AND resp.id_empleado = emp_enc.id_empleado) 
                        THEN 'Correcta' ELSE 'Incorrecta' END
                    WHEN typep.id_tipo = 4 THEN  
                        CASE WHEN UPPER(p.resp_direct_quest_value) = 
                                (SELECT UPPER(opts.nombre) FROM refividrio.res_encuesta_empleado resp 
                                    INNER JOIN refividrio.opciones opts ON resp.id_opcion = opts.id_opcion
                                        WHERE resp.id_pregunta =  p.id_pregunta 
                                        AND resp.id_empleado = emp_enc.id_empleado LIMIT 1)
                        THEN 'Correcta' ELSE 'Incorrecta' END  
                    ELSE 
                        CASE WHEN 
                            (SELECT COUNT(resp2.id_opcion) FROM refividrio.res_encuesta_empleado resp2 
                                    WHERE resp2.id_pregunta =  p.id_pregunta 
                                    AND resp2.id_empleado = emp_enc.id_empleado
                                    AND resp2.id_opcion IN (SELECT op.id_opcion FROM refividrio.opciones op 
                                                                WHERE op.id_pregunta = p.id_pregunta 
                                                                AND op.is_correct_answer = true) 
                            ) = ( SELECT COUNT(*)
                                    FROM refividrio.opciones op 
                                        WHERE op.id_pregunta = p.id_pregunta AND op.is_correct_answer = true 
                                ) 
                        AND   
                            (SELECT COUNT(resp2.id_opcion) FROM refividrio.res_encuesta_empleado resp2 
                                    WHERE resp2.id_pregunta =  p.id_pregunta 
                                    AND resp2.id_empleado = emp_enc.id_empleado 
                            ) = ( SELECT COUNT(*)
                                    FROM refividrio.opciones op 
                                        WHERE op.id_pregunta = p.id_pregunta AND op.is_correct_answer = true 
                                ) 
                        THEN 'Correcta' ELSE 'Incorrecta' END  
                                    END 
                                    ELSE 'NEE' END) = 'Correcta' THEN 1 ELSE 0 END)  As respuestas_correctas 
                        FROM refividrio.encuesta e
                            INNER JOIN refividrio.empleado_encuesta emp_enc ON emp_enc.id_encuesta = e.id_encuesta 
                            INNER JOIN refividrio.pregunta p ON p.id_encuesta = e.id_encuesta 
                            INNER JOIN refividrio.tipo typep ON typep.id_tipo = p.id_tipo
                        WHERE 
                        e.id_Encuesta = ee.id_encuesta
                        AND emp_enc.id_empleado = ee.id_empleado  
                    )As respuestas ON true
        INNER JOIN refividrio.empleado empleado ON empleado.id_empleado = ee.id_empleado
        WHERE ee.id_empleado = " . $_SESSION['id_empleado'] ."
        AND ee.id_encuesta = " . $received_data->id_encuesta ;
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }  
    if ($received_data->action == 'seachPollComplete') {
        // $query = "
        // SELECT 
        //         coalesce(TO_CHAR(res.fecha_creado, 'DD/MM/YYYY HH12:MI:SS AM'),'--') As respuesta,
        //     CASE 
        //         WHEN res.fecha_creado BETWEEN enc.validodesde AND enc.validohasta THEN
        //         'Correcto'
        //         WHEN now() BETWEEN enc.validodesde AND enc.validohasta THEN
        //         'En Captura'
        //         WHEN now() < enc.validodesde THEN
        //         'Aún No Disponible'
        //         WHEN res.fecha_creado IS NULL THEN
        //         'No se Respondió' 
        //         ELSE 
        //         'Contestada Fuera de Tiempo'
        //     END AS estado
        //     ,enc.id_encuesta, enc.id_creadopor, enc.fecha_creado, enc.nombre, enc.observaciones, 
        //     enc.activo, enc.id_actualizado, enc.fecha_actualizado, 
        //     TO_CHAR(enc.validohasta, 'DD/MM/YYYY HH12:MI:SS AM') As validohasta,
        //     TO_CHAR(enc.validodesde, 'DD/MM/YYYY HH12:MI:SS AM') As validodesde,
        //     row_number() over (partition by emp.id_empleado order by enc.id_encuesta ASC) As no_enc
        //     ,coalesce((SELECT COUNT(*) as total FROM refividrio.enc_leccion lec WHERE lec.id_encuesta = ee.id_encuesta ),0) As totallecciones
        //     ,coalesce(esta_lecc.estado,'NO') As estado_leccion 
        // FROM refividrio.empleado emp
        // INNER JOIN refividrio.segmento seg ON seg.id_segmento = emp.id_segmento
        // INNER JOIN refividrio.empresa empres ON empres.id_empresa = seg.id_empresa
        // INNER JOIN refividrio.empresa_encuesta ee ON ee.id_empresa = empres.id_empresa
        // INNER JOIN refividrio.encuesta enc ON enc.id_encuesta = ee.id_encuesta
        // LEFT JOIN refividrio.empleado_encuesta res ON res.id_encuesta = ee.id_encuesta AND res.id_empleado = emp.id_empleado
        // LEFT JOIN refividrio.enc_encuesta_leccion_empleado esta_lecc ON esta_lecc.id_encuesta = enc.id_encuesta AND esta_lecc.id_empleado = emp.id_empleado
        // WHERE 
        //     emp.id_empleado = " . $_SESSION['id_empleado'] ."
        //     AND  emp.fecha_alta_cerberus < enc.validodesde 
        //     ORDER BY enc.id_encuesta DESC "; 
        $query = "
                SELECT  
                coalesce(TO_CHAR(res.fecha_creado, 'DD/MM/YYYY HH12:MI:SS AM'),'--') As respuesta,
            CASE 
                WHEN res.fecha_creado BETWEEN enc.validodesde AND enc.validohasta THEN
                'Correcto'
                WHEN now() BETWEEN enc.validodesde AND enc.validohasta THEN
                'En Captura'
                WHEN now() < enc.validodesde THEN
                'Aún No Disponible'
                WHEN res.fecha_creado IS NULL THEN
                'No se Respondió' 
                ELSE 
                'Contestada Fuera de Tiempo'
            END AS estado
            ,enc.id_encuesta, enc.id_creadopor, enc.fecha_creado, enc.nombre, enc.observaciones, 
            enc.activo, enc.id_actualizado, enc.fecha_actualizado, 
            TO_CHAR(enc.validohasta, 'DD/MM/YYYY HH12:MI:SS AM') As validohasta,
            TO_CHAR(enc.validodesde, 'DD/MM/YYYY HH12:MI:SS AM') As validodesde,
            row_number() over (partition by emp.id_empleado order by enc.id_encuesta ASC) As no_enc
            ,coalesce((SELECT COUNT(*) as total FROM refividrio.enc_leccion lec WHERE lec.id_encuesta = enc.id_encuesta ),0) As totallecciones
            ,coalesce(esta_lecc.estado,'NO') As estado_leccion 
        FROM refividrio.empleado emp
        INNER JOIN refividrio.segmento seg ON seg.id_segmento = emp.id_segmento
        INNER JOIN refividrio.empresa empres ON empres.id_empresa = seg.id_empresa
        INNER JOIN  refividrio.encuesta enc
            ON enc.id_encuesta 
            IN (
                SELECT id_encuesta
                FROM refividrio.empresa_encuesta ee
                WHERE ee.id_empresa = seg.id_empresa 
            ) 
            OR 
            enc.id_encuesta 
            IN (
                SELECT id_encuesta 
                FROM refividrio.acceso_encuesta as aig_seg 
                WHERE 
                    (aig_seg.entidad = 'segmento' AND aig_seg.id_entidad = seg.id_segmento )
                OR (aig_seg.entidad = 'empleado' AND aig_seg.id_entidad = emp.id_empleado)
            )    
        LEFT JOIN refividrio.empleado_encuesta res ON res.id_encuesta = enc.id_encuesta 
            AND res.id_empleado = emp.id_empleado
        LEFT JOIN refividrio.enc_encuesta_leccion_empleado esta_lecc ON esta_lecc.id_encuesta = enc.id_encuesta 
            AND esta_lecc.id_empleado = emp.id_empleado 
        WHERE 
            emp.id_empleado = " . $_SESSION['id_empleado'] ."
            AND  emp.fecha_alta_cerberus < enc.validodesde 
            ORDER BY enc.id_encuesta DESC
        ";
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 
    if ($received_data->action == 'insertLeccionEmpleado') {
        try {
            $data = array(
                    ':id_encuesta' => $received_data->model->id_encuesta,
                        ':id_empleado' =>  $_SESSION['id_empleado'],     
                        
                    ); 
            $query = 'INSERT INTO refividrio.enc_encuesta_leccion_empleado(id_encuesta, id_empleado,creado,actualizado) 
                            VALUES (:id_encuesta, :id_empleado,now(),now());';
            $statement = $connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Inserted'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        } 
    } 
    if ($received_data->action == 'UpdateLeccionEmpleado') {
        try {
            $data = array(
                    ':id_encuesta' => $received_data->model->id_encuesta,
                        ':id_empleado' =>  $_SESSION['id_empleado'],     
                    ); 
            $query = "UPDATE refividrio.enc_encuesta_leccion_empleado  SET actualizado=now(),estado='CO' WHERE id_empleado=:id_empleado  AND id_encuesta = :id_encuesta;";
            $statement = $connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Updated'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        } 
    }  
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 
?>
<?php 
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) { 
    require_once "../postgres.php"; 
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array();

    if ($received_data->action == 'getPooll' && $received_data->filter == 'pending') {
        $query ="SELECT 
                    --e.*
                    e.id_encuesta, e.id_creadopor, e.fecha_creado, e.nombre, e.observaciones, 
                    e.activo, e.id_actualizado, e.fecha_actualizado, 
                    TO_CHAR(e.validohasta, 'DD/MM/YYYY HH12:MI:SS AM') As validohasta,
                    TO_CHAR(e.validodesde, 'DD/MM/YYYY HH12:MI:SS AM') As validodesde
                FROM refividrio.encuesta e  
                LEFT JOIN refividrio.empleado empl ON empl.id_empleado = " . $_SESSION['id_empleado'] ."
                INNER JOIN refividrio.segmento seg ON empl.id_segmento = seg.id_segmento
                WHERE 
                e.id_encuesta NOT IN (SELECT id_encuesta FROM refividrio.empleado_encuesta WHERE id_empleado = empl.id_empleado )
                AND seg.id_empresa IN (SELECT id_empresa FROM empresa_encuesta WHERE  e.id_encuesta = id_encuesta)
                AND e.activo = true
                AND
                (	
                    ( now() >= e.validodesde AND now() <=  e.validohasta)
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

    if ($received_data->action == 'seachPollComplete') {
        $query = "
        SELECT 
                coalesce(TO_CHAR(res.fecha_creado, 'MM/DD/YYYY HH12:MI:SS AM'),'--') As respuesta,
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
            TO_CHAR(enc.validohasta, 'MM/DD/YYYY HH12:MI:SS AM') As validohasta,
            TO_CHAR(enc.validodesde, 'MM/DD/YYYY HH12:MI:SS AM') As validodesde,
            row_number() over (partition by emp.id_empleado order by enc.id_encuesta ASC) As no_enc
        FROM refividrio.empleado emp
        INNER JOIN refividrio.segmento seg ON seg.id_segmento = emp.id_segmento
        INNER JOIN refividrio.empresa empres ON empres.id_empresa = seg.id_empresa
        INNER JOIN refividrio.empresa_encuesta ee ON ee.id_empresa = empres.id_empresa
        INNER JOIN refividrio.encuesta enc ON enc.id_encuesta = ee.id_encuesta
        LEFT JOIN refividrio.empleado_encuesta res ON res.id_encuesta = ee.id_encuesta
        AND res.id_empleado = emp.id_empleado
        WHERE 
            emp.id_empleado = " . $_SESSION['id_empleado'] ."
        ORDER BY enc.id_encuesta DESC "; 
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
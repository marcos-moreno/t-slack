<?php
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) { 
    require_once "../postgres.php";
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array();  
    if ($received_data->action == 'fetchall') {
        $query = "SELECT id_encuesta, id_creadopor, fecha_creado, nombre, observaciones, activo, id_actualizado, fecha_actualizado, 
        TO_CHAR(validohasta, 'DD/MM/YYYY HH12:MI:SS AM') As validohasta,TO_CHAR(validodesde, 'DD/MM/YYYY HH12:MI:SS AM') As validodesde
            FROM refividrio.encuesta ORDER BY id_encuesta DESC";
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 
    if ($received_data->action == 'insert') {
        $data = array( 
            ':poll_name' => $received_data->poll_name,
            ':poll_help' => $received_data->poll_help,
            ':poll_validfrom' => $received_data->poll_validfrom,
            ':poll_validUntil' => $received_data->poll_validUntil,
            ':checked' => $received_data->checked 
        ); 
        $query = "INSERT INTO encuesta ( fecha_creado
                                        ,nombre
                                        ,observaciones
                                        ,activo
                                        ,validodesde
                                        ,validohasta
                                        ,fecha_actualizado
                                        ,id_actualizado
                                        ,id_creadopor
                                        ) 
                VALUES ( 
                        CURRENT_TIMESTAMP
                        ,:poll_name
                        ,:poll_help
                        ,:checked
                        ,:poll_validfrom
                        ,:poll_validUntil
                        ,CURRENT_TIMESTAMP
                        ,". $_SESSION['id_empleado'] ."
                        ,". $_SESSION['id_empleado'] ."
                        )";

        $statement = $connect->prepare($query); 
        $statement->execute($data); 
        $output = array(
            'message' => 'Data Inserted'
        ); 
        echo json_encode($output);
    }

    if ($received_data->action == 'fetchSingle') {
        $query = "SELECT * FROM encuesta WHERE id_encuesta = '" . $received_data->id . "' ";
        $statement = $connect->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        foreach ($result as $row) {
            $data['id'] = $row['id_encuesta'];
            $data['poll_name'] = $row['nombre'];
            $data['poll_help'] = $row['observaciones'];
            $data['checked'] = $row['activo'];
            $data['poll_validfrom'] = $row['validodesde'];
            $data['poll_validUntil'] = $row['validohasta'];
            $data['link'] = $row['link'];
            $data['link_final'] = $row['link_final']; 
        }
        echo json_encode($data);
    }

    session_start();
    if ($received_data->action == 'copy') {

        $validUntil = new DateTime($received_data->validUntil);
    
        $validfrom = new DateTime($received_data->validfrom);
    
        $query = "";
        try {
            $query = "SELECT refividrio.copy_poll('" . $received_data->name . "' , " . $received_data->id_encuesta . " , "
            . $_SESSION['id_empleado'] . ",'" .   $validfrom->format('Y-m-d H:i:s') . "'::timestamp with time zone , '" .
            $validUntil->format('Y-m-d H:i:s') . "'::timestamp with time zone  ) as results";
        
        $statement = $connect->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        foreach ($result as $row) {
            $data['results'] = $row['results']; 
        } 
        echo json_encode($data);
        } catch (\Throwable $th) { 
            echo  $th . "  "  ;
        }
    
    } 
    if ($received_data->action == 'update') {
        try {
            $data = array(
                ':poll_name' => $received_data->poll_name,
                ':observaciones' => $received_data->poll_help, 
                ':checked' => $received_data->checked, 
                ':validohasta'   => $received_data->poll_validUntil,
                ':validodesde'   => $received_data->poll_validfrom,
                ':id'   => $received_data->hiddenId,
                ':id_actualizado'   => $_SESSION['id_empleado'],
            );
            $query = " UPDATE encuesta SET nombre = :poll_name, observaciones = :observaciones ,fecha_actualizado = CURRENT_TIMESTAMP 
            ,activo = :checked,validohasta = :validohasta, validodesde = :validodesde ,id_actualizado = :id_actualizado
            WHERE id_encuesta= :id";
            $statement = $connect->prepare($query);
            $statement->execute($data);
            $output = array(
                'message' => 'Data Updated'
            ); 
            echo json_encode($output); 
        } catch (PDOException $e) {
            echo json_encode($e);
        } 
    }

    if ($received_data->action == 'delete') {
        try {
            $query = "DELETE FROM encuesta WHERE id_encuesta = '" . $received_data->id . "' ";
            $statement = $connect->prepare($query);
            $statement->execute();
            $output = array(
                'message' => 'Data Deleted'
            );
            echo json_encode($output);
        } catch (Exception $th) {
            echo json_encode($th->errorInfo);
        } 
    }  
    if ($received_data->action == 'fetchByType') {
        $query = "SELECT  * FROM encuesta
        WHERE  
        CASE WHEN " . $received_data->typePoolSelected . "= 2 THEN 
            (   activo = true 
                AND now()  >= validodesde 
                AND now()  <= validohasta  )
        WHEN " . $received_data->typePoolSelected . "= 0 THEN
            true ELSE   (  now() > validohasta ) END  
        ORDER BY validodesde
        ";
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
    // Sanciones, no contestar encuesta
    if ($received_data->action == 'fn_incidencias') {
        $output = "";  
        $validUntil = new DateTime($received_data->validUntil);
        $validfrom = new DateTime($received_data->validfrom);
        $query = "";
        try {
            $query = "SELECT * FROM refividrio.enc_sanciones()";
            $statement = $connect->prepare($query);
            $statement->execute();
            $output =  'success'; 
        } catch (\Throwable $th) { 
            $output = $th ; 
        }
        echo  $output;
    }
    if ($received_data->action == 'getIncidencias_creadas') {
        $query = "SELECT e.nombre,paterno,materno,e.id_cerberus_empleado,s.nombre As segmento,empres.empresa_nombre,i.*,true::boolean as sincronizar
        ,REPLACE(REPLACE(REPLACE(CONCAT(ids_encuestas_sancionadas->'id_encuesta1','-',ids_encuestas_sancionadas->'id_encuesta2','-',ids_encuestas_sancionadas->'id_encuesta3','*'),'--*',''),'-*',''),'*','') As ids_encuestas_sancionadas 
        FROM enc_incidencias_creadas i
                    INNER JOIN empleado e ON i.id_empleado = e.id_empleado
                    INNER JOIN segmento s ON s.id_segmento = e.id_segmento
                    INNER JOIN empresa empres ON empres.id_empresa = s.id_empresa
                    WHERE valor_inicio = false
                    AND sincronizado = " . $received_data->sincronizadas ." ORDER BY id_incidencias_creadas DESC,s.nombre
                    ";
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
    if ($received_data->action == 'updateIncidencia') {
        try {
            $data = array( 
                ':descripcion_cerberus'   => $received_data->descripcion_cerberus,
                ':id_incidencias_creadas'   => $received_data->id_incidencias_creadas 
            );
            $query = " UPDATE enc_incidencias_creadas SET descripcion_cerberus = :descripcion_cerberus
                        , sincronizado = true
                        WHERE id_incidencias_creadas= :id_incidencias_creadas";
            $statement = $connect->prepare($query);
            $statement->execute($data);
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
    }
    ///// Sanciones, no contestar encuesta 
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 
?>
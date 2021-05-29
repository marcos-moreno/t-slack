<?php
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) {
     
    require_once "../postgres.php";
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array();

    if ($received_data->action == 'fetchall') {
        $query = " SELECT * FROM empresa ORDER BY id_empresa DESC ";
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
            ':id_creado' =>  $_SESSION['id_empleado'],
            ':id_actualizadopor' =>  $_SESSION['id_empleado']


        );

        $query = "INSERT INTO empresa (id_creado,fecha_creado,empresa_nombre, empresa_rfc,empresa_activo,id_actualizado,fecha_actualizado)
        VALUES (:id_creado,CURRENT_TIMESTAMP,:first_name, :last_name,:checked::boolean,:id_actualizadopor,CURRENT_TIMESTAMP)";

        $statement = $connect->prepare($query);

        $statement->execute($data);

        $output = array(
            'message' => 'Empresa Registrada'
        );

        echo json_encode($output);

    }

    if ($received_data->action == 'fetchSingle') {
        $query = "SELECT * FROM empresa WHERE id_empresa = '" . $received_data->id . "' ";

        $statement = $connect->prepare($query);

        $statement->execute();

        $result = $statement->fetchAll();

        foreach ($result as $row) {
            $data['id'] = $row['id_empresa'];
            $data['first_name'] = $row['empresa_nombre'];
            $data['last_name'] = $row['empresa_rfc'];
            $data['checked'] = $row['empresa_activo'];

        } 
        echo json_encode($data);
    } 

    if ($received_data->action == 'Modificar') {
        $data = array(
            ':first_name' => $received_data->firstName,
            ':last_name' => $received_data->lastName,
            ':checked' => $received_data->checked,
            ':id_actualizadopor' =>  $_SESSION['id_empleado'],
            ':id' => $received_data->hiddenId

        );

        $query = "UPDATE empresa SET empresa_nombre = :first_name
                                    ,empresa_rfc = :last_name 
                                    ,fecha_actualizado = CURRENT_TIMESTAMP
                                    ,empresa_activo = :checked 
                                    ,id_actualizado = :id_actualizadopor
                WHERE id_empresa = :id";

        $statement = $connect->prepare($query);
        $statement->execute($data);

        $output = array(
            'message' => 'Empresa Actualizada'
        );

        echo json_encode($output);
    }

    if ($received_data->action == 'delete') {

        $query = "DELETE FROM empresa WHERE id_empresa = '" . $received_data->id . "' ";

        $statement = $connect->prepare($query);

        $statement->execute();

        $output = array(
            'message' => 'Empresa Eliminada'
        );

        echo json_encode($output);
    }

    if ($received_data->action == 'insertImage') {  
            try {
                $data = file_get_contents( '../img/empresas/adirh.jpg' );  
                $escaped = bin2hex( $data );   
                $datas = array(  ); 
                $query = "INSERT INTO refividrio.file(file, name, type, id_table) VALUES ( decode('{$escaped}' , 'hex'), 'logoADIRH', 'jpg' , 5); "; 
                $statement = $connect->prepare($query); 
                $statement->execute($datas); 
                $output = array(
                    'message' => 'Imagen Guardada'
                ); 
                echo json_encode($output);
            } catch (\Throwable $th) {
                echo $th;
            } 

            try {
                $data = file_get_contents( '../../img/edc2.png' );  
                $escaped = bin2hex( $data );   
                $datas = array(  ); 
                // $query = "INSERT INTO refividrio.file(file, name, type, id_table) VALUES 
                // ( decode('{$escaped}' , 'hex'), 'hoolman', 'png' , 4); "; 
                $query = "UPDATE refividrio.file 
                            SET 
                            file = decode('{$escaped}' , 'hex')
                            ,name = 'hoolman'
                            ,type='png'
                            WHERE id_file = 21;"; 
                $statement = $connect->prepare($query); 
                $statement->execute($datas); 
                $output = array(
                    'message' => 'Imagen Guardada'
                ); 
                echo json_encode($output);
            } catch (\Throwable $th) {
                echo $th;
            }
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

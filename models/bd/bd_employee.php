<?php
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) {
    require_once "../postgres.php";
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array();

    if ($received_data->action == 'fetchall') {
        $query = " SELECT e.*, s.nombre As segmento,empresa.empresa_nombre,empresa.id_empresa_cerberus  FROM empleado e
        INNER JOIN segmento s ON s.id_segmento =  e.id_segmento
        INNER JOIN empresa empresa ON empresa.id_empresa = s.id_empresa 
        ORDER BY empresa.id_empresa, s.nombre,e.paterno,e.materno,e.nombre DESC";
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 

    if ($received_data->action == 'insertSinc') {
        $query = "";
        try {
            $data = array( 
                ':id_creadopor' =>  $_SESSION['id_empleado'],
                ':nombre' => $received_data->model->nombre,
                ':paterno' => $received_data->model->paterno,
                ':materno' => $received_data->model->materno,
                ':celular' => $received_data->model->celular,
                ':correo' => $received_data->model->correo,
                ':genero' => $received_data->model->genero,
                ':id_actualizadopor' =>  $_SESSION['id_empleado'],
                ':usuario' => $received_data->model->usuario, 
                ':fecha_nacimiento' => $received_data->model->fecha_nacimiento,
                ':nss' =>  $received_data->model->nss,
                ':rfc' => $received_data->model->rfc,
                ':id_cerberus_empleado' => $received_data->model->id_cerberus_empleado,
                ':fecha_alta_cerberus' => $received_data->model->fecha_alta_cerberus, 
                ':perfilcalculo' => $received_data->model->perfilcalculo, 
                ':iddepartamento_cerbeus' => $received_data->model->iddepartamento_cerbeus, 
                ':idempresa_cerberus' => $received_data->model->idempresa_cerberus, 
                ':idsucursal_cerberus' => $received_data->model->idsucursal_cerberus,  
                ':id_compac' => $received_data->model->id_compac,  
                ':correo_verificado' => $received_data->model->correo_verificado,   
            ); 
            $query = "INSERT INTO empleado (id_segmento,id_creadopor,fecha_creado,nombre,paterno,materno,activo
                                ,celular,correo,genero,id_actualizadopor,fecha_actualizado,usuario
                                ,password,fecha_nacimiento,nss,rfc,id_cerberus_empleado
                                ,fecha_alta_cerberus,perfilcalculo,id_empresa,departamento_id
                                ,id_compac,correo_verificado) 
                    VALUES (
                    (SELECT id_segmento FROM refividrio.segmento WHERE id_cerberus = :idsucursal_cerberus LIMIT 1)
                    ,:id_creadopor,CURRENT_TIMESTAMP,:nombre,:paterno,:materno,true,:celular,:correo,:genero,
                    :id_actualizadopor,CURRENT_TIMESTAMP,LOWER(:usuario),md5('refividrio'),:fecha_nacimiento
                    ,(CASE WHEN :nss = '0' THEN NULL ELSE :nss END)
                    ,(CASE WHEN :rfc = '0' THEN NULL ELSE :rfc END)
                    ,:id_cerberus_empleado,:fecha_alta_cerberus,:perfilcalculo
                    ,(SELECT id_empresa FROM refividrio.empresa WHERE id_empresa_cerberus = :idempresa_cerberus LIMIT 1)
                    ,(SELECT departamento_id FROM refividrio.departamento WHERE id_cerberus = :iddepartamento_cerbeus LIMIT 1)
                    ,:id_compac
                    ,:correo_verificado
                    )  RETURNING id_empleado";
            $statement = $connect->prepare($query);
            $statement->execute($data);
            $result = $statement->fetchAll();
            foreach ($result as $row) {
                $datarol = array(
                    ':idempleado' =>  $row['id_empleado']
                );
                $queryrol = "INSERT INTO empleado_rol (id_rol, id_empleado) VALUES (2, :idempleado)";
                $statementrol = $connect->prepare($queryrol);
                $statementrol->execute($datarol);
            }
            $output = array(
                'message' => 'sinc succes'
            );
            echo json_encode($output); 
        } catch (PDOException $exc) {
            $output = array(
                'message' => 'error',
                'error' => $exc->getMessage()
            ); 
            echo json_encode($output); 
        }  
     }

     if ($received_data->action == 'empleados_parametro') {
        try { 
            $query = "SELECT * FROM Empleado WHERE activo = true AND id_cerberus_empleado is NOT null";
            $statement = $connect->prepare($query);
            $statement->execute();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            echo json_encode($data);
        } catch (PDOException $exc) {
            $output = array(
                'message' => 'error',
                'error' => $exc->getMessage()
            ); 
            echo json_encode($output); 
        }  
       
    }

    if ($received_data->action == 'updateParameter') {
        try {
            $data = array(  
                ':perfilcalculo'   => $received_data->model->perfilcalculo,
                ':id_empleado' => $received_data->model->id_empleado,
            );
            $query = "UPDATE empleado SET perfilcalculo =  :perfilcalculo  WHERE id_empleado = :id_empleado";
            $statement = $connect->prepare($query);
            $statement->execute($data);
            $output = array('message'=>'Usuario Actualizado','data'=>$received_data->model);
            echo json_encode($output);
        } catch (PDOException $exc) {
            $output = array(
                'message' => 'error',
                'error' => $exc->getMessage()
            ); 
            echo json_encode($output); 
        }  
    }
 
    if ($received_data->action == 'updateSync') {
        try {
            $data = array(  
                ':activo' => $received_data->model->activo,  
                ':id'   => $received_data->model->id_empleado,
                ':nss'   => $received_data->model->nss,
                ':rfc'   => $received_data->model->rfc,
                ':id_cerberus_empleado' => $received_data->model->id_cerberus_empleado,
            );
            $query = " UPDATE empleado SET   
                    fecha_actualizado = CURRENT_TIMESTAMP  
                    ,nss = :nss
                    ,rfc = :rfc
                    ,id_cerberus_empleado = :id_cerberus_empleado
                    ,activo = :activo  
                    WHERE id_empleado= :id";
            $statement = $connect->prepare($query);
            $statement->execute($data);
            $output = array(
                'message' => 'Usuario Actualizado',
                'data' => $received_data->model
            );
            echo json_encode($output);
        } catch (PDOException $exc) {
            $output = array(
                'message' => 'error',
                'error' => $exc->getMessage()
            ); 
            echo json_encode($output); 
        }  
        
    }

    // if ($received_data->action == 'insert') {
    //     try {
    //         $data = array(
    //             ':organization' => $received_data->organization,
    //             ':first_name' => $received_data->firstName,
    //             ':paternal_name' => $received_data->paternal_name,
    //             ':maternal_name' => $received_data->maternal_name,
    //             ':cellphone' => $received_data->cellphone,
    //             ':emp_email' => $received_data->emp_email,
    //             ':checked' => $received_data->checked,
    //             ':age' => $received_data->age,
    //             ':picked' => $received_data->picked,
    //             ':checked_poll' => $received_data->checked_poll,
    //             ':id_creadopor' =>  $_SESSION['id_empleado'],
    //             ':id_actualizadopor' =>  $_SESSION['id_empleado']
    //         );
    //         $user1 = strstr($received_data->firstName, ' ', true);
    //         $value = strstr($received_data->firstName, ' ', true) == '' ? $received_data->firstName : strstr($received_data->firstName, ' ', true);
    //         $user = $value   .".". $received_data->paternal_name;
    //         $password = "refividrio";
    //         $query = "INSERT INTO empleado (id_segmento,id_creadopor,fecha_creado,nombre,paterno,materno,activo,celular,correo,enviar_encuesta,genero,id_actualizadopor,fecha_actualizado,usuario,password,fecha_nacimiento) 
    //                 VALUES (:organization,:id_creadopor,CURRENT_TIMESTAMP,:first_name,:paternal_name,:maternal_name,:checked,:cellphone,:emp_email,:checked_poll,:picked,:id_actualizadopor,CURRENT_TIMESTAMP,LOWER('$user'),md5('$password'),:age)  RETURNING id_empleado";
    //         $statement = $connect->prepare($query);
    //         $statement->execute($data);
    //         $result = $statement->fetchAll();
    //         foreach ($result as $row) {
    //             $datarol = array(
    //                 ':idempleado' =>  $row['id_empleado']
    //             );
    //             $queryrol = "INSERT INTO empleado_rol (id_rol, id_empleado) VALUES (2, :idempleado)";
    //             $statementrol = $connect->prepare($queryrol);
    //             $statementrol->execute($datarol);
    //         }
    //         $output = array(
    //             'message' => 'Usuario Registrado'
    //         );
    //         echo json_encode($output); 
    //     } catch (PDOException $exc) {
    //         $output = array(
    //             'message' => 'error',
    //             'error' => $exc->getMessage()
    //         ); 
    //         echo json_encode($output); 
    //     }  
    // }

    // if ($received_data->action == 'fetchSingle') {
    //     $query = "SELECT * FROM empleado WHERE id_empleado = '" . $received_data->id . "' ";
    //     $statement = $connect->prepare($query);
    //     $statement->execute();
    //     $result = $statement->fetchAll();
    //     foreach ($result as $row) {
    //         $data['id'] = $row['id_empleado'];
    //         $data['organization'] = $row['id_segmento'];
    //         $data['first_name'] = $row['nombre'];
    //         $data['paternal_name'] = $row['paterno'];
    //         $data['maternal_name'] = $row['materno'];
    //         $data['cellphone'] = $row['celular'];
    //         $data['emp_email'] = $row['correo'];
    //         $data['checked'] = $row['activo'];
    //         $data['age'] = $row['fecha_nacimiento'];
    //         $data['picked'] = $row['genero'];
    //         $data['user'] = $row['usuario'];
    //         $data['checked_poll'] = $row['enviar_encuesta'];
    //         $data['nss'] = $row['nss'];
    //         $data['rfc'] = $row['rfc'];
    //         $data['id_cerberus_empleado'] = $row['id_cerberus_empleado'];
    //     }
    //     echo json_encode($data);
    // }

    // if ($received_data->action == 'update') {
    //     $data = array(
    //         ':organization' => $received_data->organization,
    //         ':first_name' => $received_data->firstName,
    //         ':paternal_name' => $received_data->paternal_name,
    //         ':maternal_name' => $received_data->maternal_name,
    //         ':cellphone' => $received_data->cellphone,
    //         ':emp_email' => $received_data->emp_email,
    //         ':checked' => $received_data->checked,
    //         ':user' => $received_data->user,
    //         ':checked_poll' => $received_data->checked_poll,
    //         ':age' => $received_data->age,
    //         ':picked' => $received_data->picked,        
    //         ':id'   => $received_data->hiddenId,
    //         ':nss'   => $received_data->nss,
    //         ':rfc'   => $received_data->rfc,
    //         ':id_cerberus_empleado' => $received_data->id_cerberus_empleado,
          
    //     );
    //     $query = " UPDATE empleado SET 
    //                 id_segmento = :organization
    //                 ,nombre = :first_name
    //                 ,paterno = :paternal_name 
    //                 ,materno = :maternal_name 
    //                 ,celular = :cellphone 
    //                 ,correo = :emp_email 
    //                 ,fecha_actualizado = CURRENT_TIMESTAMP 
    //                 ,activo = :checked 
    //                 ,enviar_encuesta = :checked_poll
    //                 ,fecha_nacimiento = :age
    //                 ,genero = :picked
    //                 ,usuario = :user
    //                 ,nss = :nss
    //                 ,rfc = :rfc
    //                 ,id_cerberus_empleado = :id_cerberus_empleado
    //                 WHERE id_empleado= :id";
    //     $statement = $connect->prepare($query);
    //     $statement->execute($data);
    //     $output = array(
    //         'message' => 'Usuario Actualizado'
    //     );
    //     echo json_encode($output);
    // }

    // if ($received_data->action == 'delete') {
    //     $query = "DELETE FROM empleado WHERE id_empleado = '" . $received_data->id . "' ";
    //     $statement = $connect->prepare($query);
    //     $statement->execute();
    //     $output = array(
    //         'message' => 'Usuario Eliminado'
    //     );
    //     echo json_encode($output);
    // }

    // if ($received_data->action == 'fetchByDepartament') {
    //     $query = "SELECT e.*,concat(paterno,' ',materno ,' ',nombre)As nom_largo FROM empleado e 
    //                 WHERE e.id_segmento = ". $received_data->id_segmento ." AND activo = true
    //                     ORDER BY nom_largo ASC";
    //     $statement = $connect->prepare($query);
    //     $statement->execute();
    //     while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    //         $data[] = $row;
    //     }
    //     echo json_encode($data);
    // }
    
    // if ($received_data->action == 'resetPassword') { 
    //     try {  
    //             $query = " UPDATE empleado SET  password =  md5('refividrio')  WHERE id_empleado= " . $received_data->id_empleado;
    //             $statement = $connect->prepare($query);
    //             $statement->execute(); 
    //             echo json_encode('Reset Password Success');  
    //     } catch (PDOException $exc) {
    //         $output = array(
    //             'message' => 'error',
    //             'error' => $exc->getMessage()
    //         ); 
    //         echo json_encode($output); 
    //     }  
    // }
 
    // if ($received_data->action == 'sincSelect') {
    //     $query = " SELECT e.*, s.nombre As segmento,empresa.empresa_nombre,empresa.id_empresa_cerberus  FROM empleado e
    //     INNER JOIN segmento s ON s.id_segmento =  e.id_segmento
    //     INNER JOIN empresa empresa ON empresa.id_empresa = s.id_empresa 
    //     WHERE id_cerberus_empleado IS NULL 
    //     ORDER BY empresa.id_empresa, s.nombre,e.paterno,e.materno,e.nombre DESC";
    //     $statement = $connect->prepare($query);
    //     $statement->execute();
    //     while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    //         $data[] = $row;
    //     }
    //     echo json_encode($data);
    // } 
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

 

?>
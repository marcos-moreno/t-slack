<?php
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) { 
    require_once "../postgres.php";
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array(); 



    if ($received_data->action == 'empleado') {
        $query = "SELECT

        emp.ID_Empresa
        ,E.nombre
        ,E.paterno
        ,E.materno
        ,D.modificado
        
        FROM Empresa AS Emp
            INNER JOIN Segmento AS S
                ON S.ID_Empresa = Emp.ID_Empresa
                AND S.Activo = true
            INNER JOIN Empleado AS E
                ON E.ID_Segmento = S.ID_Segmento
                AND E.Activo = true
            INNER JOIN empleado_direccion AS D
                ON D.ID_Empleado = E.ID_Empleado
            WHERE 
                E.id_empleado = " . $_SESSION['id_empleado'] ."    
        ORDER BY emp.ID_Empresa    
        
        ";

        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }

    if ($received_data->action == 'empleadoDireccion') {
        $query = "SELECT

        Emp.ID_Empleado
        ,Emp.ID_Cerberus_Empleado
        ,D.celular
        ,D.telefono_casa
        ,D.correo_electronico
        ,D.estado_civil
        ,D.escolaridad
        ,D.calle
        ,D.no_interior
        ,D.no_exterior
        ,D.referencia
        ,D.id_codigo_postal
        ,CP.codigo_postal
        ,CP.estado
        ,CP.municipio
        ,CP.cuidad
        ,CP.tipo_asentamiento
        ,CP.asentamiento
        
        FROM Empleado AS Emp
            INNER JOIN empleado_direccion AS D
                ON D.Cerberus = Emp.ID_Cerberus_Empleado
            INNER JOIN codigo_postal AS CP
                ON CP.id_codigo_postal = D.id_codigo_postal
        WHERE 
        Emp.ID_Empleado = " . $_SESSION['id_empleado'] ."    
        
        ";

        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }

    if ($received_data->action == 'editarDireccion') {
        $data = array(
            ':calle' => $received_data->calle,
            ':no_interior'=> $received_data->no_interior,
            ':no_exterior'=> $received_data->no_exterior,
            ':celular'=> $received_data->celular,
            ':casa'=> $received_data->casa,
            ':correo'=> $received_data->correo,
            ':estadocivil'=> $received_data->estadocivil,
            ':escolaridad'=> $received_data->escolaridad,
            ':asentamiento'=> $received_data->asentamiento,

        ); 
        
        //var_dump($received_data->no_interior);

        $query = "UPDATE refividrio.empleado_direccion SET 
                                     calle = :calle
                                     ,no_interior = :no_interior 
                                     ,no_exterior = :no_exterior
                                     ,celular = :celular
                                     ,telefono_casa = :casa
                                     ,correo_electronico = :correo
                                     ,estado_civil = :estadocivil
                                     ,escolaridad = :escolaridad
                                     ,id_codigo_postal = :asentamiento
                                     ,modificado = true

                 WHERE id_empleado =".$_SESSION['id_empleado']; 
        $statement = $connect->prepare($query);
        $statement->execute($data);

        //echo $query;
    
        $output = array(
            'message' => 'Datos Actualizados'
        );
    
        echo json_encode($output);
    }




    if ($received_data->action == 'getAddress') {
        $query = "SELECT 

                    CP.id_codigo_postal
                    ,CP.codigo_postal
                    ,CP.estado
                    ,CP.municipio
                    ,CP.cuidad
                    ,CP.tipo_asentamiento
                    ,CP.asentamiento

                    FROM  Codigo_Postal AS CP

                    WHERE 
                    CP.codigo_postal = '$received_data->codigo_postal'       
        
        ";

        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 

    if ($received_data->action == 'getAddress5') {
        $query = "SELECT 

                    CP.id_codigo_postal
                    ,CP.codigo_postal
                    ,CP.estado
                    ,CP.municipio
                    ,CP.cuidad
                    ,CP.tipo_asentamiento
                    ,CP.asentamiento

                    FROM  Codigo_Postal AS CP

                    WHERE 
                    CP.codigo_postal = '$received_data->codigo_postal'
                    AND CP.tipo_asentamiento = '$received_data->tipo_asentamiento'
        
        ";

        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 


    if ($received_data->action == 'getAddress5v2') {
        $query = "SELECT 

                    CP.id_codigo_postal
                    ,CP.codigo_postal
                    ,CP.estado
                    ,CP.municipio
                    ,CP.cuidad
                    ,CP.tipo_asentamiento
                    ,CP.asentamiento

                    FROM  Codigo_Postal AS CP

                    WHERE 
                    CP.codigo_postal = '$received_data->codigo_postal'
                    AND CP.tipo_asentamiento = '$received_data->tipo_asentamiento'
                    AND CP.id_codigo_postal = $received_data->id_cp
        
        ";

        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 


    if ($received_data->action == 'getAddress1') {
        $query = "SELECT 

                    CP.codigo_postal
                    ,CP.estado

                    FROM  Codigo_Postal AS CP

                    WHERE 
                    CP.codigo_postal = '$received_data->codigo_postal' 

                     GROUP BY 1,2     
        
        ";

        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 


    if ($received_data->action == 'getAddress2') {
        $query = "SELECT 

                    CP.codigo_postal
                    ,CP.municipio

                    FROM  Codigo_Postal AS CP

                    WHERE 
                    CP.codigo_postal = '$received_data->codigo_postal' 

                    GROUP BY 1,2
        ";

        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 


    if ($received_data->action == 'getAddress3') {
        $query = "SELECT 

                    CP.codigo_postal
                    ,CP.cuidad


                    FROM  Codigo_Postal AS CP

                    WHERE 
                    CP.codigo_postal = '$received_data->codigo_postal'

                    GROUP BY 1,2

        ";

        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 


   if ($received_data->action == 'getAddress4') {
        $query = "SELECT 

                    CP.codigo_postal
                    ,CP.tipo_asentamiento


                    FROM  Codigo_Postal AS CP

                    WHERE 
                    CP.codigo_postal = '$received_data->codigo_postal'
                    
                    GROUP BY 1,2

        ";

        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 





    if ($received_data->action == 'fetchAccount') {
        $query = " SELECT e.id_empleado, e.id_segmento, e.id_creadopor, e.fecha_creado, 
                        e.nombre, e.paterno, e.materno, e.activo, e.celular, 
                        e.correo, e.enviar_encuesta, e.genero, e.id_actualizadopor, 
                        e.fecha_actualizado, e.usuario, e.password, e.fecha_nacimiento,
                        s.nombre AS segmento,empresa.empresa_nombre,empresa.id_empresa,
                        e.id_talla_playera,e.id_numero_zapato,e.id_cerberus_empleado,e.fecha_alta_cerberus
                        ,e.correo_verificado
                    FROM refividrio.empleado e
                    INNER JOIN refividrio.segmento s ON s.id_segmento = e.id_segmento
                    INNER JOIN refividrio.empresa empresa ON empresa.id_empresa = s.id_empresa 
                    WHERE  id_empleado =".$_SESSION['id_empleado'];
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 
    if ($received_data->action == 'validaDatos') {
        $query = " SELECT CASE WHEN (id_talla_playera IS NULL OR id_numero_zapato IS NULL) THEN false ELSE true END valido
                    FROM refividrio.empleado e
                    INNER JOIN refividrio.segmento s ON s.id_segmento = e.id_segmento
                    INNER JOIN refividrio.empresa empresa ON empresa.id_empresa = s.id_empresa 
                    WHERE  id_empleado =".$_SESSION['id_empleado'];
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } 
    if ($received_data->action == 'update') {
        $data = array(
            ':fecha_nacimiento' => $received_data->data->fecha_nacimiento,
            ':celular'=> $received_data->data->celular,
            ':correo'=> $received_data->data->correo, 
            ':id_actualizadopor' =>  $_SESSION['id_empleado'],  
            ':id_talla_playera'=> $received_data->data->id_talla_playera,
            ':id_numero_zapato'=> $received_data->data->id_numero_zapato,
            ':correo_verificado'=> $received_data->data->correo_verificado,
        ); 
        $query = "UPDATE refividrio.empleado SET 
                                     celular = :celular
                                     ,correo = :correo 
                                     ,fecha_actualizado = CURRENT_TIMESTAMP
                                     ,fecha_nacimiento = :fecha_nacimiento 
                                     ,id_actualizadopor = :id_actualizadopor
                                     ,id_talla_playera = :id_talla_playera
                                     ,id_numero_zapato = :id_numero_zapato 
                                     ,correo_verificado = :correo_verificado 
                                     ,desc_mail_v = 'accepted_email'
                 WHERE id_empleado =".$_SESSION['id_empleado']; 
        $statement = $connect->prepare($query);
        $statement->execute($data);
    
        $output = array(
            'message' => 'data update'
        );
    
        echo json_encode($output);
    }

    if ($received_data->action == 'updateStateEmailError') {
        $data = array(
            ':desc_mail_v' => $received_data->message, 
        ); 
        $query = "UPDATE refividrio.empleado SET  desc_mail_v = :desc_mail_v ,correo_verificado = false 
                 WHERE id_empleado =".$_SESSION['id_empleado']; 
        $statement = $connect->prepare($query);
        $statement->execute($data);
        $output = array(
            'message' => 'data update'
        ); 
        echo json_encode($output);
    }

}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
}
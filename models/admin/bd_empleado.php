<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Empleado($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Empleado($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Empleado($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Empleado($data,$connect,$received_data);
            $model->select();
        break;
        case 'gteEmpleadosByLider': 
            $model = new Empleado($data,$connect,$received_data);
            $model->gteEmpleadosByLider();
        break;
        case 'resetPassword': 
            $model = new Empleado($data,$connect,$received_data);
            $model->resetPassword();
        break;
        case 'searchById': 
            $model = new Empleado($data,$connect,$received_data);
            $model->searchById($received_data->id_empleado);
        break;
        case 'searchBySession': 
            $model = new Empleado($data,$connect,$received_data);
            $model->searchById($_SESSION['id_empleado']); 
        break; 
        case 'selectSimple': 
            $model = new Empleado($data,$connect,$received_data);
            $model->selectSimple(); 
        break; 
        
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Empleado 
{   
    
    private $output = null;
    private $data = array(); 
    private $connect = null;
    private $received_data = null;
    public function __construct($data,$connect,$received_data){
        $this->data  = $data;
        $this->connect = $connect;
        $this->received_data = $received_data;
    }

    public function insert(){
        try {
            $data = array(
                    ':id_segmento' => $this->received_data->model->id_segmento,
                        ':id_creadopor' => $_SESSION['id_empleado'], 
                        ':nombre' => $this->received_data->model->nombre,
                        ':paterno' => $this->received_data->model->paterno,
                        ':materno' => $this->received_data->model->materno,
                        ':activo' => $this->received_data->model->activo,
                        ':celular' => $this->received_data->model->celular,
                        ':correo' => $this->received_data->model->correo, 
                        ':genero' => $this->received_data->model->genero,
                        ':id_actualizadopor' => $_SESSION['id_empleado'], 
                        ':usuario' => $this->received_data->model->usuario,
                        ':password' => 'refividrio',
                        ':fecha_nacimiento' => $this->received_data->model->fecha_nacimiento,
                        ':nss' => $this->received_data->model->nss,
                        ':rfc' => $this->received_data->model->rfc,
                        ':id_cerberus_empleado' => $this->received_data->model->id_cerberus_empleado,
                        ':fecha_alta_cerberus' => $this->received_data->model->fecha_alta_cerberus,
                        ':perfilcalculo' => $this->received_data->model->perfilcalculo,  
                        ':correo_verificado' => $this->received_data->model->correo_verificado,  
                        ':id_compac' => $this->received_data->model->id_compac,  
                        ':ev_puesto_id' => $this->received_data->model->ev_puesto_id,  
                        ':departamento_id' => $this->received_data->model->departamento_id,  

                    ); 
            $query = 'INSERT INTO empleado (id_segmento,id_creadopor,fecha_creado,nombre,paterno,materno,activo,celular,correo,genero,id_actualizadopor,
            fecha_actualizado,usuario,password,fecha_nacimiento,nss,rfc,id_cerberus_empleado,fecha_alta_cerberus,perfilcalculo,
            correo_verificado,id_compac,ev_puesto_id,departamento_id) 
                VALUES 
            (:id_segmento,:id_creadopor,now(),:nombre,:paterno,:materno,:activo,:celular,:correo,:genero,:id_actualizadopor,
            now(),:usuario,MD5(:password),:fecha_nacimiento,:nss,:rfc,:id_cerberus_empleado,:fecha_alta_cerberus,:perfilcalculo,
            :correo_verificado,:id_compac,:ev_puesto_id,:departamento_id) RETURNING id_empleado ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            $result = $statement->fetchAll();
            foreach ($result as $row) {
                // echo  $row['id_empleado'];
                $datarol = array(
                    ':idempleado' =>  $row['id_empleado']
                );
                $queryrol = "INSERT INTO empleado_rol (id_rol, id_empleado) VALUES (2, :idempleado)";
                $statementrol = $this->connect->prepare($queryrol);
                $statementrol->execute($datarol);
            }

            $output = array('message' => 'Data Inserted'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        } 
    } 

    public function update(){
        try {
            $parameters = array(
                    ':id_empleado' => $this->received_data->model->id_empleado, 
                        ':id_segmento' => $this->received_data->model->id_segmento,   
                        ':nombre' => $this->received_data->model->nombre, 
                        ':paterno' => $this->received_data->model->paterno, 
                        ':materno' => $this->received_data->model->materno, 
                        ':activo' => $this->received_data->model->activo, 
                        ':celular' => $this->received_data->model->celular, 
                        ':correo' => $this->received_data->model->correo,  
                        ':genero' => $this->received_data->model->genero, 
                        ':id_actualizadopor' => $_SESSION['id_empleado'],  
                        ':usuario' => $this->received_data->model->usuario,  
                        ':fecha_nacimiento' => $this->received_data->model->fecha_nacimiento, 
                        ':nss' => $this->received_data->model->nss, 
                        ':rfc' => $this->received_data->model->rfc, 
                        ':id_cerberus_empleado' => $this->received_data->model->id_cerberus_empleado,  
                        ':fecha_alta_cerberus' => $this->received_data->model->fecha_alta_cerberus, 
                        ':perfilcalculo' => $this->received_data->model->perfilcalculo, 
                        ':correo_verificado' => $this->received_data->model->correo_verificado, 
                        ':id_empresa' => $this->received_data->model->id_empresa, 
                        ':id_compac' => $this->received_data->model->id_compac, 
                        ':ev_puesto_id' => $this->received_data->model->ev_puesto_id,  
                        ':departamento_id' => $this->received_data->model->departamento_id,   
                    ); 
            $query = 'UPDATE empleado SET id_segmento=:id_segmento,nombre=:nombre,paterno=:paterno,materno=:materno,
            activo=:activo,
            celular=:celular,correo=:correo,genero=:genero,id_actualizadopor=:id_actualizadopor,fecha_actualizado=now(),
            usuario=:usuario,fecha_nacimiento=:fecha_nacimiento,nss=:nss,rfc=:rfc,id_cerberus_empleado=:id_cerberus_empleado
           ,fecha_alta_cerberus=:fecha_alta_cerberus,perfilcalculo=:perfilcalculo
            ,correo_verificado=:correo_verificado,id_empresa=:id_empresa 
            ,id_compac=:id_compac,ev_puesto_id=:ev_puesto_id,departamento_id=:departamento_id
            WHERE  id_empleado = :id_empleado ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);  
            $output = array('message' => 'Data Updated'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    } 
    public function resetPassword(){
        try {  
                $data = array(
                    ':id_empleado' => $this->received_data->model->id_empleado,  
                ); 
                $query = "UPDATE empleado SET  password =  md5('refividrio') WHERE  id_empleado = :id_empleado ;";
                $statement = $this->connect->prepare($query); 
                $statement->execute($data);
                echo json_encode('Reset Password Success');  
        } catch (PDOException $e) {
            echo json_encode( $e );
        }  
    }

    public function gteEmpleadosByLider(){
        try {
            $parameters = array(
                    ':id_empleado' => $_SESSION['id_empleado'],  
                );   
            $query = '
                SELECT 
                    id_empleado,id_segmento,id_creadopor,fecha_creado,nombre
                    ,paterno,materno,activo,celular
                    ,correo,enviar_encuesta
                    ,genero,id_actualizadopor,fecha_actualizado,usuario,password
                    ,fecha_nacimiento,nss,rfc,id_cerberus_empleado
                    ,id_talla_playera,id_numero_zapato,fecha_alta_cerberus,perfilcalculo,correo_verificado,
                    id_empresa,desc_mail_v ,id_compac,ev_puesto_id,departamento_id
                FROM refividrio.empleado
                WHERE departamento_id IN 
                    (SELECT departamento_id FROM refividrio.lider_departamento WHERE id_empleado = :id_empleado)
                ORDER BY departamento_id DESC,paterno,materno,nombre;        
            ' ;         
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    // $row['un_talla'] = $this->search_union($row,'un_talla','id_talla','id_numero_zapato');
                    // $row['segmento'] = $this->search_union($row,'segmento','id_segmento','id_segmento');
                    // // $row['un_talla'] = $this->search_union($row,'un_talla','id_talla','id_talla_playera');
                    // $row['empresa'] = $this->search_unions('empresa','id_empresa', $row['segmento'][0]['id_empresa']  );
                    // $row['ev_puesto'] = $this->search_union($row,'ev_puesto','ev_puesto_id','ev_puesto_id');
                    // $row['departamento'] = $this->search_union($row,'departamento','departamento_id','departamento_id');
                    // $row['un_talla'] = $this->search_union($row,'un_talla','id_talla','id_talla_playera');
                    $data[] = $row;
            } 
            echo json_encode($data); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    }
   
    public function selectSimple(){
        try {  
            $query = "
                SELECT 
                    e.id_empleado,e.id_segmento,e.id_creadopor,e.fecha_creado,e.nombre
                    ,e.paterno,e.materno,e.activo,e.celular
                    ,e.correo,e.enviar_encuesta
                    ,e.genero,e.id_actualizadopor,e.fecha_actualizado,e.usuario,e.password
                    ,e.fecha_nacimiento,e.nss,e.rfc,e.id_cerberus_empleado
                    ,e.id_talla_playera,e.id_numero_zapato,e.fecha_alta_cerberus,e.perfilcalculo,e.correo_verificado,
                    e.id_empresa,e.desc_mail_v ,e.id_compac,e.ev_puesto_id,e.departamento_id,d.nombre As departamento
                FROM empleado  e
                INNER JOIN departamento d ON d.departamento_id = e.departamento_id
                WHERE e.activo=true
                ";
            switch ($this->received_data->method) {
                case 'reportes':
                    $query .= " AND e.id_segmento IN (SELECT id_segmento from segmento WHERE id_empresa IN (1,2,3)) ORDER BY id_empleado DESC";
                    break; 
                case 'reportesJasperAdmin':
                    $query .= " AND e.id_segmento = " . $this->received_data->filter;
                    break; 
                default:
                    $query .= "";
                    break;
            }
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            } 
            echo json_encode($data); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    }

    public function select(){
        try {  
            $parameters = array(
                ':filter' => $this->received_data->filter,  
                ':activo' => $this->received_data->activo,  
                ':id_segmento' => $this->received_data->id_segmento,  
                ':id_empresa' => $this->received_data->id_empresa,  
                ':id_empleado_filtro' =>  $this->received_data->id_empleado_filtro,  
            );   
            $query = "
                SELECT 
                    e.id_empleado,e.id_segmento,e.id_creadopor,e.fecha_creado,e.nombre
                    ,e.paterno,e.materno,e.activo,e.celular
                    ,e.correo,e.enviar_encuesta
                    ,e.genero,e.id_actualizadopor,e.fecha_actualizado,e.usuario,e.password
                    ,e.fecha_nacimiento,e.nss,e.rfc,e.id_cerberus_empleado
                    ,e.id_talla_playera,e.id_numero_zapato,e.fecha_alta_cerberus,e.perfilcalculo,e.correo_verificado,
                    e.id_empresa,e.desc_mail_v ,e.id_compac,e.ev_puesto_id,e.departamento_id
                    ,em.empresa_observaciones,s.nombre As segmento,d.nombre As departamento
                FROM empleado e
                INNER JOIN segmento s ON s.id_segmento = e.id_segmento 
                INNER JOIN empresa em ON em.id_empresa = s.id_empresa
                INNER JOIN departamento d ON d.departamento_id = e.departamento_id
                    WHERE  
                    (   
                        CONCAT(e.paterno ,' ',e.materno,' ',e.nombre) ILIKE '%' || :filter || '%' 
                        OR e.nombre ILIKE '%' || :filter || '%' 
                        OR e.paterno ILIKE '%' || :filter || '%' 
                        OR e.materno ILIKE '%' || :filter || '%' 
                        OR CAST(e.nss AS VARCHAR (100)) ILIKE '%' || :filter || '%' 
                        OR e.rfc ILIKE '%' || :filter || '%' 
                        OR e.perfilcalculo ILIKE '%' || :filter || '%' 
                        OR e.usuario ILIKE '%' || :filter || '%' 
                        OR CAST(e.id_cerberus_empleado AS VARCHAR (100)) ILIKE '%' || :filter || '%' 
                        OR CAST(e.id_empleado AS VARCHAR (100)) ILIKE '%' || :filter || '%'  
                    ) 
                    AND  e.activo = :activo
                    AND  e.id_segmento = (CASE WHEN :id_segmento::Integer = 0 THEN e.id_segmento ELSE :id_segmento END)
                    AND  s.id_empresa = :id_empresa
                    AND  e.id_empleado::character varying = (CASE WHEN :id_empleado_filtro <> '' 
                                                                    THEN :id_empleado_filtro::character varying 
                                                                    ELSE e.id_empleado::character varying END)
                ORDER BY e.id_segmento ASC, CONCAT(e.paterno ,' ',e.materno,' ',e.nombre) ASC
                ";  
            $statement = $this->connect->prepare($query);
            $statement->execute($parameters);
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            echo json_encode($data); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage(),"filter" => $this->received_data); 
            echo json_encode($output); 
            return false;
        }  
    }
 
    public function searchById($id){
        try {  
            $parameters = array('id_empleado' => $id);
            $query = '
                SELECT 
                    id_empleado,id_segmento,id_creadopor,fecha_creado,nombre
                    ,paterno,materno,activo,celular
                    ,correo,enviar_encuesta
                    ,genero,id_actualizadopor,fecha_actualizado,usuario,password
                    ,fecha_nacimiento,nss,rfc,id_cerberus_empleado
                    ,id_talla_playera,id_numero_zapato,fecha_alta_cerberus,perfilcalculo,correo_verificado,
                    id_empresa,desc_mail_v ,id_compac,ev_puesto_id,departamento_id
                FROM empleado   
                    WHERE id_empleado=:id_empleado';
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    // $row['segmento'] = $this->search_union($row,'segmento','id_segmento','id_segmento');
                    // $row['empresa'] = $this->search_unions('empresa','id_empresa', $row['segmento'][0]['id_empresa']  );
                    // $row['ev_puesto'] = $this->search_union($row,'ev_puesto','ev_puesto_id','ev_puesto_id');
                    // $row['departamento'] = $this->search_union($row,'departamento','departamento_id','departamento_id');
                    $data[] = $row;
            }
            echo json_encode($data); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    }
    
    public function search_union($row,$table_origen,$fk_table_origen,$fk_table_usage){
        $data = array(); 
        try {    
            $query = 'SELECT * FROM '. $table_origen . '   WHERE '. $fk_table_origen . ' = ' . (isset($row[$fk_table_usage])==false?0:$row[$fk_table_usage]) ;               
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
                    $data[] = $row;
            }  
            return $data; 
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage());  
            return array("nombre"=>"No Asignado","id_departamento"=>0); 
        }  
    }

    public function search_unions($table_origen,$fk_table_usage,$fk_value){
        $data = array(); 
        try {    
            $query = 'SELECT * FROM '. $table_origen . '   WHERE '. $fk_table_usage . ' = ' . $fk_value;               
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
                    $data[] = $row;
            }  
            return $data; 
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return array("nombre"=>"No Asignado","id_departamento"=>0); 
        }  
    }
    public function delete(){
        try {
            $data = array(
                   ':id_empleado' => $this->received_data->model->id_empleado,
                    ); 
            $query = 'DELETE FROM empleado WHERE id_empleado = :id_empleado ;'; 
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Deleted'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
        }  
    }

  
    

} 
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
        case 'resetPassword': 
            $model = new Empleado($data,$connect,$received_data);
            $model->resetPassword();
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
                        
                    ); 
            $query = 'INSERT INTO empleado (id_segmento,id_creadopor,fecha_creado,nombre,paterno,materno,activo,celular,correo,genero,id_actualizadopor,
            fecha_actualizado,usuario,password,fecha_nacimiento,nss,rfc,id_cerberus_empleado,fecha_alta_cerberus,perfilcalculo,
            correo_verificado,id_compac) VALUES 
            (:id_segmento,:id_creadopor,now(),:nombre,:paterno,:materno,:activo,:celular,:correo,:genero,:id_actualizadopor,
            now(),:usuario,MD5(:password),:fecha_nacimiento,:nss,:rfc,:id_cerberus_empleado,:fecha_alta_cerberus,:perfilcalculo,
            :correo_verificado,id_compac) RETURNING id_empleado ;';

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
            $data = array(
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
                        
                    ); 
            $query = 'UPDATE empleado SET id_segmento=:id_segmento,nombre=:nombre,paterno=:paterno,materno=:materno,
            activo=:activo,
            celular=:celular,correo=:correo,genero=:genero,id_actualizadopor=:id_actualizadopor,fecha_actualizado=now(),
            usuario=:usuario,fecha_nacimiento=:fecha_nacimiento,nss=:nss,rfc=:rfc,id_cerberus_empleado=:id_cerberus_empleado
           ,fecha_alta_cerberus=:fecha_alta_cerberus,perfilcalculo=:perfilcalculo
            ,correo_verificado=:correo_verificado,id_empresa=:id_empresa 
            ,id_compac=:id_compac
            WHERE  id_empleado = :id_empleado ;';

            $statement = $this->connect->prepare($query); 
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
    public function select(){
        try {  
             
        $query = 'SELECT id_empleado,id_segmento,id_creadopor,fecha_creado,nombre,paterno,materno,activo,celular,correo,enviar_encuesta
                ,genero,id_actualizadopor,fecha_actualizado,usuario,password,fecha_nacimiento,nss,rfc,id_cerberus_empleado
                ,id_talla_playera,id_numero_zapato,fecha_alta_cerberus,perfilcalculo,correo_verificado,
                id_empresa,desc_mail_v ,id_compac
                    FROM empleado  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    // $row['un_talla'] = $this->search_union($row,'un_talla','id_talla','id_numero_zapato');
                    $row['segmento'] = $this->search_union($row,'segmento','id_segmento','id_segmento');
                    // $row['un_talla'] = $this->search_union($row,'un_talla','id_talla','id_talla_playera');
                    $row['empresa'] = $this->search_unions('empresa','id_empresa', $row['segmento'][0]['id_empresa']  );
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
    
    public function search_union($row,$table_origen,$fk_table_origen,$fk_table_usage){
        $data = array(); 
        try {    
            $query = 'SELECT * FROM '. $table_origen . '   WHERE '. $fk_table_origen . ' = ' .$row[$fk_table_usage] ;               
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
                    $data[] = $row;
            }  
            return $data; 
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
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
            $output = array('message' => $exc->getMessage()
        ); 
            echo json_encode($output); 
            return false;
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
            return false;
        }  
    }

  
    

} 
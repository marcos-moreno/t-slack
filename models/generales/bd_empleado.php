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
                            ':id_creadopor' => $this->received_data->model->id_creadopor,
                            ':fecha_creado' => $this->received_data->model->fecha_creado,
                            ':nombre' => $this->received_data->model->nombre,
                            ':paterno' => $this->received_data->model->paterno,
                            ':materno' => $this->received_data->model->materno,
                            ':activo' => $this->received_data->model->activo,
                            ':celular' => $this->received_data->model->celular,
                            ':correo' => $this->received_data->model->correo,
                            ':enviar_encuesta' => $this->received_data->model->enviar_encuesta,
                            ':genero' => $this->received_data->model->genero,
                            ':id_actualizadopor' => $this->received_data->model->id_actualizadopor,
                            ':fecha_actualizado' => $this->received_data->model->fecha_actualizado,
                            ':usuario' => $this->received_data->model->usuario,
                            ':password' => $this->received_data->model->password,
                            ':fecha_nacimiento' => $this->received_data->model->fecha_nacimiento,
                            ':nss' => $this->received_data->model->nss,
                            ':rfc' => $this->received_data->model->rfc,
                            ':id_cerberus_empleado' => $this->received_data->model->id_cerberus_empleado,
                            
                    ); 
        $query = 'INSERT INTO empleado (id_segmento,id_creadopor,fecha_creado,nombre,paterno,materno,activo,celular,correo,enviar_encuesta,genero,id_actualizadopor,fecha_actualizado,usuario,password,fecha_nacimiento,nss,rfc,id_cerberus_empleado) VALUES (:id_segmento,:id_creadopor,:fecha_creado,:nombre,:paterno,:materno,:activo,:celular,:correo,:enviar_encuesta,:genero,:id_actualizadopor,:fecha_actualizado,:usuario,:password,:fecha_nacimiento,:nss,:rfc,:id_cerberus_empleado) ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Inserted'); 
            echo json_encode($output); 
            return true;
        } catch (Exception $exc) {
            $output = array('message' => $exc); 
            echo json_encode($output); 
            return false;
        } 
    } 

    public function update(){
        try {
            $data = array(
                    ':id_empleado' => $this->received_data->model->id_empleado, 
                            ':id_segmento' => $this->received_data->model->id_segmento, 
                            ':id_creadopor' => $this->received_data->model->id_creadopor, 
                            ':fecha_creado' => $this->received_data->model->fecha_creado, 
                            ':nombre' => $this->received_data->model->nombre, 
                            ':paterno' => $this->received_data->model->paterno, 
                            ':materno' => $this->received_data->model->materno, 
                            ':activo' => $this->received_data->model->activo, 
                            ':celular' => $this->received_data->model->celular, 
                            ':correo' => $this->received_data->model->correo, 
                            ':enviar_encuesta' => $this->received_data->model->enviar_encuesta, 
                            ':genero' => $this->received_data->model->genero, 
                            ':id_actualizadopor' => $this->received_data->model->id_actualizadopor, 
                            ':fecha_actualizado' => $this->received_data->model->fecha_actualizado, 
                            ':usuario' => $this->received_data->model->usuario, 
                            ':password' => $this->received_data->model->password, 
                            ':fecha_nacimiento' => $this->received_data->model->fecha_nacimiento, 
                            ':nss' => $this->received_data->model->nss, 
                            ':rfc' => $this->received_data->model->rfc, 
                            ':id_cerberus_empleado' => $this->received_data->model->id_cerberus_empleado, 
                    ); 
            $query = 'UPDATE empleado SET id_segmento=:id_segmento,id_creadopor=:id_creadopor,fecha_creado=:fecha_creado,nombre=:nombre,paterno=:paterno,
            materno=:materno,activo=:activo,celular=:celular,correo=:correo,enviar_encuesta=:enviar_encuesta,genero=:genero,id_actualizadopor=:id_actualizadopor
            ,fecha_actualizado=:fecha_actualizado,usuario=:usuario,password=:password,fecha_nacimiento=:fecha_nacimiento,nss=:nss,rfc=:rfc,
            id_cerberus_empleado=:id_cerberus_empleado
            WHERE  id_empleado = :id_empleado ;';
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Updated'); 
            echo json_encode($output); 
            return true;
        } catch (Exception $exc) {
            $output = array('message' => $exc); 
            echo json_encode($output); 
            return false;
        }  
    } 

    public function select(){
        try {  
             
        $query = 'SELECT id_empleado,id_segmento,id_creadopor,fecha_creado,nombre,paterno,materno,activo,celular,correo,enviar_encuesta,genero,id_actualizadopor,
        fecha_actualizado,usuario,password,fecha_nacimiento,nss,rfc,id_cerberus_empleado,id_talla_playera,id_numero_zapato 
                    FROM empleado  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['segmento'] = $this->search_union($row,'segmento','id_segmento','id_segmento');
                    $data[] = $row;
            }

        
            echo json_encode($data); 
            return true;
        } catch (Exception $exc) {
            $output = array('message' => $exc); 
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
        } catch (Exception $exc) {
            $output = array('message' => $exc); 
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
        } catch (Exception $exc) {
            $output = array('message' => $exc); 
            echo json_encode($output); 
            return false;
        }  
    }

  
    

} 
<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Empresa($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Empresa($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Empresa($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Empresa($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Empresa 
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
                    ':id_creado' =>  $_SESSION['id_empleado'], 
                            ':empresa_nombre' => $this->received_data->model->empresa_nombre,
                            ':empresa_rfc' => $this->received_data->model->empresa_rfc,
                            ':empresa_observaciones' => $this->received_data->model->empresa_observaciones,
                            ':empresa_activo' => $this->received_data->model->empresa_activo,
                            ':id_actualizado' =>  $_SESSION['id_empleado'] ,
                            ':id_empresa_cerberus' => $this->received_data->model->id_empresa_cerberus,
                            
                    ); 
        $query = 'INSERT INTO empresa (id_creado,fecha_creado,empresa_nombre,empresa_rfc,empresa_observaciones,empresa_activo,id_actualizado,fecha_actualizado,id_empresa_cerberus)
         VALUES (:id_creado,NOW(),:empresa_nombre,:empresa_rfc,:empresa_observaciones,:empresa_activo,:id_actualizado,now(),:id_empresa_cerberus) ;';

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
                    ':id_empresa' => $this->received_data->model->id_empresa,   
                            ':empresa_nombre' => $this->received_data->model->empresa_nombre, 
                            ':empresa_rfc' => $this->received_data->model->empresa_rfc, 
                            ':empresa_observaciones' => $this->received_data->model->empresa_observaciones, 
                            ':empresa_activo' => $this->received_data->model->empresa_activo, 
                            ':id_actualizado' =>  $_SESSION['id_empleado'],  
                            ':id_empresa_cerberus' => $this->received_data->model->id_empresa_cerberus, 
                             
                    ); 
        $query = 'UPDATE empresa SET empresa_nombre=:empresa_nombre,empresa_rfc=:empresa_rfc,empresa_observaciones=:empresa_observaciones,empresa_activo=:empresa_activo,id_actualizado=:id_actualizado,fecha_actualizado=now(),id_empresa_cerberus=:id_empresa_cerberus WHERE  id_empresa = :id_empresa ;';

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
             
        $query = 'SELECT id_empresa,id_creado,fecha_creado,empresa_nombre,empresa_rfc,empresa_observaciones,empresa_activo,id_actualizado,
        fecha_actualizado,id_empresa_cerberus 
                    FROM empresa  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {    $data[] = $row;
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
                   ':id_empresa' => $this->received_data->model->id_empresa,
                            
                    ); 
        $query = 'DELETE FROM empresa WHERE id_empresa = :id_empresa ;'; 

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
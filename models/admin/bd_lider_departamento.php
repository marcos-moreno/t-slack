<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Lider_departamento($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Lider_departamento($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Lider_departamento($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Lider_departamento($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Lider_departamento 
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
                    ':id_empleado' => $this->received_data->model->id_empleado,
                        ':departamento_id' => $this->received_data->model->departamento_id,
                        ':tipo_lider' => $this->received_data->model->tipo_lider,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        
                    ); 
        $query = 'INSERT INTO lider_departamento (id_empleado,departamento_id,tipo_lider,creado,actualizado,creadopor,actualizadopor) VALUES (:id_empleado,:departamento_id,:tipo_lider,Now(),Now(),:creadopor,:actualizadopor) ;';

            $statement = $this->connect->prepare($query); 
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

    public function update(){
        try {
            $data = array(
                    ':lider_departamento_id' => $this->received_data->model->lider_departamento_id, 
                        ':id_empleado' => $this->received_data->model->id_empleado, 
                        ':departamento_id' => $this->received_data->model->departamento_id, 
                        ':tipo_lider' => $this->received_data->model->tipo_lider, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                         
                    ); 
            $query = 'UPDATE lider_departamento SET id_empleado=:id_empleado,departamento_id=:departamento_id,tipo_lider=:tipo_lider,actualizado=Now(),actualizadopor=:actualizadopor WHERE  lider_departamento_id = :lider_departamento_id ;';

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

    public function select(){
        try {  
             
        $query = 'SELECT lider_departamento_id,id_empleado,departamento_id,tipo_lider,creado,actualizado,creadopor,actualizadopor 
                    FROM lider_departamento  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_empleado');
                  
                    $row['departamento'] = $this->search_union($row,'departamento','departamento_id','departamento_id');
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
            return json_encode($output);  
        }  
    }
    public function delete(){
        try {  
            $data = array(
                   ':lider_departamento_id' => $this->received_data->model->lider_departamento_id,
                            
                    ); 
        $query = 'DELETE FROM lider_departamento WHERE lider_departamento_id = :lider_departamento_id ;'; 

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
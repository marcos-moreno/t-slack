<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Tipo($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Tipo($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Tipo($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Tipo($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Tipo 
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
                    ':tipo' => $this->received_data->model->tipo,
                            ':descripcion' => $this->received_data->model->descripcion,
                            ':direct_data' => $this->received_data->model->direct_data,
                            ':opcion_multiple' => $this->received_data->model->opcion_multiple,
                            
                    ); 
        $query = 'INSERT INTO tipo (tipo,descripcion,direct_data,opcion_multiple) VALUES (:tipo,:descripcion,:direct_data,:opcion_multiple) ;';

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
                    ':id_tipo' => $this->received_data->model->id_tipo, 
                            ':tipo' => $this->received_data->model->tipo, 
                            ':descripcion' => $this->received_data->model->descripcion, 
                            ':direct_data' => $this->received_data->model->direct_data, 
                            ':opcion_multiple' => $this->received_data->model->opcion_multiple, 
                             
                    ); 
        $query = 'UPDATE tipo SET tipo=:tipo,descripcion=:descripcion,direct_data=:direct_data,opcion_multiple=:opcion_multiple WHERE  id_tipo = :id_tipo ;';

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
             
        $query = 'SELECT id_tipo,tipo,descripcion,direct_data,opcion_multiple 
                    FROM tipo  
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
                   ':id_tipo' => $this->received_data->model->id_tipo,
                            
                    ); 
        $query = 'DELETE FROM tipo WHERE id_tipo = :id_tipo ;'; 

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
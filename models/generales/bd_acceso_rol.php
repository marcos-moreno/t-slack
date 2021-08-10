<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Acceso_rol($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Acceso_rol($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Acceso_rol($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Acceso_rol($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Acceso_rol 
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
                    ':id_rol' => $this->received_data->model->id_rol,
                            ':id_elemento' => $this->received_data->model->id_elemento,
                            
                    ); 
        $query = 'INSERT INTO acceso_rol (id_rol,id_elemento) VALUES (:id_rol,:id_elemento) ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Inserted'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('status'  => 'erro','message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        } 
    } 

    public function update(){
        try {
            $data = array(
                    ':id_acceso' => $this->received_data->model->id_acceso, 
                            ':id_rol' => $this->received_data->model->id_rol, 
                            ':id_elemento' => $this->received_data->model->id_elemento, 
                             
                    ); 
        $query = 'UPDATE acceso_rol SET id_rol=:id_rol,id_elemento=:id_elemento WHERE  id_acceso = :id_acceso ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Updated'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('status'  => 'erro','message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        } 
    } 

    public function select(){
        try {  
             
        $query = 'SELECT id_acceso,id_rol,id_elemento 
                    FROM acceso_rol  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['rol'] = $this->search_union($row,'rol','id_rol','id_rol');
                  
                    $row['elemento'] = $this->search_union($row,'elemento','id_elemento','id_elemento');
                    $data[] = $row;
            }

        
            echo json_encode($data); 
            return true;
        } catch (PDOException $exc) {
            $output = array('status'  => 'erro','message' => $exc->getMessage()); 
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
            $output = array('status'  => 'erro','message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        } 
    }
    public function delete(){
        try {  
            $data = array(
                   ':id_acceso' => $this->received_data->model->id_acceso,
                            
                    ); 
        $query = 'DELETE FROM acceso_rol WHERE id_acceso = :id_acceso ;'; 

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Deleted'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('status'  => 'erro','message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        } 
    }

  
    

} 
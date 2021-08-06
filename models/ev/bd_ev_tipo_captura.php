<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_tipo_captura($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_tipo_captura($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_tipo_captura($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_tipo_captura($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_tipo_captura 
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
                    ':nombre' => $this->received_data->model->nombre,
                        ':es_capturado' => $this->received_data->model->es_capturado,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':direct_data' => $this->received_data->model->direct_data,
                        ':opcion_multiple' => $this->received_data->model->opcion_multiple,
                        ':dato' => $this->received_data->model->dato,
                        
                    ); 
        $query = 'INSERT INTO ev_tipo_captura (nombre,es_capturado,creado,creadopor,actualizado,actualizadopor,direct_data,opcion_multiple,dato) VALUES (:nombre,:es_capturado,Now(),:creadopor,Now(),:actualizadopor,:direct_data,:opcion_multiple,:dato) ;';

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
                    ':ev_tipo_captura_id' => $this->received_data->model->ev_tipo_captura_id, 
                        ':nombre' => $this->received_data->model->nombre, 
                        ':es_capturado' => $this->received_data->model->es_capturado, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':direct_data' => $this->received_data->model->direct_data, 
                        ':opcion_multiple' => $this->received_data->model->opcion_multiple, 
                        ':dato' => $this->received_data->model->dato, 
                         
                    ); 
            $query = 'UPDATE ev_tipo_captura SET nombre=:nombre,es_capturado=:es_capturado,actualizado=Now(),actualizadopor=:actualizadopor,direct_data=:direct_data,opcion_multiple=:opcion_multiple,dato=:dato WHERE  ev_tipo_captura_id = :ev_tipo_captura_id ;';

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
             
        $query = 'SELECT ev_tipo_captura_id,nombre,es_capturado,creado,creadopor,actualizado,actualizadopor,direct_data,opcion_multiple,dato 
                    FROM ev_tipo_captura  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {    $data[] = $row;
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
    public function delete(){
        try {  
            $data = array(
                   ':ev_tipo_captura_id' => $this->received_data->model->ev_tipo_captura_id,
                            
                    ); 
        $query = 'DELETE FROM ev_tipo_captura WHERE ev_tipo_captura_id = :ev_tipo_captura_id ;'; 

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
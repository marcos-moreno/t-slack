<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Dispositivo($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Dispositivo($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Dispositivo($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Dispositivo($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Dispositivo 
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
                            ':descripcion' => $this->received_data->model->descripcion,
                            ':codigo' => $this->received_data->model->codigo,
                            ':mac' => $this->received_data->model->mac,
                            ':num_serie' => $this->received_data->model->num_serie,
                            ':id_grupo_marca' => $this->received_data->model->id_grupo_marca,
                            ':id_marca' => $this->received_data->model->id_marca,
                            
                    ); 
        $query = 'INSERT INTO sp_dispositivo (nombre,descripcion,codigo,mac,num_serie,id_grupo_marca,id_marca) VALUES (:nombre,:descripcion,:codigo,:mac,:num_serie,:id_grupo_marca,:id_marca) ;';

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
                    ':id_dispositivo' => $this->received_data->model->id_dispositivo, 
                            ':nombre' => $this->received_data->model->nombre, 
                            ':descripcion' => $this->received_data->model->descripcion, 
                            ':codigo' => $this->received_data->model->codigo, 
                            ':mac' => $this->received_data->model->mac, 
                            ':num_serie' => $this->received_data->model->num_serie, 
                            ':id_grupo_marca' => $this->received_data->model->id_grupo_marca, 
                            ':id_marca' => $this->received_data->model->id_marca, 
                             
                    ); 
        $query = 'UPDATE sp_dispositivo SET nombre=:nombre,descripcion=:descripcion,codigo=:codigo,mac=:mac,num_serie=:num_serie,id_grupo_marca=:id_grupo_marca,id_marca=:id_marca WHERE  id_dispositivo = :id_dispositivo ;';

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
             
        $query = 'SELECT id_dispositivo,nombre,descripcion,codigo,mac,num_serie,id_grupo_marca,id_marca 
                    FROM sp_dispositivo  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['grupo_marca'] = $this->search_union($row,'sp_grupo_marca','id_grupo_marca','id_grupo_marca');
                  
                    $row['marca'] = $this->search_union($row,'sp_marca','id_marca','id_marca');
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
                   ':id_dispositivo' => $this->received_data->model->id_dispositivo,
                            
                    ); 
        $query = 'DELETE FROM sp_dispositivo WHERE id_dispositivo = :id_dispositivo ;'; 

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
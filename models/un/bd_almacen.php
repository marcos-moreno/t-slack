<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Almacen($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Almacen($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Almacen($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Almacen($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Almacen 
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
                    ':nombre_almacen' => $this->received_data->model->nombre_almacen,
                            ':id_segmento' => $this->received_data->model->id_segmento,
                            ':activo' => $this->received_data->model->activo,
                            
                    ); 
        $query = 'INSERT INTO un_almacen (nombre_almacen,id_segmento,activo) VALUES (:nombre_almacen,:id_segmento,:activo) ;';

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
                    ':id_almacen' => $this->received_data->model->id_almacen, 
                            ':nombre_almacen' => $this->received_data->model->nombre_almacen, 
                            ':id_segmento' => $this->received_data->model->id_segmento, 
                            ':activo' => $this->received_data->model->activo, 
                             
                    ); 
        $query = 'UPDATE un_almacen SET nombre_almacen=:nombre_almacen,id_segmento=:id_segmento,activo=:activo WHERE  id_almacen = :id_almacen ;';

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
        $query = 'SELECT id_almacen,nombre_almacen,id_segmento,activo 
                    FROM un_almacen  
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
                   ':id_almacen' => $this->received_data->model->id_almacen,
                            
                    ); 
        $query = 'DELETE FROM un_almacen WHERE id_almacen = :id_almacen ;'; 

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
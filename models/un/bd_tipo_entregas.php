<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Tipo_entregas($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Tipo_entregas($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Tipo_entregas($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Tipo_entregas($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Tipo_entregas 
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
                        ':activo' => $this->received_data->model->activo,
                        ':usar_fecha' => $this->received_data->model->usar_fecha,
                        ':fecha_aplicacion' => $this->received_data->model->fecha_aplicacion,
                    ); 
            $query = 'INSERT INTO  un_tipo_entregas (tipo,activo,usar_fecha,fecha_aplicacion) VALUES (:tipo,:activo,:usar_fecha,:fecha_aplicacion) ;';
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
        $query = "";
        try {
            
            $data = array(
                        ':id_tipo_entrega' => $this->received_data->model->id_tipo_entrega, 
                        ':tipo' => $this->received_data->model->tipo, 
                        ':activo' => $this->received_data->model->activo, 
                        ':usar_fecha' => $this->received_data->model->usar_fecha, 
                        ':fecha_aplicacion' => $this->received_data->model->fecha_aplicacion, 
                    ); 
            $query = "UPDATE  un_tipo_entregas SET tipo=:tipo,activo=:activo,usar_fecha=:usar_fecha,fecha_aplicacion=:fecha_aplicacion WHERE  id_tipo_entrega = :id_tipo_entrega ;";
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Updated'); 
            echo json_encode($output); 
            return true;
        } catch (Exception $exc) {
            $output = array('message' => $exc,'arr' => $data ); 
            echo json_encode($output); 
            return false;
        }  
    } 

    public function select(){
        try {  
             
        $query = 'SELECT id_tipo_entrega,tipo,activo,usar_fecha,fecha_aplicacion 
                    FROM  un_tipo_entregas  
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
                   ':id_tipo_entrega' => $this->received_data->model->id_tipo_entrega,
                            
                    ); 
        $query = 'DELETE FROM  un_tipo_entregas WHERE id_tipo_entrega = :id_tipo_entrega ;'; 

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
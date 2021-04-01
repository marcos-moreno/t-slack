<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Segmento($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Segmento($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Segmento($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Segmento($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Segmento 
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
                    ':id_empresa' => $this->received_data->model->id_empresa,
                        ':id_creadopor' =>  $_SESSION['id_empleado'],  
                        ':nombre' => $this->received_data->model->nombre,
                        ':observaciones' => $this->received_data->model->observaciones,
                        ':activo' => $this->received_data->model->activo,
                        ':id_actualizadopor' => $_SESSION['id_empleado'],  
                        
                    ); 
        $query = 'INSERT INTO segmento (id_empresa,id_creadopor,fecha_creado,nombre,observaciones,activo,id_actualizadopor,fecha_actualizado) 
        VALUES (:id_empresa,:id_creadopor,now(),:nombre,:observaciones,:activo,:id_actualizadopor,now()) ;';

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
                    ':id_segmento' => $this->received_data->model->id_segmento, 
                        ':id_empresa' => $this->received_data->model->id_empresa, 
                        ':id_creadopor' =>  $_SESSION['id_empleado'],  
                        ':nombre' => $this->received_data->model->nombre, 
                        ':observaciones' => $this->received_data->model->observaciones, 
                        ':activo' => $this->received_data->model->activo, 
                        ':id_actualizadopor' =>  $_SESSION['id_empleado'],   
                    ); 
            $query = 'UPDATE segmento SET id_empresa=:id_empresa,id_creadopor=:id_creadopor,nombre=:nombre,observaciones=:observaciones,activo=:activo,id_actualizadopor=:id_actualizadopor,fecha_actualizado=Now() WHERE  id_segmento = :id_segmento ;';

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
             
        $query = 'SELECT id_segmento,id_empresa,id_creadopor,fecha_creado,nombre,observaciones,activo,id_actualizadopor,fecha_actualizado 
                    FROM segmento  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['empresa'] = $this->search_union($row,'empresa','id_empresa','id_empresa');
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
    public function delete(){
        try {  
            $data = array(
                   ':id_segmento' => $this->received_data->model->id_segmento,
                            
                    ); 
        $query = 'DELETE FROM segmento WHERE id_segmento = :id_segmento ;'; 

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
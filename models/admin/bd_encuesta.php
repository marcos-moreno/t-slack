<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Encuesta($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Encuesta($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Encuesta($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Encuesta($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Encuesta 
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
                    ':id_creadopor' => $this->received_data->model->id_creadopor,
                        ':fecha_creado' => $this->received_data->model->fecha_creado,
                        ':nombre' => $this->received_data->model->nombre,
                        ':observaciones' => $this->received_data->model->observaciones,
                        ':activo' => $this->received_data->model->activo,
                        ':validodesde' => $this->received_data->model->validodesde,
                        ':id_actualizado' => $this->received_data->model->id_actualizado,
                        ':fecha_actualizado' => $this->received_data->model->fecha_actualizado,
                        ':validohasta' => $this->received_data->model->validohasta,
                        
                    ); 
        $query = 'INSERT INTO encuesta (id_creadopor,fecha_creado,nombre,observaciones,activo,validodesde,id_actualizado,fecha_actualizado,validohasta) VALUES (:id_creadopor,:fecha_creado,:nombre,:observaciones,:activo,:validodesde,:id_actualizado,:fecha_actualizado,:validohasta) ;';

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
                    ':id_encuesta' => $this->received_data->model->id_encuesta, 
                        ':id_creadopor' => $this->received_data->model->id_creadopor, 
                        ':fecha_creado' => $this->received_data->model->fecha_creado, 
                        ':nombre' => $this->received_data->model->nombre, 
                        ':observaciones' => $this->received_data->model->observaciones, 
                        ':activo' => $this->received_data->model->activo, 
                        ':validodesde' => $this->received_data->model->validodesde, 
                        ':id_actualizado' => $this->received_data->model->id_actualizado, 
                        ':fecha_actualizado' => $this->received_data->model->fecha_actualizado, 
                        ':validohasta' => $this->received_data->model->validohasta, 
                         
                    ); 
            $query = 'UPDATE encuesta SET id_creadopor=:id_creadopor,fecha_creado=:fecha_creado,nombre=:nombre,observaciones=:observaciones,activo=:activo,validodesde=:validodesde,id_actualizado=:id_actualizado,fecha_actualizado=:fecha_actualizado,validohasta=:validohasta WHERE  id_encuesta = :id_encuesta ;';

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
             
        $query = 'SELECT id_encuesta,id_creadopor,fecha_creado,nombre,observaciones,activo,validodesde,id_actualizado,fecha_actualizado,validohasta
                    FROM encuesta  
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
                   ':id_encuesta' => $this->received_data->model->id_encuesta,
                            
                    ); 
        $query = 'DELETE FROM encuesta WHERE id_encuesta = :id_encuesta ;'; 

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
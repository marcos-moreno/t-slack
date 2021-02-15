<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Enc_leccion($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Enc_leccion($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Enc_leccion($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Enc_leccion($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Enc_leccion 
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
                        ':descipcion' => $this->received_data->model->descipcion,
                        ':id_encuesta' => $this->received_data->model->id_encuesta,
                        ':tipo' => $this->received_data->model->tipo,
                        ':link' => $this->received_data->model->link,
                        ':valor' => $this->received_data->model->valor,
                        ':inicio' => $this->received_data->model->inicio,
                        ':final' => $this->received_data->model->final,
                        ':orden' => $this->received_data->model->orden,
                        ':leccion' => $this->received_data->model->leccion,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        
                    ); 
            $query = 'INSERT INTO enc_leccion (nombre,descipcion,id_encuesta,tipo,link,valor,inicio,final,creado,actualizado,creadopor,actualizadopor,orden,leccion) 
            VALUES (:nombre,:descipcion,:id_encuesta,:tipo,:link,:valor,:inicio,:final,Now(),Now(),:creadopor,:actualizadopor,:orden,:leccion) ;';
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
                    ':id_enc_leccion' => $this->received_data->model->id_enc_leccion, 
                        ':nombre' => $this->received_data->model->nombre, 
                        ':descipcion' => $this->received_data->model->descipcion, 
                        ':id_encuesta' => $this->received_data->model->id_encuesta, 
                        ':tipo' => $this->received_data->model->tipo, 
                        ':link' => $this->received_data->model->link, 
                        ':valor' => $this->received_data->model->valor, 
                        ':inicio' => $this->received_data->model->inicio, 
                        ':final' => $this->received_data->model->final, 
                        ':orden' => $this->received_data->model->orden,
                        ':leccion' => $this->received_data->model->leccion,
                        ':actualizadopor' => $_SESSION['id_empleado'],
                    ); 
            $query = 'UPDATE enc_leccion SET nombre=:nombre,descipcion=:descipcion,id_encuesta=:id_encuesta,tipo=:tipo,
            link=:link,valor=:valor,inicio=:inicio,final=:final,actualizado=Now(),actualizadopor=:actualizadopor,orden=:orden,leccion=:leccion
            WHERE  id_enc_leccion = :id_enc_leccion;';

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
             
            $query = 'SELECT id_enc_leccion,nombre,descipcion,id_encuesta,tipo,link,valor,inicio,final,creado,actualizado,creadopor,actualizadopor,orden,leccion 
                    FROM enc_leccion  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['encuesta'] = $this->search_union($row,'encuesta','id_encuesta','id_encuesta');
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
            $data = array(  ':id_enc_leccion' => $this->received_data->model->id_enc_leccion,   ); 
            $query = 'DELETE FROM enc_leccion WHERE id_enc_leccion = :id_enc_leccion ;'; 
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
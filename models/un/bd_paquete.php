<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Paquete($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Paquete($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Paquete($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Paquete($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 
class Paquete 
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
                    ':genero' => $this->received_data->model->genero,
                            ':id_tipo_entrege' => $this->received_data->model->id_tipo_entrege,
                            ':nombre_paquete' => $this->received_data->model->nombre_paquete,
                            ':descripcion' => $this->received_data->model->descripcion,
                            ':activo' => $this->received_data->model->activo,
                            
                    ); 
        $query = 'INSERT INTO  un_paquete (genero,id_tipo_entrege,nombre_paquete,descripcion,activo) VALUES (:genero,:id_tipo_entrege,:nombre_paquete,:descripcion,:activo) ;';

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
                    ':id_paquete' => $this->received_data->model->id_paquete, 
                            ':genero' => $this->received_data->model->genero, 
                            ':id_tipo_entrege' => $this->received_data->model->id_tipo_entrege, 
                            ':nombre_paquete' => $this->received_data->model->nombre_paquete, 
                            ':descripcion' => $this->received_data->model->descripcion, 
                            ':activo' => $this->received_data->model->activo, 
                             
                    ); 
        $query = 'UPDATE  un_paquete SET genero=:genero,id_tipo_entrege=:id_tipo_entrege,nombre_paquete=:nombre_paquete,descripcion=:descripcion,activo=:activo WHERE  id_paquete = :id_paquete ;';

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
             
        $query = 'SELECT id_paquete,genero,id_tipo_entrege,nombre_paquete,descripcion,activo 
                    FROM  un_paquete  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . str_replace('*requerid_session*', $_SESSION['id_empleado'] , $this->received_data->filter) :'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
             $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['tipo_entregas'] = $this->search_union($row,'un_tipo_entregas','id_tipo_entrega','id_tipo_entrege');
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
                   ':id_paquete' => $this->received_data->model->id_paquete,
                    ); 
        $query = 'DELETE FROM  un_paquete WHERE id_paquete = :id_paquete ;'; 

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
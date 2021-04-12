<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_indicador_general($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_indicador_general($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_indicador_general($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_indicador_general($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_indicador_general 
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
                        ':tendencia' => $this->received_data->model->tendencia,
                        ':activo' => $this->received_data->model->activo,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':origen' => $this->received_data->model->origen,
                        ':allowrepor' => $this->received_data->model->allowrepor,

                    ); 
        $query = 'INSERT INTO ev_indicador_general (nombre,descripcion,tendencia,activo,creado,creadopor
        ,actualizado,actualizadopor,origen,allowrepor) VALUES (:nombre,:descripcion,:tendencia,:activo
        ,Now(),:creadopor,Now(),:actualizadopor,:origen,:allowrepor) ;';

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
                    ':ev_indicador_general_id' => $this->received_data->model->ev_indicador_general_id, 
                        ':nombre' => $this->received_data->model->nombre, 
                        ':descripcion' => $this->received_data->model->descripcion, 
                        ':tendencia' => $this->received_data->model->tendencia, 
                        ':activo' => $this->received_data->model->activo, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':origen' => $this->received_data->model->origen, 
                        ':allowrepor' => $this->received_data->model->allowrepor,

                    ); 
            $query = 'UPDATE ev_indicador_general SET nombre=:nombre,descripcion=:descripcion,tendencia=:tendencia
            ,activo=:activo,actualizado=Now(),actualizadopor=:actualizadopor,origen=:origen,allowrepor=:allowrepor 
            WHERE  ev_indicador_general_id = :ev_indicador_general_id ;';

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
             
        $query = 'SELECT ev_indicador_general_id,nombre,descripcion,tendencia,activo,creado,creadopor
        ,actualizado,actualizadopor,origen,allowrepor
                    FROM ev_indicador_general  
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
                   ':ev_indicador_general_id' => $this->received_data->model->ev_indicador_general_id,
                            
                    ); 
        $query = 'DELETE FROM ev_indicador_general WHERE ev_indicador_general_id = :ev_indicador_general_id ;'; 

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
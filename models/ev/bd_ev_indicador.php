<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_indicador($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_indicador($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_indicador($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_indicador($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_indicador 
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
                    ':ev_puesto_nivel_id' => $this->received_data->model->ev_puesto_nivel_id,
                        ':nombre' => $this->received_data->model->nombre,
                        ':descripcion' => $this->received_data->model->descripcion,
                        ':porcentaje' => $this->received_data->model->porcentaje,
                        ':origen' => $this->received_data->model->origen,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':tendencia' => $this->received_data->model->tendencia,

                    ); 
        $query = 'INSERT INTO ev_indicador (ev_puesto_nivel_id,nombre,descripcion,porcentaje,origen,creado,creadopor,actualizado,actualizadopor,tendencia) 
        VALUES (:ev_puesto_nivel_id,:nombre,:descripcion,:porcentaje,:origen,Now(),:creadopor,Now(),:actualizadopor,:tendencia) ;';

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
                    ':ev_indicador_id' => $this->received_data->model->ev_indicador_id, 
                        ':ev_puesto_nivel_id' => $this->received_data->model->ev_puesto_nivel_id, 
                        ':nombre' => $this->received_data->model->nombre, 
                        ':descripcion' => $this->received_data->model->descripcion, 
                        ':porcentaje' => $this->received_data->model->porcentaje, 
                        ':origen' => $this->received_data->model->origen, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':tendencia' => $this->received_data->model->tendencia,
                    ); 
            $query = 'UPDATE ev_indicador SET ev_puesto_nivel_id=:ev_puesto_nivel_id,nombre=:nombre,descripcion=:descripcion,
            porcentaje=:porcentaje,origen=:origen,actualizado=Now(),actualizadopor=:actualizadopor ,tendencia=:tendencia
            WHERE  ev_indicador_id = :ev_indicador_id ;';

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
             
        $query = 'SELECT ev_indicador_id,ev_puesto_nivel_id,nombre,descripcion,porcentaje,origen,creado,creadopor,actualizado,actualizadopor,tendencia 
                    FROM ev_indicador  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_puesto_nivel'] = $this->search_union($row,'ev_puesto_nivel','ev_puesto_nivel_id','ev_puesto_nivel_id');
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
                   ':ev_indicador_id' => $this->received_data->model->ev_indicador_id,
                            
                    ); 
        $query = 'DELETE FROM ev_indicador WHERE ev_indicador_id = :ev_indicador_id ;'; 

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
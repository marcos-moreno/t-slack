<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_puesto($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_puesto($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_puesto($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_puesto($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_puesto 
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
                    ':nombre_puesto' => $this->received_data->model->nombre_puesto,
                        ':decripcion_puesto' => $this->received_data->model->decripcion_puesto,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':codigo' => $this->received_data->model->codigo,
                        ':tipo' => $this->received_data->model->tipo,
                        ':ev_nivel_p_id' => $this->received_data->model->ev_nivel_p_id,
                        
                    ); 
        $query = 'INSERT INTO ev_puesto (nombre_puesto,decripcion_puesto,creado,creadopor,actualizado,actualizadopor,codigo,tipo,ev_nivel_p_id) VALUES (:nombre_puesto,:decripcion_puesto,Now(),:creadopor,Now(),:actualizadopor,:codigo,:tipo,:ev_nivel_p_id) ;';

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
                    ':ev_puesto_id' => $this->received_data->model->ev_puesto_id, 
                        ':nombre_puesto' => $this->received_data->model->nombre_puesto, 
                        ':decripcion_puesto' => $this->received_data->model->decripcion_puesto, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':codigo' => $this->received_data->model->codigo, 
                        ':tipo' => $this->received_data->model->tipo, 
                        ':ev_nivel_p_id' => $this->received_data->model->ev_nivel_p_id, 
                         
                    ); 
            $query = 'UPDATE ev_puesto SET nombre_puesto=:nombre_puesto,decripcion_puesto=:decripcion_puesto,actualizado=Now(),actualizadopor=:actualizadopor,codigo=:codigo,tipo=:tipo,ev_nivel_p_id=:ev_nivel_p_id WHERE  ev_puesto_id = :ev_puesto_id ;';

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
        $where = '';
        $parameters = array(); 
        if ($this->received_data->filter != '') { 
            $parameters = array(':valor' => $this->received_data->filter); 
            $where = "WHERE  nombre_puesto  ILIKE '%' || :valor || '%'";
        }
        if (isset($this->received_data->searchID)) { 
            $parameters = array(':valor' => $this->received_data->filter); 
            $where = "WHERE  ev_puesto_id = :valor";
        }
        $query = "
                SELECT ev_puesto_id,nombre_puesto,decripcion_puesto,creado,creadopor
                        ,actualizado,actualizadopor,codigo,COALESCE(tipo,'') As tipo,ev_nivel_p_id 
                FROM ev_puesto  
                $where
                ORDER BY nombre_puesto DESC
                "; 
            $statement = $this->connect->prepare($query);  
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
                $row['ev_nivel_p'] = $this->search_union($row,'ev_nivel_p','ev_nivel_p_id','ev_nivel_p_id');
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
            return json_encode($output);  
        }  
    }
    public function delete(){
        try {  
            $data = array(
                   ':ev_puesto_id' => $this->received_data->model->ev_puesto_id,
                            
                    ); 
        $query = 'DELETE FROM ev_puesto WHERE ev_puesto_id = :ev_puesto_id ;'; 

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
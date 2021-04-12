<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_indicador_puesto($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_indicador_puesto($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_indicador_puesto($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_indicador_puesto($data,$connect,$received_data);
            $model->select();
        break;
        case 'selectByEmployee': 
            $model = new Ev_indicador_puesto($data,$connect,$received_data);
            $model->selectByEmployee();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_indicador_puesto 
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
                        ':porcentaje' => $this->received_data->model->porcentaje, 
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'], 
                        ':ev_indicador_general_id' => $this->received_data->model->ev_indicador_general_id,
                    ); 
            $query = 'INSERT INTO ev_indicador_puesto (ev_puesto_nivel_id,porcentaje,creado,creadopor,actualizado,actualizadopor,ev_indicador_general_id) 
            VALUES (:ev_puesto_nivel_id,:porcentaje,Now(),:creadopor,Now(),:actualizadopor,:ev_indicador_general_id) ;';
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
                        ':porcentaje' => $this->received_data->model->porcentaje,  
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':ev_indicador_general_id' => $this->received_data->model->ev_indicador_general_id,
                    ); 
            $query = 'UPDATE ev_indicador_puesto SET ev_puesto_nivel_id=:ev_puesto_nivel_id,
            porcentaje=:porcentaje,actualizado=Now(),actualizadopor=:actualizadopor ,ev_indicador_general_id=:ev_indicador_general_id
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
            $query = 'SELECT ev_indicador_id, ev_puesto_nivel_id, porcentaje, creado, creadopor, actualizado, actualizadopor, ev_indicador_general_id
                    FROM ev_indicador_puesto  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            $result = false;
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_puesto_nivel'] = $this->search_union($row,'ev_puesto_nivel','ev_puesto_nivel_id','ev_puesto_nivel_id');
                    $row['ev_indicador_general'] = $this->search_union($row,'ev_indicador_general','ev_indicador_general_id','ev_indicador_general_id');
                    $data[] = $row;
                    $result = true;
            }
            if ($result) {
                echo json_encode($data); 
            } else {
                echo json_encode(array()); 
            }
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    }
    public function selectByEmployee(){
        try {   
            $data = array(
                ':id_empleado' => $this->received_data->model->id_empleado, 
                ); 
            $query =   'SELECT ip.*
                        FROM empleado e  
                        INNER JOIN ev_puesto_nivel pn ON pn.ev_puesto_nivel_id = e.ev_puesto_nivel_id
                        INNER JOIN ev_indicador_puesto ip ON ip.ev_puesto_nivel_id = pn.ev_puesto_nivel_id
                        INNER JOIN ev_indicador_general ig ON ig.ev_indicador_general_id = ip.ev_indicador_general_id 
                        AND ig.allowrepor = true
                        WHERE e.id_empleado = :id_empleado ' ;
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            $result = false;
            $dataResult = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_puesto_nivel'] = $this->search_union($row,'ev_puesto_nivel','ev_puesto_nivel_id','ev_puesto_nivel_id');
                    $row['ev_indicador_general'] = $this->search_union($row,'ev_indicador_general','ev_indicador_general_id','ev_indicador_general_id');
                    $dataResult[] = $row;
                    $result = true;
            }
            if ($result) {
                echo json_encode($dataResult); 
            } else {
                echo json_encode(array()); 
            }
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
                        ':ev_indicador_id' => $this->received_data->model->ev_indicador_id,
                    ); 
            $query = 'DELETE FROM ev_indicador_puesto WHERE ev_indicador_id = :ev_indicador_id ;'; 
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
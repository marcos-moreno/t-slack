<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_puesto_nivel($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_puesto_nivel($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_puesto_nivel($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_puesto_nivel($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_puesto_nivel 
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
                    ':ev_puesto_id' => $this->received_data->model->ev_puesto_id,
                        ':ev_nivel_p_id' => $this->received_data->model->ev_nivel_p_id,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        
                    ); 
        $query = 'INSERT INTO ev_puesto_nivel (ev_puesto_id,ev_nivel_p_id,creado,creadopor,actualizado,actualizadopor) VALUES (:ev_puesto_id,:ev_nivel_p_id,Now(),:creadopor,Now(),:actualizadopor) ;';

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
                    ':ev_puesto_nivel_id' => $this->received_data->model->ev_puesto_nivel_id, 
                        ':ev_puesto_id' => $this->received_data->model->ev_puesto_id, 
                        ':ev_nivel_p_id' => $this->received_data->model->ev_nivel_p_id, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                         
                    ); 
            $query = 'UPDATE ev_puesto_nivel SET ev_puesto_id=:ev_puesto_id,ev_nivel_p_id=:ev_nivel_p_id,actualizado=Now(),actualizadopor=:actualizadopor WHERE  ev_puesto_nivel_id = :ev_puesto_nivel_id ;';

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
            $query = "SELECT ev_puesto_nivel_id,pn.ev_puesto_id,pn.ev_nivel_p_id
                    ,pn.creado,pn.creadopor,pn.actualizado,pn.actualizadopor 
                                        FROM ev_puesto_nivel  pn
                    INNER JOIN ev_puesto p ON p.ev_puesto_id = pn.ev_puesto_id
                    WHERE
                    ";
            $keyword = null;
            if ($this->received_data->type == "ilike") {
                $query .= " p.nombre_puesto ILIKE :filter";
                $keyword = "%".$this->received_data->filter."%";
            }elseif ($this->received_data->type == "byIDpn") {
                $keyword = $this->received_data->filter;
                $query .= " ev_puesto_nivel_id = :filter";
            }
            $query .= " ORDER BY p.nombre_puesto";
            $statement = $this->connect->prepare($query); 
            
            $statement->bindParam(':filter', $keyword, PDO::PARAM_STR);
            $statement->execute();   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_puesto'] = $this->search_union($row,'ev_puesto','ev_puesto_id','ev_puesto_id');
                    $row['ev_nivel_p'] = $this->search_union($row,'ev_nivel_p','ev_nivel_p_id','ev_nivel_p_id');
                    $data[] = $row;
            }
            // echo $query;
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
                   ':ev_puesto_nivel_id' => $this->received_data->model->ev_puesto_nivel_id,
                            
                    ); 
        $query = 'DELETE FROM ev_puesto_nivel WHERE ev_puesto_nivel_id = :ev_puesto_nivel_id ;'; 

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
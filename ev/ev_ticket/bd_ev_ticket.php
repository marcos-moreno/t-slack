<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_ticket 
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
                    ':problema' => $this->received_data->model->problema,
                        ':observacion' => $this->received_data->model->observacion,
                        ':fechacreacion' => $this->received_data->model->fechacreacion,
                        ':fechasolucion' => $this->received_data->model->fechasolucion,
                        ':estado' => $this->received_data->model->estado,
                        ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id,
                        
                    ); 
        $query = 'INSERT INTO ev_ticket (problema,observacion,fechacreacion,fechasolucion,estado,ev_catalogo_ticket_id) VALUES (:problema,:observacion,:fechacreacion,:fechasolucion,:estado,:ev_catalogo_ticket_id) ;';

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
                    ':ev_ticket_id' => $this->received_data->model->ev_ticket_id, 
                        ':problema' => $this->received_data->model->problema, 
                        ':observacion' => $this->received_data->model->observacion, 
                        ':fechacreacion' => $this->received_data->model->fechacreacion, 
                        ':fechasolucion' => $this->received_data->model->fechasolucion, 
                        ':estado' => $this->received_data->model->estado, 
                        ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id, 
                         
                    ); 
            $query = 'UPDATE ev_ticket SET problema=:problema,observacion=:observacion,fechacreacion=:fechacreacion,fechasolucion=:fechasolucion,estado=:estado,ev_catalogo_ticket_id=:ev_catalogo_ticket_id WHERE  ev_ticket_id = :ev_ticket_id ;';

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
            
            $parameters = array(
                ':valor' => $this->received_data->filter,  
            );
            $query = "SELECT ev_ticket_id,problema,observacion,fechacreacion,fechasolucion,estado,ev_catalogo_ticket_id 
                    FROM ev_ticket 
                    WHERE 
                        field  ILIKE '%' || :valor || '%' 
                    ORDER BY 1 DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_catalogo_ticket'] = $this->search_union($row,'ev_catalogo_ticket','ev_catalogo_ticket_id','ev_catalogo_ticket_id');
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
                   ':ev_ticket_id' => $this->received_data->model->ev_ticket_id,
                            
                    ); 
        $query = 'DELETE FROM ev_ticket WHERE ev_ticket_id = :ev_ticket_id ;'; 

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
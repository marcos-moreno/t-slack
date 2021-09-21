<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_ticket_ln($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_ticket_ln($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_ticket_ln($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_ticket_ln($data,$connect,$received_data);
            $model->select();
        break;
        case 'selectV': 
            $model = new Ev_ticket_ln($data,$connect,$received_data);
            $model->selectV();
        break;
        case 'insertO':
            $model = new Ev_ticket_ln($data,$connect,$received_data);
            $model->insertO();
        break;
        case 'validaInsert':
            $model = new Ev_ticket_ln($data,$connect,$received_data);
            $model->validaInsert();
        break;
        case 'updateV': 
            $model = new Ev_ticket_ln($data,$connect,$received_data);
            $model->updateV();
        break;
        case 'selecciona': 
            $model = new Ev_ticket_ln($data,$connect,$received_data);
            $model->selecciona();
        break;
    }

} 

class Ev_ticket_ln 
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
            $value = true;
            $data = array(
                    ':id_empleado' => $_SESSION['id_empleado'],
                        ':comentario' => $this->received_data->model->comentario,
                        ':estado' => $value,
                        ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id,
                        
                    ); 
        $query = 'INSERT INTO refividrio.ev_ticket_ln (id_empleado,comentario,ev_ticket_id,estado) 
        VALUES (:id_empleado,:comentario,(SELECT MAX(ev_ticket_id) FROM refividrio.ev_ticket where ev_catalogo_ticket_id = :ev_catalogo_ticket_id LIMIT 1),:estado)RETURNING ev_ticket_ln_id;';

$statement = $this->connect->prepare($query); 
$statement->execute($data);
$result = $statement->fetchAll();
$ev_ticket_ln_id = 0;  
foreach ($result as $row) {
    $ev_ticket_ln_id = $row['ev_ticket_ln_id'];
} 
$output = array('message' => 'Data Inserted','ev_ticket_ln_id' => $ev_ticket_ln_id); 
echo json_encode($output); 
return true;
} catch (PDOException $exc) {
$output = array('message' => $exc->getMessage()); 
echo json_encode($output); 
return false;
} 
    } 

    public function insertO(){
        try {
            $value = true;
            $data = array(
                    ':id_empleado' => $_SESSION['id_empleado'],
                        ':comentario' => $this->received_data->model->comentario,
                        ':estado' => $value,
                        ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id,
                        
                    ); 
        $query = 'INSERT INTO refividrio.ev_ticket_ln (id_empleado,comentario,ev_ticket_id,estado) 
        VALUES (:id_empleado,:comentario,(SELECT MAX (ev_ticket_id) FROM refividrio.ev_ticket where ev_catalogo_ticket_id = (SELECT ev_catalogo_ticket_id from refividrio.ev_catalogo_ticket where ev_catalogo_ticket_id = :ev_catalogo_ticket_id )),:estado)RETURNING ev_ticket_ln_id;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);
            $result = $statement->fetchAll();
            $ev_ticket_ln_id = 0;  
            foreach ($result as $row) {
                $ev_ticket_ln_id = $row['ev_ticket_ln_id'];
            } 
            $output = array('message' => 'Data Inserted','ev_ticket_ln_id' => $ev_ticket_ln_id); 
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
                        ':comentario' => $this->received_data->model->comentario, 
                        
                       
                         
                    ); 
            $query = 'UPDATE ev_ticket_ln SET comentario=:comentario, estado= false WHERE  ev_ticket_id = :ev_ticket_id ;';

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

    public function updateV(){
        try {
            
            $data = array(
                    ':ev_ticket_id' => $this->received_data->model->ev_ticket_id, 
                        ':comentario' => $this->received_data->model->comentario, 
                        
                       
                         
                    ); 
            $query = 'UPDATE ev_ticket_ln SET comentario=:comentario, estado= true WHERE  ev_ticket_id = :ev_ticket_id ;';

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
            $bo = true;
            $parameters = array(
                ':id_empleado' => $_SESSION['id_empleado'], 
                ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id, 
                ':departamento_id' =>  $this->received_data->model->departamento_id, 
                ':estado'  => $bo,
               
            );
            $query = "SELECT ln.*, ti.* 
            FROM refividrio.ev_ticket_ln ln
            INNER JOIN refividrio.ev_ticket ti on ti.ev_ticket_id = ln.ev_ticket_id
            WHERE 
                ln.id_empleado = :id_empleado and ti.ev_catalogo_ticket_id = :ev_catalogo_ticket_id 
                and ln.estado = :estado and ti.departamento_id = :departamento_id
                    " ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_ticket'] = $this->search_union($row,'ev_ticket','ev_ticket_id','ev_ticket_id');
                  
                    $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_empleado');
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

    public function selectV(){
        try {  
            $parameters = array(
                
                ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id, 
                ':departamento_id' => $this->received_data->model->departamento_id, 
               
               
            );
            $query = "SELECT *
            FROM refividrio.ev_ticket
            WHERE 
                ev_catalogo_ticket_id = :ev_catalogo_ticket_id and departamento_id = :departamento_id and status=true
                    " ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_ticket'] = $this->search_union($row,'ev_ticket','ev_ticket_id','ev_ticket_id');
                  
                    $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_empleado');
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


    public function selecciona(){
        try {  
            $parameters = array(
                
                ':ev_ticket_ln_id' => $this->received_data->model->ev_ticket_ln_id, 
               
               
            );
            $query = "SELECT ev_ticket_id
            FROM refividrio.ev_ticket_ln
            WHERE 
                ev_ticket_ln_id = :ev_ticket_ln_id
                    " ;
                        
                        $statement = $this->connect->prepare($query); 
                        $statement->execute($parameters);
                        $result = $statement->fetchAll();
                        $ev_ticket_id = 0;  
                        foreach ($result as $row) {
                            $ev_ticket_id = $row['ev_ticket_id'];
                        } 
                        $output = array('message' => 'Data gets','ev_ticket_id' => $ev_ticket_id); 
                        echo json_encode($output); 
                        return true;
                    } catch (PDOException $exc) {
                        $output = array('message' => $exc->getMessage()); 
                        echo json_encode($output); 
                        return false;
                    } 
    }

    public function validaInsert(){
        try { 
            $parameters = array(
                ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id,
                // ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id,
            );  

            $query = "
            SELECT MAX(ev_ticket_id) 
            from refividrio.ev_ticket where ev_catalogo_ticket_id = :ev_catalogo_ticket_id
                     
            " ;         
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
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
        $query = 'DELETE FROM ev_ticket_ln WHERE ev_ticket_id = :ev_ticket_id ;'; 

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
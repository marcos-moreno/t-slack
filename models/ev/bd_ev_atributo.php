<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_atributo($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_atributo($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_atributo($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_atributo($data,$connect,$received_data);
            $model->select();
        break;
        case 'selectAll': 
            $model = new Ev_atributo($data,$connect,$received_data);
            $model->selectAll();
        break;
        
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_atributo 
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
                    ':value' => $this->received_data->model->value,
                        ':activo' => $this->received_data->model->activo,
                        ':descripcion' => $this->received_data->model->descripcion,
                        ':tabla' => $this->received_data->model->tabla,
                        
                    ); 
        $query = 'INSERT INTO ev_atributo (value,activo,descripcion,tabla) VALUES (:value,:activo,:descripcion,:tabla) ;';

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
                    ':id_atributo' => $this->received_data->model->id_atributo, 
                        ':value' => $this->received_data->model->value, 
                        ':activo' => $this->received_data->model->activo, 
                        ':descripcion' => $this->received_data->model->descripcion, 
                        ':tabla' => $this->received_data->model->tabla, 
                         
                    ); 
            $query = 'UPDATE ev_atributo SET value=:value,activo=:activo,descripcion=:descripcion,tabla=:tabla WHERE  id_atributo = :id_atributo ;';

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
                ':valor' => $this->received_data->valor,  
            ); 
            $query = 'SELECT id_atributo,value,activo,descripcion,tabla 
                    FROM ev_atributo  
                    WHERE tabla = :valor';

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

    public function selectAll(){
        try {   
            $parameters = array(
                ':valor' => $this->received_data->valor,  
            ); 
            $query = "SELECT id_atributo,value,activo,descripcion,tabla 
                    FROM ev_atributo 
                    WHERE tabla ILIKE '%' || :valor || '%'
                    OR value ILIKE  '%' || :valor || '%'
                    ORDER BY tabla,value";

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
                   ':id_atributo' => $this->received_data->model->id_atributo,
                            
                    ); 
        $query = 'DELETE FROM ev_atributo WHERE id_atributo = :id_atributo ;'; 

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
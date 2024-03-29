<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_punto_evaluado($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_punto_evaluado($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_punto_evaluado($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_punto_evaluado($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_punto_evaluado 
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
                    ':ev_punto_evaluar_id' => $this->received_data->model->ev_punto_evaluar_id,
                        ':ev_punto_evaluar_ln_id' => $this->received_data->model->ev_punto_evaluar_ln_id,
                        ':id_empleado' => $this->received_data->model->id_empleado,
                        ':ev_evaluacion_id' => $this->received_data->model->ev_evaluacion_id,
                        ':ev_evaluacion_ln_id' => $this->received_data->model->ev_evaluacion_ln_id,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        
                    ); 
        $query = 'INSERT INTO ev_punto_evaluado (ev_punto_evaluar_id,ev_punto_evaluar_ln_id,id_empleado,ev_evaluacion_id,ev_evaluacion_ln_id,creado,actualizado,creadopor,actualizadopor) VALUES (:ev_punto_evaluar_id,:ev_punto_evaluar_ln_id,:id_empleado,:ev_evaluacion_id,:ev_evaluacion_ln_id,Now(),Now(),:creadopor,:actualizadopor) ;';

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
                    ':ev_punto_evaluado_id' => $this->received_data->model->ev_punto_evaluado_id, 
                        ':ev_punto_evaluar_id' => $this->received_data->model->ev_punto_evaluar_id, 
                        ':ev_punto_evaluar_ln_id' => $this->received_data->model->ev_punto_evaluar_ln_id, 
                        ':id_empleado' => $this->received_data->model->id_empleado, 
                        ':ev_evaluacion_id' => $this->received_data->model->ev_evaluacion_id, 
                        ':ev_evaluacion_ln_id' => $this->received_data->model->ev_evaluacion_ln_id, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                         
                    ); 
            $query = 'UPDATE ev_punto_evaluado SET ev_punto_evaluar_id=:ev_punto_evaluar_id,ev_punto_evaluar_ln_id=:ev_punto_evaluar_ln_id,id_empleado=:id_empleado,ev_evaluacion_id=:ev_evaluacion_id,ev_evaluacion_ln_id=:ev_evaluacion_ln_id,actualizado=Now(),actualizadopor=:actualizadopor WHERE  ev_punto_evaluado_id = :ev_punto_evaluado_id ;';

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
            $query = "SELECT ev_punto_evaluado_id,ev_punto_evaluar_id,ev_punto_evaluar_ln_id,id_empleado,ev_evaluacion_id,ev_evaluacion_ln_id,creado,actualizado,creadopor,actualizadopor 
                    FROM ev_punto_evaluado 
                    WHERE 
                        field  ILIKE '%' || :valor || '%' 
                    ORDER BY 1 DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_punto_evaluar'] = $this->search_union($row,'ev_punto_evaluar','ev_punto_evaluar_id','ev_punto_evaluar_id');
                  
                    $row['ev_punto_evaluar_ln'] = $this->search_union($row,'ev_punto_evaluar_ln','ev_punto_evaluar_ln_id','ev_punto_evaluar_ln_id');
                  
                    $empleado = $this->search_union($row,'empleado','id_empleado','id_empleado');     
                    $empleado[0]['password'] = '';
                    $row['empleado'] = $empleado ; 
                  
                    $row['ev_evaluacion'] = $this->search_union($row,'ev_evaluacion','ev_evaluacion_id','ev_evaluacion_id');
                  
                    $row['ev_evaluacion_ln'] = $this->search_union($row,'ev_evaluacion_ln','ev_evaluacion_ln_id','ev_evaluacion_ln_id');
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
                   ':ev_punto_evaluado_id' => $this->received_data->model->ev_punto_evaluado_id,
                            
                    ); 
        $query = 'DELETE FROM ev_punto_evaluado WHERE ev_punto_evaluado_id = :ev_punto_evaluado_id ;'; 

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
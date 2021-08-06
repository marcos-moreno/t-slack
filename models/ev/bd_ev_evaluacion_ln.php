<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_evaluacion_ln($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_evaluacion_ln($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_evaluacion_ln($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_evaluacion_ln($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_evaluacion_ln 
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
        $parameters = array(
            ':ev_evaluacion_id' => $this->received_data->model->ev_evaluacion_id,
                ':id_empleado' => $this->received_data->model->id_empleado,
                ':ev_puesto_id' => $this->received_data->model->ev_puesto_id,
                ':calificacion' => $this->received_data->model->calificacion,
                ':estado_atributo' => $this->received_data->model->estado_atributo,
                ':creadopor' => $_SESSION['id_empleado'],
                ':actualizadopor' => $_SESSION['id_empleado'],
            ); 
        try { 
            $query = 'INSERT INTO ev_evaluacion_ln (ev_evaluacion_id,id_empleado,ev_puesto_id,calificacion,estado_atributo,creado,actualizado,creadopor,actualizadopor) VALUES (:ev_evaluacion_id,:id_empleado,:ev_puesto_id,:calificacion,:estado_atributo,Now(),Now(),:creadopor,:actualizadopor) ;';
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);  
            $output = array('message' => 'Data Inserted'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage(), 'data' => $exc); 
            echo json_encode($output); 
            return false;
        } 
    } 

    public function update(){
        try {
            $data = array(
                    ':ev_evaluacion_ln_id' => $this->received_data->model->ev_evaluacion_ln_id, 
                        ':ev_evaluacion_id' => $this->received_data->model->ev_evaluacion_id, 
                        ':id_empleado' => $this->received_data->model->id_empleado, 
                        ':ev_puesto_id' => $this->received_data->model->ev_puesto_id, 
                        ':calificacion' => $this->received_data->model->calificacion, 
                        ':estado_atributo' => $this->received_data->model->estado_atributo, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                         
                    ); 
            $query = 'UPDATE ev_evaluacion_ln SET ev_evaluacion_id=:ev_evaluacion_id,id_empleado=:id_empleado,ev_puesto_id=:ev_puesto_id,calificacion=:calificacion,estado_atributo=:estado_atributo,actualizado=Now(),actualizadopor=:actualizadopor WHERE  ev_evaluacion_ln_id = :ev_evaluacion_ln_id ;';

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
                ':ev_evaluacion_id' => $this->received_data->ev_evaluacion_id,  
            );
            $query = "SELECT ev_evaluacion_ln_id,ev_evaluacion_id,id_empleado
                        ,ev_puesto_id,calificacion,estado_atributo,creado
                        ,actualizado,creadopor,actualizadopor 
                    FROM ev_evaluacion_ln 
                    WHERE 
                        ev_evaluacion_id = :ev_evaluacion_id
                    ORDER BY 1 DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['estado'] = $this->search_union($row,'ev_atributo','id_atributo','estado_atributo');         
                    $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_empleado');
                    $row['ev_puesto'] = $this->search_union($row,'ev_puesto','ev_puesto_id','ev_puesto_id');
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
                   ':ev_evaluacion_ln_id' => $this->received_data->model->ev_evaluacion_ln_id,
                            
                    ); 
        $query = 'DELETE FROM ev_evaluacion_ln WHERE ev_evaluacion_ln_id = :ev_evaluacion_ln_id ;'; 

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
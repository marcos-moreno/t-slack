<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_escala_evaluacion($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_escala_evaluacion($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_escala_evaluacion($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_escala_evaluacion($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_escala_evaluacion 
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
                    ':ev_indicador_general_id' => $this->received_data->model->ev_indicador_general_id,
                        ':porcentaje' => $this->received_data->model->porcentaje,
                        ':parametro_menor' => $this->received_data->model->parametro_menor,
                        ':parametro_mayor' => $this->received_data->model->parametro_mayor,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        
                    ); 
        $query = 'INSERT INTO ev_escala_evaluacion (ev_indicador_general_id,porcentaje,parametro_menor,parametro_mayor,creado,creadopor,actualizado,actualizadopor) VALUES (:ev_indicador_general_id,:porcentaje,:parametro_menor,:parametro_mayor,Now(),:creadopor,Now(),:actualizadopor) ;';

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
                    ':ev_escala_evaluacion_id' => $this->received_data->model->ev_escala_evaluacion_id, 
                        ':ev_indicador_general_id' => $this->received_data->model->ev_indicador_general_id, 
                        ':porcentaje' => $this->received_data->model->porcentaje, 
                        ':parametro_menor' => $this->received_data->model->parametro_menor, 
                        ':parametro_mayor' => $this->received_data->model->parametro_mayor, 
                        ':actualizadopor' => $_SESSION['id_empleado'],  
                    ); 
            $query = '
                UPDATE ev_escala_evaluacion 
                    SET ev_indicador_general_id=:ev_indicador_general_id
                    ,porcentaje=:porcentaje,parametro_menor=:parametro_menor
                    ,parametro_mayor=:parametro_mayor,actualizado=Now()
                    ,actualizadopor=:actualizadopor 
                WHERE  ev_escala_evaluacion_id = :ev_escala_evaluacion_id;'; 

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
            $query = "SELECT 
                        esc.ev_escala_evaluacion_id
                        ,esc.ev_indicador_general_id,esc.porcentaje,esc.parametro_menor
                        ,esc.parametro_mayor,esc.creado,esc.creadopor
                        ,esc.actualizado,esc.actualizadopor 
                        ,ind.nombre As ev_indicador_general
                    FROM ev_escala_evaluacion esc
                    INNER JOIN ev_indicador_general ind 
                        ON ind.ev_indicador_general_id = esc.ev_indicador_general_id
                    WHERE 
                    ind.nombre  ILIKE '%' || :valor || '%' 
                    ORDER BY esc.ev_indicador_general_id DESC,esc.porcentaje DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    // $row['ev_indicador_general'] = $this->search_union($row,'ev_indicador_general','ev_indicador_general_id','ev_indicador_general_id');
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
                   ':ev_escala_evaluacion_id' => $this->received_data->model->ev_escala_evaluacion_id,
                            
                    ); 
        $query = 'DELETE FROM ev_escala_evaluacion WHERE ev_escala_evaluacion_id = :ev_escala_evaluacion_id ;'; 

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
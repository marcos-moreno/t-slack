<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'save_dat_point': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->save_dat_point();
        break;
        case 'select': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->select();
        break;
        case 'evaluar_con_reportes': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->evaluar_con_reportes();
        break;
        case 'procesar_evaluacion': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->procesar_evaluacion();
        break; 
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_evaluacion 
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
                        ':id_lider' => $this->received_data->model->id_lider,
                        ':periodo_id' => $this->received_data->model->periodo_id,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':nombre' => $this->received_data->model->nombre,
                    );
            $query = 'INSERT INTO ev_evaluacion (id_lider,periodo_id,creado,actualizado,creadopor,actualizadopor,nombre)
                        VALUES (:id_lider,:periodo_id,Now(),Now(),:creadopor,:actualizadopor,:nombre) ;';
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
                        ':ev_evaluacion_id' => $this->received_data->model->ev_evaluacion_id, 
                        ':id_lider' => $this->received_data->model->id_lider, 
                        ':periodo_id' => $this->received_data->model->periodo_id, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':nombre' => $this->received_data->model->nombre, 
                    ); 
            $query = 'UPDATE ev_evaluacion SET id_lider=:id_lider,periodo_id=:periodo_id,actualizado=Now()
                        ,actualizadopor=:actualizadopor,nombre=:nombre 
                        WHERE  ev_evaluacion_id = :ev_evaluacion_id ;';

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

    public function procesar_evaluacion(){
        $parameters = array(
            ':ev_evaluacion_id' => $this->received_data->ev_evaluacion_id,  
            ':ev_evaluacion_ln_id' => $this->received_data->ev_evaluacion_ln_id,  
            ':id_empleado' => $this->received_data->id_empleado,    
            ':id_user' => $_SESSION['id_empleado'],  
            ':no_faltas' => $this->received_data->no_faltas,  
            ':no_retardos' => $this->received_data->no_retardos,  
        );
        try {
            $query = "
                    SELECT refividrio.procesar_evaluacion(
                        :ev_evaluacion_id,:ev_evaluacion_ln_id,:id_empleado,:id_user,:no_faltas,:no_retardos
                    )" ; 
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $data = $row;
            } 
            $output = array('status' => 'success','data' => $data); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('status' => 'error','data' => $exc->getMessage(),'paramas' => $parameters ); 
            echo json_encode($output); 
            return false;
        }  
    }
    public function save_dat_point(){
        try {  
            $parameters = array(
                ':points' => $this->received_data->points,  
                ':id_lider' => $_SESSION['id_empleado'],
            );
            $query = "SELECT refividrio.save_dat_point(:points,:id_lider)";
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
    public function select(){
        try {  
            $parameters = array(
                ':valor' => $this->received_data->filter,  
                ':id_lider' => $_SESSION['id_empleado'],
            );
            $query = "SELECT 
                        ev_evaluacion_id,id_lider,periodo_id,creado,actualizado
                        ,creadopor,actualizadopor,nombre 
                    FROM ev_evaluacion 
                    WHERE 
                        id_lider = :id_lider
                        AND nombre  ILIKE '%' || :valor || '%' 
                    ORDER BY ev_evaluacion_id DESC" ;
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
                    $empleado = $this->search_union($row,'empleado','id_empleado','id_lider');     
                    $empleado[0]['password'] = '';
                    $row['empleado'] = $empleado ; 
                    $row['periodo'] = $this->search_union($row,'periodo','periodo_id','periodo_id');
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
    public function evaluar_con_reportes(){
        $parameters = array(
            ':id_empleado' => $this->received_data->id_empleado,  
            ':ev_indicador_general_id' => $this->received_data->ev_indicador_general_id,  
            ':ev_evaluacion_id' => $this->received_data->ev_evaluacion_id,  
            ':id_user' => $_SESSION['id_empleado'],  
        );
        try {  
            $query = "
                    SELECT refividrio.evaluar_con_reportes(
                        :id_empleado,:ev_indicador_general_id,:ev_evaluacion_id,:id_user
                    )" ; 
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $data = $row;
            } 
            $output = array('status' => 'success','data' => $data); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('status' => 'error','data' => $exc->getMessage()); 
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
                   ':ev_evaluacion_id' => $this->received_data->model->ev_evaluacion_id,
                            
                    ); 
        $query = 'DELETE FROM ev_evaluacion WHERE ev_evaluacion_id = :ev_evaluacion_id ;'; 

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
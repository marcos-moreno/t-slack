<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_punto_evaluar($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_punto_evaluar($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_punto_evaluar($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_punto_evaluar($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_punto_evaluar 
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
                        ':ev_tipo_captura_id' => $this->received_data->model->ev_tipo_captura_id,
                        ':nombre' => $this->received_data->model->nombre,
                        ':descripcion' => $this->received_data->model->descripcion,
                        ':porcentaje_tl' => $this->received_data->model->porcentaje_tl,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':min_escala' => $this->received_data->model->min_escala, 
                        ':max_escala' => $this->received_data->model->max_escala,
                        ':incremento' => $this->received_data->model->incremento,
                    ); 
        $query = 'INSERT INTO ev_punto_evaluar (ev_indicador_general_id,ev_tipo_captura_id,nombre,
        descripcion,porcentaje_tl,creado,creadopor,actualizado,actualizadopor,min_escala,max_escala,incremento) 
        VALUES (:ev_indicador_general_id,:ev_tipo_captura_id,:nombre,:descripcion,:porcentaje_tl,Now(),:creadopor,Now(),:actualizadopor
        ,:min_escala,:max_escala,:incremento) ;';

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
                    ':ev_punto_evaluar_id' => $this->received_data->model->ev_punto_evaluar_id, 
                        ':ev_indicador_general_id' => $this->received_data->model->ev_indicador_general_id, 
                        ':ev_tipo_captura_id' => $this->received_data->model->ev_tipo_captura_id, 
                        ':nombre' => $this->received_data->model->nombre, 
                        ':descripcion' => $this->received_data->model->descripcion, 
                        ':porcentaje_tl' => $this->received_data->model->porcentaje_tl, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':min_escala' => $this->received_data->model->min_escala, 
                        ':max_escala' => $this->received_data->model->max_escala,
                        ':incremento' => $this->received_data->model->incremento,
                    ); 
            $query = 'UPDATE ev_punto_evaluar SET ev_indicador_general_id=:ev_indicador_general_id,ev_tipo_captura_id=:ev_tipo_captura_id,
            nombre=:nombre,descripcion=:descripcion,porcentaje_tl=:porcentaje_tl,actualizado=Now(),actualizadopor=:actualizadopor 
            ,min_escala=:min_escala,max_escala=:max_escala,incremento=:incremento
            WHERE  ev_punto_evaluar_id = :ev_punto_evaluar_id ;';

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
                ':ev_indicador_general_id' => $this->received_data->filter
            );
            $query = 'SELECT ev_punto_evaluar_id,ev_indicador_general_id,ev_tipo_captura_id,nombre,descripcion,porcentaje_tl,creado
                    ,creadopor,actualizado,actualizadopor,min_escala,max_escala,incremento
                    FROM ev_punto_evaluar  
                    WHERE 
                    ev_indicador_general_id = :ev_indicador_general_id;';
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_indicador'] = $this->search_union($row,'ev_indicador_puesto','ev_indicador_general_id','ev_indicador_general_id');
                    $row['ev_tipo_captura'] = $this->search_union($row,'ev_tipo_captura','ev_tipo_captura_id','ev_tipo_captura_id');
                    $data[] = $row;
            } 
            echo json_encode($data); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode([]); 
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
            echo json_encode([]); 
            return false;
        }  
    }
    public function delete(){
        try {  
            $data = array(
                   ':ev_punto_evaluar_id' => $this->received_data->model->ev_punto_evaluar_id,
                            
                    ); 
            $query = 'DELETE FROM ev_punto_evaluar WHERE ev_punto_evaluar_id = :ev_punto_evaluar_id ;'; 
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
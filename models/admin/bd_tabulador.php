<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Tabulador($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Tabulador($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Tabulador($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Tabulador($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Tabulador 
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
                    ':tabulador' => $this->received_data->model->tabulador,
                        ':id_empresa' => $this->received_data->model->id_empresa,
                        ':activo' => $this->received_data->model->activo,
                        ':sueldo' => $this->received_data->model->sueldo,
                        ':costo_hora' => $this->received_data->model->costo_hora,
                        ':septimo_dia' => $this->received_data->model->septimo_dia,
                        ':costo_hora_extra' => $this->received_data->model->costo_hora_extra,
                        ':orden' => $this->received_data->model->orden,
                        ':ev_nivel_p_id' => $this->received_data->model->ev_nivel_p_id,
                        
                    ); 
        $query = 'INSERT INTO tabulador (tabulador,id_empresa,activo,sueldo,costo_hora,septimo_dia,costo_hora_extra,orden,ev_nivel_p_id) VALUES (:tabulador,:id_empresa,:activo,:sueldo,:costo_hora,:septimo_dia,:costo_hora_extra,:orden,:ev_nivel_p_id) ;';

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
                    ':id_tabulador' => $this->received_data->model->id_tabulador, 
                        ':tabulador' => $this->received_data->model->tabulador, 
                        ':id_empresa' => $this->received_data->model->id_empresa, 
                        ':activo' => $this->received_data->model->activo, 
                        ':sueldo' => $this->received_data->model->sueldo, 
                        ':costo_hora' => $this->received_data->model->costo_hora, 
                        ':septimo_dia' => $this->received_data->model->septimo_dia, 
                        ':costo_hora_extra' => $this->received_data->model->costo_hora_extra, 
                        ':orden' => $this->received_data->model->orden, 
                        ':ev_nivel_p_id' => $this->received_data->model->ev_nivel_p_id, 
                         
                    ); 
            $query = 'UPDATE tabulador SET tabulador=:tabulador,id_empresa=:id_empresa,activo=:activo,sueldo=:sueldo,costo_hora=:costo_hora,septimo_dia=:septimo_dia,costo_hora_extra=:costo_hora_extra,orden=:orden,ev_nivel_p_id=:ev_nivel_p_id WHERE  id_tabulador = :id_tabulador ;';

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
            $query = "
                    SELECT 
                        id_tabulador,tabulador,id_empresa,activo,sueldo,costo_hora
                        ,septimo_dia,costo_hora_extra,orden,ev_nivel_p_id 
                    FROM tabulador 
                    WHERE 
                        tabulador  ILIKE '%' || :valor || '%' 
                    ORDER BY ev_nivel_p_id,tabulador,orden DESC";
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_nivel_p'] = $this->search_union($row,'ev_nivel_p','ev_nivel_p_id','ev_nivel_p_id');
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
                   ':id_tabulador' => $this->received_data->model->id_tabulador,
                            
                    ); 
        $query = 'DELETE FROM tabulador WHERE id_tabulador = :id_tabulador ;'; 

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
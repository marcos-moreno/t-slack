<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Solicitud_uniforme($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Solicitud_uniforme($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Solicitud_uniforme($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Solicitud_uniforme($data,$connect,$received_data);
            $model->select();
        break; 
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Solicitud_uniforme 
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
            $id_empleado = (isset($this->received_data->admin) == false) ?  $_SESSION['id_empleado'] : $this->received_data->model->id_empleado;
            $data = array(
                            ':id_empleado' => $id_empleado ,
                            ':estado' => $this->received_data->model->estado,
                            ':total' => $this->received_data->model->total, 
                            ':creadopor' => $_SESSION['id_empleado'],  
                            ':id_tipo_entrega' => $this->received_data->model->id_tipo_entrega, 
                            ':fecha_entrega' => $this->received_data->model->fecha_entrega, 
                    ); 
            $query = 'INSERT INTO  un_solicitud_uniforme (id_empleado,estado,total,fecha_creado,creadopor,id_tipo_entrega,fecha_entrega) VALUES 
                        (:id_empleado,:estado,:total,CURRENT_TIMESTAMP,:creadopor,:id_tipo_entrega,:fecha_entrega) RETURNING id_solicitud_uniforme ;';
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $id = 0;
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                $id = $row['id_solicitud_uniforme']; 
            } 
            $output = array('message' => 'Data Inserted','id_insert' => $id); 
            echo json_encode($output); 
            return true;
        } catch (Exception $exc) {
            $output = array('message' => $exc); 
            echo json_encode($output); 
            return false;
        } 
    }  

    public function update(){
        try {
            $data = array(
                    ':id_solicitud_uniforme' => $this->received_data->model->id_solicitud_uniforme, 
                            ':id_empleado' => $this->received_data->model->id_empleado, 
                            ':estado' => $this->received_data->model->estado, 
                            ':total' => $this->received_data->model->total, 
                            ':fecha_entrega' => $this->received_data->model->fecha_entrega, 
                            ':id_tipo_entrega' => $this->received_data->model->id_tipo_entrega, 
                    ); 
            $query = 'UPDATE  un_solicitud_uniforme SET id_empleado=:id_empleado,estado=:estado,total=:total,
            fecha_entrega=:fecha_entrega,id_tipo_entrega=:id_tipo_entrega 
            WHERE  id_solicitud_uniforme = :id_solicitud_uniforme ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Updated'); 
            echo json_encode($output); 
            return true;
        } catch (Exception $exc) {
            $output = array('message' => $exc); 
            echo json_encode($output); 
            return false;
        }  
    } 

    public function select(){
        try {   
            $query = 'SELECT e.id_solicitud_uniforme,e.id_empleado,e.estado,e.total,
                            s.fecha_creado   ,
                            s.nombre As segmento, emp.empresa_nombre , e.fecha_entrega  AS fecha_entrega,id_tipo_entrega
                    FROM  un_solicitud_uniforme  e
                    INNER JOIN empleado emplea ON emplea.id_empleado = e.id_empleado 
                    INNER JOIN segmento s ON s.id_segmento = emplea.id_segmento 
                    INNER JOIN empresa emp ON emp.id_empresa = s.id_empresa  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' .str_replace( '_SESSION_id_empleado', $_SESSION['id_empleado'], $this->received_data->filter) :'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_empleado');
                    $row['tipo_entregas'] = $this->search_union($row,'un_tipo_entregas','id_tipo_entrega','id_tipo_entrega');
                    $data[] = $row;
            } 
            echo json_encode($data); 
            return true;
        } catch (Exception $exc) {
            $output = array('message' => $exc); 
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
        } catch (Exception $exc) {
            $output = array('message' => $exc); 
            echo json_encode($output); 
            return false;
        }  
    }
    public function delete(){
        try {  
            $data = array(
                   ':id_solicitud_uniforme' => $this->received_data->model->id_solicitud_uniforme,
                            
                    ); 
        $query = 'DELETE FROM  un_solicitud_uniforme WHERE id_solicitud_uniforme = :id_solicitud_uniforme ;'; 

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Deleted'); 
            echo json_encode($output); 
            return true;
        } catch (Exception $exc) {
            $output = array('message' => $exc); 
            echo json_encode($output); 
            return false;
        }  
    }

  
    

} 
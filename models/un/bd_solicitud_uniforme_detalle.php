<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Solicitud_uniforme_detalle($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Solicitud_uniforme_detalle($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Solicitud_uniforme_detalle($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Solicitud_uniforme_detalle($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Solicitud_uniforme_detalle 
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
                    ':id_solicitud_uniforme' => $this->received_data->model->id_solicitud_uniforme,
                            ':id_producto' => $this->received_data->model->id_producto,
                            ':id_talla' => $this->received_data->model->id_talla,
                            ':total_linea' => $this->received_data->model->total_linea,
                            ':id_color' => $this->received_data->model->id_color,
                            ':cantidad' => $this->received_data->model->cantidad,
                            ':permitir_cambiar_color' => $this->received_data->model->permitir_cambiar_color,
                            ':id_paquete' => $this->received_data->model->id_paquete,
                             
                    ); 
            $query = 'INSERT INTO  un_solicitud_uniforme_detalle (id_solicitud_uniforme,id_producto,id_talla,total_linea,id_color,cantidad,permitir_cambiar_color,id_paquete) 
                        VALUES (:id_solicitud_uniforme,:id_producto,:id_talla,:total_linea,:id_color,:cantidad,:permitir_cambiar_color,:id_paquete) ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Inserted'); 
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
                    ':id_solicitud_uniforme_detalle' => $this->received_data->model->id_solicitud_uniforme_detalle, 
                            ':id_solicitud_uniforme' => $this->received_data->model->id_solicitud_uniforme, 
                            ':id_producto' => $this->received_data->model->id_producto, 
                            ':id_talla' => $this->received_data->model->id_talla, 
                            ':total_linea' => $this->received_data->model->total_linea, 
                            ':id_color' => $this->received_data->model->id_color, 
                            ':cantidad' => $this->received_data->model->cantidad, 
                            ':permitir_cambiar_color' => $this->received_data->model->permitir_cambiar_color, 
                            ':id_paquete' => $this->received_data->model->id_paquete, 
                            
                             
                    ); 
        $query = 'UPDATE  un_solicitud_uniforme_detalle SET id_solicitud_uniforme=:id_solicitud_uniforme,id_producto=:id_producto
        ,id_talla=:id_talla,total_linea=:total_linea,id_color=:id_color,cantidad=:cantidad,permitir_cambiar_color=:permitir_cambiar_color,
        id_paquete=:id_paquete
        WHERE  id_solicitud_uniforme_detalle = :id_solicitud_uniforme_detalle ;';

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
             
        $query = 'SELECT id_solicitud_uniforme_detalle,id_solicitud_uniforme,id_producto,id_talla,total_linea,id_color,cantidad,
                    permitir_cambiar_color,id_paquete 
                    FROM  un_solicitud_uniforme_detalle  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['solicitud_uniforme'] = $this->search_union($row,'un_solicitud_uniforme','id_solicitud_uniforme','id_solicitud_uniforme');
                  
                    $row['producto'] = $this->search_union($row,'un_producto','id_producto','id_producto');
                  
                    $row['talla'] = $this->search_union($row,'un_talla','id_talla','id_talla');
                  
                    $row['color'] = $this->search_union($row,'un_color','id_color','id_color');
 
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
        $query = "";
        try {  
            $data = array(
                   ':id_solicitud_uniforme_detalle' => $this->received_data->model->id_solicitud_uniforme_detalle,
                            
                    ); 
            $query = 'DELETE FROM  un_solicitud_uniforme_detalle WHERE  ' . (isset($this->received_data->filter) ? $this->received_data->filter : '  id_solicitud_uniforme_detalle = :id_solicitud_uniforme_detalle ') ; 

            $statement = $this->connect->prepare($query); 
            if (isset($this->received_data->filter)) {
                $statement->execute();  
            }else{
                $statement->execute($data);  
            }
           
            $output = array('message' => 'Data Deleted'); 
            echo json_encode($output); 
            return true;
        } catch (Exception $exc) {
            $output = array('message' => $exc,'qry' =>$query); 
            echo json_encode($output); 
            return false;
        }  
    }

  
    

} 
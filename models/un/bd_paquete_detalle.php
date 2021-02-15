<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Paquete_detalle($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Paquete_detalle($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Paquete_detalle($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Paquete_detalle($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Paquete_detalle 
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
                    ':id_paquete' => $this->received_data->model->id_paquete,
                            ':id_producto' => $this->received_data->model->id_producto,
                            ':cantidad' => $this->received_data->model->cantidad,
                            ':costo' => $this->received_data->model->costo,
                            ':id_color' => $this->received_data->model->id_color,
                            ':permitir_cambiar_color' => $this->received_data->model->permitir_cambiar_color,
                            
                    ); 
        $query = 'INSERT INTO  un_paquete_detalle  (id_paquete,id_producto,cantidad,costo,id_color,permitir_cambiar_color)
                                         VALUES (:id_paquete,:id_producto,:cantidad,:costo,:id_color,:permitir_cambiar_color) ;';

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
                    ':id_paquete_detalle' => $this->received_data->model->id_paquete_detalle, 
                            ':id_paquete' => $this->received_data->model->id_paquete, 
                            ':id_producto' => $this->received_data->model->id_producto, 
                            ':cantidad' => $this->received_data->model->cantidad, 
                            ':costo' => $this->received_data->model->costo, 
                            ':id_color' => $this->received_data->model->id_color,
                            ':permitir_cambiar_color' => $this->received_data->model->permitir_cambiar_color,
                             
                    ); 
        $query = 'UPDATE  un_paquete_detalle  
        SET id_paquete=:id_paquete,id_producto=:id_producto,cantidad=:cantidad,costo=:costo,id_color=:id_color,
        permitir_cambiar_color=:permitir_cambiar_color
        WHERE  id_paquete_detalle = :id_paquete_detalle ;';

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
             
        $query = 'SELECT id_paquete_detalle,id_paquete,id_producto,cantidad,costo ,id_color,permitir_cambiar_color
                    FROM  un_paquete_detalle   
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['paquete'] = $this->search_union($row,'un_paquete','id_paquete','id_paquete');
                    $row['producto'] = $this->search_union($row,'un_producto','id_producto','id_producto');
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
        try {  
            $data = array(
                   ':id_paquete_detalle' => $this->received_data->model->id_paquete_detalle,
                            
                    ); 
        $query = 'DELETE FROM  un_paquete_detalle  WHERE id_paquete_detalle = :id_paquete_detalle ;'; 

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
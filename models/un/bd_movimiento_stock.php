<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Movimiento_stock($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Movimiento_stock($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Movimiento_stock($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Movimiento_stock($data,$connect,$received_data);
            $model->select();
        break;
        case 'insert_stock': 
            $model = new Movimiento_stock($data,$connect,$received_data);
            $model->insert_stock();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Movimiento_stock 
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
                    ':id_almacen' => $this->received_data->model->id_almacen,
                            ':id_producto' => $this->received_data->model->id_producto,
                            ':cantidad' => $this->received_data->model->cantidad,
                            ':id_tipo_movimiento' => $this->received_data->model->id_tipo_movimiento,
                            ':id_movimiento' => $this->received_data->model->id_movimiento,
                            ':descripcion' => $this->received_data->model->descripcion,
                            ':activo' => $this->received_data->model->activo,
                            ':id_color' => $this->received_data->model->id_color,
                            ':id_talla' => $this->received_data->model->id_talla,
                            
                    ); 
        $query = 'INSERT INTO un_movimiento_stock  (id_almacen,id_producto,cantidad,id_tipo_movimiento,id_movimiento,descripcion,activo,id_color,id_talla) VALUES (:id_almacen,:id_producto,:cantidad,:id_tipo_movimiento,:id_movimiento,:descripcion,:activo,:id_color,:id_talla) ;';

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
                    ':id_movimiento_stock' => $this->received_data->model->id_movimiento_stock, 
                            ':id_almacen' => $this->received_data->model->id_almacen, 
                            ':id_producto' => $this->received_data->model->id_producto, 
                            ':cantidad' => $this->received_data->model->cantidad, 
                            ':id_tipo_movimiento' => $this->received_data->model->id_tipo_movimiento, 
                            ':id_movimiento' => $this->received_data->model->id_movimiento, 
                            ':descripcion' => $this->received_data->model->descripcion, 
                            ':activo' => $this->received_data->model->activo, 
                            ':id_color' => $this->received_data->model->id_color, 
                            ':id_talla' => $this->received_data->model->id_talla, 
                             
                    ); 
        $query = 'UPDATE un_movimiento_stock  SET id_almacen=:id_almacen,id_producto=:id_producto,cantidad=:cantidad,id_tipo_movimiento=:id_tipo_movimiento,id_movimiento=:id_movimiento,descripcion=:descripcion,activo=:activo,id_color=:id_color,id_talla=:id_talla WHERE  id_movimiento_stock = :id_movimiento_stock ;';

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
    
    public function insert_stock(){
        try {  
            $query = 'SELECT refividrio.create_movement_stock(' . $this->received_data->model->id_movimiento .','. $this->received_data->model->id_tipo_movimiento  .');'  ;
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
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
    public function select(){
        try {  
             
        $query = 'SELECT id_movimiento_stock,id_almacen,id_producto,cantidad,id_tipo_movimiento,id_movimiento,descripcion,activo,id_color,id_talla, fecha_movimiento,creado,codigo
                    FROM un_movimiento_stock   
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['almacen'] = $this->search_union($row,'un_almacen','id_almacen','id_almacen');
                  
                    $row['producto'] = $this->search_union($row,'un_producto','id_producto','id_producto');
                  
                    $row['tipo_movimiento'] = $this->search_union($row,'un_tipo_movimiento','id_tipo_movimiento','id_tipo_movimiento');
                  
                    $row['color'] = $this->search_union($row,'un_color','id_color','id_color');
                  
                    $row['talla'] = $this->search_union($row,'un_talla','id_talla','id_talla');
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
                   ':id_movimiento_stock' => $this->received_data->model->id_movimiento_stock,
                            
                    ); 
        $query = 'DELETE FROM un_movimiento_stock  WHERE id_movimiento_stock = :id_movimiento_stock ;'; 

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
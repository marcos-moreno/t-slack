<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Compra($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Compra($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Compra($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Compra($data,$connect,$received_data);
            $model->select();
        break;
        case 'calculo_total': 
            $model = new Compra($data,$connect,$received_data);
            $model->calculo_total();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Compra 
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
                    ':id_proveedor' => $this->received_data->model->id_proveedor,
                            ':total' => $this->received_data->model->total,
                            ':fecha_compra' => $this->received_data->model->fecha_compra,
                            ':nombre' => $this->received_data->model->nombre,
                            ':estado' => $this->received_data->model->estado,
                            
                    ); 
        $query = 'INSERT INTO un_compra (id_proveedor,total,fecha_compra,nombre,estado) VALUES (:id_proveedor,:total,:fecha_compra,:nombre,:estado) ;';

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
                    ':id_compra' => $this->received_data->model->id_compra, 
                            ':id_proveedor' => $this->received_data->model->id_proveedor, 
                            ':total' => $this->received_data->model->total, 
                            ':fecha_compra' => $this->received_data->model->fecha_compra, 
                            ':nombre' => $this->received_data->model->nombre, 
                            ':estado' => $this->received_data->model->estado, 
                             
                    ); 
        $query = 'UPDATE un_compra SET id_proveedor=:id_proveedor,total=:total,fecha_compra=:fecha_compra,nombre=:nombre,estado=:estado WHERE  id_compra = :id_compra ;';

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
    public function calculo_total(){
        try {  
        $query = 'SELECT calcular_total_compra ('. $this->received_data->id_compra . ")";
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
             
        $query = 'SELECT id_compra,id_proveedor,total,fecha_compra,nombre,estado 
                    FROM un_compra 
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['proveedor'] = $this->search_union($row,'proveedor','id_proveedor','id_proveedor');
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
                   ':id_compra' => $this->received_data->model->id_compra,
                            
                    ); 
        $query = 'DELETE FROM un_compra WHERE id_compra = :id_compra ;'; 

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
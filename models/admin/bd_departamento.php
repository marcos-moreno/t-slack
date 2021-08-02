<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Departamento($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Departamento($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Departamento($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Departamento($data,$connect,$received_data);
            $model->select();
        break; 
        case 'getByLider': 
            $model = new Departamento($data,$connect,$received_data);
            $model->getByLider();
        break;  
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Departamento 
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
            $parameters = array(
                    ':nombre' => $this->received_data->model->nombre,
                        ':activo' => $this->received_data->model->activo,
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':id_empresa' => $this->received_data->model->id_empresa,
                        ':id_segmento' => $this->received_data->model->id_segmento,
                        ':id_cerberus' => $this->received_data->model->id_cerberus,
                        
                    ); 
        $query = 'INSERT INTO departamento (nombre,activo,creado,actualizado,actualizadopor,creadopor,id_empresa,id_segmento,id_cerberus) VALUES (:nombre,:activo,Now(),Now(),:actualizadopor,:creadopor,:id_empresa,:id_segmento,:id_cerberus) ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);  
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
            $parameters = array(
                    ':departamento_id' => $this->received_data->model->departamento_id, 
                        ':nombre' => $this->received_data->model->nombre, 
                        ':activo' => $this->received_data->model->activo, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':id_empresa' => $this->received_data->model->id_empresa, 
                        ':id_segmento' => $this->received_data->model->id_segmento, 
                        ':id_cerberus' => $this->received_data->model->id_cerberus, 
                         
                    ); 
            $query = 'UPDATE departamento SET nombre=:nombre,activo=:activo,actualizado=Now(),actualizadopor=:actualizadopor,id_empresa=:id_empresa,id_segmento=:id_segmento,id_cerberus=:id_cerberus WHERE  departamento_id = :departamento_id ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);  
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
             
        $query = 'SELECT departamento_id,nombre,activo,creado,actualizado,actualizadopor,creadopor,id_empresa,id_segmento,id_cerberus 
                    FROM departamento  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') .  ' ORDER BY id_empresa,departamento_id DESC';
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['empresa'] = $this->search_union($row,'empresa','id_empresa','id_empresa');
                    $row['segmento'] = $this->search_union($row,'segmento','id_segmento','id_segmento');
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

    public function getByLider(){
        try {  
            $parameters = array( 
                    ':id_empleado' => $_SESSION['id_empleado'], 
                ); 
            $query = '	SELECT d.*
            FROM refividrio.lider_departamento ld
            INNER JOIN refividrio.departamento d ON d.departamento_id =  ld.departamento_id
            WHERE id_empleado = :id_empleado'; 
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['empresa'] = $this->search_union($row,'empresa','id_empresa','id_empresa');
                    $row['segmento'] = $this->search_union($row,'segmento','id_segmento','id_segmento');
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
                   ':departamento_id' => $this->received_data->model->departamento_id,
                            
                    ); 
        $query = 'DELETE FROM departamento WHERE departamento_id = :departamento_id ;'; 

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
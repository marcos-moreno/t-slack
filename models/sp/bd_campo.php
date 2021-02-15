<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Campo($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Campo($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Campo($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Campo($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Campo 
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
                    ':nombre' => $this->received_data->model->nombre,
                            ':descripcion' => $this->received_data->model->descripcion,
                            ':obligatorio' => $this->received_data->model->obligatorio,
                            ':activo' => $this->received_data->model->activo,
                            ':id_grupo_marca' => $this->received_data->model->id_grupo_marca,
                            ':id_tipo' => $this->received_data->model->id_tipo,
                            
                    ); 
        $query = 'INSERT INTO sp_campo (nombre,descripcion,obligatorio,activo,id_grupo_marca,id_tipo) VALUES (:nombre,:descripcion,:obligatorio,:activo,:id_grupo_marca,:id_tipo) ;';

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
                    ':id_campo' => $this->received_data->model->id_campo, 
                            ':nombre' => $this->received_data->model->nombre, 
                            ':descripcion' => $this->received_data->model->descripcion, 
                            ':obligatorio' => $this->received_data->model->obligatorio, 
                            ':activo' => $this->received_data->model->activo, 
                            ':id_grupo_marca' => $this->received_data->model->id_grupo_marca, 
                            ':id_tipo' => $this->received_data->model->id_tipo, 
                             
                    ); 
        $query = 'UPDATE sp_campo SET nombre=:nombre,descripcion=:descripcion,obligatorio=:obligatorio,activo=:activo,id_grupo_marca=:id_grupo_marca,id_tipo=:id_tipo WHERE  id_campo = :id_campo ;';

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
             
        $query = 'SELECT id_campo,nombre,descripcion,obligatorio,activo,id_grupo_marca,id_tipo 
                    FROM sp_campo  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['grupo_marca'] = $this->search_union($row,'sp_grupo_marca','id_grupo_marca','id_grupo_marca');
                  
                    $row['tipo'] = $this->search_union($row,'tipo','id_tipo','id_tipo');
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
                   ':id_campo' => $this->received_data->model->id_campo,
                            
                    ); 
        $query = 'DELETE FROM sp_campo WHERE id_campo = :id_campo ;'; 

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
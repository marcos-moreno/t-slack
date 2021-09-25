<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Jerarquizacion($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Jerarquizacion($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Jerarquizacion($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Jerarquizacion($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Jerarquizacion 
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
                    ':id_empleado' => $this->received_data->model->id_empleado,
                        ':id_atributo_nivel' => $this->received_data->model->id_atributo_nivel,
                        ':activo' => $this->received_data->model->activo, 
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':departamento_id' => ($this->received_data->model->departamento_id = "null" ? NULL : $this->received_data->model->departamento_id),
                        ':id_superior' => ($this->received_data->model->id_superior = "null" ? NULL : $this->received_data->model->id_superior) ,
                    ); 
        $query = "INSERT INTO jerarquizacion (id_empleado,id_atributo_nivel,activo,id_superior,creado,actualizado,creadopor,actualizadopor,departamento_id) 
                    VALUES (:id_empleado,:id_atributo_nivel,:activo,
                    :id_superior
                    ,Now(),Now(),:creadopor,:actualizadopor,:departamento_id) ;";
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
                    ':jerarquizacion_id' => $this->received_data->model->jerarquizacion_id, 
                        ':id_empleado' => $this->received_data->model->id_empleado, 
                        ':id_atributo_nivel' => $this->received_data->model->id_atributo_nivel, 
                        ':activo' => $this->received_data->model->activo,  
                        ':actualizadopor' => $_SESSION['id_empleado'], 
                        ':departamento_id' => ($this->received_data->model->departamento_id = "null" ? NULL : $this->received_data->model->departamento_id),
                        ':id_superior' => ($this->received_data->model->id_superior = "null" ? NULL : $this->received_data->model->id_superior) ,
                    ); 
            $query = 'UPDATE jerarquizacion SET id_empleado=:id_empleado,id_atributo_nivel=:id_atributo_nivel
                        ,activo=:activo,id_superior=:id_superior,actualizado=Now()
                        ,actualizadopor=:actualizadopor 
                        ,departamento_id=:departamento_id 
                        WHERE  jerarquizacion_id = :jerarquizacion_id ;';

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
            $query = "SELECT 
                        j.jerarquizacion_id,j.id_empleado,j.id_atributo_nivel,
                        j.activo,j.id_superior,j.creado,j.actualizado,j.creadopor,j.actualizadopor
                        ,CONCAT(e.paterno,' ',e.materno,' ',e.nombre) As nombre_empleado
                        ,CONCAT(s.paterno,' ',s.materno,' ',s.nombre) As superior
                        ,a.value
                        ,a.descripcion
                        ,j.departamento_id
                    FROM jerarquizacion j
                    INNER JOIN empleado e ON e.id_empleado = j.id_empleado
                    LEFT JOIN empleado s ON s.id_empleado = j.id_superior
                    INNER JOIN ev_atributo a ON a.id_atributo = j.id_atributo_nivel
                    WHERE 
                    CONCAT(e.paterno,' ',e.materno,' ',e.nombre)  ILIKE '%' || :valor || '%' 
                    OR CONCAT(s.paterno,' ',s.materno,' ',s.nombre) ILIKE '%' || :valor || '%' 
                    ORDER BY a.value ASC" ;
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    // $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_empleado');
                    // $row['ev_atributo'] = $this->search_union($row,'ev_atributo','id_atributo','id_atributo_nivel');
                    // $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_superior');
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


    public function selectLiders(){
        try {  
            
            $parameters = array(
                ':valor' => $this->received_data->filter,  
            );
            $query = "SELECT 
                            j.jerarquizacion_id,j.id_empleado,j.id_atributo_nivel,
                            j.activo,j.id_superior,j.creado,j.actualizado,j.creadopor,j.actualizadopor
                            ,CONCAT(e.paterno,' ',e.materno,' ',e.nombre) As nombre_empleado
                            ,CONCAT(s.paterno,' ',s.materno,' ',s.nombre) As superior
                            ,a.value
                            ,a.descripcion
                        FROM jerarquizacion j
                        INNER JOIN empleado e ON e.id_empleado = j.id_empleado
                        INNER JOIN empleado s ON s.id_empleado = j.id_superior
                        INNER JOIN ev_atributo a ON a.id_atributo = j.id_atributo_nivel 
                        ORDER BY a.value ASC" ;
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    // $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_empleado');
                    // $row['ev_atributo'] = $this->search_union($row,'ev_atributo','id_atributo','id_atributo_nivel');
                    $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_superior');
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
                   ':jerarquizacion_id' => $this->received_data->model->jerarquizacion_id,
                            
                    ); 
        $query = 'DELETE FROM jerarquizacion WHERE jerarquizacion_id = :jerarquizacion_id ;'; 

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
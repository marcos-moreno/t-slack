<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Periodo($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Periodo($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Periodo($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Periodo($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Periodo 
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
                    ':nombre_periodo' => $this->received_data->model->nombre_periodo,
                        ':inicio_periodo' => $this->received_data->model->inicio_periodo,
                        ':fin_periodo' => $this->received_data->model->fin_periodo,
                        ':ejercicio' => $this->received_data->model->ejercicio,
                        ':id_empresa' => $this->received_data->model->id_empresa==0?null:$this->received_data->model->id_empresa,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':numero_periodo' => $this->received_data->model->numero_periodo,
                        ':activo' => $this->received_data->model->activo,
                        ':elemento_sistema_atributo' => $this->received_data->model->elemento_sistema_atributo,
                        
                    ); 
            $query = 'INSERT INTO periodo (nombre_periodo,inicio_periodo,fin_periodo,ejercicio,id_empresa,creado,actualizado,creadopor,actualizadopor,numero_periodo,activo,elemento_sistema_atributo) VALUES (:nombre_periodo,:inicio_periodo,:fin_periodo,:ejercicio,:id_empresa,Now(),Now(),:creadopor,:actualizadopor,:numero_periodo,:activo,:elemento_sistema_atributo) ;';
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
                    ':nombre_periodo' => $this->received_data->model->nombre_periodo, 
                        ':inicio_periodo' => $this->received_data->model->inicio_periodo, 
                        ':fin_periodo' => $this->received_data->model->fin_periodo, 
                        ':ejercicio' => $this->received_data->model->ejercicio, 
                        ':id_empresa' => $this->received_data->model->id_empresa, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':numero_periodo' => $this->received_data->model->numero_periodo, 
                        ':activo' => $this->received_data->model->activo, 
                        ':elemento_sistema_atributo' => $this->received_data->model->elemento_sistema_atributo, 
                        ':periodo_id' => $this->received_data->model->periodo_id, 
                         
                    ); 
            $query = 'UPDATE periodo SET nombre_periodo=:nombre_periodo,inicio_periodo=:inicio_periodo,fin_periodo=:fin_periodo,ejercicio=:ejercicio,id_empresa=:id_empresa,actualizado=Now(),actualizadopor=:actualizadopor,numero_periodo=:numero_periodo,activo=:activo,elemento_sistema_atributo=:elemento_sistema_atributo WHERE  periodo_id = :periodo_id ;';

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
            $activo_sql = "";
            $parameters = array(
                ':valor' => $this->received_data->filter
            );
            if ($this->received_data->activo === true) {
                $activo_sql = " AND p.activo = true ";
            }
            $query = "SELECT nombre_periodo
                        ,to_char(inicio_periodo, 'yyyy-MM-ddThh:mm') As inicio_periodo
                        ,to_char(fin_periodo, 'yyyy-MM-ddThh:mm') As fin_periodo,ejercicio
                        ,p.id_empresa,p.creado,p.actualizado,p.creadopor
                        ,p.actualizadopor,numero_periodo,p.activo
                        ,p.elemento_sistema_atributo,p.periodo_id 
                    FROM refividrio.periodo p
                    INNER JOIN refividrio.ev_atributo at ON at.id_atributo = elemento_sistema_atributo 
                    WHERE
                        at.value = :valor
                        ". $activo_sql ."
                    ORDER BY ejercicio DESC, numero_periodo DESC, elemento_sistema_atributo DESC" ;
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_atributo_es'] = $this->search_union($row,'ev_atributo','id_atributo','elemento_sistema_atributo');
                    $row['empresa'] = $this->search_union($row,'empresa','id_empresa','id_empresa');
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
                   ':periodo_id' => $this->received_data->model->periodo_id,
                            
                    ); 
        $query = 'DELETE FROM periodo WHERE periodo_id = :periodo_id ;'; 

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
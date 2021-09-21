<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_propuesta($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_propuesta($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_propuesta($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_propuesta($data,$connect,$received_data);
            $model->select();
        break;
        case 'selectAll': 
            $model = new Ev_propuesta($data,$connect,$received_data);
            $model->selectAll();
        break;
        case 'selected': 
            $model = new Ev_propuesta($data,$connect,$received_data);
            $model->selected();
        break;
        case 'selectPropuesta': 
            $model = new Ev_propuesta($data,$connect,$received_data);
            $model->selectPropuesta();
        break;
        case 'selectEstado': 
            $model = new Ev_propuesta($data,$connect,$received_data);
            $model->selectEstado();
        break;
        case 'selectConEstado': 
            $model = new Ev_propuesta($data,$connect,$received_data);
            $model->selectConEstado();
        break;
        case 'selectConEstado2': 
            $model = new Ev_propuesta($data,$connect,$received_data);
            $model->selectConEstado2();
        break;
        case 'selectConEstadoUser': 
            $model = new Ev_propuesta($data,$connect,$received_data);
            $model->selectConEstadoUser();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_propuesta 
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

    public function selectEstado(){
        try { 
            $valor = 'statuspropuesta';
            $parameters = array(
                ':estado' => $valor,
            );  

            $query = "
                select id_atributo, value, activo, descripcion, tabla 
                from ev_atributo where tabla = :estado
                     
            " ;         
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
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


    

    public function selectConEstadoUser(){
        try {  
          
            $parameters = array(
                ':valor' => $this->received_data->filterestado, 
                ':id_empleado' => $_SESSION['id_empleado'] 
         
            );
            $query = "SELECT propuesta_id,id_creadopor,TO_CHAR(p.fecha_creado, 'DD-MM-YYYY HH12:MI:SS AM') as fecha_creado ,id_empleado,departamento_id,texto,estado,propuesta 
                    FROM ev_propuesta 
                    WHERE 
                    estado = :valor and id_empleado = :id_empleado
                    ORDER BY 1 DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                   
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

    public function selectConEstado(){
        try {  
            $stado = 'Solución de Problemas';
            $parameters = array(
                ':valor' => $this->received_data->filterestado,  
                ':propuesta' => $stado,
                ':id_empleado' => $this->received_data->model->id_empleado
            );
            $query = "SELECT p.propuesta_id,p.id_creadopor,TO_CHAR(p.fecha_creado, 'DD-MM-YYYY HH12:MI:SS AM') as fecha_creado,p.id_empleado,p.departamento_id,p.texto,p.estado,p.propuesta, e.id_empleado, e.nombre,e.paterno,e.materno
            FROM refividrio.ev_propuesta as p
            INNER JOIN refividrio.empleado as e on p.id_empleado = e.id_empleado
                    WHERE 
                    p.estado = :valor and p.propuesta = :propuesta and p.id_empleado = :id_empleado
                    ORDER BY 1 DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                   
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

    public function selectConEstado2(){
        try {  
            $stado = 'Proactividad';
            $parameters = array(
                ':valor' => $this->received_data->filterestado,  
                ':propuesta' => $stado,
                ':id_empleado' => $this->received_data->model->id_empleado

            );
            $query = "SELECT p.propuesta_id,p.id_creadopor,TO_CHAR(p.fecha_creado, 'DD-MM-YYYY HH12:MI:SS AM') as fecha_creado,p.id_empleado,p.departamento_id,p.texto,p.estado,p.propuesta, e.id_empleado, e.nombre,e.paterno,e.materno
            FROM refividrio.ev_propuesta as p
            INNER JOIN refividrio.empleado as e on p.id_empleado = e.id_empleado
                    WHERE 
                    p.estado = :valor and p.propuesta = :propuesta and p.id_empleado = :id_empleado
                    ORDER BY 1 DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                   
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

    public function insert(){
        try {
            $data = array(
                    ':id_creadopor' => $_SESSION['id_empleado'],
                        ':id_empleado' => $_SESSION['id_empleado'],
                       
                        ':texto' => $this->received_data->model->texto,
                        ':estado' => 'PA',
                        ':propuesta' => $this->received_data->model->propuesta,
                        
                    ); 
        $query = 'INSERT INTO ev_propuesta (id_creadopor,fecha_creado,id_empleado,departamento_id,texto,estado,propuesta) VALUES (:id_creadopor,now(),:id_empleado,(SELECT departamento_id FROM empleado where id_empleado=:id_empleado),:texto,:estado,:propuesta) ;';

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
                    ':propuesta_id' => $this->received_data->model->propuesta_id, 
                        // ':id_creadopor' => $this->received_data->model->id_creadopor, 
                        // ':fecha_creado' => $this->received_data->model->fecha_creado, 
                        // ':id_empleado' => $this->received_data->model->id_empleado, 
                        // ':departamento_id' => $this->received_data->model->departamento_id, 
                        // ':texto' => $this->received_data->model->texto, 
                        ':estado' => $this->received_data->model->estado, 
                        // ':propuesta' => $this->received_data->model->propuesta, 
                         
                    ); 
            $query = 'UPDATE ev_propuesta SET estado=:estado WHERE  propuesta_id = :propuesta_id ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Estado Actualizado'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    } 

    public function selectAll(){
        try {  
            
            $parameters = array(
                // ':valor' => $this->received_data->filter,  
                ':id_empleado' => $_SESSION['id_empleado'], 
            );
            $query = "SELECT propuesta_id,id_creadopor,TO_CHAR(p.fecha_creado, 'DD-MM-YYYY HH12:MI:SS AM')as fecha_creado,id_empleado,departamento_id,texto,estado,propuesta 
                    FROM ev_propuesta 
                    WHERE 
                        id_empleado = :id_empleado 
                    ORDER BY 1 DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_empleado');
                  
                    $row['departamento'] = $this->search_union($row,'departamento','departamento_id','departamento_id');
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

    public function select(){
        try {  
            
            $parameters = array(
                // ':valor' => $this->received_data->filter,  
                ':id_empleado' => $this->received_data->model->id_empleado, 
            );
            $query = "SELECT p.propuesta_id,p.id_creadopor,TO_CHAR(p.fecha_creado, 'DD-MM-YYYY HH12:MI:SS AM') as fecha_creado,p.id_empleado,p.departamento_id,p.texto,p.estado,p.propuesta, e.id_empleado, e.nombre,e.paterno,e.materno
            FROM refividrio.ev_propuesta as p
            INNER JOIN refividrio.empleado as e on p.id_empleado = e.id_empleado 
                    WHERE 
                        p.id_empleado = :id_empleado and p.estado= 'PA' and p.propuesta = 'Solución de Problemas' 
                    ORDER BY 1 DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_empleado');
                  
                    $row['departamento'] = $this->search_union($row,'departamento','departamento_id','departamento_id');
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

    public function selected(){
        try {  
            
            $parameters = array(
                // ':valor' => $this->received_data->filter,  
                ':id_empleado' => $this->received_data->model->id_empleado, 
            );
            $query = "SELECT p.propuesta_id,p.id_creadopor,TO_CHAR(p.fecha_creado, 'DD-MM-YYYY HH12:MI:SS AM') as fecha_creado,p.id_empleado,p.departamento_id,p.texto,p.estado,p.propuesta, e.id_empleado, e.nombre,e.paterno,e.materno
            FROM refividrio.ev_propuesta as p
            INNER JOIN refividrio.empleado as e on p.id_empleado = e.id_empleado
                    WHERE 
                        p.id_empleado = :id_empleado and p.estado= 'PA' and p.propuesta = 'Proactividad' 
                    ORDER BY 1 DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_empleado');
                  
                    $row['departamento'] = $this->search_union($row,'departamento','departamento_id','departamento_id');
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
                   ':propuesta_id' => $this->received_data->model->propuesta_id,
                            
                    ); 
        $query = 'DELETE FROM ev_propuesta WHERE propuesta_id = :propuesta_id ;'; 

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

    public function selectPropuesta(){
        try {  
            
            $query = "SELECT id_atributo, value, activo, descripcion, tabla
                    FROM ev_atributo 
                    WHERE 
                        tabla = 'propuesta' 
                    " ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['empleado'] = $this->search_union($row,'empleado','id_empleado','id_empleado');
                  
                    $row['departamento'] = $this->search_union($row,'departamento','departamento_id','departamento_id');
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

    

  
    

} 
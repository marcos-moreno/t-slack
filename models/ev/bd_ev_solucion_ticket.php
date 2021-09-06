<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_solucion_ticket($data,$connect,$received_data);
            $model->update();
        break;
        case 'select': 
            $model = new Ev_solucion_ticket($data,$connect,$received_data);
            $model->select();
        break;
        case 'selectEstado': 
            $model = new Ev_solucion_ticket($data,$connect,$received_data);
            $model->selectEstado();
        break;
        case 'selectEstados': 
            $model = new Ev_solucion_ticket($data,$connect,$received_data);
            $model->selectEstados();
        break;
        case 'selectFilter': 
            $model = new Ev_solucion_ticket($data,$connect,$received_data);
            $model->selectFilter();
        break;
        case 'uniones': 
            $model = new Ev_solucion_ticket($data,$connect,$received_data);
            $model->uniones();
        break;
        case 'selectLinea': 
            $model = new Ev_solucion_ticket($data,$connect,$received_data);
            $model->selectLinea();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_solucion_ticket 
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

    public function update(){
        try {
            $va = false;
            $data = array(
                    ':ev_ticket_id' => $this->received_data->model->ev_ticket_id, 
                        ':estado' => $this->received_data->model->estado, 
                        ':comentario_solucion' => $this->received_data->model->comentario_solucion,
                        ':id_empleado' => $_SESSION['id_empleado'], 
                         
                    ); 
            $query = 'UPDATE ev_ticket SET fechasolucion=now(),estado=:estado, status= false ,comentario_solucion=:comentario_solucion, solucionadopor= :id_empleado WHERE  ev_ticket_id = :ev_ticket_id ;';
   
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

    public function uniones(){
        try {
            
            $data = array(
                ':ev_ticket_id' => $this->received_data->model->ev_ticket_id, 
                
            ); 
           
            $query = 'UPDATE ev_ticket_ln SET estado=false WHERE  ev_ticket_id = :ev_ticket_id ;';

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
                ':id_empleado' => $_SESSION['id_empleado'], 
                ':estado' => 'AB',
            );
            $query = "SELECT distinct ti.ev_ticket_id,
            TO_CHAR(ti.fechacreacion, 'yyyy-MM-ddThh:mm') as fechacreacion,
            TO_CHAR(ti.fechasolucion, 'yyyy-MM-ddThh:mm') as fechasolucion,
            ti.estado,ti.ev_catalogo_ticket_id , d.departamento_id, ca.situacion,ti.comentario_solucion, 
			e.nombre, d.nombre
                    FROM refividrio.ev_ticket ti
                    INNER JOIN refividrio.departamento d on ti.departamento_id = d.departamento_id
                    INNEr join refividrio.ev_catalogo_ticket ca on ti.ev_catalogo_ticket_id = ca.ev_catalogo_ticket_id
					INNER JOIN refividrio.empleado e on e.departamento_id = d.departamento_id
                    WHERE 
                        ca.situacion  ILIKE '%' || :valor || '%'   and e.id_empleado = :id_empleado  and ti.estado =:estado
                    ORDER BY ti.ev_ticket_id DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_catalogo_ticket'] = $this->search_union($row,'ev_catalogo_ticket','ev_catalogo_ticket_id','ev_catalogo_ticket_id');
                    $row['problema'] = $this->search_union($row,'ev_ticket_ln','ev_ticket_id','ev_ticket_id');
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

    public function selectFilter(){
        try {  
            
            $parameters = array(
                ':valor' => $this->received_data->filterestado, 
                ':id_empleado' => $_SESSION['id_empleado'], 
            );
            $query = "SELECT distinct ti.ev_ticket_id,
            TO_CHAR(ti.fechacreacion, 'yyyy-MM-ddThh:mm') as fechacreacion,
            TO_CHAR(ti.fechasolucion, 'yyyy-MM-ddThh:mm') as fechasolucion,
            ti.estado,ti.ev_catalogo_ticket_id , d.departamento_id, ca.situacion,ti.comentario_solucion, 
			e.nombre, d.nombre
                    FROM refividrio.ev_ticket ti
                    INNER JOIN refividrio.departamento d on ti.departamento_id = d.departamento_id
                    INNEr join refividrio.ev_catalogo_ticket ca on ti.ev_catalogo_ticket_id = ca.ev_catalogo_ticket_id
					INNER JOIN refividrio.empleado e on e.departamento_id = d.departamento_id
                    WHERE 
                        ti.estado = :valor  and e.id_empleado = :id_empleado
                    ORDER BY ti.ev_ticket_id DESC" ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_catalogo_ticket'] = $this->search_union($row,'ev_catalogo_ticket','ev_catalogo_ticket_id','ev_catalogo_ticket_id');
                    $row['problema'] = $this->search_union($row,'ev_ticket_ln','ev_ticket_id','ev_ticket_id');
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

   
    public function selectEstado(){
        try { 

            $query = 'select id_atributo, value, activo, descripcion, tabla 
                from refividrio.ev_atributo where id_atributo BETWEEN 46  AND 49;
            ' ;         
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

    public function selectLinea(){
        try {  
            $status = true;
            $parameters = array(
                ':estado' => $this->received_data->model->estado,
                'ev_ticket_id' => $this->received_data->model->ev_ticket_id,
                // 'statuss' => $status,
            );
            $query = "SELECT  ti.ev_ticket_id,
            TO_CHAR(ti.fechacreacion, 'yyyy-MM-ddThh:mm') as fechacreacion,
            TO_CHAR(ti.fechasolucion, 'yyyy-MM-ddThh:mm') as fechasolucion,
            ti.estado,ti.ev_catalogo_ticket_id , d.departamento_id, ca.situacion,ti.comentario_solucion, 
			d.nombre, ln.id_empleado, e.nombre, e.paterno, e.materno, ln.comentario
                    FROM refividrio.ev_ticket ti
					INNER JOIN refividrio.ev_ticket_ln ln on ti.ev_ticket_id = ln.ev_ticket_id
                    INNER JOIN refividrio.departamento d on ti.departamento_id = d.departamento_id
                    INNEr join refividrio.ev_catalogo_ticket ca on ti.ev_catalogo_ticket_id = ca.ev_catalogo_ticket_id
					INNER JOIN refividrio.empleado e on e.id_empleado = ln.id_empleado
                    WHERE 
                       ti.estado =:estado and ti.ev_ticket_id = :ev_ticket_id 
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

    public function selectEstados(){
        try { 

            $query = 'select id_atributo, value, activo, descripcion, tabla 
                from refividrio.ev_atributo where id_atributo BETWEEN 48  AND 49;
            ' ;         
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

   
  
    

} 
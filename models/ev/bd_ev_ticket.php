<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->select();
        break;
        case 'selectCatalogo': 
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->selectCatalogo();
        break;
        case 'selectEstado': 
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->selectEstado();
        break;
        case 'selectConEstado': 
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->selectConEstado();
        break;
        case 'valida': 
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->valida();
        break;
        case 'verificaExistencia': 
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->verificaExistencia();
        break;
        case 'updateV': 
            $model = new Ev_ticket($data,$connect,$received_data);
            $model->updateV();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_ticket 
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
            $va = true;
            $data = array(
                    ':estado' => 'AB',
                    ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id,
                    ':solucionadopor' => null,
                    ':departamento_id' => $this->received_data->model->departamento_id,
                    ':status'  => $va,
                    ':id_empleado' => $_SESSION['id_empleado'],
                    ); 

        $query = 'INSERT INTO ev_ticket (fechacreacion,fechasolucion,estado,ev_catalogo_ticket_id, solucionadopor, departamento_id, status, creadopor) VALUES
         (now(),null,:estado,:ev_catalogo_ticket_id, :solucionadopor, :departamento_id, :status, :id_empleado) ;';

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
            $verdad = false;
            $data = array(
                    ':ev_ticket_id' => $this->received_data->model->ev_ticket_id, 
                        ':fechacreacion' => $this->received_data->model->fechacreacion, 
                        ':fechasolucion' => $this->received_data->model->fechasolucion, 
                        ':estado' => $this->received_data->model->estado, 
                        ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id, 
                         
                    ); 
            $query = 'UPDATE ev_ticket SET fechacreacion=:fechacreacion,fechasolucion=:fechasolucion,estado=:estado,ev_catalogo_ticket_id=:ev_catalogo_ticket_id, status= false WHERE  ev_ticket_id = :ev_ticket_id ;';

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

    public function updateV(){
        try {
            $data = array(
                    ':ev_ticket_id' => $this->received_data->model->ev_ticket_id, 
                        ':fechacreacion' => $this->received_data->model->fechacreacion, 
                        ':fechasolucion' => $this->received_data->model->fechasolucion, 
                        ':estado' => $this->received_data->model->estado, 
                        ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id, 
                         
                    ); 
            $query = 'UPDATE ev_ticket SET fechacreacion=:fechacreacion,fechasolucion=:fechasolucion,estado=:estado,ev_catalogo_ticket_id=:ev_catalogo_ticket_id, status= true WHERE  ev_ticket_id = :ev_ticket_id ;';

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

    public function delete(){
        try {  
            $data = array(
                   ':ev_ticket_id' => $this->received_data->model->ev_ticket_id,
                            
                    ); 
        $query = 'DELETE FROM ev_ticket WHERE ev_ticket_id = :ev_ticket_id ;'; 

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

    public function select(){
        try {  
            $defec = 'AB';
            $parameters = array(
                ':valor' => $this->received_data->filter, 
                ':id_empleado' => $_SESSION['id_empleado'], 
                ':defec' => 'AB',
            );
            $query = "SELECT ti.ev_ticket_id,
            TO_CHAR(ti.fechacreacion, 'yyyy-MM-ddThh:mm') as fechacreacion,
            TO_CHAR(ti.fechasolucion, 'yyyy-MM-ddThh:mm') as fechasolucion,
            ti.estado,ti.ev_catalogo_ticket_id , ln.comentario, d.nombre, ca.situacion, ti.comentario_solucion
                    FROM ev_ticket ti
                    INNER JOIN ev_ticket_ln ln on ti.ev_ticket_id = ln.ev_ticket_id
                    INNER JOIN departamento d on ti.departamento_id = d.departamento_id
                    INNEr join ev_catalogo_ticket ca on ti.ev_catalogo_ticket_id = ca.ev_catalogo_ticket_id
                    WHERE 
                        ca.situacion  ILIKE '%' || :valor || '%'  and ln.id_empleado = :id_empleado and ti.estado = :defec
                    " ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_catalogo_ticket'] = $this->search_union($row,'ev_catalogo_ticket','ev_catalogo_ticket_id','ev_catalogo_ticket_id');
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
            
            $parameters = array(
                ':valor' => $this->received_data->filterestado, 
                ':id_empleado' => $_SESSION['id_empleado'], 
            );
            $query = "SELECT ti.ev_ticket_id,
            TO_CHAR(ti.fechacreacion, 'yyyy-MM-ddThh:mm') as fechacreacion,
            TO_CHAR(ti.fechasolucion, 'yyyy-MM-ddThh:mm') as fechasolucion,
            ti.estado,ti.ev_catalogo_ticket_id , ln.comentario, d.nombre, ca.situacion, ti.comentario_solucion
                    FROM ev_ticket ti
                    INNER JOIN ev_ticket_ln ln on ti.ev_ticket_id = ln.ev_ticket_id
                    INNER JOIN departamento d on ti.departamento_id = d.departamento_id
                    INNEr join ev_catalogo_ticket ca on ti.ev_catalogo_ticket_id = ca.ev_catalogo_ticket_id
                    WHERE 
                         ti.estado = :valor  and ln.id_empleado = :id_empleado 
                    " ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_catalogo_ticket'] = $this->search_union($row,'ev_catalogo_ticket','ev_catalogo_ticket_id','ev_catalogo_ticket_id');
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

    public function selectCatalogo(){
        try {
            $parameters = array(
                ':departamento_id' => $this->received_data->model->departamento_id, 
                );   
            $query = '
            SELECT cat.ev_catalogo_ticket_id, cat.situacion, cat.activo, cat.creadopor, cat.modificadopor, cat.creado, cat.actualizado, cat.departamento_id,  d.nombre
            FROM refividrio.ev_catalogo_ticket CAT
            INNER JOIN refividrio.departamento d on CAT.departamento_id = d.departamento_id
                   WHERE d.departamento_id = :departamento_id  ;      
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

    public function selectEstado(){
        try { 
            $valor = 'usuario';
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
   
    public function valida(){
        try { 
            $parameters = array(
                ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id,
                // ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id,
            );  

            $query = "
            SELECT ev_ticket_id 
            from refividrio.ev_ticket 
            where ev_catalogo_ticket_id = :ev_catalogo_ticket_id  and status = true group by ev_ticket_id limit 1 
                     
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

    public function verificaExistencia(){
        try { 
            $parameters = array(
                ':ev_ticket_id' => $this->received_data->model->ev_ticket_id,
                // ':ev_catalogo_ticket_id' => $this->received_data->model->ev_catalogo_ticket_id,
                'creadopor' => $_SESSION['id_empleado'],
            );  

            $query = "
            SELECT creadopor 
            from refividrio.ev_ticket 
            where ev_ticket_id = :ev_ticket_id and creadopor = :creadopor group by ev_ticket_id limit 1 
                     
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

   
    

} 
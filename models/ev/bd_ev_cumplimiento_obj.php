<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_cumplimiento_obj($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_cumplimiento_obj($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_cumplimiento_obj($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'selectDep':
            $model = new Ev_cumplimiento_obj($data,$connect,$received_data);
            $model->selectDep();
        break;
        case 'selectCombo':
            $model = new Ev_cumplimiento_obj($data,$connect,$received_data);
            $model->selectCombo();
        break;
        case 'selectTabla':
            $model = new Ev_cumplimiento_obj($data,$connect,$received_data);
            $model->selectTabla();
        break;
        case 'selectEstado':
            $model = new Ev_cumplimiento_obj($data,$connect,$received_data);
            $model->selectEstado();
        break;
        case 'selectEstado2':
            $model = new Ev_cumplimiento_obj($data,$connect,$received_data);
            $model->selectEstado2();
        break;

    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_cumplimiento_obj 
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

    public function selectDep(){
        try {  
             
        $query = 'SELECT ev_indicador_general_id, nombre
                    FROM ev_indicador_general  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
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

    public function selectCombo(){
        try {
            $parameters = array(
                    ':id_empleado' => $this->received_data->model->id_empleado,
                );   
            $query = '
                    select  ig.nombre,ig.ev_indicador_general_id
            from refividrio.ev_indicador_general as ig 
            inner join refividrio.ev_indicador_puesto as ip on ip.ev_indicador_general_id = ig.ev_indicador_general_id
            inner join refividrio.empleado as em on em.ev_puesto_id = ip.ev_puesto_id
            where em.id_empleado = :id_empleado and ig.ev_indicador_general_id IN (24,11);      
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
            $valor = 'CUMPLIMIENTO DE OBJETIVOS';
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

    public function selectEstado2(){
        try { 
            $valor = 'PODER DE NEGOCIACIÓN';
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

    public function selectTabla(){
        try {
            
            $query = "
            SELECT em.id_empleado, co.ev_cumplimiento_obj_id, co.id_indicador, ig.nombre, ig.ev_indicador_general_id,
             concat( em.nombre, em.paterno, em.materno) as fullname,
            TO_CHAR(fechainicio, 'yyyy-MM-ddThh:mm') as fechainicio, 
            TO_CHAR(fechatermino, 'yyyy-MM-ddThh:mm') as fechatermino, 
             co.estado, co.nombre_objetivo, co.descripcion
            FROM refividrio.ev_cumplimiento_obj as co
            Inner join refividrio.ev_indicador_general as ig on ig.ev_indicador_general_id = co.id_indicador
            inner join refividrio.empleado as em on em.id_empleado = co.id_empleado
            ;      
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

    public function insert(){
        try {
            $data = array(
                ':id_indicador' => $this->received_data->model->id_indicador,
                ':id_empleado' => $this->received_data->model->id_empleado,
                ':fechainicio' => $this->received_data->model->fechainicio,
                'fechatermino' => $this->received_data->model->fechatermino,
                ':estado' => $this->received_data->model->estado,
                ':creadopor' => $_SESSION['id_empleado'],
                ':modificadopor' => $_SESSION['id_empleado'],
                ':nombre_objetivo' => $this->received_data->model->nombre_objetivo,
                ':descripcion' => $this->received_data->model->descripcion,
            ); 

           
        $query = 'INSERT INTO ev_cumplimiento_obj (id_indicador, id_empleado,fechainicio, fechatermino, estado, creadopor, modificadopor, nombre_objetivo, descripcion)
         VALUES (:id_indicador, :id_empleado, :fechainicio, :fechatermino, :estado, :creadopor, :modificadopor, :nombre_objetivo, :descripcion) ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Inserted', "Data"=> $this->received_data->model); 
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
                ':ev_cumplimiento_obj_id' => $this->received_data->model->ev_cumplimiento_obj_id,
                'fechainicio' => $this->received_data->model->fechainicio,
                'fechatermino' => $this->received_data->model->fechatermino,
                'estado' => $this->received_data->model->estado,
                ':modificadopor' => $_SESSION['id_empleado'],
                ':nombre_objetivo' => $this->received_data->model->nombre_objetivo,
                ':descripcion' => $this->received_data->model->descripcion,
            ); 
        $query = 'UPDATE ev_cumplimiento_obj SET fechainicio=:fechainicio, fechatermino=:fechatermino, 
        estado=:estado,
        modificadopor=:modificadopor,
        nombre_objetivo=:nombre_objetivo,descripcion=:descripcion 
                WHERE  ev_cumplimiento_obj_id = :ev_cumplimiento_obj_id ;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $output = array('message' => 'Data Updated'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' =>$exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    } 

                    
    public function delete(){
        try {  
            $data = array(
                   ':ev_cumplimiento_obj_id' => $this->received_data->model->ev_cumplimiento_obj_id,
                            
                    ); 
        $query = 'DELETE FROM ev_cumplimiento_obj WHERE ev_cumplimiento_obj_id = :ev_cumplimiento_obj_id ;'; 

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


    public function getByLider(){
        try {  
            $parameters = array( 
                    ':id_empleado' => $this->received_data->model->i_empleado, 
                    ':id_lider' => $_SESSION['id_empleado'],
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

  
    

} 
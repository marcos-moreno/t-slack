<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_reporte($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_reporte($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_reporte($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_reporte($data,$connect,$received_data);
            $model->select();
        break;
        case 'selectEvaluar': 
            $model = new Ev_reporte($data,$connect,$received_data);
            $model->selectEvaluar();
        break;
        case 'selectEvaluarUp': 
            $model = new Ev_reporte($data,$connect,$received_data);
            $model->selectEvaluarUp();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_reporte 
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


    public function selectEvaluarUp(){
        try {   
           
        $parameters = array(
            ':ev_indicador_puesto_id' => $this->received_data->model->ev_indicador_puesto_id,
            ':ev_reporte_id' => $this->received_data->model->ev_reporte_id,
        );
        $query = 'SELECT 
        ig.ev_indicador_general_id, ig.nombre, pu.ev_indicador_puesto_id, eva.nombre as catalogo, eva.ev_punto_evaluar_id
        FROM refividrio.ev_indicador_puesto pu
        inner join refividrio.ev_indicador_general ig on ig.ev_indicador_general_id = pu.ev_indicador_general_id
        inner join refividrio.ev_punto_evaluar eva on eva.ev_indicador_general_id = ig.ev_indicador_general_id
        INNER JOIN refividrio.ev_reporte re on re.ev_punto_evaluar = eva.ev_punto_evaluar_id
        WHERE pu.ev_indicador_puesto_id = :ev_indicador_puesto_id and re.ev_reporte_id = :ev_reporte_id' ;
                    
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

    public function selectEvaluar(){
        try {   
           
        $parameters = array(
            'ev_indicador_puesto_id' => $this->received_data->model->ev_indicador_puesto_id,
        );
        $query = 'SELECT 
        ig.ev_indicador_general_id, ig.nombre, pu.ev_indicador_puesto_id, eva.nombre as catalogo, eva.ev_punto_evaluar_id
    FROM refividrio.ev_indicador_puesto pu
    inner join refividrio.ev_indicador_general ig on ig.ev_indicador_general_id = pu.ev_indicador_general_id
    inner join refividrio.ev_punto_evaluar eva on eva.ev_indicador_general_id = ig.ev_indicador_general_id
    WHERE pu.ev_indicador_puesto_id = :ev_indicador_puesto_id ' ;
                    
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
            $parameters = array(
                        ':descripcion' => $this->received_data->model->descripcion,
                        ':fecha' => $this->received_data->model->fecha,
                        ':id_empleado' => $this->received_data->model->id_empleado,
                        ':ev_indicador_puesto_id' => $this->received_data->model->ev_indicador_puesto_id,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                    ); 
            $query = 'INSERT INTO ev_reporte (descripcion,fecha,id_empleado,ev_indicador_puesto_id,creado,creadopor,actualizado,actualizadopor) 
                    VALUES (:descripcion,:fecha,:id_empleado,:ev_indicador_puesto_id,Now(),:creadopor,Now(),:actualizadopor)    RETURNING ev_reporte_id;';

            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);  
            $result = $statement->fetchAll();
            $ev_reporte_id = 0;
            foreach ($result as $row) {
                $ev_reporte_id = $row['ev_reporte_id'];
            } 
            $output = array('message' => 'Data Inserted','ev_reporte_id' => $ev_reporte_id); 
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
                    ':ev_reporte_id' => $this->received_data->model->ev_reporte_id, 
                        ':descripcion' => $this->received_data->model->descripcion, 
                        ':fecha' => $this->received_data->model->fecha, 
                        ':id_empleado' => $this->received_data->model->id_empleado, 
                        ':ev_indicador_puesto_id' => $this->received_data->model->ev_indicador_puesto_id, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':ev_punto_evaluar' => $this->received_data->model->ev_punto_evaluar, 

                    ); 
            $query = 'UPDATE ev_reporte SET descripcion=:descripcion,fecha=:fecha
                        ,id_empleado=:id_empleado,ev_indicador_puesto_id=:ev_indicador_puesto_id
                        ,actualizado=Now(),actualizadopor=:actualizadopor , ev_punto_evaluar = :ev_punto_evaluar
                        WHERE  ev_reporte_id = :ev_reporte_id ;';

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
            $data = array(
                ':id_empleado' => $this->received_data->model->id_empleado,
                ':creadopor' => $_SESSION['id_empleado'],
                ); 
            $query = "
                SELECT 
                    rep.ev_reporte_id,rep.descripcion,TO_CHAR(rep.fecha, 'YYYY-MM-DD') as fecha
                    ,rep.id_empleado,rep.ev_indicador_puesto_id,
                    rep.creado,rep.creadopor,rep.actualizado,rep.actualizadopor,ig.nombre As nombre_indicador, rep.ev_punto_evaluar
                FROM ev_reporte rep
                INNER JOIN ev_indicador_puesto ip ON ip.ev_indicador_puesto_id=rep.ev_indicador_puesto_id
                INNER JOIN ev_indicador_general ig ON ip.ev_indicador_general_id=ig.ev_indicador_general_id
                WHERE rep.creadopor = :creadopor AND rep.id_empleado = :id_empleado
                ORDER BY rep.fecha DESC;
                ";
            $dataResult=[];       
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
                    $empleado = $this->search_union($row,'empleado','id_empleado','id_empleado');     
                    $empleado[0]['password'] = '';
                    $row['empleado'] = $empleado ; 
                    $row['ev_indicador_puesto'] = $this->search_union($row,'ev_indicador_puesto','ev_indicador_id','ev_indicador_puesto_id');
                    $dataResult[] = $row;
            }
            echo json_encode($dataResult); 
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
                   ':ev_reporte_id' => $this->received_data->model->ev_reporte_id,
                            
                    ); 
        $query = 'DELETE FROM ev_reporte WHERE ev_reporte_id = :ev_reporte_id ;'; 

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
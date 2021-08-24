<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_indicador_puesto($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_indicador_puesto($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_indicador_puesto($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_indicador_puesto($data,$connect,$received_data);
            $model->select();
        break;
        case 'selectByEmployee': 
            $model = new Ev_indicador_puesto($data,$connect,$received_data);
            $model->selectByEmployee();
        break;
        case 'search_employe_indicadores': 
            $model = new Ev_indicador_puesto($data,$connect,$received_data);
            $model->search_employe_indicadores();
        break;
        
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_indicador_puesto 
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
        $parameters = array();
        try {
            $parameters = array(
                        ':ev_puesto_id' => $this->received_data->model->ev_puesto_id, 
                        ':porcentaje' => $this->received_data->model->porcentaje, 
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'], 
                        ':ev_indicador_general_id' => $this->received_data->model->ev_indicador_general_id,
                    ); 
            $query = 'INSERT INTO ev_indicador_puesto (ev_puesto_id,porcentaje,creado,creadopor
            ,actualizado,actualizadopor,ev_indicador_general_id) 
            VALUES (:ev_puesto_id,:porcentaje,Now(),:creadopor,Now(),:actualizadopor
            ,:ev_indicador_general_id) ;';
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);
            $output = array('message' => 'Data Inserted'); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage(),'messages' => $parameters); 
            echo json_encode($output); 
            return false;
        } 
    } 

    public function update(){
        try {
            $data = array(
                        ':ev_indicador_puesto_id' => $this->received_data->model->ev_indicador_puesto_id, 
                        ':ev_puesto_id' => $this->received_data->model->ev_puesto_id,  
                        ':porcentaje' => $this->received_data->model->porcentaje,  
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':ev_indicador_general_id' => $this->received_data->model->ev_indicador_general_id,
                    ); 
            $query = 'UPDATE ev_indicador_puesto SET ev_puesto_id=:ev_puesto_id,
            porcentaje=:porcentaje,actualizado=Now(),actualizadopor=:actualizadopor ,ev_indicador_general_id=:ev_indicador_general_id
            WHERE  ev_indicador_puesto_id = :ev_indicador_puesto_id ;';

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
                    ':filter' => $this->received_data->filter,
                ); 
            $query = '
                    SELECT ev_indicador_puesto_id, ev_puesto_id, porcentaje, creado,
                         creadopor, actualizado, actualizadopor, ev_indicador_general_id
                    FROM ev_indicador_puesto   
                    WHERE ev_puesto_id = :filter
                    ORDER BY porcentaje DESC
                    '; 
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            $result = false;
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_puesto'] = $this->search_union($row,'ev_puesto','ev_puesto_id','ev_puesto_id');
                    $temp = $this->search_union($row,'ev_indicador_general','ev_indicador_general_id','ev_indicador_general_id');
                    $temp['tipo_captura'] = $this->search_union($temp[0],'ev_atributo','id_atributo','tipo_captura_atributo');
                    $row['ev_indicador_general'] = $temp;
                    $data[] = $row;
                    $result = true;
            }
            if ($result) {
                echo json_encode($data); 
            } else {
                echo json_encode(array()); 
            }
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    }

    public function search_employe_indicadores(){
        try {   
            $parameters = array( 
                    ':id_empleado' => $this->received_data->id_empleado,
                    ':ev_evaluacion_ln_id' => $this->received_data->ev_evaluacion_ln_id,
                ); 
            $query = '
                SELECT 
                        ip.ev_indicador_puesto_id, ip.ev_puesto_id, ip.porcentaje, ip.creado,
                        ip.creadopor,ip.actualizado,ip.actualizadopor, ip.ev_indicador_general_id
                        ,COALESCE(ie.calificacion_indicador,0) As calificacion_indicador
                FROM ev_indicador_puesto   ip
                INNER JOIN empleado e ON e.id_empleado = :id_empleado
                     AND ip.ev_puesto_id = e.ev_puesto_id
                LEFT JOIN ev_indicador_evaluado  ie ON ie.id_empleado = e.id_empleado 
                    AND ip.ev_indicador_general_id = ie.ev_indicador_general_id
                    AND ie.ev_evaluacion_ln_id = :ev_evaluacion_ln_id
                ORDER BY porcentaje DESC 
                    '; 
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);
            $result = false;
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_puesto'] = $this->search_union($row,'ev_puesto','ev_puesto_id','ev_puesto_id');
                    $temp = $this->search_union($row,'ev_indicador_general','ev_indicador_general_id','ev_indicador_general_id');
                    $temp['tipo_captura'] = $this->search_union($temp[0],'ev_atributo','id_atributo','tipo_captura_atributo');
                    $row['ev_indicador_general'] = $temp;
                    $temp2 = $this->get_Puntos_evaluar($row);
                    for ($i=0; $i < count($temp2); $i++) {
                        $temp2[$i]['ev_puntos_evaluar_ln'] = $this->get_ev_puntos_evaluar_ln(
                            $temp2[$i],$row,$this->received_data->id_empleado,$this->received_data->ev_evaluacion_id
                        );
                    }
                    $row['ev_puntos_evaluar'] = $temp2;
                    $data[] = $row;
                    $result = true;
            }
            if ($result) {
                echo json_encode($data); 
            } else {
                echo json_encode(array()); 
            }
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    }

    public function selectByEmployee(){
        try {   
            $data = array(
                ':id_empleado' => $this->received_data->model->id_empleado, 
                ); 
            $query =   'SELECT 
                            * 
                        FROM empleado e  
                        INNER JOIN ev_puesto pn ON pn.ev_puesto_id = e.ev_puesto_id
                        INNER JOIN ev_indicador_puesto ip ON ip.ev_puesto_id = e.ev_puesto_id
                        INNER JOIN ev_indicador_general ig ON ig.ev_indicador_general_id = ip.ev_indicador_general_id 
                        AND ig.allowrepor = true
                        WHERE e.id_empleado = :id_empleado ' ;
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            $result = false;
            $dataResult = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_puesto_nivel'] = $this->search_union($row,'ev_puesto_nivel','ev_puesto_nivel_id','ev_puesto_nivel_id');
                    $row['ev_indicador_general'] = $this->search_union($row,'ev_indicador_general','ev_indicador_general_id','ev_indicador_general_id');
                    $dataResult[] = $row;
                    $result = true;
            }
            if ($result) {
                echo json_encode($dataResult); 
            } else {
                echo json_encode(array()); 
            }
            return true;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    }

    public function get_ev_puntos_evaluar_ln($row,$row_general,$id_empleado,$ev_evaluacion_id){
        $data = array(); 
        try {
            $parameters = array(
                ':id_lider' => $_SESSION['id_empleado'],
                ':ev_punto_evaluar_id' => $row['ev_punto_evaluar_id'],
                ':id_empleado' => $id_empleado,
                ':ev_punto_evaluar_id' => $row['ev_punto_evaluar_id'],
                ':ev_evaluacion_id' => $ev_evaluacion_id
            );
            $query = '
                SELECT
                    pt.*,
                    COALESCE(cal_pt.is_checked,false) As is_checked,
                    e.id_empleado,
                    eval.ev_evaluacion_id
                FROM ev_punto_evaluar_ln pt
                INNER JOIN empleado e ON e.id_empleado = :id_empleado
                INNER JOIN ev_evaluacion eval ON eval.ev_evaluacion_id = :ev_evaluacion_id
                LEFT JOIN ev_calif_p_evaluar_ln cal_pt
                    ON  cal_pt.ev_punto_evaluar_ln_id = pt.ev_punto_evaluar_ln_id
                    AND cal_pt.id_lider = :id_lider
                    AND cal_pt.id_empleado = e.id_empleado
                    AND cal_pt.ev_evaluacion_id = eval.ev_evaluacion_id
                WHERE
                    pt.ev_punto_evaluar_id = :ev_punto_evaluar_id';
            $statement = $this->connect->prepare($query);
            $statement->execute($parameters);
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            return [];
        }
    }
    
    public function get_Puntos_evaluar($row){
        $data = array(); 
        try {
            $query = 'SELECT 
                        pe.*,tc.nombre As tipo_evaluacion,es_capturado,direct_data,opcion_multiple
                        FROM ev_punto_evaluar pe
                        INNER JOIN refividrio.ev_tipo_captura tc ON tc.ev_tipo_captura_id = pe.ev_tipo_captura_id
                        WHERE ev_indicador_general_id = ' .$row['ev_indicador_general_id'] ;               
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
                    $data[] = $row;
            }  
            return $data; 
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            return [];  
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
            return [];  
        }  
    }
    public function delete(){
        try {  
            $data = array(
                        ':ev_indicador_puesto_id' => $this->received_data->model->ev_indicador_puesto_id,
                    );
            $query = 'DELETE FROM ev_indicador_puesto WHERE ev_indicador_puesto_id = :ev_indicador_puesto_id ;'; 
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
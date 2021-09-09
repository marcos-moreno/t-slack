<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'save_dat_point': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->save_dat_point();
        break;
        case 'select': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->select();
        break;
        case 'evaluar_con_reportes': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->evaluar_con_reportes();
        break;
        case 'procesar_evaluacion': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->procesar_evaluacion();
        break; 
        case 'select_evaluacion_lider': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->select_evaluacion_lider();
        break; 
        case 'select_puntos_evaluar': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->select_puntos_evaluar();
        break;
        case 'save_dat_evaluac_por_user': 
            $model = new Ev_evaluacion($data,$connect,$received_data);
            $model->save_dat_evaluac_por_user();
        break; 
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_evaluacion 
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
                        ':id_lider' => $this->received_data->model->id_lider,
                        ':periodo_id' => $this->received_data->model->periodo_id,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':nombre' => $this->received_data->model->nombre,
                    );
            $query = 'INSERT INTO ev_evaluacion (id_lider,periodo_id,creado,actualizado,creadopor,actualizadopor,nombre)
                        VALUES (:id_lider,:periodo_id,Now(),Now(),:creadopor,:actualizadopor,:nombre) ;';
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
                        ':ev_evaluacion_id' => $this->received_data->model->ev_evaluacion_id, 
                        ':id_lider' => $this->received_data->model->id_lider, 
                        ':periodo_id' => $this->received_data->model->periodo_id, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                        ':nombre' => $this->received_data->model->nombre, 
                    ); 
            $query = 'UPDATE ev_evaluacion SET id_lider=:id_lider,periodo_id=:periodo_id,actualizado=Now()
                        ,actualizadopor=:actualizadopor,nombre=:nombre 
                        WHERE  ev_evaluacion_id = :ev_evaluacion_id ;';

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
    public function procesar_evaluacion(){
        $parameters = array(
            ':ev_evaluacion_id' => $this->received_data->ev_evaluacion_id,  
            ':ev_evaluacion_ln_id' => $this->received_data->ev_evaluacion_ln_id,  
            ':id_empleado' => $this->received_data->id_empleado,    
            ':id_user' => $_SESSION['id_empleado'],  
            ':no_faltas' => $this->received_data->no_faltas,  
            ':no_retardos' => $this->received_data->no_retardos,  
        );
        try {
            $query = "
                    SELECT refividrio.procesar_evaluacion(
                        :ev_evaluacion_id,:ev_evaluacion_ln_id,:id_empleado,:id_user,:no_faltas,:no_retardos
                    )" ; 
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $data = $row;
            } 
            $output = array('status' => 'success','data' => $data); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('status' => 'error','data' => $exc->getMessage(),'paramas' => $parameters ); 
            echo json_encode($output); 
            return false;
        }  
    }
    public function save_dat_point(){
        try {  
            $parameters = array(
                ':points' => $this->received_data->points,  
                ':id_lider' => $_SESSION['id_empleado'],
            );
            $query = "SELECT refividrio.save_dat_point(:points,:id_lider)";
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
    public function select(){
        try {  
            $parameters = array(
                ':valor' => $this->received_data->filter,  
                ':id_lider' => $_SESSION['id_empleado'],
            );
            $query = "SELECT 
                        ev_evaluacion_id,id_lider,periodo_id,creado,actualizado
                        ,creadopor,actualizadopor,nombre 
                    FROM ev_evaluacion 
                    WHERE 
                        id_lider = :id_lider
                        AND nombre  ILIKE '%' || :valor || '%' 
                    ORDER BY ev_evaluacion_id DESC" ;
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
                    $empleado = $this->search_union($row,'empleado','id_empleado','id_lider');     
                    $empleado[0]['password'] = '';
                    $row['empleado'] = $empleado ; 
                    $row['periodo'] = $this->search_union($row,'periodo','periodo_id','periodo_id');
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
    public function save_dat_evaluac_por_user(){
        $parameters = array();
        try {
            $data = [];
            $parameters = array(
                ':respuestas_collection' => $this->received_data->respuestas_collection,
                ':v_id_lider' => $this->received_data->id_lider,
                ':v_id_user' => $_SESSION['id_empleado'],
            );
            $query = "
                SELECT refividrio.save_dat_evaluac_por_user(
                    :respuestas_collection, 
                    :v_id_lider, 
                    :v_id_user
                )
            ";
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            $output = array('status' => 'success','data' => $data);
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('status' => 'error','message' => $exc->getMessage(),'params' => $parameters); 
            echo json_encode($output); 
            return false;
        }  
    }
    public function select_evaluacion_lider(){
        try {
            $data = [];
            $parameters = array(
                ':id_empleado' => $_SESSION['id_empleado'],
            );
            $query = "
                SELECT
                        nm1.id_empleado,
                -- 		d.nombre As departamento,
                -- 		ld.tipo_lider,
                        lid.id_empleado As id_lider,
                        CONCAT(lid.paterno,' ',lid.materno,' ',lid.nombre) As nombre_lider,
                        ev_evaluacion_por_user_id
                        
                FROM
                (
                    SELECT
                        e.departamento_id,
                        e.id_empleado,
                        CASE WHEN j.id_superior IS NOT NULL 
                            THEN j.id_superior 
                            ELSE jd.id_empleado 
                        END As id_superior,
                        epu.ev_evaluacion_por_user_id
                    FROM
                    empleado e
                    LEFT JOIN jerarquizacion j ON j.id_empleado = e.id_empleado
                    LEFT JOIN jerarquizacion jd ON jd.departamento_id = e.departamento_id AND j.id_superior IS NULL
                    LEFT JOIN ev_evaluacion_por_user epu 
                        ON epu.id_usuario = e.id_empleado 
                    --epu.id_lider = j.id_superior
                    --    AND epu.id_usuario = e.id_empleado
                    -- 	AND id_indicador_general  
                    --    AND epu.mes = date_part('month',NOW())
                    --    AND epu.ejercicio = date_part('year',NOW())
                )As nm1
                INNER JOIN empleado lid ON lid.id_empleado = nm1.id_superior
                WHERE nm1.id_empleado = :id_empleado
                AND ev_evaluacion_por_user_id IS NULL
                ORDER BY id_empleado,nm1.departamento_id
            ";
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            $output = array('status' => 'success','data' => $data);
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('status' => 'error','message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    }
    public function select_puntos_evaluar(){
        try {
            $data = [];
            // id_lider id_indicador
            $parameters = array(
                ':ev_indicador_general_id' => $this->received_data->id_indicador,
            );
            $query = "
                SELECT 
                    pe.ev_punto_evaluar_id, pe.ev_indicador_general_id, pe.ev_tipo_captura_id, pe.nombre, pe.descripcion, pe.porcentaje_tl,
                    pe.creado, pe.creadopor, pe.actualizado, pe.actualizadopor, pe.min_escala, pe.max_escala, pe.incremento
                    ,tc.nombre As tipo_captura,tc.es_capturado,tc.direct_data,tc.opcion_multiple,tc.dato,tc.es_evaluado,
                    --CASE WHEN tc.nombre = 'RADIO' THEN '' ELSE '' END
                    '' AS respuesta
                FROM refividrio.ev_punto_evaluar pe
                    INNER JOIN ev_tipo_captura tc ON tc.ev_tipo_captura_id = pe.ev_tipo_captura_id
                WHERE pe.ev_indicador_general_id = :ev_indicador_general_id
                ORDER BY pe.ev_punto_evaluar_id DESC
                ;
            ";
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $row['ev_punto_evaluar_ln'] = $this->search_union($row,'ev_punto_evaluar_ln','ev_punto_evaluar_id','ev_punto_evaluar_id');
                $data[] = $row;
            }
            $output = array('status' => 'success','data' => $data);
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('status' => 'error','message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    }
      // public function ev_punto_evaluar_ln($row){
    //     $data = array(); 
    //     try {    
    //         $query = "
    //         SELECT 
    //         ln.*
    //         ,CASE WHEN tc.nombre = 'RADIO' THEN 'false' ELSE '' END AS is_checked
    //         FROM ev_punto_evaluar_ln  ln
    //         INNER JOIN ev_punto_evaluar pe ON pe.ev_punto_evaluar_id = ln.ev_punto_evaluar_id
    //         INNER JOIN ev_tipo_captura tc ON tc.ev_tipo_captura_id = pe.ev_tipo_captura_id
    //         WHERE 
    //         ln.ev_punto_evaluar_id = " . $row['ev_punto_evaluar_id'] ;               
    //         $statement = $this->connect->prepare($query); 
    //         $statement->execute($data);   
    //         while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
    //                 $data[] = $row;
    //         }  
    //         return $data; 
    //     } catch (PDOException $exc) {
    //         $output = array('message' => $exc->getMessage()); 
    //         return json_encode($output);  
    //     }  
    // }
    public function evaluar_con_reportes(){
        $parameters = array(
            ':id_empleado' => $this->received_data->id_empleado,  
            ':ev_indicador_general_id' => $this->received_data->ev_indicador_general_id,  
            ':ev_evaluacion_id' => $this->received_data->ev_evaluacion_id,  
            ':id_user' => $_SESSION['id_empleado'],  
        );
        try {  
            $query = "
                    SELECT refividrio.evaluar_con_reportes(
                        :id_empleado,:ev_indicador_general_id,:ev_evaluacion_id,:id_user
                    )" ; 
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $data = $row;
            } 
            $output = array('status' => 'success','data' => $data); 
            echo json_encode($output); 
            return true;
        } catch (PDOException $exc) {
            $output = array('status' => 'error','data' => $exc->getMessage()); 
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
                   ':ev_evaluacion_id' => $this->received_data->model->ev_evaluacion_id,
                            
                    ); 
        $query = 'DELETE FROM ev_evaluacion WHERE ev_evaluacion_id = :ev_evaluacion_id ;'; 

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
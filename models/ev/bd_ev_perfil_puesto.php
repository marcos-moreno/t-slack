<?php  

 require_once '../../models/postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch($received_data->action){
        case 'update': 
            $model = new Ev_perfil_puesto($data,$connect,$received_data);
            $model->update();
        break;
        case 'insert':
            $model = new Ev_perfil_puesto($data,$connect,$received_data);
            $model->insert();
        break;
        case 'delete':
            $model = new Ev_perfil_puesto($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new Ev_perfil_puesto($data,$connect,$received_data);
            $model->select();
        break;
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
} 

class Ev_perfil_puesto 
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
                    ':genero_atributo' => $this->received_data->model->genero_atributo,
                        ':edad_minima' => $this->received_data->model->edad_minima,
                        ':edad_maxima' => $this->received_data->model->edad_maxima,
                        ':estado_civil_atributo' => $this->received_data->model->estado_civil_atributo,
                        ':grado_avance_atributo' => $this->received_data->model->grado_avance_atributo,
                        ':areas_conocimiento' => $this->received_data->model->areas_conocimiento,
                        ':minimo_experiencia_anios' => $this->received_data->model->minimo_experiencia_anios,
                        ':minimo_experiencia_meses' => $this->received_data->model->minimo_experiencia_meses,
                        ':areas_experiencia' => $this->received_data->model->areas_experiencia,
                        ':conocimientos_especificos' => $this->received_data->model->conocimientos_especificos,
                        ':equipo_software_herramientas' => $this->received_data->model->equipo_software_herramientas,
                        ':ev_tabulador_id_minimo' => $this->received_data->model->ev_tabulador_id_minimo,
                        ':ev_tabulador_id_maximo' => $this->received_data->model->ev_tabulador_id_maximo,
                        ':sueldo_promedio' => $this->received_data->model->sueldo_promedio,
                        ':media_salarial_mes' => $this->received_data->model->media_salarial_mes,
                        ':media_salarial_zona' => $this->received_data->model->media_salarial_zona,
                        ':competencias' => $this->received_data->model->competencias,
                        ':aptitudes' => $this->received_data->model->aptitudes,
                        ':observaciones_adicionales' => $this->received_data->model->observaciones_adicionales,
                        ':actitudes_puesto' => $this->received_data->model->actitudes_puesto,
                        ':nivel_estudios_atributo' => $this->received_data->model->nivel_estudios_atributo,
                        ':idioma_atributo' => $this->received_data->model->idioma_atributo,
                        ':ev_puesto_id' => $this->received_data->model->ev_puesto_id,
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'],
                    ); 
        $query = 'INSERT INTO ev_perfil_puesto (genero_atributo,edad_minima,edad_maxima,estado_civil_atributo,grado_avance_atributo,areas_conocimiento,minimo_experiencia_anios,minimo_experiencia_meses,areas_experiencia,conocimientos_especificos,equipo_software_herramientas,ev_tabulador_id_minimo,ev_tabulador_id_maximo,sueldo_promedio,media_salarial_mes,media_salarial_zona,competencias,aptitudes,observaciones_adicionales,actitudes_puesto,nivel_estudios_atributo,idioma_atributo,ev_puesto_id,creado,actualizado,creadopor,actualizadopor) VALUES (:genero_atributo,:edad_minima,:edad_maxima,:estado_civil_atributo,:grado_avance_atributo,:areas_conocimiento,:minimo_experiencia_anios,:minimo_experiencia_meses,:areas_experiencia,:conocimientos_especificos,:equipo_software_herramientas,:ev_tabulador_id_minimo,:ev_tabulador_id_maximo,:sueldo_promedio,:media_salarial_mes,:media_salarial_zona,:competencias,:aptitudes,:observaciones_adicionales,:actitudes_puesto,:nivel_estudios_atributo,:idioma_atributo,:ev_puesto_id,Now(),Now(),:creadopor,:actualizadopor) ;';

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
                    ':ev_perfil_puesto_id' => $this->received_data->model->ev_perfil_puesto_id, 
                        ':genero_atributo' => $this->received_data->model->genero_atributo, 
                        ':edad_minima' => $this->received_data->model->edad_minima, 
                        ':edad_maxima' => $this->received_data->model->edad_maxima, 
                        ':estado_civil_atributo' => $this->received_data->model->estado_civil_atributo, 
                        ':grado_avance_atributo' => $this->received_data->model->grado_avance_atributo, 
                        ':areas_conocimiento' => $this->received_data->model->areas_conocimiento, 
                        ':minimo_experiencia_anios' => $this->received_data->model->minimo_experiencia_anios, 
                        ':minimo_experiencia_meses' => $this->received_data->model->minimo_experiencia_meses, 
                        ':areas_experiencia' => $this->received_data->model->areas_experiencia, 
                        ':conocimientos_especificos' => $this->received_data->model->conocimientos_especificos, 
                        ':equipo_software_herramientas' => $this->received_data->model->equipo_software_herramientas, 
                        ':ev_tabulador_id_minimo' => $this->received_data->model->ev_tabulador_id_minimo, 
                        ':ev_tabulador_id_maximo' => $this->received_data->model->ev_tabulador_id_maximo, 
                        ':sueldo_promedio' => $this->received_data->model->sueldo_promedio, 
                        ':media_salarial_mes' => $this->received_data->model->media_salarial_mes, 
                        ':media_salarial_zona' => $this->received_data->model->media_salarial_zona, 
                        ':competencias' => $this->received_data->model->competencias, 
                        ':aptitudes' => $this->received_data->model->aptitudes, 
                        ':observaciones_adicionales' => $this->received_data->model->observaciones_adicionales, 
                        ':actitudes_puesto' => $this->received_data->model->actitudes_puesto, 
                        ':nivel_estudios_atributo' => $this->received_data->model->nivel_estudios_atributo, 
                        ':idioma_atributo' => $this->received_data->model->idioma_atributo, 
                        ':ev_puesto_id' => $this->received_data->model->ev_puesto_id, 
                        ':actualizadopor' => $_SESSION['id_empleado'],
                         
                    ); 
            $query = 'UPDATE ev_perfil_puesto SET genero_atributo=:genero_atributo,edad_minima=:edad_minima,edad_maxima=:edad_maxima,estado_civil_atributo=:estado_civil_atributo,grado_avance_atributo=:grado_avance_atributo,areas_conocimiento=:areas_conocimiento,minimo_experiencia_anios=:minimo_experiencia_anios,minimo_experiencia_meses=:minimo_experiencia_meses,areas_experiencia=:areas_experiencia,conocimientos_especificos=:conocimientos_especificos,equipo_software_herramientas=:equipo_software_herramientas,ev_tabulador_id_minimo=:ev_tabulador_id_minimo,ev_tabulador_id_maximo=:ev_tabulador_id_maximo,sueldo_promedio=:sueldo_promedio,media_salarial_mes=:media_salarial_mes,media_salarial_zona=:media_salarial_zona,competencias=:competencias,aptitudes=:aptitudes,observaciones_adicionales=:observaciones_adicionales,actitudes_puesto=:actitudes_puesto,nivel_estudios_atributo=:nivel_estudios_atributo,idioma_atributo=:idioma_atributo,ev_puesto_id=:ev_puesto_id,actualizado=Now(),actualizadopor=:actualizadopor WHERE  ev_perfil_puesto_id = :ev_perfil_puesto_id ;';

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
            $parameters = array(); 
            $query = '
                SELECT 
                    ev_perfil_puesto_id,genero_atributo,edad_minima,
                    edad_maxima,estado_civil_atributo,grado_avance_atributo,
                    areas_conocimiento,minimo_experiencia_anios,minimo_experiencia_meses
                    ,areas_experiencia,conocimientos_especificos,equipo_software_herramientas
                    ,ev_tabulador_id_minimo,ev_tabulador_id_maximo,sueldo_promedio,media_salarial_mes
                    ,media_salarial_zona,competencias,aptitudes,observaciones_adicionales
                    ,actitudes_puesto,nivel_estudios_atributo,idioma_atributo,p.ev_puesto_id
                    ,p.creado,p.actualizado,p.creadopor,p.actualizadopor 
                FROM refividrio.ev_perfil_puesto p
                INNER JOIN refividrio.ev_puesto pues ON pues.ev_puesto_id = p.ev_puesto_id
                
            ';
            if ($this->received_data->id > 0) {
                $parameters = array(
                    ':id' => $this->received_data->id,  
                );
                $query .= ' WHERE ev_puesto_id = :id ';  
            }
            if (isset($this->received_data->filter)) {
                $parameters = array(
                    ':filter' => $this->received_data->filter,  
                );
                $query .= " WHERE pues.codigo  ILIKE '%' || :filter || '%' OR pues.nombre_puesto ILIKE '%' || :filter || '%' ";  
            }
            $query .= ' ORDER BY ev_perfil_puesto_id DESC' ;
            $statement = $this->connect->prepare($query); 
            $statement->execute($parameters);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                    $row['ev_puesto'] = $this->search_union($row,'ev_puesto','ev_puesto_id','ev_puesto_id');
                    $row['tabulador_minimo'] = $this->search_union($row,'tabulador','id_tabulador','ev_tabulador_id_minimo');
                    $row['tabulador_maximo'] = $this->search_union($row,'tabulador','id_tabulador','ev_tabulador_id_maximo');
                    $row['genero'] = $this->search_union($row,'ev_atributo','id_atributo','genero_atributo');
                    $row['estado_civil'] = $this->search_union($row,'ev_atributo','id_atributo','estado_civil_atributo');
                    $row['grado_avance'] = $this->search_union($row,'ev_atributo','id_atributo','grado_avance_atributo');
                    $row['nivel_estudios'] = $this->search_union($row,'ev_atributo','id_atributo','nivel_estudios_atributo');
                    $row['idioma'] = $this->search_union($row,'ev_atributo','id_atributo','idioma_atributo');
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
                        ':ev_perfil_puesto_id' => $this->received_data->model->ev_perfil_puesto_id,
                    ); 
            $query = 'DELETE FROM ev_perfil_puesto WHERE ev_perfil_puesto_id = :ev_perfil_puesto_id ;'; 
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
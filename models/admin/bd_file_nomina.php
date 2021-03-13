<?php  

 require_once '../../models/postgres.php';

 require_once '../../models/auth/check.php';
 
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;

if (check_session()) {  
    if (isset($_POST['type'])) { 
        $model = new File_nomina($data,$connect,'');
        $model->insert($_POST,$_FILES);
        return;
    }
    switch($received_data->action){
        case 'update': 
            $model = new File_nomina($data,$connect,json_decode("{}"));
            $model->update();
        break;
        // case 'insert':
        //     $model = new File_nomina($data,$connect,$received_data);
        //     $model->insert();
        // break;
        case 'delete':
            $model = new File_nomina($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new File_nomina($data,$connect,$received_data);
            $model->select();
        break;
        case 'select_file_item': 
            $model = new File_nomina($data,$connect,$received_data);
            $model->getfile();
        break;
        case 'select_user': 
            $model = new File_nomina($data,$connect,$received_data);
            $model->select_user();
        break;
        default: 
            echo "No action";
        break; 
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output);  
} 

class File_nomina 
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

    public function insert($_POST_var,$_FILES_var){
        try {  
            if($_FILES_var['file']['type'] == "application/pdf" || $_FILES_var['file']['type'] == "text/xml" ){
                $model = json_decode($_POST['model']); 
                $value_document = explode("_", $_FILES_var['file']['name']);
                if (count($value_document) != 7) {
                    $output = array('status' =>'error','message' => "El Nombre del Archivo no cumple con la Nomenclatura."); 
                    echo json_encode($output); 
                    return false;
                }
                $data = array(
                            ':nombre' => $value_document[5]."/Semana:".$value_document[4]."/Ejercicio:".$value_document[3],
                            ':creadopor' => $_SESSION['id_empleado'],
                            ':actualizadopor' => $_SESSION['id_empleado'],
                            ':id_empresa' => $model->id_empresa,
                            ':id_compac' => $value_document[5],
                            ':semana' => $value_document[4] ,
                            ':ejercicio' => $value_document[3],
                            ':code' => $_FILES_var['file']['name'],
                            ':type_file' => $_FILES_var['file']['type'], 
                ); 
                $data_file = file_get_contents($_FILES_var["file"]["tmp_name"]);
                $escaped = bin2hex( $data_file );
                $query = "INSERT INTO file_nomina (nombre,creado,creadopor,actualizadopor,actualizado,id_empresa,id_compac,semana,ejercicio,code,type_file,data) VALUES
                            (:nombre,Now(),:creadopor,:actualizadopor,Now(),:id_empresa,:id_compac,:semana,:ejercicio,:code,:type_file,decode('{$escaped}' , 'hex')) ;";
                $statement = $this->connect->prepare($query); 
                $statement->execute($data);  
                $output = array('status' =>'succes','message' => 'Data Inserted'); 
                echo json_encode($output); 
                return true;
            }else{
                $output = array('status' =>'error','message' => "Extención Inválida."); 
                echo json_encode($output); 
                return false;
            }
        } catch (PDOException $exc) {
            $output = array('status' =>'error','message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        } 
    } 
    public function select_user(){
        try {  
             
            $query = 'SELECT fil_nom.id_file_nomina,fil_nom.nombre,fil_nom.id_empresa,
                    fil_nom.id_compac,fil_nom.semana,fil_nom.ejercicio
                    ,fil_nom.code,fil_nom.type_file
                    FROM file_nomina fil_nom 
                    INNER JOIN empleado emp ON emp.id_compac = fil_nom.id_compac
                    AND emp.id_empleado = ' . $_SESSION['id_empleado'] . '
                    WHERE (SELECT seg.id_empresa FROM segmento seg
                             WHERE seg.id_segmento =  emp.id_segmento) = fil_nom.id_empresa
                    ORDER BY fil_nom.ejercicio,fil_nom.semana DESC,type_file,fil_nom.nombre';
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
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
             
        $query = 'SELECT id_file_nomina,nombre,creado,creadopor,actualizadopor,actualizado,id_empresa,id_compac,semana,ejercicio,code,type_file
                    FROM file_nomina  
                    ' . (isset($this->received_data->filter) ? ' 
                    WHERE ' . $this->received_data->filter:'') . 
                    (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
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
    public function getfile(){ 
        try {    
            // http://localhost/new%20version%20t-slack/t-slack/models/admin/bd_file_nomina.php
            $data = array(
                ':id_file_nomina' => $this->received_data->model->id_file_nomina
            ); 
            $query = "SELECT encode(data, 'base64') AS data, type_file 
                        FROM file_nomina
                      WHERE id_file_nomina = :id_file_nomina" ;
            $statement = $this->connect->prepare($query);  
            $statement->execute($data);
            while($row = $statement->fetch(PDO::FETCH_ASSOC)) { 
                // echo base64_decode($row['data']);
                echo $row['data'] ;
            }   
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
            echo json_encode($output); 
            return false;
        }  
    }
    public function delete(){
        try {  
            $data = array(
                   ':id_file_nomina' => $this->received_data->model->id_file_nomina,
                            
                    ); 
        $query = 'DELETE FROM file_nomina WHERE id_file_nomina = :id_file_nomina ;'; 

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
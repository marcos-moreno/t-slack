<?php  

 require_once '../../models/postgres.php';

 require_once '../../models/auth/check.php';
 
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;

if (check_session()) {  
    if (isset($_POST['type'])) { 
        $model = new File_adjunto($data,$connect,'');
        $model->insert($_POST,$_FILES);
        return;
    }
    if (isset($_GET['type_getFile'])) { 
        $model = new File_adjunto($data,$connect,'');
        $model->getfile_get($_GET);
        return;
    }
    if (isset($_GET['type_getFile_admin'])) { 
        $model = new File_adjunto($data,$connect,'');
        $model->getfile_admin($_GET);
        return;
    }
    switch($received_data->action){
        case 'update': 
            $model = new File_adjunto($data,$connect,json_decode("{}"));
            $model->update();
        break;
        // case 'insert':
        //     $model = new File_adjunto($data,$connect,$received_data);
        //     $model->insert();
        // break;
        case 'delete':
            $model = new File_adjunto($data,$connect,$received_data);
            $model->delete(); 
        break;
        case 'select': 
            $model = new File_adjunto($data,$connect,$received_data);
            $model->select();
        break;
        // case 'select_file_item': 
        //     $model = new File_adjunto($data,$connect,$received_data);
        //     $model->getfile();
        // break;
        case 'select_preview': 
            $model = new File_adjunto($data,$connect,$received_data);
            $model->select_preview();
        break;
        default: 
            echo "No action";
        break; 
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output);  
} 

class File_adjunto 
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
            $data = array(
                        ':tabla' => $_POST_var['tabla'],
                        ':id_tabla' => $_POST_var['id_tabla'], 
                        ':creadopor' => $_SESSION['id_empleado'],
                        ':actualizadopor' => $_SESSION['id_empleado'], 
                        ':type_file' => $_FILES_var['file']['type'], 
                        ':name' => $_POST_var['name'],
            ); 
            $data_file = file_get_contents($_FILES_var["file"]["tmp_name"]);
            $escaped = bin2hex( $data_file );
            $query = "INSERT INTO file_adjunto (
                tabla,id_tabla,name,creado,creadopor,actualizadopor,actualizado,
                type_file,data) 
            VALUES(
                :tabla,:id_tabla,:name,Now(),:creadopor,:actualizadopor,Now(),
                :type_file,decode('{$escaped}' , 'hex'));";
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);
            $output = array('status' =>'succes','message' => 'Data Inserted'); 
            echo json_encode($output); 
            return true; 
        } catch (PDOException $exc) {
            $output = array('status' =>'error','message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        } 
    } 
    public function select_preview(){
        try {  
            $data = array(
                ':tabla' => $this->received_data->tabla,
                ':id_tabla' =>$this->received_data->id_tabla,  
            );  
            $query = '
                    SELECT 
                        id_file_adjunto,id_tabla,tabla
                        ,type_file,creado,actualizado
                        ,creadopor,actualizadopor,name 
                    FROM refividrio.file_adjunto 
                    WHERE  id_tabla = :id_tabla AND tabla = :tabla
                    ';
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);  
            $data = []; 
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
        // try {  
             
        // $query = 'SELECT id_file_adjunto,nombre,creado,creadopor,actualizadopor
        //             ,actualizado,id_empresa,id_compac,semana,ejercicio,code,type_file
        //             FROM file_adjunto  
        //             ' . (isset($this->received_data->filter) ? ' 
        //             WHERE ' . $this->received_data->filter:'') . 
        //             (isset($this->received_data->order) ? $this->received_data->order:'') ;
                        
        //     $statement = $this->connect->prepare($query); 
        //     $statement->execute($data);   
        //     while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
        //         $data[] = $row;
        //     }  
        //     echo json_encode($data); 
        //     return true;
        // } catch (PDOException $exc) {
        //     $output = array('message' => $exc->getMessage()); 
        //     echo json_encode($output); 
        //     return false;
        // }  
    } 
    public function getfile_admin($_GET_res){  
        try {    
            $data = array(
                ':id_file_adjunto' => $_GET_res["id_file"], 
            ); 
            $query = "  SELECT encode(data, 'base64') AS data, type_file ,name
                        FROM file_adjunto  
                        WHERE id_file_adjunto = :id_file_adjunto";
            $statement = $this->connect->prepare($query);  
            $statement->execute($data);
            while($row = $statement->fetch(PDO::FETCH_ASSOC)) {  
                $data = base64_decode($row['data']);
                // if ($row['type_file'] == "text/xml") {
                //     header('Content-Type: ' . $row['type_file']); 
                //     header('Content-Disposition: attachment; filename="' . $row['code'] . '"'); 
                // } else { 
                header('Content-Type: ' . $row['type_file']); 
                header('Content-Disposition: inline; filename="' . $row['code'] . '"'); 
                header('Content-Description: File Transfer');
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Transfer-Encoding: binary');
                // }  
                print $data;
            }    
        } catch (PDOException $exc) {
            $output = array('message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }
    } 
    public function getBase64Size($base64){ //return memory size in B, KB, MB
        try{
            $size_in_bytes = (int) (strlen(rtrim($base64, '=')) * 3 / 4);
            $size_in_kb    = $size_in_bytes / 1024;
            $size_in_mb    = $size_in_kb / 1024; 
            return $size_in_kb;
        }
        catch(Exception $e){
            return $e;
        }
    }
    public function getfile_get($_GET_res){ 
        try {    
            $data = array(
                ':id_file_adjunto' => $_GET_res["id_file"],
                ':id_empleado' => $_SESSION['id_empleado']
            ); 
            $query = "  SELECT encode(data, 'base64') AS data, type_file ,f.code
                        FROM file_adjunto f
                        INNER JOIN empleado e ON e.id_compac = f.id_compac 
                            AND e.id_empleado = :id_empleado
                        WHERE id_file_adjunto = :id_file_adjunto" ;
            $statement = $this->connect->prepare($query);  
            $statement->execute($data);
            while($row = $statement->fetch(PDO::FETCH_ASSOC)) { 
                $data = base64_decode($row['data']);
                if ($row['type_file'] == "text/xml") {
                    header('Content-Type: ' . $row['type_file']); 
                    header('Content-Disposition: attachment; filename="' . $row['code'] . '"'); 
                } else {  
                    header('Content-Type: ' . $row['type_file']); 
                    header('Content-Disposition: inline; filename="' . $row['code'] . '"'); 
                    header('Content-Description: File Transfer');
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Content-Transfer-Encoding: binary'); 
                } 
                print $data;
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
                   ':id_file_adjunto' => $this->received_data->model->id_file_adjunto,
                            
                    ); 
        $query = 'DELETE FROM file_adjunto WHERE id_file_adjunto = :id_file_adjunto ;'; 

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
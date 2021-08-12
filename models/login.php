<?php 
 require_once 'postgres.php';
 $received_data = json_decode(file_get_contents('php://input')); 
 $model = null;
 require_once 'auth/check.php'; 

if (check_session()) {  
    switch($received_data->action){ 
        case 'changePassword':
            $model = new Log_Request($data,$connect,$received_data);
            $model->changePassword(); 
        break; 
        case 'getRoles': 
            $output = array('status'  => 'session_active'); 
            echo json_encode($output);
        break; 
        case 'login':
            $output = array('status'  => 'session_active'); 
            echo json_encode($output);
        break;
        case 'isPass_default':
            $model = new Log_Request($data,$connect,$received_data);
            $model->isPass_default();
        break; 
    }
}else{
    if($received_data->action == 'getRoles'){
        $model = new Log_Request($data,$connect,$received_data);
        $model->getRoles();
    }else if($received_data->action == 'login'){
        $model = new Log_Request($data,$connect,$received_data);
        $model->login();
    }else{
        $output = array('message' => 'Not authorized'); 
        echo json_encode($output); 
    } 
} 

class Log_Request 
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

    public function changePassword(){
        try { 
            $output = array('status'  => '','data' => array()); 
            $parametros = array(
                ':id_empleado' => $_SESSION['id_empleado'], 
                ':password'=> $this->received_data->password_old
            ); 
            $query =  "
            SELECT 	
                e.id_empleado
            FROM refividrio.empleado e
            INNER JOIN empleado_rol er ON er.id_empleado = e.id_empleado
            INNER JOIN rol r ON r.id_rol = er.id_rol
            WHERE 
                password = md5(:password) AND e.id_empleado = :id_empleado AND e.activo = true
            LIMIT 1    
            ";  
            $statement = $this->connect->prepare($query); 
            $statement->execute($parametros); 
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                if ($row['id_empleado'] == $_SESSION['id_empleado']) {
                    try {
                        $params = array(
                            ':password_new' => $this->received_data->password_new,
                            ':id_empleado' => $_SESSION['id_empleado'] 
                        );
                        $query = " UPDATE empleado SET  password =  md5(:password_new)  WHERE id_empleado= :id_empleado"  ;
                        $statement2 = $this->connect->prepare($query); 
                        $statement2->execute($params);  
                        $output = array('status'  => 'success'); 
                        echo json_encode($output);
                        return true;
                    } catch (PDOException $exc) {
                        $output = array('status'  => 'errorChangePass','data' => $exc->getMessage()); 
                        echo json_encode($output);
                        return false;
                    }
                   
                }
            } 
            $output = array('status'  => 'errorOldPass','data' => 'no hay conincidencia'); 
            echo json_encode($output);
            return false;
        } catch (PDOException $exc) {
            $output = array('status'  => 'errorOldPass','data' => $exc->getMessage()); 
            echo json_encode($output);
            return false;
        }
    } 
    
    public function getRoles(){  
        try { 
            $data = array();
            $parametros = array(
                ':user' => $this->received_data->user, 
                ':password'=> $this->received_data->password
            ); 
            $query =  "
            SELECT 	
                e.id_empleado,e.nombre
                ,e.materno, e.paterno
                ,r.rol,r.id_rol  
            FROM refividrio.empleado e
            INNER JOIN empleado_rol er ON er.id_empleado = e.id_empleado
            INNER JOIN rol r ON r.id_rol = er.id_rol
            WHERE
            (
                (UPPER(usuario) = UPPER(:user) AND usuario IS NOT NULL) 
                OR (UPPER(celular) = UPPER(:user) AND celular IS NOT NULL) 
                OR (UPPER(correo) = UPPER(:user) AND correo IS NOT NULL) 
            )
            AND password = md5(:password)
            AND e.activo = true";

            $statement = $this->connect->prepare($query); 
            $statement->execute($parametros);
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            $output = array('status'  => 'success','data' => $data); 
            echo json_encode($output);   
        } catch (PDOException $exc) {
            $output = array('status'  => 'error','message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    } 

    public function isPass_default(){  
        try { 
            $data = array();
            $parametros = array(':id_empleado' => $_SESSION['id_empleado']); 
            $query =  "
            SELECT 	
                e.id_empleado
            FROM refividrio.empleado e
            INNER JOIN empleado_rol er ON er.id_empleado = e.id_empleado
            INNER JOIN rol r ON r.id_rol = er.id_rol
            WHERE e.id_empleado = :id_empleado 
            AND password = md5('refividrio')
            AND e.activo = true"; 
            $statement = $this->connect->prepare($query); 
            $statement->execute($parametros);
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row; 
                $output = array('status'  => 'success','data' => true); 
                echo json_encode($output);   
                return;
            }
            $output = array('status'  => 'success','data' => false); 
            echo json_encode($output);   
            return;
        } catch (PDOException $exc) {
            $output = array('status'  => 'error','data' => false); 
            echo json_encode($output); 
            return false;
        }  
    } 
    
    public function login(){  
        try { 
            $data = array();
            $parametros = array(
                ':user' => $this->received_data->user, 
                ':password'=> $this->received_data->password,
                ':id_rol'=> $this->received_data->id_rol
            ); 
            $query =  "
            SELECT 	
                e.id_empleado,e.id_segmento, e.nombre
                ,e.materno, e.paterno, e.genero,e.usuario
                ,r.rol,r.id_rol
                ,coalesce(e.color_back,'#fff') color_back
                ,r.pagina_inicio
            FROM refividrio.empleado e
            INNER JOIN empleado_rol er ON er.id_empleado = e.id_empleado
            INNER JOIN rol r ON r.id_rol  = :id_rol
                AND r.id_rol = er.id_rol
            WHERE
            (
                (UPPER(usuario) = UPPER(:user) AND usuario IS NOT NULL) 
                OR (UPPER(celular) = UPPER(:user) AND celular IS NOT NULL) 
                OR (UPPER(correo) = UPPER(:user) AND correo IS NOT NULL) 
            )
            AND password = md5(:password) 
            AND e.activo = true
            LIMIT 1
            "; 
            $statement = $this->connect->prepare($query); 
            $statement->execute($parametros);
            $result_ = false;
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data = $row;
                $result_ = true;
            } 
            if ($result_ == true) {
                session_start();
                $_SESSION['id_empleado'] = $data['id_empleado'];  
                $_SESSION['nombre'] = $data['nombre'];
                $_SESSION['paterno'] = $data['paterno'];  
                $_SESSION['materno'] = $data['materno'];  
                $_SESSION['rol'] = $data['rol'];  
                $_SESSION['rol'] = $data['rol'];  
                $_SESSION['pagina_inicio'] = $data['pagina_inicio']; 
                $_SESSION['color_back'] = $data['color_back'];  
                $resulToken = $this->get_token($data);
                if ($resulToken != false) {
                    $resulToken = json_decode($resulToken);
                }
                $ou_js = array(
                    'id_empleado' => $data['id_empleado']
                    ,'pagina_inicio' => $data['pagina_inicio']
                    ,'api_key_cerberus' => $resulToken 
                );
                $output = array('status'  => 'success','data' => $ou_js, 'session' => $_SESSION); 
                echo json_encode($output);  
            }else{
                $output = array('status'  => 'erro','message' => "No se encontró el usuario."); 
                echo json_encode($output); 
            } 
        } catch (PDOException $exc) {
            $output = array('status'  => 'erro','message' => $exc->getMessage()); 
            echo json_encode($output); 
            return false;
        }  
    } 
    public function get_token($data)
    {
        try {
            $url = 'https://rep.refividrio.com.mx:5858/api/login';
            $ch = curl_init($url);
            $jsonData = array(
                'API_KEY'=>'surver_$MGsecretkey$Surver$NodeJS&ApiCerberus',
                "id_empleado" => $data['id_empleado'],
                "rol" => $data['rol']
            );
            $jsonDataEncoded = json_encode($jsonData);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ( $status !== 201 && $status !== 200 ) {
                return false;
                // die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
            }
            curl_close($ch);
            return  $result;
        } catch (\Throwable $th) {
            return false;
        }   
    }  
}  
?>
<?php 
session_start();   
$received_data = json_decode(file_get_contents("php://input")); 
$user = $received_data->user;
$password = $received_data->password; 
$rol =  $received_data->rol;

if ($received_data->action == 'login') { 
    if ($rol != "0" ) {
        try { 
            $_SESSION['id_empleado'] = $rol->id_empleado;  
            $_SESSION['nombre'] = $rol->nombre;
            $_SESSION['paterno'] = $rol->paterno;  
            $_SESSION['materno'] = $rol->materno;  
            $_SESSION['rol'] = $rol->rol;  
            $_SESSION['password'] = $rol->password; 
            $_SESSION['color_back'] = $rol->color_back;   
            echo 'succes';  
        } catch (\Throwable $th) {  echo  $th;   }   
    } else {
        if((!$user) || (!$password)){  
            echo "error";
        }else{   
                require_once "postgres.php"; 
                try {
                    $parametros = array(); 
                    $parametros = array(
                        ':user' => $user, 
                        ':password'=> $password
                    ); 
                    $query =  "
                    SELECT 	
                        e.id_empleado,e.id_segmento, e.nombre, e.materno, e.paterno, e.genero,e.usuario,r.rol,r.id_rol,e.password
                        ,coalesce(e.color_back,'#fff') color_back
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
                    $statement = $connect->prepare($query);
                    $statement->execute($parametros); 
                    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                        $data[] = $row;
                    }  
                    echo json_encode($data);   
                } catch (Exception $e) {
                    echo "¡Error!" ; 
                } 
        }
    }
}
if ($received_data->action == 'comparePassword') {
    if (md5($received_data->password_old) == $_SESSION['password']) {
        echo 'contraseña válida';
    } else {
        echo 'La contraseña NO es válida.';
    } 
}

if ($received_data->action == 'changePassword') {
    if (md5($received_data->password_old) == $_SESSION['password']) {
        $query = '';
        try {
            if ($received_data->password_new != '') {
                require_once "postgres.php";  
                $query = " UPDATE empleado SET  password =  md5('" . $received_data->password_new . "')  WHERE id_empleado= " . $_SESSION['id_empleado']  ;
                $statement = $connect->prepare($query);
                $statement->execute(); 
                echo json_encode('Password Updated'); 
            }else{
                echo 'La contraseña NO es válida.';
            } 
        } catch (PDOException $e) {
            echo json_encode( $query );
        } 
    } else {
        echo 'La contraseña NO es válida.';
    } 
}
 
?>
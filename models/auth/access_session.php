<?php     

$received_data = json_decode(file_get_contents('php://input'));  
$nombre = ''; 
$elementos = null;
session_start();   
if (isset($_SESSION['rol'])) {
    $validado = false;
   
    $access_session = new Access_session($_SESSION['id_empleado'],$connect,$_SESSION['rol']); 
    $elementos = $access_session->get_access(); 
    $elementos = json_decode($elementos);  
    $elementos_string = ""; 
    foreach ($elementos as $key) { 
        if ($key->ismenu) {
            $elementos_string .= '
            <li class="nav-item active">
                <a class="nav-link" href="'.$key->path.'">'.$key->name.'<span class="sr-only">(current)</span></a>
            </li> '; 
            $validado = true;
        } 
    } 
    if ($validado == false) { 
        header('location: ../logout.php'); 
    }else{
        $nombre = $_SESSION['nombre']. ' '. $_SESSION['paterno'] //. ' '. $_SESSION['materno']
        ; 
    }
}else{
    header('location: ../logout.php'); 
}
 
class Access_session{
    
    private $connect = null;
    private $id_empleado = null;
    private $rol = null;

    public function __construct($id_empleado,$connect,$rol){
        $this->id_empleado  = $id_empleado;
        $this->connect = $connect; 
        $this->rol = $rol;  
    }

    public function get_access(){
        try {  
             
        $query = "SELECT el.* FROM 
                    empleado e
                    INNER JOIN empleado_rol er ON er.id_empleado = e.id_empleado 
                    INNER JOIN rol r ON r.id_rol = er.id_rol
                    INNER JOIN acceso_rol ar ON ar.id_rol = r.id_rol
                    INNER JOIN elemento el ON el.id_elemento = ar.id_elemento 
                    WHERE e.id_empleado =" . $this->id_empleado . "
                    AND r.rol = '" . $this->rol . "' ORDER BY el.name";
                                    
            $statement = $this->connect->prepare($query); 
            $statement->execute($data);   
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {    $data[] = $row;
            } 
            return json_encode($data);  
        } catch (Exception $exc) { 
            echo json_encode('{}'); 
            return false;
        }  
    }
}
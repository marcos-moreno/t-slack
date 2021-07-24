<?php require "../header.php"; ?> 

<div class="container" >  

<?php

require_once "../../models/postgres.php";
require_once "generate_string.php";
require_once "generator_file_html.php"; 
require_once "generator_js_controller.php"; 

$query_TABLE = " SELECT table_name,*
            FROM information_schema.tables
            WHERE table_schema = 'refividrio' 
            ORDER BY table_name;";   
$statement = $connect->prepare($query_TABLE);
$statement->execute();   
$options_tables_bd = "";

while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
    $options_tables_bd .= "<option ".( isset($_POST['table']) ? ( $_POST['table'] == $row['table_name']? 'selected' : '') : '' ) ." value=". $row['table_name'] . "> " . $row['table_name'] . "</option>";   
}   

if (isset($_POST['table']) && isset($_POST['nombreCRPETA'])){ 
    $nombreCRPETA = $_POST['nombreCRPETA'];
    echo "<br/>###########################################".strtoupper($_POST['table'])."#############################################<br/>";
    echo "<br/><h1>CRUD SELECCIONADO  " . strtoupper($_POST['table']) . "</h1>" ;
    $data = new Data($connect,$_POST['table'],$nombreCRPETA);
    $data->generar_php_api();
    echo "<br/>##########################################FIN ".strtoupper($_POST['table'])."############################################<br/>";
}else{
    echo  "<br/><p>Nada Seleccionado.</p>" ;
}
?> 

    <div class="table-responsive">
        <div class="alert alert-success"   role="alert">
            <form action="execute.php" method="post">
                
                <div class='custom-control'>
                    <select name='table' class="custom-select mb-2 mr-sm-2 mb-sm-0">
                        <?php echo $options_tables_bd; ?>
                    </select>
                </div>  
                <br/>
                <div class='custom-control'>
                    <label>Carpeta</label> 
                    <input type="text" name="nombreCRPETA" class='form-control'  /> 
                </div>  
                <br/> 
                <div class='custom-control'>
                    <input type="submit" value="Crear CRUD" class="btn btn-info btn-xs" />
                </div>  

            </form>
        </div>
    </div>
</div>

<?php 
 
class Data
{  
    private $data_field = array();
    private $data_keys = array(); 
    private $name_crud = ""; 
    private $field_json = [];   
    private $field_primary_key = '';
    private $data_forenings_keys = array(); 
    private $connect = null;
    private $nombreCRPETACLASS = "";
    public function __construct($connect,$name_crud,$nombreCRPETAs){ 
        $this->connect = $connect; 
        $this->name_crud = $name_crud; 
        $this->nombreCRPETACLASS = $nombreCRPETAs;
        if (isset($this->name_crud)) {
            $this->getFields(); 
        }  
    }
    
    function getFields(){
        // ===============================fields===============================================
        $query = "select
            cols.table_catalog,cols.table_name,cols.column_name,data_type,pg_catalog.col_description(c2.oid,cols.ordinal_position::int)
            from information_schema.columns cols
            inner join pg_catalog.pg_class c on  c.relname=cols.table_name
            inner join pg_catalog.pg_class c2 on c2.relname=cols.table_name
            where cols.table_name= '$this->name_crud'";   
        $statement = $this->connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) { 
            $this->data_field[] = $row;
        } 
        $this->field_json = json_encode($this->data_field);
        $this->field_json = json_decode($this->field_json);  
        // ===============================fields=============================================== 
        // ===============================keys===============================================
        $query = "SELECT tc.constraint_type,kcu.column_name AS fk_table_usage,kcu.table_name As table_usage,ccu.table_name As table_origen,ccu.column_name AS fk_table_origen 
         FROM information_schema.table_constraints tc 
                    INNER JOIN information_schema.key_column_usage kcu ON tc.constraint_catalog = kcu.constraint_catalog
                    AND tc.constraint_schema = kcu.constraint_schema AND tc.constraint_name = kcu.constraint_name  
                    LEFT JOIN information_schema.referential_constraints rc
                    ON tc.constraint_catalog = rc.constraint_catalog
                    AND tc.constraint_schema = rc.constraint_schema
                    AND tc.constraint_name = rc.constraint_name 
                    LEFT JOIN information_schema.constraint_column_usage ccu
                    ON rc.unique_constraint_catalog = ccu.constraint_catalog
                    AND rc.unique_constraint_schema = ccu.constraint_schema
                    AND rc.unique_constraint_name = ccu.constraint_name 
                    WHERE lower(tc.constraint_type) in ('foreign key', 'primary key')
                    AND tc.table_name= '$this->name_crud'
                    ORDER BY tc.constraint_type ";   
        $statement = $this->connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {   
            if ($row['constraint_type'] == "PRIMARY KEY") {
                $this->field_primary_key = $row["fk_table_usage"]; 
            }
            if ($row['constraint_type'] == "FOREIGN KEY" ) {
                $this->data_forenings_keys[] = $row; 
            } 
        }// ===============================keys=============================================== 
        $this->data_forenings_keys = json_encode($this->data_forenings_keys);
        $this->data_forenings_keys = json_decode($this->data_forenings_keys);  
        echo " <p style='color:#4F8238;  font-weight: italic;'> LLAVE PRIMARIA DE LA TABLA: " . $this->field_primary_key." <p/>"; 
        print_r($this->data_forenings_keys); 
    }

    function generar_php_api(){  
        echo "<br/><p style='color:blue;  font-weight: bold;'>Inicio---------------------------------------API</p>";
        $estructura = '../../models/'.$this->nombreCRPETACLASS.'/';  
        if (file_exists($estructura)) {
            echo "Los ficheros se Remplazarán.<br/>";
            $this->generate_php_string();
        } else {
            echo "El fichero $estructura no existe<br/>";
            if(!mkdir($estructura, 7777, true)) {
                die('Fallo al crear las carpetas...');  
            }else{
                echo "Fichero $estructura Creado<br/>";
                $this->generate_php_string();
            }
        } 
        echo "<p style='color:blue;  font-weight: bold;'>Fin---------------------------------------FIN API</p><br/>";

        echo "<br/><p style='color:blue;  font-weight: bold;'>Inicio---------------------------------------HTML Template</p>";
        $estructura = '../../views/'.$this->nombreCRPETACLASS.'/';  
        if (file_exists($estructura)) {
            echo "Los ficheros se Remplazarán.<br/>";
            $this->generate_file_html();
        } else {
            echo "El fichero $estructura no existe<br/>";
            if(!mkdir($estructura, 7777, true)) {
                die('Fallo al crear las carpetas...');  
            }else{
                echo "Fichero $estructura Creado<br/>";
                $this->generate_file_html();
            }
        } 
        echo "<p style='color:blue;  font-weight: bold;'>Fin---------------------------------------HTML Template</p><br/>";

        echo "<br/><p style='color:blue;  font-weight: bold;'>Inicio---------------------------------------JS javaScript</p>";
        $estructura = '../../controllers/'.$this->nombreCRPETACLASS.'/';  
        if (file_exists($estructura)) {
            echo "Los ficheros se Remplazarán.<br/>";
            $this->generate_controller_js();
        } else {
            echo "El fichero $estructura no existe<br/>";
            if(!mkdir($estructura, 7777, true)) {
                die('Fallo al crear las carpetas...');  
            }else{
                echo "Fichero $estructura Creado<br/>";
                $this->generate_controller_js();
            }
        } 
        echo "<p style='color:blue;  font-weight: bold;'>Fin---------------------------------------FIN JS javaScript</p><br/>";
    }

    function generate_php_string(){
        try {
            $generator = new generate_string($this->field_json,$this->name_crud,$this->field_primary_key,
                                            $this->data_forenings_keys,$this->connect); 
            $contenido = $generator->generator_content(); 
            $archivo = fopen('../../models/'.$this->nombreCRPETACLASS.'/bd_'.$this->name_crud.'.php','w+');
            fputs($archivo,$contenido);
            fclose($archivo);
            echo " <p style='color:green;  font-weight: bold;'>** SUCCESS CREATING API FILE PHP <p/>";
        } catch (\Throwable $th) {
            echo $th;
        }
    }  

    function generate_file_html(){
        try {
            $generator = new generator_file_html($this->field_json,$this->name_crud,$this->field_primary_key,
                                                $this->data_forenings_keys,$this->connect,$this->nombreCRPETACLASS); 
            $contenido = $generator->generator_content(); 
            $archivo = fopen('../../views/'.$this->nombreCRPETACLASS.'/v_'.$this->name_crud.'.php','w+');
            fputs($archivo,$contenido);
            fclose($archivo);
            echo "<p style='color:green;  font-weight: bold;'>** SUCCESS CREATING FILE TEMPLATE  <p/>";
        } catch (\Throwable $th) {
            echo $th;
        }
    } 

    function generate_controller_js(){
        try {
            $generator = new generator_js_controller($this->field_json,$this->name_crud,$this->field_primary_key,
                                                        $this->data_forenings_keys,$this->connect,$this->nombreCRPETACLASS); 
            $contenido = $generator->generator_content(); 
            $archivo = fopen('../../controllers/'.$this->nombreCRPETACLASS.'/c_'.$this->name_crud.'.js','w+');
            fputs($archivo,$contenido);
            fclose($archivo);
            echo "<p style='color:green;  font-weight: bold;'>** SUCCESS CREATING JS CONTROLLER  <p/>";
        } catch (\Throwable $th) {
            echo $th;
        }
    } 

} 

?> 
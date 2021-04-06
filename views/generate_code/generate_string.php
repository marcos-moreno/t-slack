
<?php

class generate_string
{ 
    private $field_primary_key = '';
    private $field_json = []; 
    private $name_crud = "";
    private $data_forenings_keys = array();  
    private $connect = null;

    public function __construct($field_json,$name_crud,$field_primary_key,$data_forenings_keys,$connect){
        $this->field_json = $field_json; 
        $this->name_crud  = $name_crud;
        $this->data_forenings_keys = $data_forenings_keys;
        $this->field_primary_key = $field_primary_key;  
        $this->connect = $connect;
    }

    public function function_generate_insert(){
        $cadena = "%data = array(
                    "; 
        $campos = "";
        $valores = ""; 
        foreach ($this->field_json as $field) { 
            if ($this->field_primary_key != $field->column_name) { 
                if ($field->column_name != "creado" && $field->column_name != "actualizado" ) {
                    if ($field->column_name == "creadopor" || $field->column_name == "actualizadopor" ) {
                        $cadena  .= "':$field->column_name' => %_SESSION['id_empleado'],
                        ";
                    }else {
                        $cadena  .= "':$field->column_name' => %this->received_data->model->$field->column_name,
                        ";
                    } 
                }  
                 
                $campos  .= "$field->column_name,";
                 
                if ($field->column_name == "creado" || $field->column_name == "actualizado" ) {
                    $valores .= "Now(),";
                }else {
                    $valores .= ":$field->column_name,";
                }
            }
        }
        $cadena .= "
                    ); 
        %query = 'INSERT INTO $this->name_crud ($campos) VALUES ($valores) ;';"; 


        return str_replace(",)",")",$cadena);
    }

    public function function_generate_update(){
        $cadena = "%data = array(
                    "; 
        $campos = ""; 
        foreach ($this->field_json as $field) {  
                if ($field->column_name != "creado" && $field->column_name != "creadopor" && $field->column_name != "actualizado") {
                    if ($field->column_name == "actualizadopor" ) {
                        $cadena  .= "':$field->column_name' => %_SESSION['id_empleado'],
                        ";
                    }else {
                        $cadena  .= "':$field->column_name' => %this->received_data->model->$field->column_name, 
                        "; 
                    } 
                }   
                if ( $this->field_primary_key != $field->column_name && "creadopor"  != $field->column_name && "creado"  != $field->column_name) {
                    if ($field->column_name == "actualizado" ) {
                        $campos  .= "$field->column_name=Now(),";   
                    }else {
                        $campos  .= "$field->column_name=:$field->column_name,";
                    }
                }   
        }
        $cadena .= " 
                    ); 
            %query = 'UPDATE $this->name_crud SET $campos WHERE $this->field_primary_key = :$this->field_primary_key ;';"; 
        return str_replace(", WHERE"," WHERE ",$cadena);
    }

    public function function_generate_delete(){ 
        $cadena = "%data = array(
            ";  
        foreach ($this->field_json as $field) {  
            if ($this->field_primary_key == $field->column_name) {
                $cadena  .= "       ':$field->column_name' => %this->received_data->model->$field->column_name,
                            "; 
            } 
        } 
        $cadena .= "
                    ); 
        %query = 'DELETE FROM $this->name_crud WHERE $this->field_primary_key = :$this->field_primary_key ;';"; 
        return $cadena;
    }
  
    public function function_generate_select(){
        $campos = "";
        foreach ($this->field_json as $field) {  
            $campos  .= "$field->column_name,";
        }   
        $cadena = " 
        %query = 'SELECT $campos *-
                    FROM $this->name_crud  
                    ' . (isset(%this->received_data->filter) ? ' 
                    WHERE ' . %this->received_data->filter:'') . 
                    (isset(%this->received_data->order) ? %this->received_data->order:'') ;
                    "; 

        $cadena = str_replace(", *-"," ",$cadena);     
        $cadena .= "    
            %statement = %this->connect->prepare(%query); 
            %statement->execute(%data);   
            while (%row = %statement->fetch(PDO::FETCH_ASSOC)) {";
        if (count($this->data_forenings_keys)>0) {
            foreach ($this->data_forenings_keys as $key) {
                $cadena .="  
                    %row['$key->table_origen'] = %this->search_union(%row,'$key->table_origen','$key->fk_table_origen','$key->fk_table_usage');
                ";
            };
        } 
        $cadena .= "    %data[] = %row;
            }";     
        return $cadena;
    } 
    function getFields($table){ 
        $data_fields = array(); 
        $query = "select
            cols.table_catalog,cols.table_name,cols.column_name,data_type,pg_catalog.col_description(c2.oid,cols.ordinal_position::int)
            from information_schema.columns cols
            inner join pg_catalog.pg_class c on  c.relname=cols.table_name
            inner join pg_catalog.pg_class c2 on c2.relname=cols.table_name
            where cols.table_name= '$table'";   
        $statement = $this->connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) { 
            $data_fields[] = $row;
        } 
        $data_fields = json_encode($data_fields);
        $data_fields = json_decode($data_fields);  
        return $data_fields; 
    }


        function generator_content(){
            $contenido = 
 "<?php  

 require_once '../../models/postgres.php';
 %received_data = json_decode(file_get_contents('php://input')); 
 %model = null;
 require_once '../../models/auth/check.php';

if (check_session()) { 
    switch(%received_data->action){
        case 'update': 
            %model = new ".ucwords($this->name_crud)."(%data,%connect,%received_data);
            %model->update();
        break;
        case 'insert':
            %model = new ".ucwords($this->name_crud)."(%data,%connect,%received_data);
            %model->insert();
        break;
        case 'delete':
            %model = new ".ucwords($this->name_crud)."(%data,%connect,%received_data);
            %model->delete(); 
        break;
        case 'select': 
            %model = new ".ucwords($this->name_crud)."(%data,%connect,%received_data);
            %model->select();
        break;
    }
}else{
    %output = array('message' => 'Not authorized'); 
    echo json_encode(%output); 
} 

class ".ucwords($this->name_crud)." 
{   
    
    private %output = null;
    private %data = array(); 
    private %connect = null;
    private %received_data = null;
    public function __construct(%data,%connect,%received_data){
        %this->data  = %data;
        %this->connect = %connect;
        %this->received_data = %received_data;
    }

    public function insert(){
        try {
            " . $this->function_generate_insert() . "

            %statement = %this->connect->prepare(%query); 
            %statement->execute(%data);  
            %output = array('message' => 'Data Inserted'); 
            echo json_encode(%output); 
            return true;
        } catch (PDOException %exc) {
            %output = array('message' => %exc->getMessage()); 
            echo json_encode(%output); 
            return false;
        } 
    } 

    public function update(){
        try {
            " . $this->function_generate_update() . "

            %statement = %this->connect->prepare(%query); 
            %statement->execute(%data);  
            %output = array('message' => 'Data Updated'); 
            echo json_encode(%output); 
            return true;
        } catch (PDOException %exc) {
            %output = array('message' => %exc->getMessage()); 
            echo json_encode(%output); 
            return false;
        }  
    } 

    public function select(){
        try {  
            " . $this->function_generate_select() . "

        
            echo json_encode(%data); 
            return true;
        } catch (PDOException %exc) {
            %output = array('message' => %exc->getMessage()); 
            echo json_encode(%output); 
            return false;
        }  
    }
    
    public function search_union(%row,%table_origen,%fk_table_origen,%fk_table_usage){
        %data = array(); 
        try {    
            %query = 'SELECT * FROM '. %table_origen . '   WHERE '. %fk_table_origen . ' = ' .%row[%fk_table_usage] ;               
            %statement = %this->connect->prepare(%query); 
            %statement->execute(%data);   
            while (%row = %statement->fetch(PDO::FETCH_ASSOC)) {   
                    %data[] = %row;
            }  
            return %data; 
        } catch (PDOException %exc) {
            %output = array('message' => %exc->getMessage()); 
            return json_encode(%output);  
        }  
    }
    public function delete(){
        try {  
            " . $this->function_generate_delete() . " 

            %statement = %this->connect->prepare(%query); 
            %statement->execute(%data);  
            %output = array('message' => 'Data Deleted'); 
            echo json_encode(%output); 
            return true;
        } catch (PDOException %exc) {
            %output = array('message' => %exc->getMessage()); 
            echo json_encode(%output); 
            return false;
        }  
    }

  
    

} ";   
           $contenido = str_replace("%","$",$contenido); 
           return $contenido;
        } 


    } 
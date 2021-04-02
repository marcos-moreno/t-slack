<?php
class generator_file_html{

    private $field_primary_key = '';
    private $field_json = []; 
    private $name_crud = "";
    private $data_forenings_keys = array();  
    private $connect = null;
    private $nombreCRPETACLASS = "";

    public function __construct($field_json,$name_crud,$field_primary_key,$data_forenings_keys,$connect,$nombreCRPETACLASS){
        $this->field_json = $field_json; 
        $this->name_crud  = $name_crud;
        $this->data_forenings_keys = $data_forenings_keys;
        $this->field_primary_key = $field_primary_key;  
        $this->connect = $connect;
        $this->nombreCRPETACLASS = $nombreCRPETACLASS;
        
    }
    function print_table(){
        $encabezados = '';
        $columnas = '';

        foreach ($this->field_json as $field) {  
            $encabezados 
            .= '
                        <th>'.$field->column_name.'</th>
                                    ';
                                    
                                    $columnas .= '
                        <td>{{ '. $this->name_crud.'.'.$field->column_name.'}}</td>
            ';
        }  
        $cadena = '
        <br>
        <div class="panel-body"  v-if="isFormCrud==false">
            <h4>'. $this->name_crud .'</h4>
            <br>
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  
                        <li>
                            <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="numByPag" @change="paginator(1)" > 
                                <option value=5  >5</option>
                                <option value=10 >10</option>
                                <option value=15 >15</option>
                                <option value=20 >20</option>
                            </select>
                        </li>
                        <li v-for="li in paginas" class="page-item">
                            <a class="page-link" @click="paginator(li.element)" >{{ li.element }} <div v-if="li.element == paginaActual" >_</div></a> 
                        </li>
                    </ul>  
                </nav>
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_'. $this->name_crud .'()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> '.$encabezados.' 
                        <th></th> 
                    </tr>
                    <tr v-for="'. $this->name_crud .' in paginaCollection" >
                        '.$columnas.'   
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_'. $this->name_crud .'('. $this->name_crud .'.'.$this->field_primary_key.')"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_'. $this->name_crud .'('. $this->name_crud .'.'.$this->field_primary_key.')"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>';
       return  $cadena;
    }
    function print_form(){
       $cadena ='
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ' . $this->name_crud . '.' .$this->field_primary_key.' }}</label>  
            </div>';  
            foreach ($this->field_json as $field) { 
                $in_fkey = false; 

                
                foreach ($this->data_forenings_keys as $key) { 
                     if (strcmp($key->fk_table_usage, $field->column_name) == 0) { 
                        $in_fkey = true; 
                        $cadena .= " 
                                    <div class='form-group'>
                                        <label>".str_replace("_"," ",str_replace("id_","",$field->column_name))."</label> 
                                        <select class='form-control' size='1'  v-model='".$this->name_crud . "." . $field->column_name."' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in ".$key->table_origen."Collection' v-bind:value='rows.".$key->fk_table_origen."'>{{ rows }}</option>
                                        </select>
                                    </div> ";
                    }  
                }


                if ($this->field_primary_key != $field->column_name && $in_fkey == false)  {   
                    // echo $field->data_type;
                    switch ($field->data_type) {
                        case 'character varying':
                            $cadena .= "
            <div class='form-group'>
                <label>".str_replace("_"," ",str_replace("id_","",$field->column_name))."</label>
                <input type='text' class='form-control' v-model='$this->name_crud.$field->column_name' />
            </div>  ";
                            break;
                        case 'boolean':
                            $cadena .= " 
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='" .$this->name_crud.$field->column_name ." _id'   v-model='$this->name_crud.$field->column_name'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='" .$this->name_crud.$field->column_name ." _id'  >".str_replace("_"," ",str_replace("id_","",$field->column_name))."</label>
            </div> ";
                            break;
                            case 'int':
                                if ($field->column_name != "creadopor" && $field->column_name != "actualizadopor") {
            $cadena .= "
            <div class='form-group'>
                <label>".str_replace("_"," ",str_replace("id_","",$field->column_name))."</label>
                <input type='number' class='form-control' v-model='$this->name_crud.$field->column_name' />
            </div>  ";
                                } 
                                break;
                        case 'integer':
                            if ($field->column_name != "creadopor" && $field->column_name != "actualizadopor") {
                            $cadena .= "
            <div class='form-group'>
                <label>".str_replace("_"," ",str_replace("id_","",$field->column_name))."</label>
                <input type='number' class='form-control' v-model='$this->name_crud.$field->column_name' />
            </div>  ";
                            }
                            break;
                            case 'double precision':
                            $cadena .= "
            <div class='form-group'>
                <label>".str_replace("_"," ",str_replace("id_","",$field->column_name))."</label>
                <input type='number' class='form-control' v-model='$this->name_crud.$field->column_name' />
            </div>  ";
                                break;
                            case 'numeric':
                            $cadena .= "
            <div class='form-group'>
                <label>".str_replace("_"," ",str_replace("id_","",$field->column_name))."</label>
                <input type='number' class='form-control' v-model='$this->name_crud.$field->column_name' />
            </div>  ";
                                break;
                                case 'date':
                                    $cadena .= "
            <div class='form-group'>
                <label>".str_replace("_"," ",str_replace("id_","",$field->column_name))."</label>
                <input type='date' class='form-control' v-model='$this->name_crud.$field->column_name' />
            </div>  ";
                                break;
                                case 'timestamp with time zone':
                                    if ($field->column_name != "creado" && $field->column_name != "actualizado") {
                                    $cadena .= "
            <div class='form-group'>
                <label>".str_replace("_"," ",str_replace("id_","",$field->column_name))."</label>
                <input type='datetime-local' class='form-control' v-model='$this->name_crud.$field->column_name' />
            </div>  ";  
                                    }
                                break;
                        default:
                            # code...
                            break;
                    }


                }
            } 

      $cadena.='  
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_'. $this->name_crud .'()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_'. $this->name_crud .'()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>';
      return $cadena;
    }
    function generator_content(){
        $contenido = 
'<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_'. $this->name_crud .'" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30%" v-if="isFormCrud==false">
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" v-model="filter" />
                                </td> 
                                <td>
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="get'. $this->name_crud .'s()"> filtrar</button>
                                </td> 
                            </tr>
                        </table>
                    </td>
                    <td >
                        <div class="pre-scrollable" >
                            <div class="alert alert-primary" v-if="typeMessage == \'info\'" role="alert">{{msg}}</div>
                            <div class="alert alert-danger"  v-if="typeMessage == \'error\'" role="alert">{{msg}}</div>
                            <div class="alert alert-success" v-if="typeMessage == \'success\'" role="alert">{{msg}}</div>
                        </div> 
                    </td> 
                </tr>
            </table> 
        </div> 

        '.$this->print_table().'  
            '.$this->print_form().'  
    </div>
</div>
<script type="text/javascript" src="../../controller/' . $this->nombreCRPETACLASS . '/c_' . $this->name_crud . '.js"></script>
';

        // $contenido = str_replace("%","$",$contenido); 
        return $contenido;
        } 




}



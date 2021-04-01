<?php
class generator_js_controller{

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
    function generateJson_blank_model(){
        $cadena = "{"; 
        foreach ($this->field_json as $field) { 
            if ($this->field_primary_key != $field->column_name) {  
                $cadena .= $field->column_name. ":'',";  
            }else{
                $cadena .= $field->column_name. ":0,";  
            }
        } 
        $cadena .= "}"; 
        return str_replace(",}","}",$cadena);
    }
    function print_keys_metod(){
        $cadena = "";
        $colection = "";
        foreach ($this->data_forenings_keys as $key) { 
            $colection .= $key->table_origen."Collection:[],
            "; 

            $cadena .=  " 
            const response_$key->table_origen = await this.request('../../models/".$key->table_origen."/bd_".$key->table_origen.".php',{'order' : 'ORDER BY " . $key->fk_table_origen ." DESC','action' : 'select'});
            try{  
                if(response_$key->table_origen.length > 0){  
                    this.".$key->table_origen."Collection = response_$key->table_origen; 
                }  
            }catch(error){
                this.show_message('No hay ".$key->table_origen."s.','info');
            } ";   
       }
       $array = array(
        "method." => $cadena,
        "colection" => $colection,
        );         
       return $array;
    }
    
    function getFieldSearch(){
        $value = "$";
        foreach ($this->field_json as $field) { 
            if ($this->field_primary_key != $field->column_name && $field->data_type == 'character varying') {  
                $value = $value . " OR ". $field->column_name ." ILIKE '%\" + this.filter + \"%' ";   
            }
        } 
        return str_replace("$ OR", "", $value);
    }
    function generator_content(){
        $array = $this->print_keys_metod(); 
        $methodo = $array["method."];
        $colection = $array["colection"];
        $contenido =" 
var application = new Vue({
    el:'#app_". $this->name_crud ."',
    data:{ 
        ". $this->name_crud ." : null,
        ". $this->name_crud ."Collection : [],
        isFormCrud: false,
        path : '../../models/". $this->nombreCRPETACLASS ."/bd_". $this->name_crud .".php',
        typeMessage : '',
        msg:'',
        ".  $colection  ."

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async get". $this->name_crud ."s(){  
            this.". $this->name_crud ."Collection  = [];
            this.paginaCollection = [];
            let filtrarPor =  \"(". $this->getFieldSearch() ." )\";  
           const response = await this.request(this.path,{'order' : 'ORDER BY ".$this->field_primary_key." DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.". $this->name_crud ."Collection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_". $this->name_crud ."(".$this->field_primary_key."){  
            this.". $this->name_crud ." = this.search_". $this->name_crud ."ByID(".$this->field_primary_key.");
            if(this.". $this->name_crud.".".$this->field_primary_key." > 0){
                const response = await this.request(this.path,{model:this.". $this->name_crud .",'action' : 'delete'});
                this.". $this->name_crud ."Collection = response; 
                if(response.message == 'Data Deleted'){
                    await this.get". $this->name_crud ."s();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_". $this->name_crud ."(){ 
            if(this.". $this->name_crud.".".$this->field_primary_key." > 0){
                const response = await this.request(this.path,{model:this.". $this->name_crud .",'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.get". $this->name_crud ."s();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.". $this->name_crud.".".$this->field_primary_key." == 0){ 
                const response = await this.request(this.path,{model:this.". $this->name_crud .",'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.get". $this->name_crud ."s();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_". $this->name_crud ."(".$this->field_primary_key."){ 
            if(".$this->field_primary_key." > 0){
                this.". $this->name_crud ." = this.search_". $this->name_crud ."ByID(".$this->field_primary_key.");
                if(this.". $this->name_crud .".".$this->field_primary_key." > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_". $this->name_crud ."(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_". $this->name_crud ."(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_". $this->name_crud ."ByID(".$this->field_primary_key."){
            for (let index = 0; index < this.". $this->name_crud ."Collection.length; index++) {
                const element = this.". $this->name_crud ."Collection[index]; 
                if (".$this->field_primary_key." == element.".$this->field_primary_key.") { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.". $this->name_crud ." = ".$this->generateJson_blank_model().";
        },
        async request(path,jsonParameters){
            const response = await axios.post(path, jsonParameters).then(function (response) {   
                    return response.data; 
                }).catch(function (response) {  
                    return response.data;
                })
            return response; 
        },
        async fill_f_keys(){
            ".  $methodo  ."
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.". $this->name_crud ."Collection.length / this.numByPag);
            this.paginas = []; 
            if (i === 'Ant' ) {
                if (this.paginaActual == 1) {  i = 1;  }else{  i = this.paginaActual -1; } 
            }else if (i === 'Sig') { 
                if (this.paginaActual == cantidad_pages) {  i = cantidad_pages; } else { i = this.paginaActual + 1; } 
            }else{ this.paginaActual = i; } 
            this.paginaActual = i; 
            this.paginas.push({'element':'Ant'}); 
            for (let indexI = 0; indexI < cantidad_pages; indexI++) {
                this.paginas.push({'element':(indexI + 1)});
                if (indexI == (i - 1) ) { 
                    this.paginaCollection = [];  
                    let inicio = ( i == 1 ? 0 : ((i-1) *  parseInt(this.numByPag)));
                    inicio = parseInt(inicio);
                    let fin = (cantidad_pages == i ? this.". $this->name_crud ."Collection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.". $this->name_crud ."Collection[index];
                        this.paginaCollection.push(element); 
                    }  
                }  
            }  
            this.paginas.push({'element':'Sig'});
        }

    },
    async mounted() {    
    },
    async created(){
       await this.get". $this->name_crud ."s();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        "; 
        return $contenido;
    }  
}



 
var application = new Vue({
    el:'#app_campo',
    data:{ 
        campo : null,
        campoCollection : [],
        isFormCrud: false,
        path : '../../models/sp/bd_campo.php',
        typeMessage : '',
        msg:'',
        grupo_marcaCollection:[],
            tipoCollection:[],
            

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getcampos(){  
            this.campoCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY 1 DESC','action' : 'select','filter' : filtrarPor});
           console.log(response);
            try{ 
                this.show_message(response.length + ' Registro Encontrados.','success');
                this.campoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_campo(id_campo){   
            if(id_campo > 0){
                const response = await this.request(this.path,{model:{'id_campo':id_campo},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getcampos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_campo(){ 
            if(this.campo.id_campo > 0){
                this.campo.activo = (this.campo.activo == true || this.campo.activo == "true" ? 'true' : 'false'); 
                const response = await this.request(this.path,{model:this.campo,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getcampos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.campo.id_campo == 0){ 
                this.campo.activo = (this.campo.activo == true || this.campo.activo == "true" ? 'true' : 'false'); 
                const response = await this.request(this.path,{model:this.campo,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getcampos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_campo(id_campo){ 
            if(id_campo > 0){
                this.campo = this.search_campoByID(id_campo);
                if(this.campo.id_campo > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_campo(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_campo(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_campoByID(id_campo){
            for (let index = 0; index < this.campoCollection.length; index++) {
                const element = this.campoCollection[index]; 
                if (id_campo == element.id_campo) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.campo = {id_campo:0,nombre:'',descripcion:'',obligatorio:'',activo:'true',id_grupo_marca:'',id_tipo:''};
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
             
            const response_grupo_marca = await this.request('../../models/sp/bd_grupo_marca.php',{'order' : 'ORDER BY id_grupo_marca DESC','action' : 'select'});
            try{  
                if(response_grupo_marca.length > 0){  
                    this.grupo_marcaCollection = response_grupo_marca; 
                }  
            }catch(error){
                this.show_message('No hay grupo_marcas.','info');
            }  
            const response_tipo = await this.request('../../models/sp/bd_tipo.php',{'order' : 'ORDER BY id_tipo DESC','action' : 'select'});
            try{  
                if(response_tipo.length > 0){  
                    this.tipoCollection = response_tipo; 
                }  
            }catch(error){
                this.show_message('No hay tipos.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.campoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.campoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.campoCollection[index];
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
       await this.getcampos();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
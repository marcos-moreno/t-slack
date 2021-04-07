 
var application = new Vue({
    el:'#app_empresa',
    data:{ 
        empresa : null,
        empresaCollection : [],
        isFormCrud: false,
        path : '../../models/generales/bd_empresa.php',
        typeMessage : '',
        msg:'',
        

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getempresas(){  
            this.empresaCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(empresa_nombre ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_empresa DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.empresaCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_empresa(id_empresa){  
            this.empresa = this.search_empresaByID(id_empresa);
            if(this.empresa.id_empresa > 0){
                const response = await this.request(this.path,{model:this.empresa,'action' : 'delete'});
                this.empresaCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getempresas();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_empresa(){ 
            if(this.empresa.id_empresa > 0){
                const response = await this.request(this.path,{model:this.empresa,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getempresas();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.empresa.id_empresa == 0){ 
                const response = await this.request(this.path,{model:this.empresa,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getempresas();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_empresa(id_empresa){ 
            if(id_empresa > 0){
                this.empresa = this.search_empresaByID(id_empresa);
                if(this.empresa.id_empresa > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_empresa(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_empresa(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_empresaByID(id_empresa){
            for (let index = 0; index < this.empresaCollection.length; index++) {
                const element = this.empresaCollection[index]; 
                if (id_empresa == element.id_empresa) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.empresa = {id_empresa:0,id_creado:'',fecha_creado:'',empresa_nombre:'',empresa_rfc:'',empresa_observaciones:'',empresa_activo:'',id_actualizado:'',fecha_actualizado:'',id_empresa_cerberus:''};
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
            
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.empresaCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.empresaCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.empresaCollection[index];
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
       await this.getempresas();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
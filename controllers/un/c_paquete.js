 
var application = new Vue({
    el:'#app_paquete',
    data:{ 
        paquete : null,
        paqueteCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_paquete.php',
        typeMessage : '',
        msg:'',
        tipo_entregasCollection:[],
            

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getpaquetes(){  
            this.paqueteCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre_paquete ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_paquete DESC','action' : 'select','filter' : filtrarPor});
        //    console.log(response);
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.paqueteCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_paquete(id_paquete){   
            if(id_paquete > 0){
                const response = await this.request(this.path,{model:{'id_paquete':id_paquete},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getpaquetes();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_paquete(){ 
            if(this.paquete.id_paquete > 0){
                this.paquete.activo = (this.paquete.activo == true || this.paquete.activo == "true" ? 'true' : 'false'); 
                const response = await this.request(this.path,{model:this.paquete,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getpaquetes();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.paquete.id_paquete == 0){ 
                this.paquete.activo = (this.paquete.activo == true || this.paquete.activo == "true" ? 'true' : 'false'); 
                const response = await this.request(this.path,{model:this.paquete,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getpaquetes();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_paquete(id_paquete){ 
            if(id_paquete > 0){
                this.paquete = this.search_paqueteByID(id_paquete);
                if(this.paquete.id_paquete > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_paquete(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_paquete(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_paqueteByID(id_paquete){
            for (let index = 0; index < this.paqueteCollection.length; index++) {
                const element = this.paqueteCollection[index]; 
                if (id_paquete == element.id_paquete) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.paquete = {id_paquete:0,genero:'U',id_tipo_entrege:'0',nombre_paquete:'',descripcion:'',activo:'true'};
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
             
            const response_tipo_entregas = await this.request('../../models/un/bd_tipo_entregas.php',{'order' : 'ORDER BY id_tipo_entrega DESC','action' : 'select'});
            try{  
                if(response_tipo_entregas.length > 0){  
                    this.tipo_entregasCollection = response_tipo_entregas; 
                }  
            }catch(error){
                this.show_message('No hay tipo_entregass.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.paqueteCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.paqueteCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.paqueteCollection[index];
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
       await this.getpaquetes();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
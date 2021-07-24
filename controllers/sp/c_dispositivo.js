 
var application = new Vue({
    el:'#app_dispositivo',
    data:{ 
        dispositivo : null,
        dispositivoCollection : [],
        isFormCrud: false,
        path : '../../models/sp/bd_dispositivo.php',
        typeMessage : '',
        msg:'',
        grupo_marcaCollection:[],
        marcaCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getdispositivos(){  
            this.dispositivoCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_grupo_marca DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registro Encontrados.','success');
                this.dispositivoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_dispositivo(id_dispositivo){   
            if(id_dispositivo > 0){
                const response = await this.request(this.path,{model:{'id_dispositivo':id_dispositivo},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getdispositivos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_dispositivo(){ 
            if(this.dispositivo.id_dispositivo > 0){
                const response = await this.request(this.path,{model:this.dispositivo,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getdispositivos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.dispositivo.id_dispositivo == 0){ 
                const response = await this.request(this.path,{model:this.dispositivo,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getdispositivos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_dispositivo(id_dispositivo){ 
            if(id_dispositivo > 0){
                this.dispositivo = this.search_dispositivoByID(id_dispositivo);
                if(this.dispositivo.id_dispositivo > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_dispositivo(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_dispositivo(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_dispositivoByID(id_dispositivo){
            for (let index = 0; index < this.dispositivoCollection.length; index++) {
                const element = this.dispositivoCollection[index]; 
                if (id_dispositivo == element.id_dispositivo) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.dispositivo = {id_dispositivo:0,nombre:'',descripcion:'',codigo:'',mac:'',num_serie:'',id_grupo_marca:'',id_marca:''};
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
            const response_marca = await this.request('../../models/sp/bd_marca.php',{'order' : 'ORDER BY id_marca DESC','action' : 'select'});
            try{  
                if(response_marca.length > 0){  
                    this.marcaCollection = response_marca; 
                }  
            }catch(error){
                this.show_message('No hay marcas.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.dispositivoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.dispositivoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.dispositivoCollection[index];
                        this.paginaCollection.push(element); 
                    }  
                }  
            }  
            this.paginas.push({'element':'Sig'});
        },async getFieldsByGroup(){
            if (this.dispositivo.id_grupo_marca !=0) {
                console.log(this.dispositivo.id_grupo_marca);
            }
        },

    },
    async mounted() {    
    },
    async created(){
       await this.getdispositivos();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
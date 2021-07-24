 
var application = new Vue({
    el:'#app_acceso_rol',
    data:{ 
        acceso_rol : null,
        acceso_rolCollection : [],
        isFormCrud: false,
        path : '../../models/generales/bd_acceso_rol.php',
        typeMessage : '',
        msg:'',
        rolCollection:[],
            elementoCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getacceso_rols(){  
            this.acceso_rolCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(id_rol IN  (SELECT id_rol FROM rol WHERE rol ILIKE '%" + this.filter + "%') OR id_elemento IN  (SELECT id_elemento FROM elemento WHERE name ILIKE '%" + this.filter + "%') )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_acceso DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.acceso_rolCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_acceso_rol(id_acceso){  
            if(id_acceso > 0){
                const response = await this.request(this.path,{model:{'id_acceso':id_acceso},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getacceso_rols();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_acceso_rol(){ 
            if(this.acceso_rol.id_acceso > 0){
                const response = await this.request(this.path,{model:this.acceso_rol,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getacceso_rols();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.acceso_rol.id_acceso == 0){ 
                const response = await this.request(this.path,{model:this.acceso_rol,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getacceso_rols();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_acceso_rol(id_acceso){ 
            if(id_acceso > 0){
                this.acceso_rol = this.search_acceso_rolByID(id_acceso);
                if(this.acceso_rol.id_acceso > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_acceso_rol(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_acceso_rol(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_acceso_rolByID(id_acceso){
            for (let index = 0; index < this.acceso_rolCollection.length; index++) {
                const element = this.acceso_rolCollection[index]; 
                if (id_acceso == element.id_acceso) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.acceso_rol = {id_acceso:0,id_rol:'',id_elemento:''};
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
             
            const response_rol = await this.request('../../models/generales/bd_rol.php',{'order' : 'ORDER BY id_rol DESC','action' : 'select'});
            try{  
                if(response_rol.length > 0){  
                    this.rolCollection = response_rol; 
                }  
            }catch(error){
                this.show_message('No hay rols.','info');
            }  
            const response_elemento = await this.request('../../models/generales/bd_elemento.php',{'order' : 'ORDER BY id_elemento DESC','action' : 'select'});
            try{  
                if(response_elemento.length > 0){  
                    this.elementoCollection = response_elemento; 
                }  
            }catch(error){
                this.show_message('No hay elementos.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.acceso_rolCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.acceso_rolCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.acceso_rolCollection[index];
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
       await this.getacceso_rols();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
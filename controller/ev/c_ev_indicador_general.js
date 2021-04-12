 
var application = new Vue({
    el:'#app_ev_indicador_general',
    data:{ 
        ev_indicador_general : null,
        ev_indicador_generalCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_indicador_general.php',
        typeMessage : '',
        msg:'',
        

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getev_indicador_generals(){  
            this.ev_indicador_generalCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "( nombre ILIKE '%" + this.filter + "%'  OR descripcion ILIKE '%" + this.filter + "%'  OR tendencia ILIKE '%" + this.filter + "%'  OR origen ILIKE '%" + this.filter + "%'  )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY ev_indicador_general_id DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_indicador_generalCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_ev_indicador_general(ev_indicador_general_id){  
            this.ev_indicador_general = this.search_ev_indicador_generalByID(ev_indicador_general_id);
            if(this.ev_indicador_general.ev_indicador_general_id > 0){
                const response = await this.request(this.path,{model:this.ev_indicador_general,'action' : 'delete'});
                this.ev_indicador_generalCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getev_indicador_generals();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_indicador_general(){ 
            if(this.ev_indicador_general.ev_indicador_general_id > 0){
                this.ev_indicador_general.allowrepor == true || this.ev_indicador_general.allowrepor == 'true' ?  
                this.ev_indicador_general.allowrepor = 'true' : this.ev_indicador_general.allowrepor = 'false';

                this.ev_indicador_general.activo == true || this.ev_indicador_general.activo == 'true' ?  
                this.ev_indicador_general.activo = 'true' : this.ev_indicador_general.activo = 'false';
                const response = await this.request(this.path,{model:this.ev_indicador_general,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_indicador_generals();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_indicador_general.ev_indicador_general_id == 0){ 
                this.ev_indicador_general.allowrepor == true || this.ev_indicador_general.allowrepor == 'true' ?  
                this.ev_indicador_general.allowrepor = 'true' : this.ev_indicador_general.allowrepor = 'false';

                this.ev_indicador_general.activo == true || this.ev_indicador_general.activo == 'true' ?  
                this.ev_indicador_general.activo = 'true' : this.ev_indicador_general.activo = 'false';
                const response = await this.request(this.path,{model:this.ev_indicador_general,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_indicador_generals();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_indicador_general(ev_indicador_general_id){ 
            if(ev_indicador_general_id > 0){
                this.ev_indicador_general = this.search_ev_indicador_generalByID(ev_indicador_general_id);
                if(this.ev_indicador_general.ev_indicador_general_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_indicador_general(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_indicador_general(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_indicador_generalByID(ev_indicador_general_id){
            for (let index = 0; index < this.ev_indicador_generalCollection.length; index++) {
                const element = this.ev_indicador_generalCollection[index]; 
                if (ev_indicador_general_id == element.ev_indicador_general_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_indicador_general = {ev_indicador_general_id:0,nombre:'',descripcion:''
            ,tendencia:'',activo:'true',creado:'',creadopor:'',actualizado:''
            ,actualizadopor:'',origen:'',allowrepor:false};
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
            let cantidad_pages = Math.ceil(this.ev_indicador_generalCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_indicador_generalCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_indicador_generalCollection[index];
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
       await this.getev_indicador_generals();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
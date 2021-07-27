 
var application = new Vue({
    el:'#app_ev_punto_evaluar_ln',
    data:{ 
        ev_punto_evaluar_ln : null,
        ev_punto_evaluar_lnCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_punto_evaluar_ln.php',
        typeMessage : '',
        msg:'',
        ev_punto_evaluarCollection:[],
            

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getev_punto_evaluar_lns(){  
            this.ev_punto_evaluar_lnCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "( nombre ILIKE '%" + this.filter + "%'  OR icon ILIKE '%" + this.filter + "%'  )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY ev_punto_evaluar_ln_id DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_punto_evaluar_lnCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_ev_punto_evaluar_ln(ev_punto_evaluar_ln_id){   
            if(ev_punto_evaluar_ln_id > 0){
                const response = await this.request(this.path,{model:{'ev_punto_evaluar_ln_id':ev_punto_evaluar_ln_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getev_punto_evaluar_lns();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_punto_evaluar_ln(){ 
            if(this.ev_punto_evaluar_ln.ev_punto_evaluar_ln_id > 0){
                const response = await this.request(this.path,{model:this.ev_punto_evaluar_ln,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_punto_evaluar_lns();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_punto_evaluar_ln.ev_punto_evaluar_ln_id == 0){ 
                const response = await this.request(this.path,{model:this.ev_punto_evaluar_ln,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_punto_evaluar_lns();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_punto_evaluar_ln(ev_punto_evaluar_ln_id){ 
            if(ev_punto_evaluar_ln_id > 0){
                this.ev_punto_evaluar_ln = this.search_ev_punto_evaluar_lnByID(ev_punto_evaluar_ln_id);
                if(this.ev_punto_evaluar_ln.ev_punto_evaluar_ln_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_punto_evaluar_ln(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_punto_evaluar_ln(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_punto_evaluar_lnByID(ev_punto_evaluar_ln_id){
            for (let index = 0; index < this.ev_punto_evaluar_lnCollection.length; index++) {
                const element = this.ev_punto_evaluar_lnCollection[index]; 
                if (ev_punto_evaluar_ln_id == element.ev_punto_evaluar_ln_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_punto_evaluar_ln = {ev_punto_evaluar_ln_id:0,ev_punto_evaluar_id:'',nombre:'',icon:'',valor:'',creado:'',creadopor:'',actualizado:'',actualizadopor:''};
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
             
            const response_ev_punto_evaluar = await this.request('../../models/ev_punto_evaluar/bd_ev_punto_evaluar.php',{'order' : 'ORDER BY ev_punto_evaluar_id DESC','action' : 'select'});
            try{  
                if(response_ev_punto_evaluar.length > 0){  
                    this.ev_punto_evaluarCollection = response_ev_punto_evaluar; 
                }  
            }catch(error){
                this.show_message('No hay ev_punto_evaluars.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.ev_punto_evaluar_lnCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_punto_evaluar_lnCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_punto_evaluar_lnCollection[index];
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
       await this.getev_punto_evaluar_lns();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
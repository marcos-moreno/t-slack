 
var application = new Vue({
    el:'#app_ev_punto_evaluado',
    data:{ 
        ev_punto_evaluado : null,
        ev_punto_evaluadoCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_punto_evaluado.php',
        typeMessage : '',
        msg:'',
        ev_punto_evaluarCollection:[],
            ev_punto_evaluar_lnCollection:[],
            empleadoCollection:[],
            ev_evaluacionCollection:[],
            ev_evaluacion_lnCollection:[],
            

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getev_punto_evaluados(){  
            this.ev_punto_evaluadoCollection  = [];
            this.paginaCollection = [];
            const response = await this.request(this.path,{'action' : 'select','filter' : this.filter});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_punto_evaluadoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_ev_punto_evaluado(ev_punto_evaluado_id){   
            if(ev_punto_evaluado_id > 0){
                const response = await this.request(this.path,{model:{'ev_punto_evaluado_id':ev_punto_evaluado_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getev_punto_evaluados();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_punto_evaluado(){ 
            if(this.ev_punto_evaluado.ev_punto_evaluado_id > 0){
                const response = await this.request(this.path,{model:this.ev_punto_evaluado,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_punto_evaluados();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_punto_evaluado.ev_punto_evaluado_id == 0){ 
                const response = await this.request(this.path,{model:this.ev_punto_evaluado,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_punto_evaluados();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_punto_evaluado(ev_punto_evaluado_id){ 
            if(ev_punto_evaluado_id > 0){
                this.ev_punto_evaluado = this.search_ev_punto_evaluadoByID(ev_punto_evaluado_id);
                if(this.ev_punto_evaluado.ev_punto_evaluado_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_punto_evaluado(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_punto_evaluado(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_punto_evaluadoByID(ev_punto_evaluado_id){
            for (let index = 0; index < this.ev_punto_evaluadoCollection.length; index++) {
                const element = this.ev_punto_evaluadoCollection[index]; 
                if (ev_punto_evaluado_id == element.ev_punto_evaluado_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_punto_evaluado = {ev_punto_evaluado_id:0,ev_punto_evaluar_id:'',ev_punto_evaluar_ln_id:'',id_empleado:'',ev_evaluacion_id:'',ev_evaluacion_ln_id:'',creado:'',actualizado:'',creadopor:'',actualizadopor:''};
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
            const response_ev_punto_evaluar_ln = await this.request('../../models/ev_punto_evaluar_ln/bd_ev_punto_evaluar_ln.php',{'order' : 'ORDER BY ev_punto_evaluar_ln_id DESC','action' : 'select'});
            try{  
                if(response_ev_punto_evaluar_ln.length > 0){  
                    this.ev_punto_evaluar_lnCollection = response_ev_punto_evaluar_ln; 
                }  
            }catch(error){
                this.show_message('No hay ev_punto_evaluar_lns.','info');
            }  
            const response_empleado = await this.request('../../models/empleado/bd_empleado.php',{'order' : 'ORDER BY id_empleado DESC','action' : 'select'});
            try{  
                if(response_empleado.length > 0){  
                    this.empleadoCollection = response_empleado; 
                }  
            }catch(error){
                this.show_message('No hay empleados.','info');
            }  
            const response_ev_evaluacion = await this.request('../../models/ev_evaluacion/bd_ev_evaluacion.php',{'order' : 'ORDER BY ev_evaluacion_id DESC','action' : 'select'});
            try{  
                if(response_ev_evaluacion.length > 0){  
                    this.ev_evaluacionCollection = response_ev_evaluacion; 
                }  
            }catch(error){
                this.show_message('No hay ev_evaluacions.','info');
            }  
            const response_ev_evaluacion_ln = await this.request('../../models/ev_evaluacion_ln/bd_ev_evaluacion_ln.php',{'order' : 'ORDER BY ev_evaluacion_ln_id DESC','action' : 'select'});
            try{  
                if(response_ev_evaluacion_ln.length > 0){  
                    this.ev_evaluacion_lnCollection = response_ev_evaluacion_ln; 
                }  
            }catch(error){
                this.show_message('No hay ev_evaluacion_lns.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.ev_punto_evaluadoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_punto_evaluadoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_punto_evaluadoCollection[index];
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
       await this.getev_punto_evaluados();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
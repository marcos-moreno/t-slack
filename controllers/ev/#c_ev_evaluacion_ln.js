 
var application = new Vue({
    el:'#app_ev_evaluacion_ln',
    data:{ 
        ev_evaluacion_ln : null,
        ev_evaluacion_lnCollection : [],
        isFormCrud_ln: false,
        path : '../../models/ev/bd_ev_evaluacion_ln.php',
        typeMessage : '',
        msg:'',
        ev_evaluacionCollection:[],
        empleadoCollection:[],
        ev_puestoCollection:[],
            

        //paginador
        numByPag_ln : 15, 
        paginas_ln : [],
        paginaCollection_ln : [],
        paginaActual_ln : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getev_evaluacion_lns(){  
            this.ev_evaluacion_lnCollection  = [];
            this.paginaCollection_ln = [];
            const response = await this.request(this.path,{'action' : 'select','filter' : this.filter});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_evaluacion_lnCollection = response;
                this.paginaCollection_ln = response;
                this.paginator_ln(1);  
                this.isFormCrud_ln=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud_ln=false;
            } 
        }, 
        async delete_ev_evaluacion_ln(ev_evaluacion_ln_id){   
            if(ev_evaluacion_ln_id > 0){
                const response = await this.request(this.path,{model:{'ev_evaluacion_ln_id':ev_evaluacion_ln_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getev_evaluacion_lns();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_evaluacion_ln(){ 
            if(this.ev_evaluacion_ln.ev_evaluacion_ln_id > 0){
                const response = await this.request(this.path,{model:this.ev_evaluacion_ln,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_evaluacion_lns();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty_ln();
                    this.isFormCrud_ln = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_evaluacion_ln.ev_evaluacion_ln_id == 0){ 
                const response = await this.request(this.path,{model:this.ev_evaluacion_ln,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_evaluacion_lns();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty_ln();
                    this.isFormCrud_ln = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_evaluacion_ln(ev_evaluacion_ln_id){ 
            if(ev_evaluacion_ln_id > 0){
                this.ev_evaluacion_ln = this.search_ev_evaluacion_lnByID(ev_evaluacion_ln_id);
                if(this.ev_evaluacion_ln.ev_evaluacion_ln_id > 0){
                    this.isFormCrud_ln = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_evaluacion_ln(){  
            this.model_empty_ln();
            this.isFormCrud_ln = true;
        },  
        cancel_ev_evaluacion_ln(){  
            this.model_empty_ln();
            this.isFormCrud_ln = false;
        },  
        search_ev_evaluacion_lnByID(ev_evaluacion_ln_id){
            for (let index = 0; index < this.ev_evaluacion_lnCollection.length; index++) {
                const element = this.ev_evaluacion_lnCollection[index]; 
                if (ev_evaluacion_ln_id == element.ev_evaluacion_ln_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty_ln(){
            this.ev_evaluacion_ln = {ev_evaluacion_ln_id:0,ev_evaluacion_id:'',id_empleado:'',ev_puesto_id:'',caalificacion:'',estado_atributo:'',creado:'',actualizado:'',creadopor:'',actualizadopor:''};
        }, 
        async fill_f_keys(){
            const response_empleado = await this.request('../../models/empleado/bd_empleado.php',{'order' : 'ORDER BY id_empleado DESC','action' : 'select'});
            try{  
                if(response_empleado.length > 0){  
                    this.empleadoCollection = response_empleado; 
                }  
            }catch(error){
                this.show_message('No hay empleados.','info');
            }  
            const response_ev_puesto = await this.request('../../models/ev_puesto/bd_ev_puesto.php',{'order' : 'ORDER BY ev_puesto_id DESC','action' : 'select'});
            try{  
                if(response_ev_puesto.length > 0){  
                    this.ev_puestoCollection = response_ev_puesto; 
                }  
            }catch(error){
                this.show_message('No hay ev_puestos.','info');
            } 
        },paginator_ln(i){ 
            let cantidad_pages = Math.ceil(this.ev_evaluacion_lnCollection.length / this.numByPag_ln);
            this.paginas_ln = []; 
            if (i === 'Ant' ) {
                if (this.paginaActual == 1) {  i = 1;  }else{  i = this.paginaActual_ln -1; } 
            }else if (i === 'Sig') { 
                if (this.paginaActual_ln == cantidad_pages) {  i = cantidad_pages; } else { i = this.paginaActual_ln + 1; } 
            }else{ this.paginaActual_ln = i; } 
            this.paginaActual_ln = i; 
            this.paginas_ln.push({'element':'Ant'}); 
            for (let indexI = 0; indexI < cantidad_pages; indexI++) {
                this.paginas_ln.push({'element':(indexI + 1)});
                if (indexI == (i - 1) ) { 
                    this.paginaCollection_ln = [];  
                    let inicio = ( i == 1 ? 0 : ((i-1) *  parseInt(this.numByPag_ln)));
                    inicio = parseInt(inicio);
                    let fin = (cantidad_pages == i ? this.ev_evaluacion_lnCollection.length : (parseInt(inicio) + parseInt(this.numByPag_ln)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_evaluacion_lnCollection[index];
                        this.paginaCollection_ln.push(element); 
                    }  
                }  
            }  
            this.paginas_ln.push({'element':'Sig'});
        }

    },
    async mounted() {    
    },
    async created(){
       await this.getev_evaluacion_lns();
       await this.model_empty_ln();
       await this.fill_f_keys();
       this.paginator_ln(1);
    }
}); 
        
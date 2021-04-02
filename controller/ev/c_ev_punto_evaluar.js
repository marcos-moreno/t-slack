 
var application = new Vue({
    el:'#app_ev_punto_evaluar',
    data:{ 
        ev_punto_evaluar : null,
        ev_punto_evaluarCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_punto_evaluar.php',
        typeMessage : '',
        msg:'',
        ev_indicador:{},
            ev_tipo_capturaCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
        display_modal_rubros : false,
        load : false,
        isFormCrudRubro: false,
        ev_punto_evaluar_ln : {},
        ev_punto_evaluar_lnCollection : [],

    },
    methods:{
// Rubros Start
        async openModalRubros(model){
            this.load = true;
            this.ev_punto_evaluar = model;
            this.display_modal_rubros = true;
            const response = await this.request('../../models/ev/bd_ev_punto_evaluar_ln.php',
            {'order' : 'ORDER BY ev_punto_evaluar_ln_id DESC','action' : 'select','filter' : "ev_punto_evaluar_id= " + model.ev_punto_evaluar_id});
            this.ev_punto_evaluar_lnCollection = response
            console.log(response);
            this.load = false;
        },
        async delete_ev_punto_evaluar_ln(ev_punto_evaluar_ln_id){  
            this.ev_punto_evaluar_ln = this.search_ev_punto_evaluar_lnByID(ev_punto_evaluar_ln_id);
            if(this.ev_punto_evaluar_ln.ev_punto_evaluar_ln_id > 0){
                const response = await this.request(this.path,{model:this.ev_punto_evaluar_ln,'action' : 'delete'});
                this.ev_punto_evaluar_lnCollection = response; 
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
                    this.isFormCrudRubro = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_punto_evaluar_ln.ev_punto_evaluar_ln_id == 0){ 
                const response = await this.request(this.path,{model:this.ev_punto_evaluar_ln,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_punto_evaluar_lns();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrudRubro = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_punto_evaluar_ln(ev_punto_evaluar_ln_id){ 
            if(ev_punto_evaluar_ln_id > 0){
                this.ev_punto_evaluar_ln = this.search_ev_punto_evaluar_lnByID(ev_punto_evaluar_ln_id);
                if(this.ev_punto_evaluar_ln.ev_punto_evaluar_ln_id > 0){
                    this.isFormCrudRubro = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
// Rubros ENd


        async getev_punto_evaluars(){  
            this.ev_punto_evaluarCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "( nombre ILIKE '%" + this.filter + "%'  OR descripcion ILIKE '%" + this.filter + "%'  )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY ev_punto_evaluar_id DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_punto_evaluarCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_ev_punto_evaluar(ev_punto_evaluar_id){  
            this.ev_punto_evaluar = this.search_ev_punto_evaluarByID(ev_punto_evaluar_id);
            if(this.ev_punto_evaluar.ev_punto_evaluar_id > 0){
                const response = await this.request(this.path,{model:this.ev_punto_evaluar,'action' : 'delete'});
                this.ev_punto_evaluarCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getev_punto_evaluars();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_punto_evaluar(){ 
            if(this.ev_punto_evaluar.ev_punto_evaluar_id > 0){
                this.ev_punto_evaluar.ev_indicador_id = this.ev_indicador.ev_indicador_id;
                const response = await this.request(this.path,{model:this.ev_punto_evaluar,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_punto_evaluars();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_punto_evaluar.ev_punto_evaluar_id == 0){ 
                this.ev_punto_evaluar.ev_indicador_id = this.ev_indicador.ev_indicador_id;
                const response = await this.request(this.path,{model:this.ev_punto_evaluar,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_punto_evaluars();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_punto_evaluar(ev_punto_evaluar_id){ 
            if(ev_punto_evaluar_id > 0){
                this.ev_punto_evaluar = this.search_ev_punto_evaluarByID(ev_punto_evaluar_id);
                if(this.ev_punto_evaluar.ev_punto_evaluar_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_punto_evaluar(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_punto_evaluar(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_punto_evaluarByID(ev_punto_evaluar_id){
            for (let index = 0; index < this.ev_punto_evaluarCollection.length; index++) {
                const element = this.ev_punto_evaluarCollection[index]; 
                if (ev_punto_evaluar_id == element.ev_punto_evaluar_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_punto_evaluar = {ev_punto_evaluar_id:0,ev_indicador_id:this.ev_indicador.ev_indicador_id,ev_tipo_captura_id:''
            ,nombre:'',descripcion:'',porcentaje_tl:'',creado:'',creadopor:'',actualizado:'',actualizadopor:'',min_escala:1,max_escala:10,incremento:1};
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
            const response_ev_tipo_captura = await this.request('../../models/ev/bd_ev_tipo_captura.php',{'order' : 'ORDER BY ev_tipo_captura_id DESC','action' : 'select'});
            try{  
                if(response_ev_tipo_captura.length > 0){  
                    this.ev_tipo_capturaCollection = response_ev_tipo_captura; 
                }  
            }catch(error){
                this.show_message('No hay ev_tipo_capturas.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.ev_punto_evaluarCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_punto_evaluarCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_punto_evaluarCollection[index];
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
        let ev_indicador_id = document.getElementById("ev_indicador_id").value;
        if (!isNaN(ev_indicador_id) && ev_indicador_id > 0) {
            const ev_indicador = await this.request('../../models/ev/bd_ev_indicador.php',{'action' : 'select','filter' : ' ev_indicador_id = ' + ev_indicador_id});
            if (ev_indicador[0].ev_indicador_id > 0) {
                this.ev_indicador = ev_indicador[0];
            }else{
                location.href="v_ev_puesto_nivel.php";
            } 
        } else {
            location.href="v_ev_puesto_nivel.php";
        }
       await this.getev_punto_evaluars();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
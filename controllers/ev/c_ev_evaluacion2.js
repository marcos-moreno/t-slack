 
var application = new Vue({
    el:'#app_ev_evaluacion',
    data:{ 
        is_load : false,
        text_modal : "",
        ev_evaluacion : {},
        ev_evaluacionCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_evaluacion.php',
        typeMessage : '',
        msg:'',
        periodoCollection:[],
        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador
        filter : '',
        evaluador : {},
        is_evaluacion : true,
        is_evaluacion_ln : false, 
        // :::: Line
        isFormCrud_ln : false,
        filter_ln : "",
        empleadoCollection:[],
        empleadoCollectionfiltro : [],
        departamentos : [],
        ev_puestoCollection:[],
        ev_evaluacion_ln : null,
        ev_evaluacion_lnCollection : [],
        path_ln : '../../models/ev/bd_ev_evaluacion_ln.php',
        //paginador
        numByPag_ln : 15, 
        paginas_ln : [],
        paginaCollection_ln : [],
        paginaActual_ln : 1,
        ////paginador  
        empleadoByln : 0,

        filtroEmpleado : "", 

        //::::::::::Evaluacion
        itIsEvaluation : false,
        indicadoresEvaluacion : [],
    },
    methods:{
        //::::::::::::::Evaluar
        async evaluar_reportes(ev_evaluacion_ln,indicador){ 
            const response_evaluar_reportes = await this.request(this.path,{
                'action' : 'evaluar_con_reportes'
                ,'id_empleado' :  ev_evaluacion_ln.id_empleado
                ,'ev_evaluacion_id' : ev_evaluacion_ln.ev_evaluacion_id
                ,'ev_indicador_general_id' : indicador.ev_indicador_general_id
            });  
            if (response_evaluar_reportes.status == 'success') {
                indicador.calificacion_indicador = response_evaluar_reportes.data.evaluar_con_reportes;
                this.show_message("Procesado",'success'); 
            } else {
                indicador.calificacion_indicador = 'Error procesando';
                this.show_message("No se pudo procesar, error -> " + response_evaluar_reportes.data,'error'); 
            }
        },
        //::::::::::::::Evaluar
       
        // ::::::Indicadores:::::::::::::::::
        async getIncidenciasCerberus(inicio,fin,idEmpleado_cerberus){
            const response = await axios
            .get(configEP.EndPointCerberus + 'no_faltas_retardos'
                ,{
                    headers:{
                        "token" : localStorage.getItem("API_KEY_CERBERUS")
                    }, 
                    params:{ 
                            fechaInicio: inicio,
                            fechaFin:fin,
                            idEmpleado: idEmpleado_cerberus
                        }
                })
            .then(function(response){ return response.data;})
            .catch(function(response){ return response;}); 
            if (response.status == "success" && response.data.length > 0) {
                return response.data[0];
            }else{
                return {no_retardos: 0, no_faltas: 0, idEmpleado: idEmpleado_cerberus};
            }
        },
        async procesar_evaluacion(ev_evaluacion_ln){
            this.is_load = true;
            this.text_modal = "estamos calculando la evaluación del colaborador."
            let jsonResponce = await this.getIncidenciasCerberus(
                this.ev_evaluacion.periodo[0].inicio_periodo,
                this.ev_evaluacion.periodo[0].fin_periodo,
                ev_evaluacion_ln.empleado[0].id_cerberus_empleado
            );
            const response_evaluar_reportes = await this.request(this.path,{
                'action' : 'procesar_evaluacion'
                ,'id_empleado' :  ev_evaluacion_ln.id_empleado
                ,'ev_evaluacion_id' : ev_evaluacion_ln.ev_evaluacion_id 
                ,'ev_evaluacion_ln_id' : ev_evaluacion_ln.ev_evaluacion_ln_id 
                ,'no_faltas' : jsonResponce.no_faltas
                ,'no_retardos' : jsonResponce.no_retardos
            });
            if (response_evaluar_reportes.status == 'success') {
                ev_evaluacion_ln.calificacion = response_evaluar_reportes.data.procesar_evaluacion;
                this.show_message("Evaluaciones Procesadas",'success'); 
            } else { 
                this.show_message("No se pudo procesar, error -> " + response_evaluar_reportes.data,'error'); 
            }
        },
        async save_dat_point(
                data_save
                ,ev_indicador_general_id 
                ,id_empleado
                ,ev_evaluacion_ln_id
                ,ev_evaluacion_id 
            ){
            this.is_load = true;
            const result_save_point = await this.request(this.path,{
                'action' : 'save_dat_point',
                'points' : JSON.stringify(data_save)
                ,'ev_indicador_general_id' : ev_indicador_general_id
                ,'id_empleado' : id_empleado
                ,'ev_evaluacion_ln_id' : ev_evaluacion_ln_id
                ,'ev_evaluacion_id' : ev_evaluacion_id
            }); 
            await this.show_indicadores(this.ev_evaluacion_ln);
        },
        async show_indicadores(ev_evaluacion_ln){
            if (ev_evaluacion_ln.estado[0].value == "BO") {
                await this.procesar_evaluacion(ev_evaluacion_ln);
                this.ev_evaluacion_ln = ev_evaluacion_ln;
                this.isFormCrud_ln = false;
                this.is_evaluacion = false;
                this.is_evaluacion_ln = false;
                this.itIsEvaluation = true;
                const response = await this.request('../../models/ev/bd_ev_indicador_puesto.php',{
                    'action' : 'search_employe_indicadores',
                    'id_empleado' : ev_evaluacion_ln.id_empleado,
                    'ev_evaluacion_ln_id' : ev_evaluacion_ln.ev_evaluacion_ln_id,
                    'ev_evaluacion_id' : ev_evaluacion_ln.ev_evaluacion_id
                });
                if (response.length > 0) {
                    this.indicadoresEvaluacion = response;
                } else {
                    this.indicadoresEvaluacion = [];
                }
            }else
                this.show_message('La evaluación ya no esta disponible.','error');
            
            this.is_load = false;
        },
        // ::::::Indicadores:::::::::::::::::

        ///::::::Line:::::::::
        async getev_evaluacion_lns(){
            this.ev_evaluacion_lnCollection  = [];
            this.paginaCollection_ln = [];
            const response = await this.request(this.path_ln
            ,{'action' : 'select','ev_evaluacion_id' : this.ev_evaluacion.ev_evaluacion_id,filter : this.filter_ln});
            // console.log(response);
            try{
                this.show_message(response.length + ' Empleados Encontrados.','success');
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
                const response = await this.request(this.path_ln,{model:{'ev_evaluacion_ln_id':ev_evaluacion_ln_id},'action' : 'delete'});
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
        paginator_ln(i){ 
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
        },
       
        async buscarValorEmpleado(){
            this.empleadoCollectionfiltro = [];
            let asignado = false;
            for (let index = 0; index < this.empleadoCollection.length; index++) {
                const element = this.empleadoCollection[index];
                let nomCompuesto = element.paterno + ' ' + element.materno + ' ' + element.nombre;
                try {
                    if (nomCompuesto.toUpperCase().includes(this.filtroEmpleado.toUpperCase())) {
                        this.empleadoCollectionfiltro.push(element);
                        if (asignado==false) {
                            this.ev_evaluacion_ln.id_empleado = element.id_empleado;
                            this.seleccionEmpleado();
                            asignado = true; 
                        }
                    } 
                } catch (error) {
                    console.log(error);
                    this.empleadoCollectionfiltro = [];
                }
            }
        },
        async save_ev_evaluacion_ln(){
            this.ev_evaluacion_ln.ev_puesto_id = this.empleadoByln.ev_puesto_id;
            if (this.ev_evaluacion_ln.ev_puesto_id == null || this.ev_evaluakcion_ln.ev_puesto_id == 0 || this.ev_evaluacion_ln.ev_puesto_id == '') {
                this.show_message('El empleado no tiene asignado un puesto.','error');
                return;
            }
            if(this.ev_evaluacion_ln.ev_evaluacion_ln_id > 0){
                const response = await this.request(this.path_ln,{model:this.ev_evaluacion_ln,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_evaluacion_lns();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty_ln();
                    this.isFormCrud_ln = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_evaluacion_ln.ev_evaluacion_ln_id == 0){ 
                const response = await this.request(this.path_ln,{model:this.ev_evaluacion_ln,'action' : 'insert'}); 
                if(response.message == 'Data Inserted'){
                    await this.getev_evaluacion_lns();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty_ln();
                    this.isFormCrud_ln = false;
                }else{
                    try {
                        if (response.data.errorInfo[0] == "23505") {
                            this.show_message('El colaborador ya tiene una evaluación asignada.','error');
                        }else{
                            this.show_message(response.message,'error');
                        }
                    } catch (error) {
                        this.show_message(response.message,'error'); 
                    } 
                }
            }
        },
        cancel_ev_evaluacion_ln(){  
            this.model_empty_ln();
            this.isFormCrud_ln = false;
        },   
        seleccionEmpleado(){
            for (let i = 0; i < this.empleadoCollection.length; i++) {
                const element = this.empleadoCollection[i];
                if (this.ev_evaluacion_ln.id_empleado == element.id_empleado) {
                    this.empleadoByln = element;
                }
            } 
        },
        add_ev_evaluacion_ln(){
            this.model_empty_ln();
            this.isFormCrud_ln = true;
        },
        model_empty_ln(){
            this.ev_evaluacion_ln = {
                ev_evaluacion_ln_id:0,ev_evaluacion_id:this.ev_evaluacion.ev_evaluacion_id
                ,id_empleado:'',ev_puesto_id:'',calificacion:0,estado_atributo:30,creado:''
                ,actualizado:'',creadopor:'',actualizadopor:''
            };
        },  
        //:::::::Line:::::::::


        display_line(model){
            this.ev_evaluacion = model;
            this.is_evaluacion = false;
            this.is_evaluacion_ln = true;
            this.getev_evaluacion_lns();
            // this.paginator_ln(1);
        },
        formatDate(dates) {
            if (dates === undefined)return "Error de Fecha" 
            try {
                var month= ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Nobiembre","Diciembre"];  
                let date = new Date(Date.parse(dates));  
                return `${date.getDate()} de ${month[date.getMonth()]} del ${date.getFullYear()}`;
            } catch (error) {
                console.log(error);
                return "Error de Fecha";
            }
        }, 
        async getev_evaluacions(){  
            this.ev_evaluacionCollection  = [];
            this.paginaCollection = [];
            const response = await this.request(this.path,{'action' : 'select','filter' : this.filter});
            try{ 
                this.show_message(response.length + ' Evaluaciones Encontradas.','success');
                this.ev_evaluacionCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 

        async delete_ev_evaluacion(ev_evaluacion_id){   
            if(ev_evaluacion_id > 0){
                const response = await this.request(this.path,{model:{'ev_evaluacion_id':ev_evaluacion_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getev_evaluacions();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_evaluacion(){ 
            if(this.ev_evaluacion.ev_evaluacion_id > 0){
                const response = await this.request(this.path,{model:this.ev_evaluacion,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_evaluacions();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_evaluacion.ev_evaluacion_id == 0){ 
                const response = await this.request(this.path,{model:this.ev_evaluacion,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_evaluacions();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_evaluacion(ev_evaluacion_id){ 
            if(ev_evaluacion_id > 0){
                this.ev_evaluacion = this.search_ev_evaluacionByID(ev_evaluacion_id);
                if(this.ev_evaluacion.ev_evaluacion_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_evaluacion(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_evaluacion(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_evaluacionByID(ev_evaluacion_id){
            for (let index = 0; index < this.ev_evaluacionCollection.length; index++) {
                const element = this.ev_evaluacionCollection[index]; 
                if (ev_evaluacion_id == element.ev_evaluacion_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 10000);
        },model_empty(){
            this.ev_evaluacion = {
                ev_evaluacion_id:0,
                id_lider:this.evaluador.id_empleado,
                periodo_id:null,creado:'',actualizado:'',creadopor:'',actualizadopor:'',nombre:''
            };
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
            let responce = await this.request(
                '../../models/ev/bd_ev_atributo.php',
                {'action' : 'select','valor' : 'estado_evaluacion'}
            );
            try{
                if(responce.length > 0){
                    this.estados = responce;
                }else
                    this.estados = [];
            }catch(error){
                console.log(error); 
            }     

            const response_periodo = await this.request('../../models/admin/bd_periodo.php',
            {'action' : 'select','filter':'EV', activo : true});
            try{  
                if(response_periodo.length > 0){  
                    this.periodoCollection = response_periodo; 
                }  
            }catch(error){
                this.show_message('No hay periodos.','info');
            }  
            const response_empleado = await this.request('../../models/admin/bd_empleado.php',{'action' : 'gteEmpleadosByLider'});
            try{
                if(response_empleado.length > 0){
                    this.empleadoCollection = response_empleado;
                    this.empleadoCollectionfiltro = response_empleado;
                }
            }catch(error){
                this.show_message('No hay empleados.','info');
            }
            const response_ev_puesto = await this.request('../../models/ev/bd_ev_puesto.php',{'order' : 'ORDER BY ev_puesto_id DESC','action' : 'select'});
            try{
                if(response_ev_puesto.length > 0){
                    this.ev_puestoCollection = response_ev_puesto; 
                }
            }catch(error){
                this.show_message('No hay ev_puestos.','info');
            }
            const responseDepas = await this.request('../../models/admin/bd_departamento.php',
            {'action' : 'getByLider'}); 
            try{  
                if(responseDepas.length > 0){  
                    this.departamentos = responseDepas; 
                }  
            }catch(error){
                this.show_message('No se encontrarón Departamentos.','info');
            }  
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.ev_evaluacionCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_evaluacionCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_evaluacionCollection[index];
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
        this.evaluador = await this.request('../../models/admin/bd_empleado.php',{'action' : 'searchBySession'});
        if (this.evaluador.length > 0) {
            this.evaluador = this.evaluador[0];
        } 
        await this.getev_evaluacions();
        await this.model_empty();
        this.fill_f_keys();
        this.paginator(1);
    }
}); 
        
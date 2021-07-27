 
var application = new Vue({
    el:'#app_ev_reporte',
    data:{ 
        ev_reporte : null,
        ev_reporteCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_reporte.php',
        typeMessage : '',
        msg:'',
        empleadoCollection:[],
        ev_indicador_puestoCollection:[],
        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
        filtroLider:'',
        empleadoCollectionfiltro : [],
        empleadoSelected_id :0,
        empresas : [],
        empresa_id_filter:0,
        segmento_id_filter:0,
        segmentoFilterCollection:[],
        depas:[]
    },
    methods:{
        async buscarValorEmpleado(){
            this.empleadoCollectionfiltro = [];
            let asignado = false;
            for (let index = 0; index < this.empleadoCollection.length; index++) {
                const element = this.empleadoCollection[index];
                let nomCompuesto = element.paterno + ' ' + element.materno + ' ' + element.nombre;
                try {
                    if (this.segmento_id_filter == 0) {
                        if (nomCompuesto.toUpperCase().includes(this.filtroLider.toUpperCase())) {
                            this.empleadoCollectionfiltro.push(element);
                            if (asignado==false) {
                                this.empleadoSelected_id = element.id_empleado;
                                this.getev_reportes();
                                asignado = true;
                            } 
                       } 
                    }else{
                        if (nomCompuesto.toUpperCase().includes(this.filtroLider.toUpperCase()) 
                        && element.id_segmento == this.segmento_id_filter) {
                            this.empleadoCollectionfiltro.push(element);
                            if (asignado==false) {
                                this.empleadoSelected_id = element.id_empleado;
                                this.getev_reportes();
                                asignado = true;
                            } 
                        }
                    } 
                } catch (error) {
                    console.log(error);
                    this.empleadoCollectionfiltro = [];
                } 
            }
        },
        async get_ev_indicador(){
            const response_ev_indicador_puesto = await this.request('../../models/ev/bd_ev_indicador_puesto.php'
            ,{'action' : 'selectByEmployee'
            ,'model':{"id_empleado":this.empleadoSelected_id}});
            try{  
                if(response_ev_indicador_puesto.length > 0){  
                    this.ev_indicador_puestoCollection = response_ev_indicador_puesto; 
                }  
            }catch(error){
                this.show_message('No hay indicadores.','info');
            } 
        },
        async get_segmentosFilter(){
            this.empleadoCollectionfiltro = [];
            const response_segmento = await this.request('../../models/admin/bd_segmento.php'
            ,{'order' : 'ORDER BY id_segmento ASC'
            ,'action' : 'select',filter:" id_empresa = " + this.empresa_id_filter});
            try{  
                if(response_segmento.length > 0){  
                    this.segmentoFilterCollection = response_segmento; 
                }  
            }catch(error){
                this.show_message('No hay segmentos.','info');
            }  
        }, 
        async getev_reportes(){ 
            this.get_ev_indicador(); 
            this.ev_reporteCollection  = [];
            this.paginaCollection = []; 
            const response = await this.request(this.path,
                {'order' : 'ORDER BY fecha DESC','action' : 'select'
                ,'model':{'id_empleado':this.empleadoSelected_id} });
            try{ 
                this.show_message(response.length + ' Reportes Encontrados.','success');
                this.ev_reporteCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            }
        }, 
        async delete_ev_reporte(ev_reporte_id){   
            if(ev_reporte_id > 0){
                const response = await this.request(this.path,{model:{ev_reporte_id:'ev_reporte_id'},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getev_reportes();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_reporte(){ 
            if(this.ev_reporte.ev_reporte_id > 0){
                this.ev_reporte.id_empleado = this.empleadoSelected_id;
                const response = await this.request(this.path,{model:this.ev_reporte,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_reportes();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_reporte.ev_reporte_id == 0){ 
                this.ev_reporte.id_empleado = this.empleadoSelected_id;
                const response = await this.request(this.path,{model:this.ev_reporte,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_reportes();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_reporte(ev_reporte_id){ 
            if(ev_reporte_id > 0){
                this.ev_reporte = this.search_ev_reporteByID(ev_reporte_id);
                if(this.ev_reporte.ev_reporte_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_reporte(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_reporte(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_reporteByID(ev_reporte_id){
            for (let index = 0; index < this.ev_reporteCollection.length; index++) {
                const element = this.ev_reporteCollection[index]; 
                if (ev_reporte_id == element.ev_reporte_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_reporte = {ev_reporte_id:0,descripcion:'',fecha:'',id_empleado:'',ev_indicador_puesto_id:'',id_lider:'',creado:'',creadopor:'',actualizado:'',actualizadopor:''};
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
            const response_empleado = await this.request('../../models/admin/bd_empleado.php'
            ,{'order' : 'ORDER BY id_empleado DESC'
            ,'action' : 'select'
            ,filter:" activo='true' AND id_segmento IN (SELECT id_segmento from segmento WHERE id_empresa IN (1,2,3))"});
            try{  
                if(response_empleado.length > 0){  
                    this.empleadoCollection = response_empleado; 
                }  
            }catch(error){
                this.show_message('No hay empleados.','info');
            }  
         
            const response_empresa = await this.request('../../models/generales/bd_empresa.php'
            ,{filter:" id_empresa IN (1,2,3)",'order' : 'ORDER BY empresa_observaciones DESC'
            ,'action' : 'select'}); 
            try{  
                if(response_empresa.length > 0){  
                    this.empresas = response_empresa; 
                }  
            }catch(error){
                this.show_message('No se encontrarón Empresas.','info');
            }  

            const responseDepas = await this.request('../../models/admin/bd_departamento.php',{'action' : 'select','filter' : ' id_empresa IN (1,2,3)'});
            try{  
                if(responseDepas.length > 0){  
                    this.depas = responseDepas; 
                }  
            }catch(error){
                this.show_message('No se encontrarón Departamentos.','info');
            }  
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.ev_reporteCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_reporteCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_reporteCollection[index];
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
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
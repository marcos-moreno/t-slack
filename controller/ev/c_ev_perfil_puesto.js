 
var application = new Vue({
    el:'#app_ev_perfil_puesto',
    data:{ 
        ev_perfil_puesto : null,
        ev_perfil_puestoCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_perfil_puesto.php',
        typeMessage : '',
        msg:'',
        tabuladorCollection:[], 
        ev_atr_genero_Collection:[],
        ev_atr_estado_civilCollection:[],
        ev_atr_grado_avanceCollection:[],
        ev_atrnivel_estudioCollection:[],
        ev_atr_idiomaCollection:[],
        ev_puestoCollection:[],

        //paginador
        numByPag : 20, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getev_perfil_puestos(){  
            this.ev_perfil_puestoCollection  = [];
            this.paginaCollection = [];
            const response = await this.request(this.path,{'action' : 'select','filter' : this.filter});
            try{
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_perfil_puestoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            }
        }, 
        async delete_ev_perfil_puesto(ev_perfil_puesto_id){  
            this.ev_perfil_puesto = this.search_ev_perfil_puestoByID(ev_perfil_puesto_id);
            if(this.ev_perfil_puesto.ev_perfil_puesto_id > 0){
                const response = await this.request(this.path,{model:this.ev_perfil_puesto,'action' : 'delete'});
                this.ev_perfil_puestoCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getev_perfil_puestos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_perfil_puesto(){ 
            if(this.ev_perfil_puesto.ev_perfil_puesto_id > 0){
                const response = await this.request(this.path,{model:this.ev_perfil_puesto,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_perfil_puestos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_perfil_puesto.ev_perfil_puesto_id == 0){ 
                const response = await this.request(this.path,{model:this.ev_perfil_puesto,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_perfil_puestos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_perfil_puesto(ev_perfil_puesto_id){ 
            if(ev_perfil_puesto_id > 0){
                this.ev_perfil_puesto = this.search_ev_perfil_puestoByID(ev_perfil_puesto_id);
                if(this.ev_perfil_puesto.ev_perfil_puesto_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        async get_atributo(valor){
            let responce = await this.request(
                '../../models/ev/bd_ev_atributo.php',
                {'action' : 'select','valor' : valor}
            );
            try{
                if(responce.length > 0){
                    return responce;
                }else
                    return [];
            }catch(error){
                console.log(error);
                return [];
            }    
        },
        add_ev_perfil_puesto(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_perfil_puesto(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_perfil_puestoByID(ev_perfil_puesto_id){
            for (let index = 0; index < this.ev_perfil_puestoCollection.length; index++) {
                const element = this.ev_perfil_puestoCollection[index]; 
                if (ev_perfil_puesto_id == element.ev_perfil_puesto_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_perfil_puesto = {ev_perfil_puesto_id:0,genero_atributo:'',edad_minima:'',edad_maxima:'',estado_civil_atributo:'',grado_avance_atributo:'',areas_conocimiento:'',minimo_experiencia_anios:'',minimo_experiencia_meses:'',areas_experiencia:'',conocimientos_especificos:'',equipo_software_herramientas:'',ev_tabulador_id_minimo:'',ev_tabulador_id_maximo:'',sueldo_promedio:'',media_salarial_mes:'',media_salarial_zona:'',competencias:'',aptitudes:'',observaciones_adicionales:'',actitudes_puesto:'',nivel_estudios_atributo:'',idioma_atributo:'',ev_puesto_id:'',creado:'',actualizado:'',creadopor:'',actualizadopor:''};
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
             
            const response_ev_puesto = await this.request(
                '../../models/ev/bd_ev_puesto.php',
                {'order' : 'ORDER BY ev_puesto_id DESC','action' : 'select'});
            try{  
                if(response_ev_puesto.length > 0){  
                    this.ev_puestoCollection = response_ev_puesto; 
                }  
            }catch(error){
                this.show_message('No hay ev_puestos.','info');
            } 

            const resp_tabulador = await this.request(
                '../../models/admin/bd_tabulador.php',
                {'order' : 'ORDER BY id_tabulador DESC','action' : 'select'}
            );
            try{  
                if(resp_tabulador.length > 0){  
                    this.tabuladorCollection = resp_tabulador; 
                }  
            }catch(error){
                this.show_message('No hay ev_atributos.','info');
            }    
            this.ev_atr_genero_Collection = await this.get_atributo('genero');  
            this.ev_atr_estado_civilCollection = await this.get_atributo('estado_civil'); 
            this.ev_atr_grado_avanceCollection = await this.get_atributo('grado_avance');
            this.ev_atrnivel_estudioCollection = await this.get_atributo('nivel_estudio');
            this.ev_atr_idiomaCollection = await this.get_atributo('idioma'); 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.ev_perfil_puestoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_perfil_puestoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_perfil_puestoCollection[index];
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
       await this.getev_perfil_puestos();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
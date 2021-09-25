 
var application = new Vue({
    el:'#app_ev_ticket',
    data:{ 
        ev_ticket : null,
        ev_ticketCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_solucion_ticket.php',
        typeMessage : '',
        staticMessage : '',
        msg:'',
        departamentoCollection: [],
        estadoCollection:[],
        estadoCollections:[],
        // comentariosCollection:[],
        LineaCollections: [],

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
        filterestado:'',
        adjunto_dialog : false,
        view_adjunto_dialog : false,
        evidencia_dialog : false,
        load_dialog : false,
        files_adjuntos : [],
        file_adjunto : {},
        
    
    },
    methods:{

        async get_file(file){ 
            window.open(`../../models/generales/bd_file_adjunto.php?type_getFile_admin=1&id_file=${file.id_file_adjunto}`
            ,'_blank');
        }, 
        async getfiles_adjuntos(ev_ticket_ln_id){

            if(ev_ticket_ln_id > 0 ){
                // console.log('entro');
                this.ev_ticket_ln.ev_ticket_ln_id = ev_ticket_ln_id;

                console.log(this.ev_ticket_ln.ev_ticket_ln_id);
                this.view_adjunto_dialog = true;
            this.files_adjuntos  = []; 
            const response = await this.request('../../models/generales/bd_file_adjunto.php',
                {
                    'action' : 'select_preview'
                    ,'tabla': 'ev_ticket_ln'
                    ,'id_tabla' : this.ev_ticket_ln.ev_ticket_ln_id
                });
            try{   
                this.files_adjuntos = response; 
                // console.log('saiend');
            }catch(error){
                console.log(error);
                this.show_message('No hay datos Para Mostrar.','info');
            }  
            }

            
        },

        async getev_tickets(){  
            this.ev_ticketCollection  = [];
            this.paginaCollection = [];
            const response = await this.request(this.path,{'action' : 'select','filter' : this.filter});
            
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_ticketCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 

        async getevConsulEstado(){  
            this.ev_ticketCollection  = [];
            this.paginaCollection = [];
            const response = await this.request(this.path,{'action' : 'selectFilter','filterestado' : this.filterestado});
            // console.log(response);
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_ticketCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 

        async SelectLinea(){
            const response = await this.request(this.path,{model:this.ev_ticket,'action' : 'selectLinea'});
            // console.log(response);
            try{ 
                this.LineaCollections = response;
            }catch(error){
                this.show_message('No hay comentarios a Mostrar.','info');
               
            } 
        },
        
        

        async save_ev_ticket(){
            if(this.ev_ticket.comentario_solucion == null || this.ev_ticket.estado == 'AB'){
                this.show_message('Para continuar llena todos los campos vacios ','error');
            } 
            else {
                            const responseUnion = await this.request(this.path,{model:this.ev_ticket,'action' : 'uniones'});
                        
                            const response = await this.request(this.path,{model:this.ev_ticket,'action' : 'update'});
                            
                            if(response.message == 'Data Updated'){
                                await this.getev_tickets();
                                this.show_message('Registro Actualizado','success');
                                this.model_empty();
                                this.isFormCrud = false;
                                this.isForm = false;
                            }else{
                                this.show_message(response.message,'error');
                            }
            }
        },
           

        async update_ev_ticket(ev_ticket_id){ 
            if(ev_ticket_id > 0){
                this.ev_ticket = this.search_ev_ticketByID(ev_ticket_id);
                if(this.ev_ticket.ev_ticket_id > 0){
                    this.SelectLinea();
                    this.isFormCrud = true;
                    
                    
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 

        

        add_ev_ticket(){  
            this.model_empty();
            this.isFormCrud = true;
        }, 
        cancel_ev_ticket(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_ticketByID(ev_ticket_id){
            for (let index = 0; index < this.ev_ticketCollection.length; index++) {
                const element = this.ev_ticketCollection[index]; 
                if (ev_ticket_id == element.ev_ticket_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },

        model_empty(){
            this.ev_ticket = {ev_ticket_id:0,problema:'',observacion:'',fechacreacion:'',fechasolucion:'',estado:'',ev_catalogo_ticket_id:'', comentario_solucion: ''};
            this.ev_ticket_ln = {ev_ticket_ln_id: 0};
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
            const responseEstado = await this.request(this.path,{'action' : 'selectEstado'});
            try{  
                if(responseEstado.length > 0){
                    this.estadoCollection = responseEstado; 
                }  
            }catch(error){
                this.show_message('No hay estados disponibles .','info');
            } 
           
            const responseEstados = await this.request(this.path,{'action' : 'selectEstados'});
            try{  
                if(responseEstados.length > 0){
                    this.estadoCollections = responseEstados; 
                }  
            }catch(error){
                this.show_message('No hay estados disponibles .','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.ev_ticketCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_ticketCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_ticketCollection[index];
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
    await this.getev_tickets();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
 
var application = new Vue({
    el:'#app_ev_ticket',
    data:{ 
        ev_ticket : null,
        ev_ticketCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_ticket.php',
        typeMessage : '',
        staticMessage : '',
        msg:'',
        ev_catalogo_ticketCollection:[],
        departamentoCollection: [],
        estadoCollection:[],
        estadoCollection2:[],

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
        filterestado:'',
        isForm: false,
        isFormInsert: false,
        isFormInsert2: false,
        fechas: false,
        com:false,
        disabled: 1,
    
    },
    methods:{
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
            const response = await this.request(this.path,{'action' : 'selectConEstado','filterestado' : this.filterestado});
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

        async get_catalogo(){
           this.ev_ticket.ev_catalogo_ticket_id = '';
            const response_ev_catalogo_ticket = await this.request(this.path,{model:this.ev_ticket,'action' : 'selectCatalogo'});
            try{  
                if(response_ev_catalogo_ticket.length > 0){ 
                    this.show_message(response_ev_catalogo_ticket.length +' Catalogos Encontrados.','success'); 
                    this.ev_catalogo_ticketCollection = response_ev_catalogo_ticket;
                }  
                
            }catch(error){
                this.show_message('No hay Catalogos registrados.','info');
                this.ev_catalogo_ticketCollection = null;
            }  
        },


        async delete_ev_ticket(ev_ticket_id){   
            if(ev_ticket_id > 0){
                const response = await this.request('../../models/ev/bd_ev_ticket_ln.php',{model:{'ev_ticket_id':ev_ticket_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getev_tickets();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        }, 

        async save_ev_ticket(){ 
            if(this.isFormInsert == true){
                if(this.ev_ticket.comentario == '' ){
                    this.show_message('Es necesario no dejar el campo vacio.','error');
                }else {
                    if(this.ev_ticket.ev_ticket_id > 0){
                        const responseCreacion = await this.request(this.path,{model:this.ev_ticket,'action' : 'verificaExistencia'});
                        console.log(responseCreacion);
                        if(responseCreacion == null){
                            this.model_empty();
                            this.show_message('Este ticket fue creado originalmento por Otro usuario por lo que se te prohibe actualizar','info');
                        } else {

                            if(this.ev_ticket.estado == 'CA'){
                                const response = await this.request(this.path,{model:this.ev_ticket,'action' : 'update'});
                                const response_ln = await this.request('../../models/ev/bd_ev_ticket_ln.php',{model:this.ev_ticket,'action' : 'update'});
                                if(response.message == 'Data Updated'){
                                    await this.getev_tickets();
                                    this.show_message('Registro Actualizado','success');
                                    this.model_empty();
                                
                                }else{
                                    this.show_message(response.message,'error');
                                }

                            } else if(this.ev_ticket.estado == 'AB') {
                                const response = await this.request(this.path,{model:this.ev_ticket,'action' : 'updateV'});
                                const response_ln = await this.request('../../models/ev/bd_ev_ticket_ln.php',{model:this.ev_ticket,'action' : 'updateV'});
                                if(response.message == 'Data Updated'){
                                    await this.getev_tickets();
                                    this.show_message('Registro Actualizado','success');
                                    this.model_empty();
                                
                                }else{
                                    this.show_message(response.message,'error');
                                }
                            }
                        }
                        
                        
                    } else if(this.ev_ticket.ev_ticket_id == 0){ 
                        const responseld = await this.request(this.path,{model:this.ev_ticket,'action' : 'valida'});
                        if(responseld == null){
                            const response = await this.request(this.path,{model:this.ev_ticket,'action' : 'insert'}); 
                            const response_lds = await this.request('../../models/ev/bd_ev_ticket_ln.php',{model:this.ev_ticket,'action' : 'insert'});
                            if(response.message == 'Data Inserted'){
                                await this.getev_tickets();
                                this.show_message('Registro Guardado.','success');
                                this.model_empty();
                                
                            }else{
                                this.show_message(response.message,'error');
                            } 
                        }else {
                            const response = await this.request(this.path,{model:this.ev_ticket,'action' : 'insert'}); 
                            const response_lds = await this.request('../../models/ev/bd_ev_ticket_ln.php',{model:this.ev_ticket,'action' : 'insertO'});
                            if( response_lds.message == 'Data Inserted'){
                                await this.getev_tickets();
                                this.show_message('Registro Guardado.','success');
                                this.model_empty();
                                
                            }else{
                                this.show_message(response.message,'error');
                            } 
                        } 
                    }
                }
            } else {
                const response_ld = await this.request('../../models/ev/bd_ev_ticket_ln.php',{model:this.ev_ticket,'action' : 'insertO'}); 
               
                    if(response_ld.message == 'Data Inserted'){
                        await this.getev_tickets();
                        this.show_message('Registro Guardado.','success');
                        this.model_empty();
                        this.isFormCrud = false;
                        this.isForm = false;
                    }else{
                        
                        this.show_message(response_ld.message,'error');
                        this.ev_catalogo_ticketCollection = null
                    } 
                
                
            }
            // }
        },

        async update_ev_ticket(ev_ticket_id){ 
            if(ev_ticket_id > 0){
                this.ev_ticket = this.search_ev_ticketByID(ev_ticket_id);
                if(this.ev_ticket.ev_ticket_id > 0){
                    this.isFormCrud = true;
                    this.isFormInsert = true;
                    this.isForm = false;
                    this.isFormInsert2 = false;
                    this.fechas = true;
                    this.disabled = 0;
                    this.com = true;
                    
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 

        async add_ev_ticketF(){
            
            if(this.ev_ticket.departamento_id > 0 && this.ev_ticket.ev_catalogo_ticket_id ){
                
                const responseld = await this.request('../../models/ev/bd_ev_ticket_ln.php',{model:this.ev_ticket,'action' : 'select'});
                if(responseld == null){
                    this.isForm = false;
                    this.getForm();
                } else {
                    if(this.ev_ticket.ev_catalogo_ticket_id == 0){
                        this.isForm = false;
                        this.getForm();
                    }else {
                        this.isFormCrud = false;
                        this.show_message('Ya haz realizado este ticket.','info');
                    }
                    
                }
            } else {
                this.show_message('NO has seleccionado.','info');
            }
        },

        async getForm(){
            const consulta = await this.request('../../models/ev/bd_ev_ticket_ln.php',{model:this.ev_ticket,'action' : 'selectV'});
                    if(consulta == null){
                        const consulta2 = await this.request('../../models/ev/bd_ev_ticket_ln.php',{model:this.ev_ticket,'action' : 'validaInsert'});
                        if(consulta2 == this.ev_ticket.ev_catalogo_ticket_id ){
                            this.isFormInsert = true;
                        } else {
                            this.isFormInsert = true;
                        }
                        
                    } else if(this.ev_ticket.ev_catalogo_ticket_id == 0){
                        this.isFormInsert = true;
                    } else {
                      
                        this.isFormInsert2 = true;
                    }
        },
        add_ev_ticket(){  
            this.model_empty();
            this.isFormCrud = true;
            this.isForm = true;

        }, 
        cancel_ev_ticket(){  
            this.model_empty();
            this.isFormCrud = false;
            this.isForm = false;
            this.isFormInsert = false;
            this.isFormInsert2 = false;
            this.ev_catalogo_ticketCollection = null;
            this.fechas = false;
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

        async show_messageStatic(msg, staticMessage){
            this.msg =msg;
            this.staticMessage = staticMessage;

        },
        
        model_empty(){
            this.ev_ticket = {ev_ticket_id:0,problema:'',observacion:'',fechacreacion:'',fechasolucion:'',estado:'',ev_catalogo_ticket_id:'',comentario:''};
            this.ev_catalogo_ticketCollection = null;
            this.disabled = 1;
            this.isFormInsert = false;
            this.isFormInsert2 = false;
            this.isFormCrud = false;
            this.isForm = false;
            this.fechas = false;
            this.com = false;
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
                         
            const response_estado = await this.request('../../models/ev/bd_ev_ticket.php',{'action' : 'selectEstado'});
            try{  
                if(response_estado.length > 0){  
                    this.estadoCollection = response_estado; 
                }  
            }catch(error){
                this.show_message('No hay estados disponibles .','info');
            } 
            const response_depa = await this.request('../../models/admin/bd_departamento.php',{'action' : 'select'});
            try{  
                if(response_depa.length > 0){  
                    this.departamentoCollection = response_depa; 
                }  
            }catch(error){
                this.show_message('No hay ev_catalogo_tickets.','info');
            } 

            const responseEstadoFull = await this.request('../../models/ev/bd_ev_solucion_ticket.php',{'action' : 'selectEstado'});
            try{  
                if(responseEstadoFull.length > 0){
                    this.estadoCollection2 = responseEstadoFull; 
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
        await this.getevConsulEstado();
       await this.getev_tickets();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
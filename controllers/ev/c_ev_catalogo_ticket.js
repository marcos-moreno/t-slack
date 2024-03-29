 
var application = new Vue({
    el:'#app_ev_catalogo_ticket',
    data:{ 
        ev_catalogo_ticket : null,
        ev_catalogo_ticketCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_catalogo_ticket.php',
        typeMessage : '',
        msg:'',
        departamentoCollection:[],
            

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getev_catalogo_tickets(){  
            this.ev_catalogo_ticketCollection  = [];
            this.paginaCollection = [];
            const response = await this.request(this.path,{'action' : 'select','filter' : this.filter});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_catalogo_ticketCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_ev_catalogo_ticket(ev_catalogo_ticket_id){   
            if(ev_catalogo_ticket_id > 0){
                const response = await this.request(this.path,{model:{'ev_catalogo_ticket_id':ev_catalogo_ticket_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getev_catalogo_tickets();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_catalogo_ticket(){ 
            if(this.ev_catalogo_ticket.ev_catalogo_ticket_id > 0){
                const response = await this.request(this.path,{model:this.ev_catalogo_ticket,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_catalogo_tickets();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_catalogo_ticket.ev_catalogo_ticket_id == 0){ 
                const response = await this.request(this.path,{model:this.ev_catalogo_ticket,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_catalogo_tickets();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_catalogo_ticket(ev_catalogo_ticket_id){ 
            if(ev_catalogo_ticket_id > 0){
                this.ev_catalogo_ticket = this.search_ev_catalogo_ticketByID(ev_catalogo_ticket_id);
                if(this.ev_catalogo_ticket.ev_catalogo_ticket_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_catalogo_ticket(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_catalogo_ticket(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_catalogo_ticketByID(ev_catalogo_ticket_id){
            for (let index = 0; index < this.ev_catalogo_ticketCollection.length; index++) {
                const element = this.ev_catalogo_ticketCollection[index]; 
                if (ev_catalogo_ticket_id == element.ev_catalogo_ticket_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_catalogo_ticket = {ev_catalogo_ticket_id:0,situacion:'',activo:'',creadopor:'',modificadopor:'',creado:'',actualizado:'',departamento_id:''};
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
             
            const response_departamento = await this.request('../../models/admin/bd_departamento.php',{'order' : 'ORDER BY departamento_id DESC','action' : 'select'});
            try{  
                if(response_departamento.length > 0){  
                    this.departamentoCollection = response_departamento; 
                }  
            }catch(error){
                this.show_message('No hay departamentos.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.ev_catalogo_ticketCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_catalogo_ticketCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_catalogo_ticketCollection[index];
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
       await this.getev_catalogo_tickets();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
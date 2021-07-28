 
var application = new Vue({
    el:'#app_ev_puesto_nivel',
    data:{ 
        ev_puesto_nivel : null,
        ev_puesto_nivelCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_puesto_nivel.php',
        typeMessage : '',
        msg:'',
        ev_puestoCollection:[],
            ev_nivel_pCollection:[],
            

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getev_puesto_nivels(){  
            this.ev_puesto_nivelCollection  = [];
            this.paginaCollection = []; 
            const response = await this.request(this.path,{'type':"ilike",'action' : 'select','filter' : this.filter});
            try{
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_puesto_nivelCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            }
        }, 
        async delete_ev_puesto_nivel(ev_puesto_nivel_id){   
            if(ev_puesto_nivel_id > 0){
                const response = await this.request(this.path,{model:{'ev_puesto_nivel_id':ev_puesto_nivel_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getev_puesto_nivels();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_puesto_nivel(){ 
            if(this.ev_puesto_nivel.ev_puesto_nivel_id > 0){
                const response = await this.request(this.path,{model:this.ev_puesto_nivel,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_puesto_nivels();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_puesto_nivel.ev_puesto_nivel_id == 0){ 
                const response = await this.request(this.path,{model:this.ev_puesto_nivel,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_puesto_nivels();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_puesto_nivel(ev_puesto_nivel_id){ 
            if(ev_puesto_nivel_id > 0){
                this.ev_puesto_nivel = this.search_ev_puesto_nivelByID(ev_puesto_nivel_id);
                if(this.ev_puesto_nivel.ev_puesto_nivel_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_puesto_nivel(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_puesto_nivel(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_puesto_nivelByID(ev_puesto_nivel_id){
            for (let index = 0; index < this.ev_puesto_nivelCollection.length; index++) {
                const element = this.ev_puesto_nivelCollection[index]; 
                if (ev_puesto_nivel_id == element.ev_puesto_nivel_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_puesto_nivel = {ev_puesto_nivel_id:0,ev_puesto_id:'',ev_nivel_p_id:'',creado:'',creadopor:'',actualizado:'',actualizadopor:''};
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
             
            const response_ev_puesto = await this.request('../../models/ev/bd_ev_puesto.php',{'order' : 'ORDER BY ev_puesto_id DESC','action' : 'select'});
            try{  
                if(response_ev_puesto.length > 0){  
                    this.ev_puestoCollection = response_ev_puesto; 
                }  
            }catch(error){
                this.show_message('No hay ev_puestos.','info');
            }  
            const response_ev_nivel_p = await this.request('../../models/ev/bd_ev_nivel_p.php',{'order' : 'ORDER BY ev_nivel_p_id DESC','action' : 'select'});
            try{  
                if(response_ev_nivel_p.length > 0){  
                    this.ev_nivel_pCollection = response_ev_nivel_p; 
                }  
            }catch(error){
                this.show_message('No hay ev_nivel_ps.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.ev_puesto_nivelCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_puesto_nivelCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_puesto_nivelCollection[index];
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
       await this.getev_puesto_nivels();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
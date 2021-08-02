 
var application = new Vue({
    el:'#app_periodo',
    data:{ 
        periodo : null,
        periodoCollection : [],
        isFormCrud: false,
        path : '../../models/admin/bd_periodo.php',
        typeMessage : '',
        msg:'',
        ev_atributoCollection:[],
        empresaCollection:[],

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
    },
    methods:{
        async getperiodos(){  
            this.periodoCollection  = [];
            this.paginaCollection = [];
            const response = await this.request(this.path,{'action' : 'select','filter' : this.filter});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.periodoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_periodo(periodo_id){   
            if(periodo_id > 0){
                const response = await this.request(this.path,{model:{'periodo_id':periodo_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getperiodos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_periodo(){ 
            if(this.periodo.periodo_id > 0){
                const response = await this.request(this.path,{model:this.periodo,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getperiodos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.periodo.periodo_id == 0){ 
                const response = await this.request(this.path,{model:this.periodo,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getperiodos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_periodo(periodo_id){ 
            if(periodo_id > 0){
                this.periodo = this.search_periodoByID(periodo_id);
                if(this.periodo.periodo_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_periodo(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_periodo(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_periodoByID(periodo_id){
            for (let index = 0; index < this.periodoCollection.length; index++) {
                const element = this.periodoCollection[index]; 
                if (periodo_id == element.periodo_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.periodo = {nombre_periodo:'',inicio_periodo:'',fin_periodo:'',ejercicio:'',id_empresa:'',creado:'',actualizado:'',creadopor:'',actualizadopor:'',numero_periodo:'',activo:'',elemento_sistema_atributo:'',periodo_id:0};
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
             
            const response_ev_atributo = await this.request(
                '../../models/ev/bd_ev_atributo.php',
                {'action' : 'select','valor' : 'periodo'}
            );
            try{  
                if(response_ev_atributo.length > 0){  
                    this.ev_atributoCollection = response_ev_atributo; 
                }  
            }catch(error){
                this.show_message('No hay ev_atributos.','info');
            }  
            const response_empresa = await this.request('../../models/generales/bd_empresa.php',{'order' : 'ORDER BY id_empresa DESC','action' : 'select'});
            try{  
                if(response_empresa.length > 0){  
                    this.empresaCollection = response_empresa; 
                }  
            }catch(error){
                this.show_message('No hay empresas.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.periodoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.periodoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.periodoCollection[index];
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
       await this.getperiodos();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
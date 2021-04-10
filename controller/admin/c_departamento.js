 
var application = new Vue({
    el:'#app_departamento',
    data:{ 
        departamento : null,
        departamentoCollection : [],
        isFormCrud: false,
        path : '../../models/admin/bd_departamento.php',
        typeMessage : '',
        msg:'',
        empresaCollection:[],
        segmentoCollection:[], 
        //paginador
        numByPag : 20, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador 
        filter : '', 
    },
    methods:{
        async getdepartamentos(){  
            this.departamentoCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "( nombre ILIKE '%" + this.filter + "%'  )";  
           const response = await this.request(this.path,{'action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.departamentoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_departamento(departamento_id){  
            this.departamento = this.search_departamentoByID(departamento_id);
            if(this.departamento.departamento_id > 0){
                const response = await this.request(this.path,{model:this.departamento,'action' : 'delete'});
                this.departamentoCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getdepartamentos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_departamento(){ 
            if(this.departamento.departamento_id > 0){
                this.departamento.activo == true || this.departamento.activo == 'true' ?  this.departamento.activo = 'true' : this.departamento.activo = 'false';
                const response = await this.request(this.path,{model:this.departamento,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getdepartamentos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.departamento.departamento_id == 0){ 
                this.departamento.activo == true || this.departamento.activo == 'true' ?  this.departamento.activo = 'true' : this.departamento.activo = 'false';
                const response = await this.request(this.path,{model:this.departamento,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getdepartamentos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_departamento(departamento_id){ 
            if(departamento_id > 0){
                this.departamento = this.search_departamentoByID(departamento_id);
                if(this.departamento.departamento_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_departamento(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_departamento(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_departamentoByID(departamento_id){
            for (let index = 0; index < this.departamentoCollection.length; index++) {
                const element = this.departamentoCollection[index]; 
                if (departamento_id == element.departamento_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.departamento = {departamento_id:0,nombre:'',activo:'',creado:'',actualizado:'',actualizadopor:'',creadopor:'',id_empresa:'',id_segmento:'',id_cerberus:''};
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
            const response_empresa = await this.request('../../models/generales/bd_empresa.php',{'order' : 'ORDER BY id_empresa DESC','action' : 'select'});
            try{  
                if(response_empresa.length > 0){  
                    this.empresaCollection = response_empresa; 
                }  
            }catch(error){
                // this.show_message('No hay empresas.','info');
            }  
            const response_segmento = await this.request('../../models/admin/bd_segmento.php',{'order' : 'ORDER BY id_empresa,nombre ASC','action' : 'select'});
            try{  
                if(response_segmento.length > 0){  
                    this.segmentoCollection = response_segmento; 
                }  
            }catch(error){
                // this.show_message('No hay segmentos.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.departamentoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.departamentoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.departamentoCollection[index];
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
       await this.getdepartamentos();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
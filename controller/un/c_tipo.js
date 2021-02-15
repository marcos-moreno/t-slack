 
var application = new Vue({
    el:'#app_tipo',
    data:{ 
        tipo : null,
        tipoCollection : [],
        isFormCrud: false,
        path : '../../models/sp/bd_tipo.php',
        typeMessage : '',
        msg:'',
        

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1
        ////paginador
    },
    methods:{
        async gettipos(){  
            const response = await this.request(this.path,{'order' : 'ORDER BY id_tipo DESC','action' : 'select'});
            try{ 
                this.show_message(response.length + ' Registro Encontrados.','success');
                this.tipoCollection = response;   
                this.paginator(1);  
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
            } 
        }, 
        async delete_tipo(id_tipo){  
            this.tipo = this.search_tipoByID(id_tipo);
            if(this.tipo.id_tipo > 0){
                const response = await this.request(this.path,{model:this.tipo,'action' : 'delete'});
                this.tipoCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.gettipos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_tipo(){ 
            if(this.tipo.id_tipo > 0){
                const response = await this.request(this.path,{model:this.tipo,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.gettipos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.tipo.id_tipo == 0){ 
                const response = await this.request(this.path,{model:this.tipo,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.gettipos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_tipo(id_tipo){ 
            if(id_tipo > 0){
                this.tipo = this.search_tipoByID(id_tipo);
                if(this.tipo.id_tipo > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_tipo(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_tipo(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_tipoByID(id_tipo){
            for (let index = 0; index < this.tipoCollection.length; index++) {
                const element = this.tipoCollection[index]; 
                if (id_tipo == element.id_tipo) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.tipo = {id_tipo:0,tipo:'',descripcion:'',direct_data:'',opcion_multiple:''};
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
            
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.tipoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.tipoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.tipoCollection[index];
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
       await this.gettipos();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
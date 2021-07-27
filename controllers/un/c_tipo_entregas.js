 
var application = new Vue({
    el:'#app_tipo_entregas',
    data:{ 
        tipo_entregas : null,
        tipo_entregasCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_tipo_entregas.php',
        typeMessage : '',
        msg:'',
        

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '', 
    },
    methods:{
        async gettipo_entregass(){  
            this.tipo_entregasCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(tipo ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_tipo_entrega DESC','action' : 'select','filter' : filtrarPor}); 
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.tipo_entregasCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_tipo_entregas(id_tipo_entrega){   
            if(id_tipo_entrega > 0){
                const response = await this.request(this.path,{model:{'id_tipo_entrega':id_tipo_entrega},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.gettipo_entregass();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_tipo_entregas(){  
            if(this.tipo_entregas.id_tipo_entrega > 0){
                this.tipo_entregas.activo = (this.tipo_entregas.activo == true || this.tipo_entregas.activo == 'true' ? 'true' : 'false');
                this.tipo_entregas.usar_fecha = (this.tipo_entregas.usar_fecha == true || this.tipo_entregas.usar_fecha ? 'true' : 'false');
                const response = await this.request(this.path,{model:this.tipo_entregas,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.gettipo_entregass();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.tipo_entregas.id_tipo_entrega == 0){ 
                this.tipo_entregas.activo = (this.tipo_entregas.activo == true || this.tipo_entregas.activo == 'true' ? 'true' : 'false');
                this.tipo_entregas.usar_fecha = (this.tipo_entregas.usar_fecha == true || this.tipo_entregas.usar_fecha ? 'true' : 'false');
                const response = await this.request(this.path,{model:this.tipo_entregas,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.gettipo_entregass();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_tipo_entregas(id_tipo_entrega){ 
            if(id_tipo_entrega > 0){
                this.tipo_entregas = {};
                this.tipo_entregas = this.search_tipo_entregasByID(id_tipo_entrega);
                if(this.tipo_entregas.id_tipo_entrega > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_tipo_entregas(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_tipo_entregas(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_tipo_entregasByID(id_tipo_entrega){
            for (let index = 0; index < this.tipo_entregasCollection.length; index++) {
                const element = this.tipo_entregasCollection[index]; 
                if (id_tipo_entrega == element.id_tipo_entrega) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.tipo_entregas = {id_tipo_entrega:0,tipo:'',activo:'true',usar_fecha:'true',fecha_aplicacion:null};
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
            let cantidad_pages = Math.ceil(this.tipo_entregasCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.tipo_entregasCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.tipo_entregasCollection[index];
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
       await this.gettipo_entregass();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
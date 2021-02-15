 
var application = new Vue({
    el:'#app_talla',
    data:{ 
        talla : null,
        tallaCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_talla.php',
        typeMessage : '',
        msg:'',
        tipo_productoCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async gettallas(){  
            this.tallaCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(valor ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_tipo_producto ASC,valor ASC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.tallaCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_talla(id_talla){  
            this.talla = this.search_tallaByID(id_talla);
            if(this.talla.id_talla > 0){
                const response = await this.request(this.path,{model:this.talla,'action' : 'delete'});
                this.tallaCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.gettallas();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_talla(){ 
            if(this.talla.id_talla > 0){
                this.talla.activo = (this.talla.activo == true || this.talla.activo == 'true' ? 'true' : 'false'); 
                const response = await this.request(this.path,{model:this.talla,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.gettallas();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.talla.id_talla == 0){ 
                this.talla.activo = (this.talla.activo == true || this.talla.activo == 'true' ? 'true' : 'false'); 
                const response = await this.request(this.path,{model:this.talla,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.gettallas();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_talla(id_talla){ 
            if(id_talla > 0){
                this.talla = this.search_tallaByID(id_talla);
                if(this.talla.id_talla > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_talla(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_talla(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_tallaByID(id_talla){
            for (let index = 0; index < this.tallaCollection.length; index++) {
                const element = this.tallaCollection[index]; 
                if (id_talla == element.id_talla) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.talla = {id_talla:0,valor:'',activo:'true',id_tipo_producto:''};
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
             
            const response_tipo_producto = await this.request('../../models/sp/bd_tipo_producto.php',{'order' : 'ORDER BY id_tipo_producto DESC','action' : 'select'});
            try{  
                if(response_tipo_producto.length > 0){  
                    this.tipo_productoCollection = response_tipo_producto; 
                }  
            }catch(error){
                this.show_message('No hay tipo_productos.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.tallaCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.tallaCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.tallaCollection[index];
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
       await this.gettallas();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
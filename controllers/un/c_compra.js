 
var application = new Vue({
    el:'#app_compra',
    data:{ 
        compra : null,
        compraCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_compra.php',
        typeMessage : '',
        msg:'',
        proveedorCollection:[],
            

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getcompras(){  
            this.compraCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_compra DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.compraCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_compra(id_compra){   
            if(id_compra > 0){
                const response = await this.request(this.path,{model:{'id_compra':id_compra},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getcompras();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        
        async completar(id_compra){ 
            if(id_compra > 0){ 
                this.compra = this.search_compraByID(id_compra);
                if(this.compra.id_compra > 0){  
                    this.compra.estado = 'CO';
                    const response = await this.request(this.path,{model:this.compra,'action' : 'update'}); 
                    if(response.message == 'Data Updated'){
                        // Stock 
                        const response_stock = await this.request('../../models/un/bd_movimiento_stock.php' ,
                        {model:{'id_movimiento':this.compra.id_compra,'id_tipo_movimiento':4},'action' : 'insert_stock'}); 
                        console.log(response_stock); 
                        //// stock
                        await this.getcompras();
                        this.show_message('Registro Actualizado','success');
                        this.model_empty();
                        this.isFormCrud = false;
                    }else{
                        this.show_message(response.message,'error');
                    }
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        },
        async save_compra(){ 
            if(this.compra.id_compra > 0){
                const response = await this.request(this.path,{model:this.compra,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getcompras();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.compra.id_compra == 0){ 
                const response = await this.request(this.path,{model:this.compra,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getcompras();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_compra(id_compra){ 
            if(id_compra > 0){
                this.compra = this.search_compraByID(id_compra);
                if(this.compra.id_compra > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_compra(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_compra(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_compraByID(id_compra){
            for (let index = 0; index < this.compraCollection.length; index++) {
                const element = this.compraCollection[index]; 
                if (id_compra == element.id_compra) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.compra = {id_compra:0,id_proveedor:'0',total:'0.0',fecha_compra:'',nombre:'',estado:'BO'};
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
            const response_proveedor = await this.request('../../models/un/bd_proveedor.php',{'order' : 'ORDER BY id_proveedor DESC','action' : 'select'});
            try{  
                if(response_proveedor.length > 0){  
                    this.proveedorCollection = response_proveedor; 
                }  
            }catch(error){
                this.show_message('No hay proveedors.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.compraCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.compraCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.compraCollection[index];
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
       await this.getcompras();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
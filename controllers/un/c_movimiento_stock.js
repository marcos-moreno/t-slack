 
var application = new Vue({
    el:'#app_movimiento_stock',
    data:{ 
        movimiento_stock : null,
        movimiento_stockCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_movimiento_stock.php',
        typeMessage : '',
        msg:'',
        almacenCollection:[],
            productoCollection:[],
            tipo_movimientoCollection:[],
            colorCollection:[],
            tallaCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getmovimiento_stocks(){  
            this.movimiento_stockCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(descripcion ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_movimiento_stock DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.movimiento_stockCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_movimiento_stock(id_movimiento_stock){   
            if(id_movimiento_stock > 0){
                const response = await this.request(this.path,{model:{'id_movimiento_stock':id_movimiento_stock},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getmovimiento_stocks();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_movimiento_stock(){ 
            if(this.movimiento_stock.id_movimiento_stock > 0){
                const response = await this.request(this.path,{model:this.movimiento_stock,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getmovimiento_stocks();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.movimiento_stock.id_movimiento_stock == 0){ 
                const response = await this.request(this.path,{model:this.movimiento_stock,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getmovimiento_stocks();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_movimiento_stock(id_movimiento_stock){ 
            if(id_movimiento_stock > 0){
                this.movimiento_stock = this.search_movimiento_stockByID(id_movimiento_stock);
                if(this.movimiento_stock.id_movimiento_stock > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_movimiento_stock(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_movimiento_stock(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_movimiento_stockByID(id_movimiento_stock){
            for (let index = 0; index < this.movimiento_stockCollection.length; index++) {
                const element = this.movimiento_stockCollection[index]; 
                if (id_movimiento_stock == element.id_movimiento_stock) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.movimiento_stock = {id_movimiento_stock:0,id_almacen:'',id_producto:'',cantidad:'',id_tipo_movimiento:'',id_movimiento:'',descripcion:'',activo:'',id_color:'',id_talla:''};
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
             
            const response_almacen = await this.request('../../models/un/bd_almacen.php',{'order' : 'ORDER BY id_almacen DESC','action' : 'select'});
            try{  
                if(response_almacen.length > 0){  
                    this.almacenCollection = response_almacen; 
                }  
            }catch(error){
                this.show_message('No hay almacens.','info');
            }  
            const response_producto = await this.request('../../models/un/bd_producto.php',{'order' : 'ORDER BY id_producto DESC','action' : 'select'});
            try{  
                if(response_producto.length > 0){  
                    this.productoCollection = response_producto; 
                }  
            }catch(error){
                this.show_message('No hay productos.','info');
            }  
            const response_tipo_movimiento = await this.request('../../models/un/bd_tipo_movimiento.php',{'order' : 'ORDER BY id_tipo_movimiento DESC','action' : 'select'});
            try{  
                if(response_tipo_movimiento.length > 0){  
                    this.tipo_movimientoCollection = response_tipo_movimiento; 
                }  
            }catch(error){
                this.show_message('No hay tipo_movimientos.','info');
            }  
            const response_color = await this.request('../../models/un/bd_color.php',{'order' : 'ORDER BY id_color DESC','action' : 'select'});
            try{  
                if(response_color.length > 0){  
                    this.colorCollection = response_color; 
                }  
            }catch(error){
                this.show_message('No hay colors.','info');
            }  
            const response_talla = await this.request('../../models/un/bd_talla.php',{'order' : 'ORDER BY id_talla DESC','action' : 'select'});
            try{  
                if(response_talla.length > 0){  
                    this.tallaCollection = response_talla; 
                }  
            }catch(error){
                this.show_message('No hay tallas.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.movimiento_stockCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.movimiento_stockCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.movimiento_stockCollection[index];
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
       await this.getmovimiento_stocks();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
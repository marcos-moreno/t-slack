 
var application = new Vue({
    el:'#app_compra_detalle',
    data:{ 
        compra_detalle : null,
        compra_detalleCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_compra_detalle.php',
        typeMessage : '',
        msg:'',
        compraCollection:[],
            colorCollection:[],
            tallaCollection:[],
            productoCollection:[],
            

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
        id_compra:0,
        productoSelected:{},

        almacenCollection:[],
        valido : false,

    },
    methods:{
        calcularTotalDetalle(){    
            try {  
                if  ( this.compra_detalle.cantidad == ''|| this.compra_detalle.cantidad == '0'|| this.compra_detalle.cantidad < 1) {
                    this.compra_detalle.total_linea = "0.00";  
                }else{
                    if (this.productoSelected.id_producto != 0) {
                        this.compra_detalle.cantidad = parseInt(this.compra_detalle.cantidad); 
                        if (this.compra_detalle.cantidad > 0) {
                            this.compra_detalle.total_linea = Number(this.compra_detalle.cantidad * this.productoSelected.costo_proveedor).toFixed(2) ; 
                        }else{ 
                            this.compra_detalle.total_linea = "0.00";  
                        }
                    }else{ 
                        this.compra_detalle.total_linea = "0.00";  
                    } 
                }
            } catch (error) {  this.compra_detalle.total_linea = "0.00";     } 

        },
        async getDataProd(){ 
            if (this.compra_detalle.id_producto != 0) {
                this.productoCollection.forEach(element => {
                if (element.id_producto == this.compra_detalle.id_producto) {
                        this.productoSelected = element;
                    }
                });
            }  
            this.tallaCollection = [];
            if (this.productoSelected.id_producto != 0) { 
                let filtrarPor = " activo = true AND id_tipo_producto IN ( SELECT id_tipo_producto FROM producto WHERE  id_producto = " + this.compra_detalle.id_producto + ")";
                const response_tallas = await this.request('../../models/un/bd_talla.php',{'order' : 'ORDER BY valor ASC','action' : 'select', 'filter' : filtrarPor});
                try{  
                    if(response_tallas.length > 0 && response_tallas[0].id_talla > 0){  
                        this.tallaCollection = response_tallas; 
                    }  
                }catch(error){
                    this.show_message('No hay Tallas Disponibles.','info');
                    console.log(response_tallas);
                }  
            }
            this.colorCollection = [];
            if (this.productoSelected.id_producto != 0) {  
                let filtrarPor = " id_color IN (SELECT id_color FROM producto_color WHERE id_producto = " + this.compra_detalle.id_producto + " ) AND activo = true ";
                const response_color = await this.request('../../models/un/bd_color.php',{'order' : 'ORDER BY nombre_color ASC','action' : 'select', 'filter' : filtrarPor});
                try{  
                    if(response_color.length > 0 && response_color[0].id_color > 0){  
                        this.colorCollection = response_color; 
                    }  
                }catch(error){
                    this.show_message('No hay productos.','info');
                    console.log(response_color);
                } 
            }  
        },
        validar_bo(){
            this.valido = false; 
            this.compraCollection.forEach(element => {
                if (element.id_compra == this.id_compra && element.estado == 'BO') {
                   this.valido = true;  
                }
            });
        },
        async getcompra_detalles(){
            this.validar_bo();
            if (this.isFormCrud == false) {  
                this.compra_detalleCollection  = [];
                this.paginaCollection = [];
                let filtrarPor =  "id_compra = " + this.id_compra;  
                const response = await this.request(this.path,{'order' : 'ORDER BY id_compra_detalle DESC','action' : 'select','filter' : filtrarPor});
                console.log(response);
                try{ 
                    this.show_message(response.length + ' Registros Encontrados.','success');
                    this.compra_detalleCollection = response;
                    this.paginaCollection = response;
                    this.paginator(1);  
                    this.isFormCrud=false;
                }catch(error){
                    this.show_message('No hay datos Para Mostrar.','info');
                    this.isFormCrud=false;
                } 
            } 
                if (this.id_compra > 0) {
                    let filtrarPor = "id_catalogo IN (SELECT id_catalogo FROM catalogo WHERE id_proveedor = (SELECT id_proveedor FROM compra WHERE id_compra = "+ this.id_compra +") )"
                    const response_producto = await this.request('../../models/un/bd_producto.php',{'order' : 'ORDER BY id_producto DESC','action' : 'select',"filter":filtrarPor});
                    try{  
                        if(response_producto.length > 0){  
                            this.productoCollection = response_producto; 
                        }  
                    }catch(error){
                        this.show_message('No hay productos.','info');
                    }   
                }
        }, 
        async delete_compra_detalle(id_compra_detalle){   
            if(id_compra_detalle > 0){
                const response = await this.request(this.path,{model:{'id_compra_detalle':id_compra_detalle},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getcompra_detalles();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_compra_detalle(){ 
            if(this.compra_detalle.id_compra_detalle > 0){
                this.compra_detalle.id_compra = this.id_compra;
                const response = await this.request(this.path,{model:this.compra_detalle,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    // Total
                    const response_compra = await this.request('../../models/un/bd_compra.php',
                    { 'action' : 'calculo_total','id_compra': this.id_compra });
                    console.log(response_compra);
                    // 
                    await this.getcompra_detalles();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.compra_detalle.id_compra_detalle == 0){ 
                this.compra_detalle.id_compra = this.id_compra;
                const response = await this.request(this.path,{model:this.compra_detalle,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    // Total
                    const response_compra = await this.request('../../models/un/bd_compra.php',
                    { 'action' : 'calculo_total','id_compra':this.id_compra});
                    console.log(response_compra);
                    // 
                    this.isFormCrud = false;
                    await this.getcompra_detalles(); 
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_compra_detalle(id_compra_detalle){ 
            if(id_compra_detalle > 0){
                this.compra_detalle = this.search_compra_detalleByID(id_compra_detalle);
                this.compra_detalle.id_compra = this.id_compra;
                if(this.compra_detalle.id_compra_detalle > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_compra_detalle(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_compra_detalle(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_compra_detalleByID(id_compra_detalle){
            for (let index = 0; index < this.compra_detalleCollection.length; index++) {
                const element = this.compra_detalleCollection[index]; 
                if (id_compra_detalle == element.id_compra_detalle) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.compra_detalle = {id_compra_detalle:0,id_compra:this.id_compra,id_color:'0',id_talla:'0',id_producto:'0',total_linea:'0.00',cantidad:'0',id_almacen:'0'};
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
            const response_compra = await this.request('../../models/un/bd_compra.php',{'order' : 'ORDER BY id_compra DESC','action' : 'select'});
            try{  
                if(response_compra.length > 0){  
                    this.compraCollection = response_compra; 
                }  
            }catch(error){
                this.show_message('No hay compras.','info');
            }    
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.compra_detalleCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.compra_detalleCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.compra_detalleCollection[index];
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
       await this.model_empty();
       await this.fill_f_keys();
       let id_cmpra = document.getElementById("id_compra").value; 
       id_cmpra > 0 ? this.id_compra = id_cmpra : id_cmpra = 0; 
       this.validar_bo();
       await this.getcompra_detalles();
       this.paginator(1);
    }
}); 
        
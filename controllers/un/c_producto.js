 
var application = new Vue({
    el:'#app_producto',
    data:{ 
        producto : null,
        productoCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_producto.php',
        typeMessage : '',
        msg:'',
        tipo_productoCollection:[],
            catalogoCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
        myModalColors: false,
        colores : [],

    },
    methods:{
        async asingColor(producto){ 
            this.colores = []; 
            this.myModalColors = true;
            this.producto = producto;
            let filtrarPor =  " (activo = true) ";  
            const colores_r = await this.request('../../models/un/bd_color.php',{'order' : 'ORDER BY nombre_color ASC','action' : 'select','filter' : filtrarPor});
            let colores_producto = [];
            let filtrarcolores_producto =  " (id_producto = "+ this.producto.id_producto +"  ) ";  
            try {
                const d = await this.request('../../models/un/bd_producto_color.php',{'order' : 'ORDER BY id_producto_color ASC','action' : 'select','filter' : filtrarcolores_producto});
                if (d.length > 0) {  colores_producto =  d;  }    } 
            catch (error) {} 
            for (let index = 0; index < colores_r.length; index++) {
                let guardado = false;
                let sele = {};
                const element = colores_r[index]; 
                for (let i = 0; i < colores_producto.length; i++) {
                    const element_prod_col = colores_producto[i];
                    if (element_prod_col.id_color == element.id_color ) { guardado = true;sele = element_prod_col} 
                } 
                this.colores.push({id_producto:this.producto.id_producto,id_color:element.id_color,
                    selected:guardado,nombre_color:element.nombre_color,original:guardado,id_producto_color: (guardado ? sele.id_producto_color :0 ) });
                    console.log(this.colores);
            }  
        },
         async guardarColores(){  
            for (let index = 0; index < this.colores.length; index++) {
                const element = this.colores[index];
                let action = "";
                if(element.selected != element.original){
                    action = (element.selected ? 'insert': 'delete' );
                    const d = await this.request('../../models/un/bd_producto_color.php',{'action' : action,'model' : element});
                    console.log(d);
                } 
            }
            this.myModalColors = false;
        },
        async getproductos(){  
            this.productoCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre_producto ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_producto DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.productoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_producto(id_producto){   
            if(id_producto > 0){
                const response = await this.request(this.path,{model:{'id_producto':id_producto},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getproductos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_producto(){ 
            if(this.producto.id_producto > 0){
                this.producto.activo = (this.producto.activo == true || this.producto.activo == 'true' ? 'true' : 'false');
                const response = await this.request(this.path,{model:this.producto,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getproductos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.producto.id_producto == 0){ 
                this.producto.activo = (this.producto.activo == true || this.producto.activo == 'true' ? 'true' : 'false');
                const response = await this.request(this.path,{model:this.producto,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getproductos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_producto(id_producto){ 
            if(id_producto > 0){
                this.producto = this.search_productoByID(id_producto);
                if(this.producto.id_producto > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_producto(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_producto(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_productoByID(id_producto){
            for (let index = 0; index < this.productoCollection.length; index++) {
                const element = this.productoCollection[index]; 
                if (id_producto == element.id_producto) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.producto = {id_producto:0,id_catalogo:'',nombre_producto:'',descripcion:'',costo:'0.00',costo_proveedor:'0.00',id_tipo_producto:'',activo:'true'};
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
             
            const response_tipo_producto = await this.request('../../models/sp/bd_tipo_producto.php',{'order' : 'ORDER BY id_tipo_producto DESC','action' : 'select', "filter" : " activo=true "});
            try{  
                if(response_tipo_producto.length > 0){  
                    this.tipo_productoCollection = response_tipo_producto; 
                }  
            }catch(error){
                this.show_message('No hay tipo_productos.','info');
            }  
            const response_catalogo = await this.request('../../models/un/bd_catalogo.php',{'order' : 'ORDER BY id_catalogo DESC','action' : 'select', "filter" : " activo=true "});
            try{  
                if(response_catalogo.length > 0){  
                    this.catalogoCollection = response_catalogo; 
                }  
            }catch(error){
                this.show_message('No hay catalogos.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.productoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.productoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.productoCollection[index];
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
       await this.getproductos();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
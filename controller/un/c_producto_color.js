 
var application = new Vue({
    el:'#app_producto_color',
    data:{ 
        producto_color : null,
        producto_colorCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_producto_color.php',
        typeMessage : '',
        msg:'',
        colorCollection:[],
            productoCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getproducto_colors(){  
            this.producto_colorCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_producto_color DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.producto_colorCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_producto_color(id_producto_color){  
            this.producto_color = this.search_producto_colorByID(id_producto_color);
            if(this.producto_color.id_producto_color > 0){
                const response = await this.request(this.path,{model:this.producto_color,'action' : 'delete'});
                this.producto_colorCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getproducto_colors();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_producto_color(){ 
            if(this.producto_color.id_producto_color > 0){
                const response = await this.request(this.path,{model:this.producto_color,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getproducto_colors();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.producto_color.id_producto_color == 0){ 
                const response = await this.request(this.path,{model:this.producto_color,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getproducto_colors();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_producto_color(id_producto_color){ 
            if(id_producto_color > 0){
                this.producto_color = this.search_producto_colorByID(id_producto_color);
                if(this.producto_color.id_producto_color > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_producto_color(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_producto_color(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_producto_colorByID(id_producto_color){
            for (let index = 0; index < this.producto_colorCollection.length; index++) {
                const element = this.producto_colorCollection[index]; 
                if (id_producto_color == element.id_producto_color) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.producto_color = {id_producto_color:0,id_color:'',id_producto:''};
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
             
            const response_color = await this.request('../../models/un/bd_color.php',{'order' : 'ORDER BY id_color DESC','action' : 'select'});
            try{  
                if(response_color.length > 0){  
                    this.colorCollection = response_color; 
                }  
            }catch(error){
                this.show_message('No hay colors.','info');
            }  
            const response_producto = await this.request('../../models/un/bd_producto.php',{'order' : 'ORDER BY id_producto DESC','action' : 'select'});
            try{  
                if(response_producto.length > 0){  
                    this.productoCollection = response_producto; 
                }  
            }catch(error){
                this.show_message('No hay productos.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.producto_colorCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.producto_colorCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.producto_colorCollection[index];
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
       await this.getproducto_colors();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
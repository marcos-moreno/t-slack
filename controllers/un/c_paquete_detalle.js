 
var application = new Vue({
    el:'#app_paquete_detalle',
    data:{ 
        paquete_detalle : null,
        paquete_detalleCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_paquete_detalle.php',
        typeMessage : '',
        msg:'',
        paqueteCollection:[],
            productoCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
        id_paquete_seleted:0,
        paqueteCollection:[],

        colorCollection:[],
    },
    methods:{
        async getColoresDisponibles(){
            if (this.paquete_detalle.id_producto > 0) {
                let filterColor = " id_color IN (SELECT id_color FROM Producto_color WHERE id_producto = "+this.paquete_detalle.id_producto+") ";
                const response_color = await this.request('../../models/un/bd_color.php',{'order' : 'ORDER BY id_color DESC','action' : 'select','filter':filterColor});
                console.log(response_color);
                try{  
                    if(response_color.length > 0){  
                        this.colorCollection = response_color; 
                    }  
                }catch(error){
                    this.show_message('No hay Colores Disponibles.','info');
                } 
            }
        },
        async getpaquete_detalles(){  
            if (this.isFormCrud==false) {
                this.paquete_detalleCollection  = [];
                this.paginaCollection = [];
                let filtrarPor =  "(id_paquete =" + this.id_paquete_seleted + " )";  
                const response = await this.request(this.path,{'order' : 'ORDER BY id_paquete DESC','action' : 'select','filter' : filtrarPor});
                try{ 
                    this.show_message(response.length + ' Registros Encontrados.','success');
                    this.paquete_detalleCollection = response;
                    this.paginaCollection = response;
                    this.paginator(1);  
                    this.isFormCrud=false;
                }catch(error){
                    this.show_message('No hay detalles del paquete.','info');
                    this.isFormCrud=false;
                } 
            }
        }, 
        async getpaquetes(){
            this.paqueteCollection  = []; 
            let filtrarPor =  "(nombre_paquete ILIKE '%" + this.filter + "%' )";  
            const response = await this.request('../../models/un/bd_paquete.php',{'order' : 'ORDER BY id_paquete DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Paquetes Encontrados.','success');
                this.paqueteCollection = response; 
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
            } 
        }, 
        async delete_paquete_detalle(id_paquete_detalle){   
            if(id_paquete_detalle > 0){
                const response = await this.request(this.path,{model:{'id_paquete_detalle':id_paquete_detalle},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getpaquete_detalles();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_paquete_detalle(){ 
            if (this.id_paquete_seleted > 0) {
                this.paquete_detalle.id_paquete = this.id_paquete_seleted;
                if(this.paquete_detalle.id_paquete_detalle > 0){
                    const response = await this.request(this.path,{model:this.paquete_detalle,'action' : 'update'});
                    if(response.message == 'Data Updated'){
                        this.show_message('Registro Actualizado','success');
                        this.model_empty();
                        this.isFormCrud = false;
                        await this.getpaquete_detalles();
                    }else{
                        this.show_message(response.message,'error');
                    }
                }else if(this.paquete_detalle.id_paquete_detalle == 0){ 
                    const response = await this.request(this.path,{model:this.paquete_detalle,'action' : 'insert'}); 
                     if(response.message == 'Data Inserted'){
                        this.show_message('Registro Guardado.','success');
                        this.model_empty();
                        this.isFormCrud = false;
                        await this.getpaquete_detalles();
                    }else{
                        this.show_message(response.message,'error');
                    }  
                }
            }else{
                this.show_message('Selecciona el Paquete por Favor.','error');
            } 
        },
        async update_paquete_detalle(id_paquete_detalle){ 
            if(id_paquete_detalle > 0){
                this.paquete_detalle = this.search_paquete_detalleByID(id_paquete_detalle);
                if(this.paquete_detalle.id_paquete_detalle > 0){
                    this.isFormCrud = true;
                    this.getColoresDisponibles(); 
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_paquete_detalle(){ 
            let id_paquete = this.paquete_detalle.id_paquete;
            this.model_empty();
            this.paquete_detalle.id_paquete = id_paquete;
            this.isFormCrud = true; 
        },  
        cancel_paquete_detalle(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_paquete_detalleByID(id_paquete_detalle){
            for (let index = 0; index < this.paquete_detalleCollection.length; index++) {
                const element = this.paquete_detalleCollection[index]; 
                if (id_paquete_detalle == element.id_paquete_detalle) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.paquete_detalle = {id_paquete_detalle:0,id_paquete:0,id_producto:0,cantidad:0,costo:'0.00',id_color:0,permitir_cambiar_color:false};
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
            // const response_paquete = await this.request('../../models/un/bd_paquete.php',{'order' : 'ORDER BY id_paquete DESC','action' : 'select'});
            // try{  
            //     if(response_paquete.length > 0){  
            //         this.paqueteCollection = response_paquete; 
            //     }  
            // }catch(error){
            //     this.show_message('No hay paquetes.','info');
            // }  
            const response_producto = await this.request('../../models/un/bd_producto.php',{'order' : 'ORDER BY id_producto DESC','action' : 'select'});
            try{  
                if(response_producto.length > 0){  
                    this.productoCollection = response_producto; 
                }  
            }catch(error){
                this.show_message('No hay productos.','info');
            } 

        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.paquete_detalleCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.paquete_detalleCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.paquete_detalleCollection[index];
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
       await this.getpaquetes();
       await this.fill_f_keys(); 
       let id_cmpra = document.getElementById("id_paquete").value; 
       id_cmpra > 0 ? this.id_paquete_seleted = id_cmpra : id_paquete_seleted = 0; 
       await this.getpaquete_detalles();
       this.paginator(1);
    }
}); 
        
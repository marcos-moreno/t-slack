 
var application = new Vue({
    el:'#app_tipo_movimiento',
    data:{ 
        tipo_movimiento : null,
        tipo_movimientoCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_tipo_movimiento.php',
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
        async gettipo_movimientos(){  
            this.tipo_movimientoCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre_tipo_movimiento ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_tipo_movimiento DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.tipo_movimientoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_tipo_movimiento(id_tipo_movimiento){   
            if(id_tipo_movimiento > 0){
                const response = await this.request(this.path,{model:{'id_tipo_movimiento':id_tipo_movimiento},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.gettipo_movimientos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_tipo_movimiento(){ 
            if(this.tipo_movimiento.id_tipo_movimiento > 0){
                const response = await this.request(this.path,{model:this.tipo_movimiento,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.gettipo_movimientos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.tipo_movimiento.id_tipo_movimiento == 0){ 
                const response = await this.request(this.path,{model:this.tipo_movimiento,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.gettipo_movimientos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_tipo_movimiento(id_tipo_movimiento){ 
            if(id_tipo_movimiento > 0){
                this.tipo_movimiento = this.search_tipo_movimientoByID(id_tipo_movimiento);
                if(this.tipo_movimiento.id_tipo_movimiento > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_tipo_movimiento(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_tipo_movimiento(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_tipo_movimientoByID(id_tipo_movimiento){
            for (let index = 0; index < this.tipo_movimientoCollection.length; index++) {
                const element = this.tipo_movimientoCollection[index]; 
                if (id_tipo_movimiento == element.id_tipo_movimiento) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.tipo_movimiento = {id_tipo_movimiento:0,nombre_tipo_movimiento:'',operador:'',prefix:''};
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
            let cantidad_pages = Math.ceil(this.tipo_movimientoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.tipo_movimientoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.tipo_movimientoCollection[index];
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
       await this.gettipo_movimientos();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
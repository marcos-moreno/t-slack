 
var application = new Vue({
    el:'#app_almacen',
    data:{ 
        almacen : null,
        almacenCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_almacen.php',
        typeMessage : '',
        msg:'',
        segmentoCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getalmacens(){  
            this.almacenCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre_almacen ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY activo DESC, nombre_almacen DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.almacenCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_almacen(id_almacen){   
            if(id_almacen > 0){
                const response = await this.request(this.path,{model:{'id_almacen':id_almacen},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getalmacens();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_almacen(){ 
            if(this.almacen.id_almacen > 0){
                this.almacen.activo = (this.almacen.activo == true || this.almacen.activo == "true" ? 'true' : 'false'); 
                const response = await this.request(this.path,{model:this.almacen,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getalmacens();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.almacen.id_almacen == 0){ 
                this.almacen.activo = (this.almacen.activo == true || this.almacen.activo == "true" ? 'true' : 'false'); 
                const response = await this.request(this.path,{model:this.almacen,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getalmacens();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_almacen(id_almacen){ 
            if(id_almacen > 0){
                this.almacen = this.search_almacenByID(id_almacen);
                if(this.almacen.id_almacen > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_almacen(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_almacen(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_almacenByID(id_almacen){
            for (let index = 0; index < this.almacenCollection.length; index++) {
                const element = this.almacenCollection[index]; 
                if (id_almacen == element.id_almacen) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.almacen = {id_almacen:0,nombre_almacen:'',id_segmento:'',activo:'true'};
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
             
            const response_segmento = await this.request('../../models/bd/bd_segmento.php',{'order' : 'ORDER BY id_segmento DESC','action' : 'select'});
            try{  
                if(response_segmento.length > 0){  
                    this.segmentoCollection = response_segmento; 
                }  
            }catch(error){
                this.show_message('No hay segmentos.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.almacenCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.almacenCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.almacenCollection[index];
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
       await this.getalmacens();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
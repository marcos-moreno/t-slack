 
var application = new Vue({
    el:'#app_grupo_marca',
    data:{ 
        grupo_marca : null,
        grupo_marcaCollection : [],
        isFormCrud: false,
        path : '../../models/sp/bd_grupo_marca.php',
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
        async getgrupo_marcas(){  
            this.grupo_marcaCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre_grupo_marca ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_grupo_marca DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.grupo_marcaCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_grupo_marca(id_grupo_marca){   
            if(id_grupo_marca > 0){
                const response = await this.request(this.path,{model:{'id_grupo_marca':id_grupo_marca},'action' : 'delete'}); 
                if(response.message == 'Data Deleted'){
                    await this.getgrupo_marcas();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_grupo_marca(){ 
            if(this.grupo_marca.id_grupo_marca > 0){
                const response = await this.request(this.path,{model:this.grupo_marca,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getgrupo_marcas();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.grupo_marca.id_grupo_marca == 0){ 
                const response = await this.request(this.path,{model:this.grupo_marca,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getgrupo_marcas();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_grupo_marca(id_grupo_marca){ 
            if(id_grupo_marca > 0){
                this.grupo_marca = this.search_grupo_marcaByID(id_grupo_marca);
                if(this.grupo_marca.id_grupo_marca > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_grupo_marca(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_grupo_marca(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_grupo_marcaByID(id_grupo_marca){
            for (let index = 0; index < this.grupo_marcaCollection.length; index++) {
                const element = this.grupo_marcaCollection[index]; 
                if (id_grupo_marca == element.id_grupo_marca) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.grupo_marca = {id_grupo_marca:0,nombre_grupo_marca:'',descripcion_grupo_marca:''};
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
            let cantidad_pages = Math.ceil(this.grupo_marcaCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.grupo_marcaCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.grupo_marcaCollection[index];
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
       await this.getgrupo_marcas();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
 
var application = new Vue({
    el:'#app_marca',
    data:{ 
        marca : null,
        marcaCollection : [],
        isFormCrud: false,
        path : '../../models/sp/bd_marca.php',
        typeMessage : '',
        msg:'', 

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        grupos_marca_collection : [], 
        marca_grupos_marca_collection : [],
        isAsingGroup : false,
        marca : [],

        filter : '',
    },
    methods:{
        async getGrupos_marca_collection(){
            const response = await this.request('../../models/sp/bd_grupo_marca.php',{'action' : 'select'}); 
            this.grupos_marca_collection = response;
        },
        async asingGroup(marca){
            this.marca_grupos_marca_collection = [];
            const response = await this.request('../../models/sp/bd_marca_grupro_marca.php',{model:this.marca,'action' : 'select','filter': " id_marca=" + marca.id_marca}); 
            for (let index = 0; index < this.grupos_marca_collection.length; index++) {
                const grupos = this.grupos_marca_collection [index];
                let seleccionado = false;
                let id_marca_grupro_marca = 0;
                try {
                    for (let index = 0; index < response.length; index++) {
                        const mgm = response[index]; 
                        if (grupos.id_grupo_marca == mgm.id_grupo_marca ) {
                            seleccionado = true; 
                            id_marca_grupro_marca = mgm.id_marca_grupro_marca; 
                         }   
                    }
                } catch (error) {  } 
                this.marca_grupos_marca_collection.push({id_marca_grupro_marca: id_marca_grupro_marca, id_marca: marca.id_marca,
                     id_grupo_marca: grupos.id_grupo_marca, selected : seleccionado, selectedOrigin:seleccionado,nombre_grupo_marca:grupos.nombre_grupo_marca});  
            }
            this.marca = marca;
            this.isAsingGroup = true;     
        },async saveGroups(){
            for (let index = 0; index < this.marca_grupos_marca_collection.length; index++) {
                const element = this.marca_grupos_marca_collection[index];
                if (element.selected != element.selectedOrigin) {
                    if (element.selected) {
                        const res = await this.request('../../models/sp/bd_marca_grupro_marca.php',{model:element,'action' : 'insert'}); 
                    }else{
                        const response = await this.request('../../models/sp/bd_marca_grupro_marca.php',{model:element,'action' : 'delete'}); 
                    } 
                }
            }
            this.isAsingGroup = false;
        },
        async getmarcas(){  
            this.marcaCollection = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre ILIKE '%" + this.filter + "%' )";  
            const response = await this.request(this.path,{'order' : 'ORDER BY id_marca DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.marcaCollection = response;  
                this.paginaCollection = response; 
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_marca(id_marca){  
            this.marca = this.search_marcaByID(id_marca);
            if(this.marca.id_marca > 0){
                const response = await this.request(this.path,{model:this.marca,'action' : 'delete'});
                this.marcaCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getmarcas();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_marca(){ 
            if(this.marca.id_marca > 0){
                const response = await this.request(this.path,{model:this.marca,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getmarcas();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.marca.id_marca == 0){ 
                const response = await this.request(this.path,{model:this.marca,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getmarcas();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_marca(id_marca){ 
            if(id_marca > 0){
                this.marca = this.search_marcaByID(id_marca);
                if(this.marca.id_marca > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_marca(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_marca(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_marcaByID(id_marca){
            for (let index = 0; index < this.marcaCollection.length; index++) {
                const element = this.marcaCollection[index]; 
                if (id_marca == element.id_marca) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 10000);
        },model_empty(){
            this.marca = {id_marca:0,nombre:'',descripcion:''};
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
            let cantidad_pages = Math.ceil(this.marcaCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.marcaCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.marcaCollection[index];
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
       await this.getmarcas();
       await this.model_empty();
       await this.fill_f_keys();
       await this.getGrupos_marca_collection();
       this.paginator(1);
    }
}); 
        
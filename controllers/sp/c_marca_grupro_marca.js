 
var application = new Vue({
    el:'#app_marca_grupro_marca',
    data:{ 
        marca_grupro_marca : null,
        marca_grupro_marcaCollection : [],
        isFormCrud: false,
        path : '../../models/sp/bd_marca_grupro_marca.php',
        typeMessage : '',
        msg:'',
        marcaCollection:[],
            grupo_marcaCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1
        ////paginador
    },
    methods:{
        async getmarca_grupro_marcas(){  
            const response = await this.request(this.path,{'order' : 'ORDER BY id_marca_grupro_marca DESC','action' : 'select'});
            try{ 
                this.show_message(response.length + ' Registro Encontrados.','success');
                this.marca_grupro_marcaCollection = response;   
                this.paginator(1);  
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
            } 
        }, 
        async delete_marca_grupro_marca(id_marca_grupro_marca){   
            if(id_marca_grupro_marca > 0){
                const response = await this.request(this.path,{model:{'id_marca_grupro_marca':id_marca_grupro_marca},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getmarca_grupro_marcas();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_marca_grupro_marca(){ 
            if(this.marca_grupro_marca.id_marca_grupro_marca > 0){
                const response = await this.request(this.path,{model:this.marca_grupro_marca,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getmarca_grupro_marcas();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.marca_grupro_marca.id_marca_grupro_marca == 0){ 
                const response = await this.request(this.path,{model:this.marca_grupro_marca,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getmarca_grupro_marcas();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_marca_grupro_marca(id_marca_grupro_marca){ 
            if(id_marca_grupro_marca > 0){
                this.marca_grupro_marca = this.search_marca_grupro_marcaByID(id_marca_grupro_marca);
                if(this.marca_grupro_marca.id_marca_grupro_marca > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_marca_grupro_marca(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_marca_grupro_marca(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_marca_grupro_marcaByID(id_marca_grupro_marca){
            for (let index = 0; index < this.marca_grupro_marcaCollection.length; index++) {
                const element = this.marca_grupro_marcaCollection[index]; 
                if (id_marca_grupro_marca == element.id_marca_grupro_marca) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.marca_grupro_marca = {id_marca_grupro_marca:0,id_marca:'',id_grupo_marca:''};
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
             
            const response_marca = await this.request('../../models/sp/bd_marca.php',{'order' : 'ORDER BY id_marca DESC','action' : 'select'});
            try{  
                if(response_marca.length > 0){  
                    this.marcaCollection = response_marca; 
                }  
            }catch(error){
                this.show_message('No hay marcas.','info');
            }  
            const response_grupo_marca = await this.request('../../models/sp/bd_grupo_marca.php',{'order' : 'ORDER BY id_grupo_marca DESC','action' : 'select'});
            try{  
                if(response_grupo_marca.length > 0){  
                    this.grupo_marcaCollection = response_grupo_marca; 
                }  
            }catch(error){
                this.show_message('No hay grupo_marcas.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.marca_grupro_marcaCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.marca_grupro_marcaCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.marca_grupro_marcaCollection[index];
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
       await this.getmarca_grupro_marcas();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
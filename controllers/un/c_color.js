 
var application = new Vue({
    el:'#app_color',
    data:{ 
        color : null,
        colorCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_color.php',
        typeMessage : '',
        msg:'',
        

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getcolors(){  
            this.colorCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre_color ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_color DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.colorCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_color(id_color){   
            if(id_color > 0){
                const response = await this.request(this.path,{model:{'id_color':id_color},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getcolors();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_color(){ 
            if(this.color.id_color > 0){
                this.color.activo = (this.color.activo == true || this.color.activo == "true" ? 'true' : 'false'); 
                const response = await this.request(this.path,{model:this.color,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getcolors();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.color.id_color == 0){ 
                this.color.activo = (this.color.activo == true || this.color.activo == "true" ? 'true' : 'false'); 
                const response = await this.request(this.path,{model:this.color,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getcolors();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_color(id_color){ 
            if(id_color > 0){
                this.color = this.search_colorByID(id_color);
                if(this.color.id_color > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_color(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_color(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_colorByID(id_color){
            for (let index = 0; index < this.colorCollection.length; index++) {
                const element = this.colorCollection[index]; 
                if (id_color == element.id_color) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.color = {id_color:0,nombre_color:'',activo:'true'};
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
            let cantidad_pages = Math.ceil(this.colorCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.colorCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.colorCollection[index];
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
       await this.getcolors();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
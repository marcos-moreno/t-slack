 
var application = new Vue({
    el:'#app_ev_atributo',
    data:{ 
        ev_atributo : null,
        ev_atributoCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_atributo.php',
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
        async getev_atributos(){  
            this.ev_atributoCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "( value ILIKE '%" + this.filter + "%'  OR descripcion ILIKE '%" + this.filter + "%'  OR tabla ILIKE '%" + this.filter + "%'  )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_atributo DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_atributoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_ev_atributo(id_atributo){  
            this.ev_atributo = this.search_ev_atributoByID(id_atributo);
            if(this.ev_atributo.id_atributo > 0){
                const response = await this.request(this.path,{model:this.ev_atributo,'action' : 'delete'});
                this.ev_atributoCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getev_atributos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_atributo(){ 
            if(this.ev_atributo.id_atributo > 0){
                const response = await this.request(this.path,{model:this.ev_atributo,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_atributos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_atributo.id_atributo == 0){ 
                const response = await this.request(this.path,{model:this.ev_atributo,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_atributos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_atributo(id_atributo){ 
            if(id_atributo > 0){
                this.ev_atributo = this.search_ev_atributoByID(id_atributo);
                if(this.ev_atributo.id_atributo > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_atributo(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_atributo(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_atributoByID(id_atributo){
            for (let index = 0; index < this.ev_atributoCollection.length; index++) {
                const element = this.ev_atributoCollection[index]; 
                if (id_atributo == element.id_atributo) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_atributo = {id_atributo:0,value:'',activo:'',descripcion:'',tabla:''};
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
            let cantidad_pages = Math.ceil(this.ev_atributoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_atributoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_atributoCollection[index];
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
       await this.getev_atributos();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
 
var application = new Vue({
    el:'#app_tabulador',
    data:{ 
        tabulador : null,
        tabuladorCollection : [],
        isFormCrud: false,
        path : '../../models/admin/bd_tabulador.php',
        typeMessage : '',
        msg:'',
        

        //paginador
        numByPag : 20, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async gettabuladors(){  
            this.tabuladorCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "( tabulador ILIKE '%" + this.filter + "%'  OR orden ILIKE '%" + this.filter + "%'  )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_tabulador DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.tabuladorCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_tabulador(id_tabulador){   
            if(id_tabulador > 0){
                const response = await this.request(this.path,{model:{'id_tabulador':id_tabulador},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.gettabuladors();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_tabulador(){ 
            if(this.tabulador.id_tabulador > 0){
                const response = await this.request(this.path,{model:this.tabulador,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.gettabuladors();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.tabulador.id_tabulador == 0){ 
                const response = await this.request(this.path,{model:this.tabulador,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.gettabuladors();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_tabulador(id_tabulador){ 
            if(id_tabulador > 0){
                this.tabulador = this.search_tabuladorByID(id_tabulador);
                if(this.tabulador.id_tabulador > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_tabulador(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_tabulador(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_tabuladorByID(id_tabulador){
            for (let index = 0; index < this.tabuladorCollection.length; index++) {
                const element = this.tabuladorCollection[index]; 
                if (id_tabulador == element.id_tabulador) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.tabulador = {id_tabulador:0,tabulador:'',id_empresa:'',activo:'',sueldo:'',costo_hora:'',septimo_dia:'',costo_hora_extra:'',orden:''};
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
            let cantidad_pages = Math.ceil(this.tabuladorCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.tabuladorCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.tabuladorCollection[index];
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
       await this.gettabuladors();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
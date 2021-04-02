 
var application = new Vue({
    el:'#app_ev_indicador',
    data:{ 
        ev_indicador : null,
        ev_indicadorCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_indicador.php',
        typeMessage : '',
        msg:'',
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
        puesto_nivel : {}

    },
    methods:{
        async getev_indicadors(){  
            this.ev_indicadorCollection  = [];
            this.paginaCollection = []; 
            let filtrarPor =  " ev_puesto_nivel_id = " +this.puesto_nivel.ev_puesto_nivel_id+ " AND ( nombre ILIKE '%" + this.filter + "%'  OR descripcion ILIKE '%" + this.filter + "%'  OR origen ILIKE '%" + this.filter + "%'  )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY ev_indicador_id DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_indicadorCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_ev_indicador(ev_indicador_id){  
            this.ev_indicador = this.search_ev_indicadorByID(ev_indicador_id);
            if(this.ev_indicador.ev_indicador_id > 0){
                const response = await this.request(this.path,{model:this.ev_indicador,'action' : 'delete'});
                this.ev_indicadorCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getev_indicadors();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_indicador(){ 
            if(this.ev_indicador.ev_indicador_id > 0){
                this.ev_indicador.ev_puesto_nivel_id = this.puesto_nivel.ev_puesto_nivel_id;
                const response = await this.request(this.path,{model:this.ev_indicador,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_indicadors();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_indicador.ev_indicador_id == 0){ 
                this.ev_indicador.ev_puesto_nivel_id = this.puesto_nivel.ev_puesto_nivel_id;
                const response = await this.request(this.path,{model:this.ev_indicador,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_indicadors();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_indicador(ev_indicador_id){ 
            if(ev_indicador_id > 0){
                this.ev_indicador = this.search_ev_indicadorByID(ev_indicador_id);
                if(this.ev_indicador.ev_indicador_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_indicador(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_indicador(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_indicadorByID(ev_indicador_id){
            for (let index = 0; index < this.ev_indicadorCollection.length; index++) {
                const element = this.ev_indicadorCollection[index]; 
                if (ev_indicador_id == element.ev_indicador_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_indicador = {ev_indicador_id:0,ev_puesto_nivel_id:this.puesto_nivel.ev_puesto_nivel_id,nombre:''
            ,descripcion:'',porcentaje:'',origen:'',creado:'',creadopor:'',actualizado:'',actualizadopor:'',tendencia:'CRECIENTE'};
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
            let cantidad_pages = Math.ceil(this.ev_indicadorCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_indicadorCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_indicadorCollection[index];
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
        let ev_puesto_nivel_id = document.getElementById("ev_puesto_nivel_id").value;
        if (!isNaN(ev_puesto_nivel_id) && ev_puesto_nivel_id > 0) {
            const puesto_nivel = await this.request('../../models/ev/bd_ev_puesto_nivel.php',{'action' : 'select','filter' : ' ev_puesto_nivel_id = ' + ev_puesto_nivel_id});
            if (puesto_nivel[0].ev_puesto_nivel_id > 0) {
                this.puesto_nivel = puesto_nivel[0];
            }else{
                location.href="v_ev_puesto_nivel.php";
            } 
        } else {
            location.href="v_ev_puesto_nivel.php";
        }
       await this.getev_indicadors();
       await this.model_empty(); 
       this.paginator(1);
    }
}); 
        
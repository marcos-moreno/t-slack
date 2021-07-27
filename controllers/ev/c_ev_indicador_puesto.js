 
var application = new Vue({
    el:'#app_ev_indicador',
    data:{ 
        ev_indicador : null,
        ev_indicadorCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_indicador_puesto.php',
        typeMessage : '',
        msg:'',
            

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
        puesto_nivel : {},
        indicadores_generales : [], 
        ev_indicador_general : {},
        totalPorcentaje : 0,
        ev_puesto : {ev_nivel_p:[]}
    },
    methods:{
        changeIndicador(){
            for (let index = 0; index < this.indicadores_generales.length; index++) {
                const element = this.indicadores_generales[index];
                if (element.ev_indicador_general_id == this.ev_indicador.ev_indicador_general_id) {
                    this.ev_indicador.ev_indicador_general[0] = element;
                }
            }  
        }, 
        async getev_indicadors(){  
            this.ev_indicadorCollection  = [];
            this.paginaCollection = [];    
            const response = await this.request(this.path,{
                'action' : 'select','filter' : this.ev_puesto.ev_puesto_id
            }); 
           try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_indicadorCollection = response;
                this.paginaCollection = response;
                this.paginator(1); 
                this.totalPorcentaje = 0; 
                this.ev_indicadorCollection.forEach(element => {
                    this.totalPorcentaje += element.porcentaje;
                });
                this.isFormCrud=false;
            }catch(error){
                console.log(error);
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_ev_indicador(ev_indicador_puesto_id){   
            if(ev_indicador_puesto_id > 0){
                const response = await this.request(this.path,{model:{'ev_indicador_puesto_id':ev_indicador_puesto_id},'action' : 'delete'});
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
            console.log(this.ev_indicador);
            if(this.ev_indicador.ev_indicador_puesto_id > 0){ 
                const response = await this.request(this.path,{model:this.ev_indicador,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_indicadors();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_indicador.ev_indicador_puesto_id == 0){  
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
        async update_ev_indicador(model){ 
            if(model.ev_indicador_puesto_id > 0){
                this.ev_indicador = model; 
                this.ev_indicador_general = this.ev_indicador.ev_indicador_general[0];
                if(this.ev_indicador.ev_indicador_puesto_id > 0){
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
            this.ev_indicador.ev_indicador_general_id=this.ev_indicador_general.ev_indicador_general_id;
            this.ev_indicador.ev_indicador_general[0]=this.ev_indicador_general;
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_indicadorByID(ev_indicador_puesto_id){
            for (let index = 0; index < this.ev_indicadorCollection.length; index++) {
                const element = this.ev_indicadorCollection[index]; 
                if (ev_indicador_puesto_id == element.ev_indicador_puesto_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_indicador = 
            {
                ev_indicador_puesto_id:0,ev_puesto_id:this.ev_puesto.ev_puesto_id,nombre:''
                ,descripcion:'',porcentaje:'',origen:'',creado:'',creadopor:'',actualizado:''
                ,actualizadopor:'',tendencia:'CRECIENTE',
                ev_indicador_general:[{ev_indicador_general_id:0,nombre:'',descripcion:'',tendencia:''}]
            }; 
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
            this.indicadores_generales = await this.request('../../models/ev/bd_ev_indicador_general.php',
                                            {'action' : 'select'}); 

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
        let ev_puesto_id = document.getElementById("ev_puesto_id").value;
        if (!isNaN(ev_puesto_id) && ev_puesto_id > 0) {
            const puesto_nivel = await this.request('../../models/ev/bd_ev_puesto.php',
            {'action' : 'select','searchID':true,'filter' : ev_puesto_id}); 
            if (puesto_nivel.length > 0) {
                this.ev_puesto = puesto_nivel[0];
            }else{
                location.href="v_ev_puesto.php";
            } 
        } else {
            location.href="v_ev_puesto.php";
        } 
        await this.fill_f_keys();
        await this.getev_indicadors();
        await this.model_empty();  
        this.paginator(1);
    }
}); 
        
 
var application = new Vue({
    el:'#app_ev_puesto',
    data:{ 
        ev_puesto : null,
        ev_puestoCollection : [],
        isFormCrud: false,
        path : '../../models/ev/bd_ev_puesto.php',
        typeMessage : '',
        msg:'',
        ev_nivel_pCollection:[],
            

        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
        perfil : {},
        modalPerfil:false,
        titleModalPerfil : ""

    },
    methods:{ 
        formatMXN(value) {
            if (value > 0) {
                var formatter = new Intl.NumberFormat('en-ES', {style: 'currency', currency: 'USD',});
                return formatter.format(value);
            }else{
                return '';
            } 
        },
        async getperfil(puesto){ 
            this.titleModalPerfil = `Perfil del puesto ${puesto.nombre_puesto} ${puesto.tipo} (${puesto.ev_nivel_p[0].nombre_nivel_puesto})`;
            const response = await this.request('../../models/ev/bd_ev_perfil_puesto.php'
            ,{'action' : 'select','id' : puesto.ev_puesto_id});
            this.perfil = response[0];
            this.modalPerfil = true;
        },
        async getev_puestos(){
            this.ev_puestoCollection  = [];
            this.paginaCollection = [];
            const response = await this.request(this.path,{'action' : 'select','filter' : this.filter});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_puestoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_ev_puesto(ev_puesto_id){  
            this.ev_puesto = this.search_ev_puestoByID(ev_puesto_id);
            if(this.ev_puesto.ev_puesto_id > 0){
                const response = await this.request(this.path,{model:this.ev_puesto,'action' : 'delete'});
                this.ev_puestoCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getev_puestos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_puesto(){ 
            if(this.ev_puesto.ev_puesto_id > 0){
                const response = await this.request(this.path,{model:this.ev_puesto,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_puestos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_puesto.ev_puesto_id == 0){ 
                const response = await this.request(this.path,{model:this.ev_puesto,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_puestos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_puesto(ev_puesto_id){ 
            if(ev_puesto_id > 0){
                this.ev_puesto = this.search_ev_puestoByID(ev_puesto_id);
                if(this.ev_puesto.ev_puesto_id > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_ev_puesto(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_puesto(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_ev_puestoByID(ev_puesto_id){
            for (let index = 0; index < this.ev_puestoCollection.length; index++) {
                const element = this.ev_puestoCollection[index]; 
                if (ev_puesto_id == element.ev_puesto_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_puesto = {ev_puesto_id:0,nombre_puesto:'',decripcion_puesto:'',creado:'',creadopor:'',actualizado:'',actualizadopor:'',codigo:'',tipo:'',ev_nivel_p_id:''};
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
             
            const response_ev_nivel_p = await this.request('../../models/ev/bd_ev_nivel_p.php',{'order' : 'ORDER BY ev_nivel_p_id DESC','action' : 'select'});
            try{  
                if(response_ev_nivel_p.length > 0){  
                    this.ev_nivel_pCollection = response_ev_nivel_p; 
                }  
            }catch(error){
                this.show_message('No hay ev_nivel_ps.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.ev_puestoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_puestoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_puestoCollection[index];
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
       await this.getev_puestos();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
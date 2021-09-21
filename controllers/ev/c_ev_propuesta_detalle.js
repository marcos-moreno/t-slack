 
var application = new Vue({
    el:'#app_ev_propuesta',
    data:{ 
        ev_propuesta : null,
        ev_propuestaCollection : [],
        ev_propuestaCollectionP : [],
        estadoCollection2: [],

        ev_propuestaCollection2 : [],
        isFormCrud: false,
        isFormCrud2: true,

        path : '../../models/ev/bd_ev_propuesta.php',
        typeMessage : '',
        msg:'',
        empleadoCollection:[],
        departamentoCollection:[],
        propuestaCollection:[],    

        //paginador
        numByPag : 15, 
        paginaCollectionP : [],
        paginas : [],
        paginaCollection : [],
        paginaCollection2 : [],

        paginaActual : 1,
        ////paginador

        filter : '',
        filterestado : '',



    },
    methods:{

        // async get_empleadoFilter(){
        //     this.isFormCrud2=false;
        //     this.getev_propuestas();
        //     this.getev_propuestas2();
        
        // }, 
        
        async getevConsulEstadoUser(){  
            this.ev_ticketCollection  = [];
            this.paginaCollection = [];
            const response = await this.request(this.path,{'action' : 'selectConEstadoUser','filterestado' : this.filterestado});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_propuestaCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 


        }, 
        
        async getev_propuestas(){  
            this.ev_propuestaCollection = [];
            this.paginaCollection = [];
            const response = await this.request('../../models/ev/bd_ev_propuesta.php',{model:this.ev_propuesta, 'action' : 'selectAll'});
            // const response = await this.request(this.path,{'action' : 'select','filter' : this.filter});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.ev_propuestaCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
      
        async delete_ev_propuesta(propuesta_id){   
            if(propuesta_id > 0){
                const response = await this.request(this.path,{model:{'propuesta_id':propuesta_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getev_propuestas();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_ev_propuesta(){ 
            if(this.ev_propuesta.propuesta_id > 0){
                console.log(this.ev_propuesta.propuesta_id);

                const response = await this.request(this.path,{model:this.ev_propuesta,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getev_propuestas();

                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.ev_propuesta.propuesta_id == 0){ 
                const response = await this.request(this.path,{model:this.ev_propuesta,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getev_propuestas();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_ev_propuesta(propuesta_id){ 
            if(propuesta_id > 0){
                this.ev_propuesta = this.search_ev_propuestaByID(propuesta_id);
                if(this.ev_propuesta.propuesta_id > 0){
                    this.save_ev_propuesta();
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
       
        add_ev_propuesta(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_ev_propuesta(){  
            this.model_empty();
            this.isFormCrud = false;
            this.isFormCrud2 = true;

        },  
        search_ev_propuestaByID(propuesta_id){
            for (let index = 0; index < this.ev_propuestaCollection.length; index++) {
                const element = this.ev_propuestaCollection[index]; 
                if (propuesta_id == element.propuesta_id) { 
                    return element;
                }
            }  
        },
        search_ev_propuestaByID2(propuesta_id){
            for (let index = 0; index < this.ev_propuestaCollection2.length; index++) {
                const element = this.ev_propuestaCollection2[index]; 
                if (propuesta_id == element.propuesta_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_propuesta = {propuesta_id:0,id_creadopor:'',fecha_creado:'',id_empleado:'',id_empleados:'',departamento_id:'',texto:'',estado:'',propuesta:'', check:'', estadoUp:''};
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

            const responsePropuesta = await this.request(this.path,{'action' : 'selectPropuesta'});
            try{  
                if(responsePropuesta.length > 0){  
                    this.propuestaCollection = responsePropuesta; 
                }  
            }catch(error){
                this.show_message('Sin opciones de propuestas en el sistema.','info');
            } 
            const responseDepartamento = await this.request('../../models/admin/bd_departamento.php',
            {'action' : 'getByLider'}); 
            try{  
                if(responseDepartamento.length > 0){  
                    this.departamentoCollection = responseDepartamento; 
                }  
            }catch(error){
                this.show_message('Nod se encontrarón Departamentos.','info');
            } 
            
            const response_empleado = await this.request('../../models/admin/bd_empleado.php',{'action' : 'gteEmpleadosByLider'});
            try{
                if(response_empleado.length > 0){
                   
                    this.empleadoCollection = response_empleado;
                }
            }catch(error){
                this.show_message('No hay empleados.','info');
            }
            const response_estado = await this.request(this.path,{'action' : 'selectEstado'});
            try{  
                if(response_estado.length > 0){  
                    console.log(response_estado);
                    this.estadoCollection2 = response_estado; 
                }  
            }catch(error){
                this.show_message('No hay estados disponibles .','info');
            } 

             
            // const response_empleado = await this.request('../../models/empleado/bd_empleado.php',{'order' : 'ORDER BY id_empleado DESC','action' : 'select'});
            // try{  
            //     if(response_empleado.length > 0){  
            //         this.empleadoCollection = response_empleado; 
            //     }  
            // }catch(error){
            //     this.show_message('No hay empleados.','info');
            // }  
            // const response_departamento = await this.request('../../models/departamento/bd_departamento.php',{'order' : 'ORDER BY departamento_id DESC','action' : 'select'});
            // try{  
            //     if(response_departamento.length > 0){  
            //         this.departamentoCollection = response_departamento; 
            //     }  
            // }catch(error){
            //     this.show_message('No hay departamentos.','info');
            // } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.ev_propuestaCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_propuestaCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_propuestaCollection[index];
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
        await this.getev_propuestas();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
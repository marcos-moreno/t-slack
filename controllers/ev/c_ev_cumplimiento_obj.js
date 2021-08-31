 
var application = new Vue({
    el:'#app_ev_cumplimiento_obj',
    data:{ 
        isFormCrud:true,
        path : '../../models/ev/bd_ev_cumplimiento_obj.php',
        typeMessage : '',
        msg:'',
        //ev_cumplimiento: {},
        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ev_cumpliCollection: [],
        ////paginador
        indicador:[],
        indicadorF: false,
        departamentos: [],
        estados: [],
        departamentoFilterCollection:[],
        depas:[],
        empleadoCollectionfiltro: [],
        empleadoselected_id: 0,
        indicadorSelected_id:0,
        // dateI: '',
        // dateF: '',
        // nameObj: '',
        // nameDes: '',
        // cumplimientoselected: '',
        muestra: false,
        tabla:false,
        boton:false,
        prueba:false,
        camposfecha: false,
        botong: false,
        div:true,

    },
    methods:{


        async fill_f_keys(){

            const responseDepartamento = await this.request('../../models/admin/bd_departamento.php',
            {'action' : 'getByLider'}); 
            try{  
                if(responseDepartamento.length > 0){  
                    this.departamentos = responseDepartamento; 
                }  
            }catch(error){
                this.show_message('Nod se encontrarón Departamentos.','info');
            } 
            
            const response_empleado = await this.request('../../models/admin/bd_empleado.php',{'action' : 'gteEmpleadosByLider'});
            try{
                if(response_empleado.length > 0){
                   
                    this.empleadoCollectionfiltro = response_empleado;
                }
            }catch(error){
                this.show_message('No hay empleados.','info');
            }

           
        },
        
        async get_empleadoFilter(){
            
            const responseIndicador = await this.request(this.path,{model:this.ev_cumplimiento
            ,'action' : 'selectCombo'}); 
            try{  
                if(responseIndicador.length > 0){
                    
                    this.indicador = responseIndicador;
                    
                }  
            }catch(error){
                this.show_message('No se encontro indicador','info');
                
            } 
        
        }, 
        async get_estado(){
            if(this.ev_cumplimiento.id_indicador === 24){
                const response_estado = await this.request(this.path,{'action' : 'selectEstado2'});
                    try{
                        if(response_estado.length > 0){
                            console.log(response_estado);
                            this.estados = response_estado;
                        }
                    }catch(error){
                    this.show_message('No hay Esatdos.','info');
                    }
            } else if(this.ev_cumplimiento.id_indicador === 11){
                const response_estado = await this.request(this.path,{'action' : 'selectEstado'});
                try{
                    if(response_estado.length > 0){
                        console.log(response_estado);
                        this.estados = response_estado;
                    }
                }catch(error){
                this.show_message('No hay Esatdos.','info');
                } 
            }
        },

        async get_register(){
            this.ev_cumpliCollection  = [];
            this.paginaCollection = []; 
            const responseIndicador = await this.request(this.path,{'action' : 'selectTabla'}); 
            // console.log(responseIndicador);
            try{ 
                this.show_message(responseIndicador.length + ' Cumplimientos y/o Negociaciones encotradas.','success');
                this.ev_cumpliCollection = responseIndicador;
                this.paginaCollection = responseIndicador;
                this.paginator(1);  
                this.tabla=true;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.tabla=false;
            }
        },
                
        async save(){
            if(this.ev_cumplimiento.fechatermino < this.ev_cumplimiento.fechainicio){
                this.show_message('Fecha no coherente verificalo.', 'error');
            }else {
                console.log('fecha coherente');
                if(this.ev_cumplimiento.nombre_objetivo == '' || this.ev_cumplimiento.descripcion == '' || this.ev_cumplimiento.estado == ''){
                    this.show_message('Hay campos vacios.', 'error');
                } else {
                    if(this.ev_cumplimiento.ev_cumplimiento_obj_id > 0){
                        // console.log(ev_cumplimiento_obj_id);
                        const response = await this.request(this.path,{model:this.ev_cumplimiento,'action' : 'update'});
                        console.log(response);
                        if(response.message == 'Data Updated'){
                            
                            this.show_message('Registro Actualizado','success');
                            this.model_empty();
                          
                        }else{
                            this.show_message(response.message,'error');
                        }
                   
                    } else if(this.ev_cumplimiento.ev_cumplimiento_obj_id == 0){
        
                        
                        
                        const response = await this.request(this.path,{model:this.ev_cumplimiento,'action' : 'insert'}); 
                        console.log(response);
                        if(response.message == 'Data Inserted'){
                            this.show_message('Registro Guardado.','success');
                            
                            this.get_register();
                            this.model_empty();
                       
                        }else{
                            this.show_message(response.message,'error');
                        } 
                    }
                }
                
            }

            
                 
        },
        
        async abre(){
            this.model_empty();
            
            this.tabla = false;
            this.boton =false;
           
        },

        async cancel_ev_cumpli(){
            this.model_empty();
            this.indicador = null;
            this.estados = null;
         
        },

        search_ev_cumpliID(ev_cumplimiento_obj_id){
            

            for (let index = 0; index < this.ev_cumpliCollection.length; index++) {
                const element = this.ev_cumpliCollection[index]; 
                if (ev_cumplimiento_obj_id == element.ev_cumplimiento_obj_id) {
                   
                    return element;
                }
            }  
        },

        async delete_ev_cumpli(ev_cumplimiento_obj_id){   
            if(ev_cumplimiento_obj_id > 0){
                const response = await this.request(this.path,{model:{'ev_cumplimiento_obj_id':ev_cumplimiento_obj_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.get_register();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },

        async update_ev_cumpli(ev_cumplimiento_obj_id){
            
            if(ev_cumplimiento_obj_id > 0){
                this.ev_cumplimiento = this.search_ev_cumpliID(ev_cumplimiento_obj_id);
                if(this.ev_cumplimiento.ev_cumplimiento_obj_id > 0){
                    this.tabla= false;
                    this.get_estado();
                 
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
            this.get_empleadoFilter();
        }, 

        async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.ev_cumplimiento ={
                ev_cumplimiento_obj_id: 0,
                id_empleado: 0,
                id_indicador: 0,
                fechainicio: '',
                fechatermino: '',
                estado: '',
                nombre_objetivo: '',
                descripcion:'',
                
             

            }

        
           
          this.tabla =true;
            
        },
       
        paginator(i){
            let cantidad_pages = Math.ceil(this.ev_cumpliCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.ev_cumpliCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.ev_cumpliCollection[index];
                        this.paginaCollection.push(element); 
                    }  
                }  
            }  
            this.paginas.push({'element':'Sig'});
        },
        async request(path,jsonParameters){
            const response = await axios.post(path, jsonParameters).then(function (response) {   
                    return response.data; 
                }).catch(function (response) {  
                    return response.data;
                })
            return response; 
        },
    },
    async mounted() {    
    },
    async created(){
       await this.model_empty();
       await this.fill_f_keys();
       await this.get_register();
       this.paginator(1);
    }
}); 
        
 
var application = new Vue({
    el:'#app_empleado',
    data:{ 
        empleado : null,
        empleadoCollection : [],
        isFormCrud: false,
        path : '../../models/generales/bd_empleado.php',
        typeMessage : '',
        msg:'',
        segmentoCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{
        async getempleados(){  
            this.empleadoCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "(nombre ILIKE '%" + this.filter + "%' )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_empleado DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.empleadoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_empleado(id_empleado){  
            this.empleado = this.search_empleadoByID(id_empleado);
            if(this.empleado.id_empleado > 0){
                const response = await this.request(this.path,{model:this.empleado,'action' : 'delete'});
                this.empleadoCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getempleados();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_empleado(){ 
            if(this.empleado.id_empleado > 0){
                const response = await this.request(this.path,{model:this.empleado,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getempleados();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.empleado.id_empleado == 0){ 
                const response = await this.request(this.path,{model:this.empleado,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getempleados();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_empleado(id_empleado){ 
            if(id_empleado > 0){
                this.empleado = this.search_empleadoByID(id_empleado);
                if(this.empleado.id_empleado > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_empleado(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_empleado(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_empleadoByID(id_empleado){
            for (let index = 0; index < this.empleadoCollection.length; index++) {
                const element = this.empleadoCollection[index]; 
                if (id_empleado == element.id_empleado) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.empleado = {id_empleado:0,id_segmento:'',id_creadopor:'',fecha_creado:'',nombre:'',paterno:'',materno:'',activo:'',celular:'',correo:'',enviar_encuesta:'',genero:'',id_actualizadopor:'',fecha_actualizado:'',usuario:'',password:'',fecha_nacimiento:'',nss:'',rfc:'',id_cerberus_empleado:''};
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
             
            const response_segmento = await this.request('../../models/bd/bd_segmento.php',{'order' : 'ORDER BY id_segmento DESC','action' : 'select'});
            try{  
                if(response_segmento.length > 0){  
                    this.segmentoCollection = response_segmento; 
                }  
            }catch(error){
                this.show_message('No hay segmentos.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.empleadoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.empleadoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.empleadoCollection[index];
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
       await this.getempleados();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
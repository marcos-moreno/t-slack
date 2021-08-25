 
var application = new Vue({
    el:'#app_empleado',
    data:{ 
        empleado : null,
        empleadoCollection : [],
        isFormCrud: false,
        path : '../../models/admin/bd_empleado.php',
        typeMessage : '',
        msg:'',
        un_tallaCollection:[],
        segmentoCollection:[],
        segmentoFilterCollection:[],  
        //paginador
        numByPag : 25, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador
        filter : '',
        empresas:[],
        empresa_id_filter:0,
        segmento_id_filter:0,
        activos_filter : true,
        myModelRol : false,
        dynamicTitle: "",
        rols:[],
        isDisabledSC:true,
        ev_puestoCollection:[],
        ev_puestoCollectionFiltro:[],
        filtroPuesto:"",
        departamentoCollection:[], 
    },
    methods:{
        
        async buscarValorPuesto(){
            this.ev_puestoCollectionFiltro = [];
            let asignado = false;
            for (let index = 0; index < this.ev_puestoCollection.length; index++) {
                const element = this.ev_puestoCollection[index];  
                let nomCompuesto = `${element.nombre_puesto} ${element.tipo} (${element.ev_nivel_p[0].nombre_nivel_puesto})`;
                if (nomCompuesto.toUpperCase().includes(this.filtroPuesto.toUpperCase())) {
                    this.ev_puestoCollectionFiltro.push(element);
                    if (asignado==false) {
                        this.empleado.ev_puesto_id = element.ev_puesto_id;
                        asignado = true;
                    }
                }
            }
        },
        async resetPassword(id_empleado){ 
            if(confirm("¿Estas seguro de Restablecer la Contraseña?"))
            {
                this.empleado = this.search_empleadoByID(id_empleado); 
                const response = await this.request(this.path,{model:this.empleado,'action' : 'resetPassword'});
                try {
                    if (response == "Reset Password Success") { 
                        alert("La contraseña del empleado con ID:" + id_empleado + " he sido Restablecida."); 
                    } else { 
                        alert("No se pudo completar la Acción."); 
                    }
                } catch (error) {
                    alert("No se pudo completar la Acción.");
                }
            } 
        },
        async getempleados(){  
            this.empleadoCollection  = [];
            this.paginaCollection = [];
            const response = await this.request(
                this.path
                ,{
                    'id_segmento' : this.segmento_id_filter
                    ,'activo' :this.activos_filter 
                    ,'id_empresa' :this.empresa_id_filter
                    ,'action' : 'select'
                    ,'filter' : this.filter.toString()
                }
            );
            console.log(response);
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
            if(id_empleado > 0){
                const response = await this.request(this.path,{model:{"id_empleado":id_empleado},'action' : 'delete'});
                console.log(response);
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
            this.empleado.activo == true || this.empleado.activo == 'true' ?  this.empleado.activo = 'true' : this.empleado.activo = 'false';
            this.empleado.correo_verificado == true || this.empleado.correo_verificado == 'true' ?  this.empleado.correo_verificado = 'true' : this.empleado.correo_verificado = 'false';

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
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 15000);
        },model_empty(){
            this.empleado = {id_empleado:0,id_segmento:'',id_creadopor:'',fecha_creado:'',nombre:''
            ,paterno:'',materno:'',activo:true,celular:'',
            correo:'',enviar_encuesta:'',genero:'',id_actualizadopor:''
            ,fecha_actualizado:'',usuario:'',password:'',fecha_nacimiento:''
            ,nss:'',rfc:'',id_cerberus_empleado:'',id_talla_playera:'',id_numero_zapato:''
            ,fecha_alta_cerberus:'',perfilcalculo:'',correo_verificado:false,id_empresa:''
            ,desc_mail_v:'',id_compac:0,departamento_id:null};
        },
        async request(path,jsonParameters){
            const response = await axios.post(path, jsonParameters).then(function (response) {   
                    return response.data; 
                }).catch(function (response) {  
                    return response.data;
                })
            return response; 
        },
        async get_segmentos(){
            const response_segmento = await this.request('../../models/admin/bd_segmento.php',
            {'order' : 'ORDER BY id_empresa,id_segmento ASC','action' : 'select'});
            try{  
                if(response_segmento.length > 0){  
                    this.segmentoCollection = response_segmento; 
                    // console.log(this.segmentoCollection);
                }  
                this.getempleados();
            }catch(error){
                this.show_message('No hay segmentos.','info');
            }  
        }, 
        async get_segmentosFilter(){
            const response_segmento = await this.request('../../models/admin/bd_segmento.php',
            {'order' : 'ORDER BY id_segmento ASC','action' : 'select',filter:" activo=true AND id_empresa = " + this.empresa_id_filter});
            try{  
                if(response_segmento.length > 0){  
                    this.segmentoFilterCollection = response_segmento; 
                }  
                this.getempleados();
            }catch(error){
                this.show_message('No hay segmentos.','info');
            }  
        }, 
        
        async fill_f_keys(){
            // this.get_segmentos(); 
            // this.get_segmentosFilter();
            const response_empresa = await this.request('../../models/bd/bd_company.php',{'action' : 'fetchall'});
            try{  
                if(response_empresa.length > 0){  
                    this.empresas = response_empresa; 
                }  
            }catch(error){
                this.show_message('No se encontrarón Puestos.','info');
            } 
            const response_ev_puesto = await this.request('../../models/ev/bd_ev_puesto.php',{'action' : 'select','filter' : ''});
            try{  
                if(response_ev_puesto.length > 0){  
                    this.ev_puestoCollection = response_ev_puesto; 
                    this.ev_puestoCollectionFiltro = this.ev_puestoCollection; 
                }  
            }catch(error){
                this.show_message('No se encontrarón Puestos.','info');
            }

            const response_departamento = await this.request('../../models/admin/bd_departamento.php'
            ,{'order' : 'ORDER BY id_empresa,id_segmento,nombre ASC','action' : 'select'});
            // console.log(response_departamento);
            try{  
                if(response_departamento.length > 0){  
                    this.departamentoCollection = response_departamento;  
                }  
            }catch(error){
                this.show_message('No se encontrarón Departamentos.','info');
            } 

            const response_tallas = await this.request('../../models/un/bd_talla.php'
            ,{'action' : 'select'});
            // console.log(response_departamento);
            try{  
                if(response_tallas.length > 0){  
                    this.un_tallaCollection = response_tallas;  
                }  
            }catch(error){
                this.show_message('No hay tallas.','info');
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
        }, 
        async asingRols(employee){  
            this.isDisabledSC = false;
            this.empleado = employee;
            const response = await axios.post('../../models/bd/bd_employeerole.php', {  action:'fetchall',id_empleado:this.empleado.id_empleado }).then(function(response){ return  response.data });
            this.rols = response;  
            this.dynamicTitle = this.empleado.nombre;
            this.myModelRol = true;   
           
        }, 
        async saveRols(){ 
            this.isDisabledSC = true; 
            const response = await axios.post('../../models/bd/bd_employeerole.php', {  action:'delete',id_empleado: this.empleado.id_empleado }).then(function(response){ return  response.data });
            // console.log(response);
            for (let index = 0; index < this.rols.length; index++) {
              const element = this.rols[index];
              if (element.selected) { 
                const response2 = await axios.post('../../models/bd/bd_employeerole.php', {  action:'insert',id_empleado: this.empleado.id_empleado,id_rol: element.id_rol }).then(function(response){ return  response.data });
                //  console.log(response2);
              } 
            } 
            this.empleado = null;
            this.myModelRol = false;   
          }

    },
    async mounted() {    
    },
    async created(){
       await this.model_empty();
       await this.fill_f_keys();
    //    await this.getempleados();
    //    this.paginator(1);
    }
}); 
        
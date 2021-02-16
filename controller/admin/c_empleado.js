 
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
            un_tallaCollection:[],
        //paginador
        numByPag : 25, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador
        filter : '',
        empresas:[],
        empresa_id_filter:1,
        segmento_id_filter:1,
        activos_filter : true
    },
    methods:{
        async resetPassword(id_empleado){ 
        if(confirm("¿Estas seguro de Restablecer la Contraseña?"))
            {
                this.empleado = this.search_empleadoByID(id_empleado); 
                console.log( this.empleado);
                const response = await this.request(this.path,{model:this.empleado,'action' : 'resetPassword'});
                try {
                    console.log(response);
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
            let filtrar_segmento = (this.segmento_id_filter > 0 ? " AND id_segmento = " + this.segmento_id_filter : " AND id_segmento = 0")
            filtrar_segmento = (this.segmento_id_filter == 'todo' ?
             " AND id_segmento IN (SELECT id_segmento FROM segmento WHERE activo = true AND id_empresa = " + this.empresa_id_filter + ")" : filtrar_segmento)
 
            let filtrarPor =  "( nombre ILIKE '%" + this.filter + "%'  OR paterno ILIKE '%" + this.filter + "%'  OR materno ILIKE '%"
                                 + this.filter + "%'  OR celular ILIKE '%" + this.filter + "%'  OR correo ILIKE '%" + this.filter +
                                  "%'  OR usuario ILIKE '%" + this.filter + "%'  OR password ILIKE '%" + this.filter + "%'  OR nss ILIKE '%" +
                                   this.filter + "%'  OR rfc ILIKE '%" + this.filter + "%'  OR perfilcalculo ILIKE '%" + this.filter + 
                                   "%'  OR   CAST (id_cerberus_empleado AS VARCHAR (100)) = '" + this.filter + "'  ) " + filtrar_segmento + "  AND  activo = " +  this.activos_filter + " ";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_empleado DESC','action' : 'select','filter' : filtrarPor});
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
            this.empleado = {id_empleado:0,id_segmento:'',id_creadopor:'',fecha_creado:'',nombre:'',paterno:'',materno:'',activo:true,celular:'',correo:'',enviar_encuesta:'',genero:'',id_actualizadopor:'',fecha_actualizado:'',usuario:'',password:'',fecha_nacimiento:'',nss:'',rfc:'',id_cerberus_empleado:'',id_talla_playera:'',id_numero_zapato:'',fecha_alta_cerberus:'',perfilcalculo:'',correo_verificado:false,id_empresa:'',desc_mail_v:''};
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
            const response_segmento = await this.request('../../models/admin/bd_segmento.php',{'order' : 'ORDER BY id_segmento ASC','action' : 'select',filter:" id_empresa = " + this.empresa_id_filter});
            try{  
                if(response_segmento.length > 0){  
                    this.segmentoCollection = response_segmento; 
                }  
                this.getempleados();
            }catch(error){
                this.show_message('No hay segmentos.','info');
            }  
        }, 
        async fill_f_keys(){
            this.get_segmentos(); 
            this.segmento_id_filter = 'todo';
            const response_empresa = await this.request('../../models/bd/bd_company.php',{'action' : 'fetchall'});
            try{  
                if(response_empresa.length > 0){  
                    this.empresas = response_empresa; 
                }  
            }catch(error){
                this.show_message('No hay Empresas.','info');
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
        
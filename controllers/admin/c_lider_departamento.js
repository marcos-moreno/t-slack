 
var application = new Vue({
    el:'#app_lider_departamento',
    data:{ 
        lider_departamento : null,
        lider_departamentoCollection : [],
        isFormCrud: false,
        path : '../../models/admin/bd_lider_departamento.php',
        typeMessage : '',
        msg:'',
        empleadoCollection:[],
        departamentoCollection:[{segmento:[{nombre:""}],empresa:[{empresa_observaciones:""}]}],
        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador
        filter : '',
        idDepatamento : 0,
        filtroLider : '',
        empleadoCollectionfiltro : []
    },
    methods:{
        async buscarValorLider(){
            this.empleadoCollectionfiltro = [];
            let asignado = false;
            for (let index = 0; index < this.empleadoCollection.length; index++) {
                const element = this.empleadoCollection[index];
                let nomCompuesto = element.paterno + ' ' + element.materno + ' ' + element.nombre;
                try {
                    if (nomCompuesto.toUpperCase().includes(this.filtroLider.toUpperCase())) {
                        this.empleadoCollectionfiltro.push(element);
                        if (asignado==false) {
                            this.lider_departamento.id_empleado = element.id_empleado;
                            asignado = true;
                        } 
                   }
                } catch (error) {
                    console.log(error);
                    this.empleadoCollectionfiltro = [];
                } 
            }
        },
        async getlider_departamentos(){  
            this.lider_departamentoCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  " departamento_id="+this.idDepatamento;  
            const response = await this.request(this.path,{'order' : 'ORDER BY lider_departamento_id DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.lider_departamentoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_lider_departamento(lider_departamento_id){   
            if(lider_departamento_id > 0){
                const response = await this.request(this.path,{model:{'lider_departamento_id':lider_departamento_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getlider_departamentos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_lider_departamento(){ 
            if(this.lider_departamento.lider_departamento_id > 0){
                const response = await this.request(this.path,{model:this.lider_departamento,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getlider_departamentos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.lider_departamento.lider_departamento_id == 0){ 
                const response = await this.request(this.path,{model:this.lider_departamento,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getlider_departamentos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_lider_departamento(lider_departamento_id){ 
            if(lider_departamento_id > 0){
                this.lider_departamento = this.search_lider_departamentoByID(lider_departamento_id);
                if(this.lider_departamento.lider_departamento_id > 0){
                    this.filtroLider = "";
                    this.empleadoCollectionfiltro = this.empleadoCollection;
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_lider_departamento(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        async cancel_lider_departamento(){  
            await this.getlider_departamentos();
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_lider_departamentoByID(lider_departamento_id){
            for (let index = 0; index < this.lider_departamentoCollection.length; index++) {
                const element = this.lider_departamentoCollection[index]; 
                if (lider_departamento_id == element.lider_departamento_id) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.lider_departamento = {lider_departamento_id:0,id_empleado:'',departamento_id:this.idDepatamento,tipo_lider:'Líder Departamento'};
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
             
            const response_empleado = await this.request('../../models/admin/bd_empleado.php',
            {'order' : 'ORDER BY id_empleado DESC','action' : 'select'});
            try{  
                if(response_empleado.length > 0){  
                    this.empleadoCollection = response_empleado; 
                    this.empleadoCollectionfiltro = response_empleado; 
                }  
            }catch(error){
                this.show_message('No hay empleados.','info');
            }  
            const response_departamento = await this.request('../../models/admin/bd_departamento.php'
            ,{'order' : 'ORDER BY departamento_id DESC','action' : 'select',filter:" departamento_id="+this.idDepatamento});
            try{  
                if(response_departamento.length > 0){  
                    this.departamentoCollection = response_departamento; 
                }  
            }catch(error){
                this.show_message('No hay departamentos.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.lider_departamentoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.lider_departamentoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.lider_departamentoCollection[index];
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
        let departamento_id = document.getElementById("departamento_id").value;
        if (!isNaN(departamento_id) && departamento_id > 0) {
            this.idDepatamento = departamento_id;
            await this.getlider_departamentos();
            await this.model_empty();
            await this.fill_f_keys();
            this.paginator(1);
        } else {
            location.href="v_departamento.php";
        }
      
    }
}); 
        
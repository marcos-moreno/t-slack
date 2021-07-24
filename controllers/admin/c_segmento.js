 
var application = new Vue({
    el:'#app_segmento',
    data:{ 
        segmento : null,
        segmentoCollection : [],
        isFormCrud: false,
        path : '../../models/admin/bd_segmento.php',
        typeMessage : '',
        msg:'',
        empresaCollection:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',
        filterCompany : 0,

    },
    methods:{
        async getsegmentos(){  
            this.segmentoCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "( nombre ILIKE '%" + this.filter + "%' ) AND (CASE WHEN " + this.filterCompany + " = 0 THEN id_empresa ELSE " + this.filterCompany + " END) = id_empresa ";  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_empresa,nombre ASC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.segmentoCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_segmento(id_segmento){   
            if(id_segmento > 0){
                const response = await this.request(this.path,{model:{"id_segmento":id_segmento},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getsegmentos();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_segmento(){ 
            if(this.segmento.id_segmento > 0){
                const response = await this.request(this.path,{model:this.segmento,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getsegmentos();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.segmento.id_segmento == 0){ 
                const response = await this.request(this.path,{model:this.segmento,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getsegmentos();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }  
            }
        },
        async update_segmento(id_segmento){ 
            if(id_segmento > 0){
                this.segmento = this.search_segmentoByID(id_segmento);
                if(this.segmento.id_segmento > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_segmento(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_segmento(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_segmentoByID(id_segmento){
            for (let index = 0; index < this.segmentoCollection.length; index++) {
                const element = this.segmentoCollection[index]; 
                if (id_segmento == element.id_segmento) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.segmento = {id_segmento:0,id_empresa:'',id_creadopor:'',fecha_creado:'',nombre:'',observaciones:'',activo:''
            ,id_actualizadopor:'',fecha_actualizado:'',id_cerberus:''};
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
            const response_empresa = await this.request('../../models/generales/bd_empresa.php',
            {'order' : 'ORDER BY id_empresa DESC','action' : 'select'});
            try{  
                if(response_empresa.length > 0){  
                    this.empresaCollection = response_empresa; 
                }  
            }catch(error){
                this.show_message('No hay empresas.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.segmentoCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.segmentoCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.segmentoCollection[index];
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
       await this.getsegmentos();
       await this.model_empty();
       await this.fill_f_keys();
       this.paginator(1);
    }
}); 
        
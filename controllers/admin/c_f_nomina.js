 
var application = new Vue({
    el:'#app_file_nomina',
    data:{ 
        file_nomina : null,
        file_nominaCollection : [],
        isFormCrud: false,
        path : '../../models/admin/bd_file_nomina.php',
        typeMessage : '',
        msg:'',
        empresaCollection:[],
             
        //paginador
        numByPag : 500, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',

        preview_file_load: true,
        src :null,
        empresas:[],
        empresa_id_filter:2,
    },
    methods:{
        async getfile_nominas(){  
            this.file_nominaCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "( nombre ILIKE '%" + this.filter + "%'  OR code ILIKE '%" + this.filter + "%'  OR type_file ILIKE '%" + 
            this.filter + "%'  ) AND id_empresa = " + this.empresa_id_filter;  
            const response = await this.request(this.path,{'order' : 'ORDER BY id_file_nomina DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.file_nominaCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async get_file(file){ 
            window.open(this.path+'?type_getFile_admin=1&id_file='+file.id_file_nomina, '_blank');
        }, 
        async delete_file_nomina(id_file_nomina){   
            if(id_file_nomina > 0){
                const response = await this.request(this.path,{model:{"id_file_nomina":id_file_nomina},'action' : 'delete'}); 
                if(response.message == 'Data Deleted'){
                    await this.getfile_nominas();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_file_nomina(){  
            if(this.file_nomina.id_file_nomina > 0){ 
            }else if(this.file_nomina.id_file_nomina == 0){ 
                if (this.file_nomina.id_empresa < 1) { 
                    alert("Por favor Selecciona la empresa.");
                    return;
                } 
                var files = document.getElementById("file").files;
                if (files.length < 1) { 
                    alert("Por favor Selecciona los documentos a cargar.");
                    return;
                }
                $("#modalLoading").modal(); 
                for (let index = 0; index < files.length; index++) {
                    const element = files[index];
                    let formData = new FormData();
                    formData.append('file', element);
                    formData.append('model', JSON.stringify(this.file_nomina));
                    formData.append('action', 'insert'); 
                    formData.append('type', 'file');  
                    const respuesta = await axios.post(this.path,
                        formData, {
                          headers: {
                            'Content-Type': 'multipart/form-data'
                          }
                        }
                      ).then(function (response) {
                        return response;
                      })
                      .catch(function (response) {
                        return response;
                      }); 
                      if (respuesta.data.status == "error") {
                        alert("Aceptar para continuar.\nExiste un error con el archivo: " + element.name + "  Error: "+ respuesta.data.message);
                      }else{
                        console.log(element.name + " : "+ respuesta.data.message);
                      } 
                }  
                await this.getfile_nominas();
                this.show_message('Proceso Completo.','success');
                this.model_empty();
                this.isFormCrud = false;
                $("#modalLoading").modal('hide'); 
            }
        },
        async update_file_nomina(id_file_nomina){ 
            if(id_file_nomina > 0){
                this.file_nomina = this.search_file_nominaByID(id_file_nomina);
                if(this.file_nomina.id_file_nomina > 0){
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_file_nomina(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_file_nomina(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_file_nominaByID(id_file_nomina){
            for (let index = 0; index < this.file_nominaCollection.length; index++) {
                const element = this.file_nominaCollection[index]; 
                if (id_file_nomina == element.id_file_nomina) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.file_nomina = {id_file_nomina:0,id_empresa:0};
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
             
            const response_empresa = await this.request('../../models/generales/bd_empresa.php',{'order' : 'ORDER BY id_empresa DESC','action' : 'select'});
            try{  
                if(response_empresa.length > 0){  
                    this.empresaCollection = response_empresa; 
                }  
            }catch(error){
                this.show_message('No hay empresas.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.file_nominaCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.file_nominaCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.file_nominaCollection[index];
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
        const response_empresa = await this.request('../../models/bd/bd_company.php',{'action' : 'fetchall'});
        this.empresas = response_empresa; 
        await this.getfile_nominas();
        await this.model_empty();
        await this.fill_f_keys();
        this.paginator(1);
    }
}); 
        
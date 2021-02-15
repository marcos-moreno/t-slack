 
var application = new Vue({
    el:'#app_enc_leccion',
    data:{ 
        enc_leccion : null,
        enc_leccionCollection : [],
        isFormCrud: false,
        path : '../../models/admin/bd_enc_leccion.php',
        typeMessage : '',
        msg:'',
        encuesta:[],
            

        //paginador
        numByPag : 5, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador

        filter : '',


    },
    methods:{ 
        validaForm(){
            try {
                console.log( this.enc_leccion.tipo);
                if (this.enc_leccion.nombre.length > 0) {
                    if (this.enc_leccion.descipcion.length > 0) {
                        if (this.enc_leccion.orden > 0) {
                            if (this.enc_leccion.tipo.length > 0) {
                                if ( this.enc_leccion.link.length > 0 && (this.enc_leccion.tipo == 'link' || this.enc_leccion.tipo == 'video' || this.enc_leccion.tipo == 'image')) {
                                    return true;
                                }else if ( this.enc_leccion.valor.length > 0 && this.enc_leccion.tipo == 'text') {
                                    return true;
                                }else{
                                    this.enc_leccion.tipo == 'text' ? this.show_message("El Valor del texto es obligatorio.",'info') : this.show_message("El Link es obligatorio.",'info');  
                                    return false;
                                }  
                            }else{
                                this.show_message("El tipo es obligatorio.",'info');
                                return false;
                            }
                        }else{
                            this.show_message("Es necesario ingresar el Orden.",'info');
                            return false;
                        }
                    }else{
                        this.show_message("La Descripción es obligatoria.",'info');
                        return false;
                    }
                }else{
                    this.show_message("El nombre es obligatorio.",'info');
                    return false;
                } 
            } catch (error) {
                this.show_message(error,'info');
            } 
        }
        ,async getenc_leccions(){  
            this.enc_leccionCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  "( nombre ILIKE '%" + this.filter + "%'  OR descipcion ILIKE '%" + this.filter + "%'  OR tipo ILIKE '%" + this.filter + "%'  OR link ILIKE '%" + this.filter + "%'  )";  
           const response = await this.request(this.path,{'order' : 'ORDER BY orden DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.enc_leccionCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_enc_leccion(id_enc_leccion){  
            this.enc_leccion = this.search_enc_leccionByID(id_enc_leccion);
            if(this.enc_leccion.id_enc_leccion > 0){
                const response = await this.request(this.path,{model:this.enc_leccion,'action' : 'delete'});
                this.enc_leccionCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getenc_leccions();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_enc_leccion(){  
            this.enc_leccion.id_encuesta = this.encuesta.id_encuesta;
            this.enc_leccion.inicio = (this.enc_leccion.inicio == 'true' || this.enc_leccion.inicio == true ? 'true':'false');
            this.enc_leccion.final = (this.enc_leccion.final == 'true' || this.enc_leccion.final == true ? 'true':'false');
            this.enc_leccion.leccion = (this.enc_leccion.leccion == 'true' || this.enc_leccion.leccion == true ? 'true':'false');
            if (this.enc_leccion.tipo == 'text') {
                var contenido = CKEDITOR.instances['ckeditor'].getData(); 
                this.enc_leccion.valor = contenido;
                // console.log(contenido);
            } else {
                this.enc_leccion.valor = "";
            }
            // console.log(this.enc_leccion.inicio);
            if (this.validaForm()) {
                if(this.enc_leccion.id_enc_leccion > 0){
                    const response = await this.request(this.path,{model:this.enc_leccion,'action' : 'update'});
                    if(response.message == 'Data Updated'){
                        await this.getenc_leccions();
                        this.show_message('Registro Actualizado','success');
                        this.model_empty();
                        this.isFormCrud = false;
                        CKEDITOR.instances['ckeditor'].setData("");
                        document.getElementById("textEditor").style.display = "none"; 
                    }else{
                        this.show_message(response,'error');
                    }
                }else if(this.enc_leccion.id_enc_leccion == 0){ 
                    const response = await this.request(this.path,{model:this.enc_leccion,'action' : 'insert'}); 
                     if(response.message == 'Data Inserted'){
                        await this.getenc_leccions();
                        this.show_message('Registro Guardado.','success');
                        this.model_empty();
                        this.isFormCrud = false;
                        CKEDITOR.instances['ckeditor'].setData("");
                        document.getElementById("textEditor").style.display = "none"; 
                    }else{
                        this.show_message(response,'error');
                    }  
                } 
            }
            
        },
        async update_enc_leccion(id_enc_leccion){ 
            if(id_enc_leccion > 0){
                this.enc_leccion = this.search_enc_leccionByID(id_enc_leccion);
                if(this.enc_leccion.id_enc_leccion > 0){
                    if (this.enc_leccion.tipo == 'text') {
                        document.getElementById("textEditor").style.display = "block"; 
                        CKEDITOR.instances['ckeditor'].setData(this.enc_leccion.valor);
                    } else {
                        CKEDITOR.instances['ckeditor'].setData("");
                        document.getElementById("textEditor").style.display = "none"; 
                    }
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_enc_leccion(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_enc_leccion(){  
            CKEDITOR.instances['ckeditor'].setData("");
            document.getElementById("textEditor").style.display = "none"; 
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_enc_leccionByID(id_enc_leccion){
            for (let index = 0; index < this.enc_leccionCollection.length; index++) {
                const element = this.enc_leccionCollection[index]; 
                if (id_enc_leccion == element.id_enc_leccion) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.enc_leccion = {id_enc_leccion:0,nombre:'',descipcion:'',id_encuesta:'',tipo:'',link:'',valor:'',inicio:false,final:false,creado:'',actualizado:'',creadopor:'',actualizadopor:'',leccion:true,orden:0};
        },
        async request(path,jsonParameters){
            const response = await axios.post(path, jsonParameters).then(function (response) {   
                    return response.data; 
                }).catch(function (response) {  
                    return response.data;
                })
            return response; 
        }, 
        paginator(i){ 
            let cantidad_pages = Math.ceil(this.enc_leccionCollection.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.enc_leccionCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.enc_leccionCollection[index];
                        this.paginaCollection.push(element); 
                    }  
                }  
            }  
            this.paginas.push({'element':'Sig'});
        }
        ,movementetype(){ 
            if(this.enc_leccion.tipo == "text"){
                console.log(this.enc_leccion.tipo);
                document.getElementById("textEditor").style.display = "block"; 
            }else{ 
                document.getElementById("textEditor").style.display = "none"; 
            }
        }

    },
    async mounted() {    
    },
    async created(){
        let id_encuesta = document.getElementById("id_encuesta").value; 
          
        const response_encuesta = await this.request('../../models/admin/bd_encuesta.php',{'order' : 'ORDER BY id_encuesta DESC','action' : 'select',"filter":" id_encuesta = " + id_encuesta });
            try{  
                if(response_encuesta.length > 0){  
                    this.encuesta = response_encuesta[0]; 
                }else{
                    location.href="p_poll.php" 
                }  
            }catch(error){
                location.href="p_poll.php"
            } 
       await this.getenc_leccions();
       await this.model_empty(); 
       this.paginator(1);
    }
}); 
        
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
        numByPag : 20, 
        paginas : [], 
        paginaActual : 1,
        ////paginador

        filter : '', 
        src :null, 
        iterator: [],
        view_modal: false
    },
    methods:{
        async getfile_nominas(){  
            this.file_nominaCollection  = []; 
            const response = await this.request(this.path,{'order' : 'ORDER BY id_file_nomina DESC','action' : 'select_user'});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.file_nominaCollection = response;  
                for (let index = 0; index < this.file_nominaCollection.length; index++) {
                    const elementOrigin = this.file_nominaCollection[index];
                    let existe = false;
                    for (let index = 0; index < this.iterator.length; index++) { 
                        if (this.iterator[index].semana == elementOrigin.semana && this.iterator[index].ejercicio == elementOrigin.ejercicio) 
                        { existe = true;}
                    }
                    if (existe == false) {
                        this.iterator.push({semana:elementOrigin.semana,ejercicio:elementOrigin.ejercicio,files:[]});
                    }
                }
                for (let i = 0; i < this.iterator.length; i++) {  
                    for (let index = 0; index < this.file_nominaCollection.length; index++) {    
                        if (this.file_nominaCollection[index].semana == this.iterator[i].semana
                            && this.file_nominaCollection[index].ejercicio == this.iterator[i].ejercicio) 
                        { 
                            this.iterator[i].files.push(this.file_nominaCollection[index]);
                        }
                    }
                }
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
            } 
        }, 
        async get_file(file){ 
            this.view_modal = true;  
            this.file_nomina = file; 
            const b64 = await this.request(this.path,
            {'action' : 'select_file_item',"model":this.file_nomina}); 
            var a = document.createElement("a"); //Create <a>
            a.href = 'data:' + this.file_nomina.type_file +';base64,' + b64; //file Base64 Goes here
            a.download = this.file_nomina.nombre.replace('/','_'); //File name Here
            a.click(); 
            this.view_modal = false; 
        },
        async base64ToArrayBuffer(base64) {
            base64 = base64.replace(/^data\:([^\;]+)\;base64,/gmi, '');
            var binaryString = atob(base64);
            var len = binaryString.length;
            var bytes = new Uint8Array(len);
            for (var i = 0; i < len; i++) {
                bytes[i] = binaryString.charCodeAt(i);
            }
            return bytes.buffer;
        },
        async show_message(msg,typeMessage){
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
       await this.getfile_nominas(); 
       this.paginator(1);
    }
}); 
        
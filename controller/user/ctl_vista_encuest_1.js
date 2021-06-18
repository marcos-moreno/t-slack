var application = new Vue({
    el:'#showPoll',
    data:{ 
        poll: []
        ,pollComplete: []
        ,lecciones: []

        ,modalLeccion:false
        ,lecciones:[]
        ,path : '../../models/admin/bd_enc_leccion.php'
        ,leccion: {}
        // ,encuesta_seleccionada:{} 
        ,force_view_leccion : false
        ,origen_leccion : ''


        //VIC
        ,modalPDFv2: false
        ,calle:''
        ,no_interior:''
        ,no_exterior:''
        ,estado:''
        ,ciudad:''
        ,codigo_postal:null
        ,municipio:''
        ,tipo_asentamiento:''
        ,asentamiento:''

        ,comboEstado:''
        ,comboMunicipio:''
        ,comboCuidad:''
        ,comboTipoAsentamiento:''
        ,comboAsentamiento:''
        
        ,obtenerempresa: []
        ,direccionEmpleado: []

        ,empresa:null
        ,modificado:''

        ,celular:''
        ,casa:''
        ,correo:''
        ,id_cp:''

        ,estadocivil:''
        ,elementos2: [
            { value: 'Soltero(a)', text: 'Soltero(a)' },
            { value: 'Casado(a)', text: 'Casado(a)' },
            { value: 'Unión Libre', text: 'Unión Libre' },
            { value: 'Divorciado', text: 'Divorciado' },
            { value: 'Viudo', text: 'Viudo' },
          ]


        ,escolaridad:''
        ,elementos: [
            { value: 'Sin Estudios', text: 'Sin Estudios' },
            { value: 'Primaria', text: 'Primaria' },
            { value: 'Secundaria', text: 'Secundaria' },
            { value: 'Bachillerato', text: 'Bachillerato' },
            { value: 'Preparatoria', text: 'Preparatoria' },
            { value: 'Licenciatura / Ingenieria', text: 'Licenciatura / Ingenieria' },
            { value: 'Maestría', text: 'Maestría' },
            { value: 'Doctorado', text: 'Doctorado' },

          ]





    },
    methods:{


        async getPoll(){ 

            const response = await this.seachPoll();
            this.poll = response; 
            const response2 = await this.seachPollComplete();
            this.pollComplete = response2;
            const response3 = await this.buscaEmpleado();
            this.obtenerempresa = response3;
            //console.log(response3);
            
            this.empresa = response3[0].id_empresa;
            this.modificado =  response3[0].modificado;

            if(this.empresa == "1" && this.modificado == false ){
                this.modalPDFv2 = true

                const response4 = await this.buscarDireccion();
                this.direccionEmpleado = response4;

                console.log(response4);
                this.calle = response4[0].calle;
                this.no_interior = response4[0].no_interior;
                this.no_exterior = response4[0].no_exterior;
                this.estado  = response4[0].estado;
                this.ciudad  = response4[0].cuidad;
                this.codigo_postal  = response4[0].codigo_postal;
                this.municipio  = response4[0].municipio;
                this.tipo_asentamiento  = response4[0].tipo_asentamiento;
                this.asentamiento  = response4[0].id_codigo_postal;
                this.celular = response4[0].celular;
                this.casa = response4[0].telefono_casa;
                this.correo = response4[0].correo_electronico;
                this.estadocivil = response4[0].estado_civil;
                this.escolaridad = response4[0].escolaridad;
                this.id_cp = response4[0].id_codigo_postal;

                this.buscarEstado();
                this.buscarMunicipio();
                this.buscarCiudad();
                this.buscarAsentamiento();
                this.seachaddress5();
                //this.seachaddress5v2(this.id_cp);

            }else{
                this.modalPDFv2 = false

            }

        },  
        openPoll(encuesta){
            console.log(encuesta);
            if (encuesta.estado_leccion == "CO" || encuesta.totallecciones == 0) { 
                location.href="usuario-encuesta.php?id_encuesta=" + encuesta.id_encuesta; 
            } else {
                this.force_view_leccion = true;
                this.getenc_leccions(encuesta); 
            }
        },
        seachPoll:async function(){
            return axios.post("../../models/bd/bd_show_poll.php", { 
                action:'getPooll'
                ,filter: 'pending' 
            })
            .then(function (response) {   
                return response.data; 
            })
            .catch(function (response) {  
            return [];
            })   
        },   
        seachPollComplete:async function(){
            return axios.post("../../models/bd/bd_show_poll.php", { 
                action:'seachPollComplete'
                ,filter: 'pending' 
            }).then(function (response) {   
                return response.data; 
            }).catch(function (response) {  
                return [];
            })   
        },  
        // Lecciones ->
        async getenc_leccions(encuesta){    
            if (encuesta.estado_leccion == "NO"){
                const response_leccion = await this.request("../../models/bd/bd_show_poll.php",{model:encuesta,'action' : 'insertLeccionEmpleado'});
                if(response_leccion.message == 'Data Inserted'){
                    for (let index = 0; index < this.poll.length; index++) { 
                        if (this.poll[index].id_encuesta == encuesta.id_encuesta ) {
                            this.poll[index].estado_leccion = "IN";
                            break;
                        }
                    }
                    for (let index = 0; index < this.pollComplete.length; index++) { 
                        if (this.pollComplete[index].id_encuesta == encuesta.id_encuesta ) {
                            this.pollComplete[index].estado_leccion = "IN";         
                            break;
                        }
                    }
                }else{
                    console.log(response_leccion);
                }
            } 

            this.lecciones  = []; 
            let filtrarPor =  " id_encuesta = "+ encuesta.id_encuesta + " AND leccion = true";  
            const response = await this.request(this.path,{'order' : ' ORDER BY orden ASC','action' : 'select','filter' : filtrarPor});
            try{  
                this.lecciones = response; 
            }catch(error){  
                this.lecciones = [];
            } 
            if (this.lecciones.length > 0 ) {
                this.modalLeccion = true;
                this.leccion = this.lecciones[0];
                this.leccion.index = 0;
            }  
        }, 
        async terminar_lecciones(id_encuesta){   
            for (let index = 0; index < this.poll.length; index++) { 
                if (this.poll[index].id_encuesta == id_encuesta ) {
                    if (this.poll[index].estado_leccion == "NO"){
                        const response_leccion = await this.request("../../models/bd/bd_show_poll.php",{model:this.poll[index],'action' : 'insertLeccionEmpleado'});
                        response_leccion.message == 'Data Inserted'? this.poll[index].estado_leccion = 'IN' : console.log(response_leccion);
                    }
                    if (this.poll[index].estado_leccion == "IN"){
                        const response_leccion_update = await this.request("../../models/bd/bd_show_poll.php",{model:this.poll[index],'action' : 'UpdateLeccionEmpleado'});
                        response_leccion_update.message == 'Data Updated' ? this.poll[index].estado_leccion = 'CO':console.log(response_leccion_update);
                    } 
                    break;
                }
            }
            for (let index = 0; index < this.pollComplete.length; index++) { 
                if (this.pollComplete[index].id_encuesta == id_encuesta ) {
                    if (this.pollComplete[index].estado_leccion == "NO"){
                        const response_leccion = await this.request("../../models/bd/bd_show_poll.php",{model:this.pollComplete[index],'action' : 'insertLeccionEmpleado'});
                        response_leccion.message == 'Data Inserted'? this.pollComplete[index].estado_leccion = 'IN' : console.log(response_leccion);
                    }
                    if (this.pollComplete[index].estado_leccion == "IN"){
                        const response_leccion_update = await this.request("../../models/bd/bd_show_poll.php",{model:this.pollComplete[index],'action' : 'UpdateLeccionEmpleado'});
                        response_leccion_update.message == 'Data Updated' ? this.pollComplete[index].estado_leccion = 'CO':console.log(response_leccion_update);
                    } 
                    break;
                }
            }
            this.modalLeccion=false;
            this.lecciones=[];
            this.force_view_leccion ? this.openPoll({'id_encuesta':id_encuesta,'estado_leccion':'CO'}) : console.log(""); 
        }, 
        async iterarLeccion(func,index){
            if (func == 'sig') {
                this.leccion = this.lecciones[index+1];
                this.leccion.index = index+1;
            }
            if (func == 'ant') {
                this.leccion = this.lecciones[index-1];
                this.leccion.index = index-1;
            }
       },
       
       async request(path,jsonParameters){
            const response = await axios.post(path, jsonParameters).then(function (response) {   
                    return response.data; 
                }).catch(function (response) {  
                    return response.data;
                })
            return response; 
        }, 
        // Lecciones <-

        //VIC

        buscaEmpleado:async function(){
            return axios.post("../../models/user/bd_address.php", { 
                action:'empleado'
                //,filter: 'pending' 
            })
            .then(function (response) {  
                //console.log(response.data[0].id_empresa);
                return response.data; 
            })
            .catch(function (response) {  
            return [];
            })   
        },

        buscarDireccion:async function(){
            return axios.post("../../models/user/bd_address.php", { 
                action:'empleadoDireccion'
                //,filter: 'pending' 
            })
            .then(function (response) {  
                //console.log(response.data);
                return response.data; 
            })
            .catch(function (response) {  
            return [];
            })   
        },        

        buscarEstado:async function(){

            let l = this;

            return axios.post("../../models/user/bd_address.php", { 
                action:'getAddress1'
                ,codigo_postal: this.codigo_postal 
            })
            .then(function (response) {
                //console.log(response.data);
                l.comboEstado=response.data;
                return response.data; 
            })
            .catch(function (response) {  
            return [];
            })   
        },  


        buscarMunicipio:async function(){
            let l = this;

            return axios.post("../../models/user/bd_address.php", { 
                action:'getAddress2'
                ,codigo_postal: this.codigo_postal 
            })
            .then(function (response) {
                //console.log(response.data);
                l.comboMunicipio=response.data;
                return response.data; 
            })
            .catch(function (response) {  
            return [];
            })   
        },  


        buscarCiudad:async function(){
            let l = this;

            return axios.post("../../models/user/bd_address.php", { 
                action:'getAddress3'
                ,codigo_postal: this.codigo_postal 
            })
            .then(function (response) {
                //console.log(response.data);
                l.comboCuidad=response.data ;
                return response.data; 
            })
            .catch(function (response) {  
            return [];
            })   
        },  

        buscarAsentamiento:async function(){
            let l = this;

            return axios.post("../../models/user/bd_address.php", { 
                action:'getAddress4'
                ,codigo_postal: this.codigo_postal 
            })
            .then(function (response) {
                //console.log(response.data);
                l.comboTipoAsentamiento=response.data ;
                return response.data; 
            })
            .catch(function (response) {  
            return [];
            })   
        },  


        seachaddress5v2:async function(id){
            let l = this;

            return axios.post("../../models/user/bd_address.php", { 
                action:'getAddress5v2'
                ,codigo_postal: this.codigo_postal
                ,id_cp : id
                ,tipo_asentamiento: this.tipo_asentamiento
            })
            .then(function (response) {
                //console.log(response.data);
                l.comboAsentamiento=response.data ;
                return response.data; 
            })
            .catch(function (response) {  
            return [];
            })   
        }, 

        seachaddress5:async function(){
            let l = this;

            return axios.post("../../models/user/bd_address.php", { 
                action:'getAddress5'
                ,codigo_postal: this.codigo_postal
                //,id_cp : this.id_cp
                ,tipo_asentamiento: this.tipo_asentamiento
            })
            .then(function (response) {
                console.log(response.data);

                //console.log(response.data[0].id_codigo_postal);
                //id_codigo_postal = response.data[0].id_codigo_postal
                l.comboAsentamiento=response.data ;
                return response.data; 
            })
            .catch(function (response) {  
            return [];
            })   
        }, 


		editarE(){


            if(this.celular == "" || this.celular == null){

                alert("Favor de Completar el Campo Teléfono Celular");

            }else if(this.casa == "" || this.casa == null){

                alert("Favor de Completar el Campo Teléfono de Casa");
    
            }else if(this.correo == "" || this.correo == null){

                alert("Favor de Completar el Campo Correo Electrónico");
    
            }else if(this.estadocivil == "" || this.estadocivil == null){

                alert("Favor de Completar el Campo Estado Civil");

            }else if(this.escolaridad == "" || this.escolaridad == null){

                alert("Favor de Completar el Campo Escolaridad");
    
            }else if(this.calle == "" || this.calle == null){

                alert("Favor de Completar el Campo Calle");
    
            }else if(this.no_interior == "" || this.no_interior == null){

                alert("Favor de Completar el Campo No. Interior");
   
            }else if(this.no_exterior == "" || this.no_exterior == null){

                alert("Favor de Completar el Campo No. Exterior");

            }else if(this.estado == "" || this.estado == null){

                alert("Favor de Completar el Campo Selecciona Estado");
                
            }else if(this.municipio == "" || this.municipio == null){

                alert("Favor de Completar el Campo Municipio");                

            }else if(this.tipo_asentamiento == "" || this.tipo_asentamiento == null){

                alert("Favor de Completar el Campo Tipo Asentamiento");

            }else if(this.asentamiento == "" || this.asentamiento == null){

                alert("Favor de Completar el Campo Asentamiento");

            }else{

                params = {

                    celular: this.celular
                    ,casa: this.casa
                    ,correo: this.correo
                    ,estadocivil: this.estadocivil
                    ,escolaridad: this.escolaridad
                    ,calle: this.calle
                    ,no_interior : this.no_interior
                    ,no_exterior : this.no_exterior
                    ,asentamiento : this.asentamiento
                    //checked : this.checked,
                    ,action:'editarDireccion'
                };
                
                axios.post('../../models/user/bd_address.php',params)
                .then((response)=>{
                console.log(response.data);
    
                alert(response.data.message);
    
                this.getPoll();
    
    
                });	


            }

		},



/*         editarE(){
            
            axios.post('../../models/user/bd_address.php', {
             action:'editarDireccion',
             calle: "asdas",
             no_interior : this.no_interior,
             no_exterior : this.no_exterior,
             codigo_postal : this.codigo_postal

            }).then(function(response){

             alert(response.data.message);
            });
           

       },
 */


        clean(){
            this.comboCuidad ='';
            this.comboMunicipio ='';
            this.comboEstado ='';
            this.comboTipoAsentamiento = '';
            this.comboAsentamiento = '';
            },

        
    },
    async mounted() {    
    },
    created: async function(){
        const account_response = await axios.post('../../models/user/bd_account.php', 
        {  action:'validaDatos' }).then(function(response){ return  response.data });
        console.log(account_response);

        if (account_response[0].valido=="false"||account_response[0].valido==false) {
            location.href ="account.php";
        }else{
            this.getPoll();
        } 
    }
   }); 

   function validarNum(e)
{
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 8) return true;
    patron = /\d/;
    te = String.fromCharCode(tecla);
    return patron.test(te);
}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46) {
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
    }
    return true;
  }
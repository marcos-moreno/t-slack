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
        ,origen_leccion : '',

        numByPag : 5,
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,

        evaluaciones_lider:[]
    },
    methods:{
        openEvaluacion(evaluacion){
            console.log(evaluacion);
            location.href="usuario-evaluacion.php?id_lider=" + evaluacion.id_lider + "&id_indicador=20&lider="+evaluacion.nombre_lider;
        },
        get_evaluaciones:async function(){
            const evaluaciones_lider = await axios.post("../../models/ev/bd_ev_evaluacion.php", {
                action:'select_evaluacion_lider'
            }).then(function (response) {
                return response.data;
            }).catch(function (response) {
                console.log(response);
                return [];
            });
            if (evaluaciones_lider.status == 'success') {
                this.evaluaciones_lider = evaluaciones_lider.data;
            }
        },
        async direccionar_encuesta_completa(id_encuesta){
            location.href = "resultado-usuario-encuesta.php?id_encuesta=" + id_encuesta;
        },
        async getPoll(){
            const response = await this.seachPoll();
            this.poll = response;
            const response2 = await this.seachPollComplete();
            this.pollComplete = response2;
        },  
        openPoll(encuesta){
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
        paginator(i){ 
            let cantidad_pages = Math.ceil(this.pollComplete.length / this.numByPag);
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
                    let fin = (cantidad_pages == i ? this.pollComplete.length : (parseInt(inicio) + parseInt(this.numByPag)));  
                    for (let index = inicio; index < fin; index++) {
                        const element = this.pollComplete[index];
                        this.paginaCollection.push(element); 
                    }  
                }  
            }  
            this.paginas.push({'element':'Sig'});
        },clase_x_status(estado){
            let value = "";
            estado = estado.toUpperCase();
            switch (estado) {
                case 'Correcto'.toUpperCase():
                    value = "p-2 mb-2 bg-success text-white";
                    break;
                case 'En captura'.toUpperCase():
                    value = "p-2 mb-2 bg-primary text-white";
                    break;
                case 'Contestada fuera de tiempo'.toUpperCase():
                    value = "p-2 mb-2 bg-info text-white";
                    break;
                case 'No se Respondió'.toUpperCase():
                    value = "p-2 mb-2 bg-danger text-white";
                    break;
                case 'Aún no disponible'.toUpperCase():
                    value = "p-2 mb-2 bg-secondary text-white"; 
                    break;
            }
            return value;
        }
    },
    async mounted() {
    },
    created: async function(){
        this.get_evaluaciones();
        const account_response = await axios.post('../../models/user/bd_account.php',{action:'validaDatos' })
        .then(function(response){
            return  response.data 
        });
        if (account_response[0].valido=="false"||account_response[0].valido==false) {
            location.href ="account.php";
        }else{
           await this.getPoll();
           this.paginator(1);
        }
    }
   }); 
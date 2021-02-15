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
    },
    methods:{
        async getPoll(){  
            const response = await this.seachPoll();
            this.poll = response; 
            const response2 = await this.seachPollComplete();
            this.pollComplete = response2; 
        },  
        openPoll(id_encuesta){
            console.log(id_encuesta);
            location.href="usuario-encuesta.php?id_encuesta=" + id_encuesta; 
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
        async getenc_leccions(id_encuesta){  
            this.lecciones  = []; 
            let filtrarPor =  " id_encuesta = "+ id_encuesta + " AND leccion = true";  
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
            console.log( this.lecciones);
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

    },
    async mounted() {    
    },
    created:function(){
        this.getPoll();
    }
   }); 
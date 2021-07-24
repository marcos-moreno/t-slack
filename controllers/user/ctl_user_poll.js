var application = new Vue({
    el:'#showPoll',
    data:{ 
        poll: null
        ,pollComplete: null
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
                return response.data;
                })   
            },   
            seachPollComplete:async function(){
            return axios.post("../../models/bd/bd_show_poll.php", { 
                action:'seachPollComplete'
                ,filter: 'pending' 
            })
            .then(function (response) {   
                return response.data; 
            })
            .catch(function (response) {  
            return response.data;
            })   
        },  
    },
    async mounted() {    
    },
    created:function(){
        this.getPoll();
    }
   }); 
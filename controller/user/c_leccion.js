
var leccion = new Vue({
    el:'#leccion',
    data:{
        modalLeccion:false,
        lecciones:[],
        path : '../../models/admin/bd_enc_leccion.php',
    },
    methods:{
        async getenc_leccions(id_encuesta){  
            this.lecciones  = []; 
            let filtrarPor =  " id_encuesta = '"+ id_encuesta ;  
           const response = await this.request(this.path,{'order' : 'ORDER BY id_enc_leccion DESC','action' : 'select','filter' : filtrarPor});
            try{  
                this.lecciones = response; 
            }catch(error){  
                this.lecciones = [];
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
    }, 

 
    mounted:function(){ 

    }
   });
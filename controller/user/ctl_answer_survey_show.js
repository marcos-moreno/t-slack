var application = new Vue({
    el:'#usuarioencuesta',
    data:{  
        btePressed:true   
        ,cargando:true 
        ,questions: []
        ,poll: {}
        ,getdatosdetermino:{}
    },
    methods:{  
        async getQuestions(id_encuesta){   
            let temporal = [];
            temporal = await axios.post("../../models/bd/bd_answer_survey.php", {action:'getQuestions',idEncuesta: id_encuesta})
            .then(function (response) {return response.data;})
            .catch(function (response) {return response.data;}); 

            for (let index = 0; index < temporal.length; index++) {
                const element = temporal[index];
                // Respuesta del empleado
                let temp = []; 
                temp = await axios.post("../../models/bd/bd_answer_survey.php", {action:'getRespuestasWET',idEncuesta: id_encuesta, id_pregunta : element.id_pregunta})
                .then(function (response) {return response.data;})
                .catch(function (response) {return response.data;});
                let resultaRespuestas = []; 
                for (let index = 0; index < temp.length; index++) {
                    const element = temp[index];
                    resultaRespuestas.push(element.respuesta);
                }
                let arma = element;
                arma.respuestas = resultaRespuestas;
                //// Respuestas del empleado

                // Busqueda de opciones válidas
                let tempOptionsCorrect = []; 
                tempOptionsCorrect = await axios.post("../../models/bd/bd_answer_survey.php", {action:'getRespuestasCorrectasCheckbox', id_pregunta : element.id_pregunta})
                .then(function (response) {return response.data;})
                .catch(function (response) {return response.data;});
                let resultaOptionsCorrect = []; 
                for (let index = 0; index < tempOptionsCorrect.length; index++) {
                    const element = tempOptionsCorrect[index];
                    resultaOptionsCorrect.push(element.nombre);
                }
                arma.valid_options = resultaOptionsCorrect;
                //// Busqueda de opciones válidas  
                this.questions.push(arma);   
            }
            // console.log(this.questions);
            // console.log(this.questions);
        },
    },
    async mounted() {    
    },
    created: async function(){ 
        let id_encuesta = document.getElementById("id_encuesta").value; 
        if (id_encuesta > 0) {  
                await this.getQuestions(id_encuesta); 
                this.poll = await axios.post("../../models/bd/bd_show_poll.php", {action:'getPoollByID', "id_encuesta" : id_encuesta})
                                .then(function (response) {return response.data[0];})
                                .catch(function (response) {return response.data;});

                this.datosdetermino = await axios.post("../../models/bd/bd_show_poll.php", {action:'getdatosdetermino', "id_encuesta" : id_encuesta})
                                .then(function (response) {return response.data[0];})
                                .catch(function (response) {return response.data;});
                                // console.log(this.datosdetermino);
                this.cargando = false; 
        } else {
            location.href="showPoll.php"; 
        } 
    }
   }); 
var application = new Vue({
    el:'#usuarioencuesta',
    data:{ 
        questions:null 
        ,btePressed:true   
        ,cargando:true
        ,is_upload:false
        ,poll:[]
    },
    methods:{
       async getRespuestas(){
            let array_answer_extra  = [] ;
            application.btePressed = true;
            let formValid = true;
            let validAnswers = []; 
            let other_answer_empty = true;
            this.questions.forEach(pregunta => {
 
                if (pregunta.direct_data) {
                    let res =  "";
                    try {
                        res = document.getElementById("res_" + pregunta.id_pregunta ).value;  
                    } catch (error) {
                        console.log(error);
                        console.log("pregunta error: " + pregunta.id_pregunta);
                    }
                    if ((res == '' || res == null) && pregunta.obligatoria) {
                        this.setColorLabel(pregunta.id_pregunta,"red");  
                        formValid = false;
                    } else {
                        this.setColorLabel(pregunta.id_pregunta,"black"); 
                        if (res != '' && res != null) {
                            validAnswers.push({"id_pregunta":pregunta.id_pregunta,"id_empleado":1,"id_opcion":null,"id_encuesta":pregunta.id_encuesta,"respuesta":res,"directa":1});
                        }
                    }  
                } else {
                    let arrayRespuestas = [] ;
                    pregunta.options.forEach(opcion => { 
                        if (document.getElementById(pregunta.id_pregunta + "_" + opcion.id_opcion).checked) {
                            arrayRespuestas.push({"respuesta": opcion.id_opcion });
                        }  
                    }); 
                    if (arrayRespuestas.length < 1  && pregunta.obligatoria) {
                        this.setColorLabel(pregunta.id_pregunta,"red");
                        formValid = false; 
                    } else {
                        this.setColorLabel(pregunta.id_pregunta,"black");
                        arrayRespuestas.forEach(element => { 
                          validAnswers.push({"id_pregunta":pregunta.id_pregunta,"id_empleado":1,"id_opcion":element.respuesta,"id_encuesta":pregunta.id_encuesta,"respuesta":null,"directa":0});
                        });
                    }  
                }  
                if (pregunta.id_tipo == 5 || pregunta.id_tipo ==  4) {
                    pregunta.options.forEach(opcion => { 
                        try {
                             if (opcion.respuesta_extra && document.getElementById(pregunta.id_pregunta + "_" + opcion.id_opcion).checked ) {
                                 try {
                                     let res = document.getElementById( opcion.id_opcion + "_respuesta_extra").value; 
                                     if (res != '' && res .length > 0 ) {
                                         this.setColorLabel(pregunta.id_pregunta,"black"); 
                                     }else{
                                         this.setColorLabel(pregunta.id_pregunta,"red");
                                         other_answer_empty = false;
                                         formValid = false; 
                                     } 
                                     array_answer_extra.push({"id_option": opcion.id_opcion , "value" : res});
                                 } catch (error) {
                                     console.log(error);
                                 } 
                             }  
                        } catch (error) {
                             alert("Existe un error, or favor reportalo con los Administradores.");
                             console.log(error);
                             return;
                        } 
                     });   
                }
            
            });   
            
            if (formValid) {
                this.is_upload = true;
                await this.completeForm(validAnswers,array_answer_extra); 
                this.is_upload = false;
            }else{
                this.is_upload = false;
                other_answer_empty ? alert("Responde a todas las Preguntas por favor."): alert("Especifica la opción Seleccionada.");   
                this.btePressed = false;
            } 
        },
        async completeForm(validAnswers,array_answer_extra){ 
            const result2 = await this.insertEmpleado_encuesta(validAnswers);  
            console.log(result2); 
            try {
                if (result2.message == "inserEncuesta_empleado Success") {
                    for (let index = 0; index < validAnswers.length; index++) {
                        const respuesta = validAnswers[index];
                        const result = await this.insertAnswer(respuesta); 
                        // console.log(respuesta); 
                    } 
                    console.log(array_answer_extra); 
                    for (let index = 0; index < array_answer_extra.length; index++) {
                        const answer_extra = array_answer_extra[index];
                        const result_answer_extra = await this.insert_answer_extra(answer_extra); 
                        // console.log(answer_extra); 
                    } 
                    location.href = "resultado-usuario-encuesta.php?id_encuesta=" + validAnswers[0].id_encuesta;
                } else {
                    alert("Revisa tu conexión.");
                }
            } catch (error) {
                alert("Revisa tu conexión. " + error);
            }
           
             
            // location.href = "resultado-usuario-encuesta.php?id_encuesta=" + validAnswers[0].id_encuesta;
            // Nueva posicion


            // const resultEnc = await axios.post("../../models/bd/bd_poll.php", { 
            //     action:'fetchSingle'
            //     ,id_encuesta: validAnswers[0].id_encuesta     
            // })
            // .then(function (response) { return response.data;}).catch(function (response) {return response.data;}) 
            // if (resultEnc.link_final == true || resultEnc.link_final == "true") {
            //     location.href= resultEnc.link_final; 
            // } else {
            //     location.href="showPoll.php"; 
            // }
        },
        async insertEmpleado_encuesta(validAnswers){
            return axios.post("../../models/bd/bd_answer_survey.php", { 
                action:'inserEncuesta_empleado'
                ,id_encuesta: validAnswers[0].id_encuesta     
            })
            .then(function (response) {   
                return response.data; 
            })
            .catch(function (response) {  
            return response.data;
            }) 
        },
        async insertAnswer(array_respuesta){
            return axios.post("../../models/bd/bd_answer_survey.php", { 
                action:'insertAnswer'
                ,respuesta: array_respuesta
            })
            .then(function (response) {   
                return response.data; 
            })
            .catch(function (response) {  
            return response.data;
            })  
        },

        async insert_answer_extra(answer_extra){
            return axios.post("../../models/bd/bd_answer_survey.php", { 
                action:'insert_answer_extra'
                ,answer_extra: answer_extra
            })
            .then(function (response) {   
                return response.data; 
            })
            .catch(function (response) {  
            return response.data;
            })  
        },
        setColorLabel(id, color){
            document.getElementById("label_" + id ).style.color = color;
        },
        async getQuestions(id_encuesta){   
            let myArray ;
            const result = await this.seachQuestions(id_encuesta); 
            myArray = result; 
            for (let index = 0; index < myArray.length; index++) {
                const result = await this.seachOption(myArray[index].id_pregunta); 
                myArray[index]['options'] = result; 
            } 
            this.questions = myArray; 
            console.log(this.questions);
        },
        seachQuestions:function(id_encuesta){
            return axios.post("../../models/bd/bd_answer_survey.php", { 
                action:'fetchallQuestion'
                ,idEncuesta: id_encuesta
            })
            .then(function (response) {  
                return response.data; 
            })
            .catch(function (response) {  
            return response.data;
            })   
        },  
        seachOption:function(vidQuestion){
            return axios.post("../../models/bd/bd_answer_survey.php", { 
                action:'fetchallOption',
                idQuestion: vidQuestion
            })
            .then(function (response) {   
                return response.data; 
            })
            .catch(function (response) {  
            return response.data;
            })   
        },
        async isValidPoll(id_encuesta){
            const valido =  await 
            axios.post("../../models/bd/bd_answer_survey.php", { 
                action:'validPoll',
                id_encuesta: id_encuesta
            })
            .then(function (response) {   
                return response.data[0].res; 
            })
            .catch(function (response) {  
            return response.data;
            })   
             return valido;
        } 
    },
    async mounted() {    
    },
    created: async function(){ 
        let id_encuesta = document.getElementById("id_encuesta").value; 
        if (id_encuesta > 0) {
            const valido = await this.isValidPoll(id_encuesta); 
            console.log(valido);
            if (valido) {
                this.poll = await axios.post("../../models/bd/bd_show_poll.php", {action:'getPoollByID', "id_encuesta" : id_encuesta})
                                .then(function (response) {return response.data[0];})
                                .catch(function (response) {return response.data;});

                await this.getQuestions(id_encuesta); 
                this.btePressed = false;
                this.cargando = false;
            } else {
                alert("La encuesta ya no esta disponible.");
                location.href="showPoll.php"; 
            } 
        } else {
            location.href="showPoll.php"; 
        } 
    }
   }); 
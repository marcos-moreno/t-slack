var application = new Vue({
    el:'#usuarioencuesta',
    data:{ 
        questions:null 
        ,btePressed:false
        ,cargando:true
        ,is_upload:false
        ,puntos_evaluar:[]
        ,intent_save : false
        ,id_lider : 0
        ,status_termino : false, estado_descripcion : false,estado_result : "", termino: false,
    },
    methods:{
        valueDefault(){
            for (let index = 0; index < this.puntos_evaluar.length; index++) {
                this.puntos_evaluar[index].respuesta = "5";
            }
        },
        async getRespuestas(){
            this.is_upload = true;
            let jsonRespuestas = [];
            this.intent_save = true;
            for (let index = 0; index < this.puntos_evaluar.length; index++) {
                const element = this.puntos_evaluar[index];
                if (element.respuesta == '') {
                    alert("Por favor responde a todas las preguntas.");
                    return false; 
                } 
                let value = element.ev_punto_evaluar_ln.find(x => x.valor == element.respuesta);
                value = (value != undefined ? value.nombre : element.respuesta);
                jsonRespuestas.push({
                    respuesta:(element.es_evaluado?element.respuesta:0),ev_indicador_general_id:element.ev_indicador_general_id
                    ,ev_punto_evaluar_id:element.ev_punto_evaluar_id,es_evaluado:element.es_evaluado,
                    respuest_valor : value
                });
            }
            const responce_save_dat_evaluac_por_user = await axios.post("../../models/ev/bd_ev_evaluacion.php", 
            {
                action:'save_dat_evaluac_por_user',
                "id_lider" : this.id_lider, 
                "respuestas_collection" :  JSON.stringify(jsonRespuestas)
            })
            .then(function (response) {return response.data;})
            .catch(function (response) {return response;});
            this.is_upload = false;
            // console.log(jsonRespuestas);
            // console.log(responce_save_dat_evaluac_por_user); 
            if (responce_save_dat_evaluac_por_user.status == "error") {
                this.status_termino = false;
                if (responce_save_dat_evaluac_por_user.message.indexOf("duplicate key value violates unique constraint") > 0) {
                    this.estado_descripcion = "Al parecer esta evaluación ya fué realizada.";
                }else{
                    this.estado_descripcion = responce_save_dat_evaluac_por_user.message;
                }
            } else {
                this.status_termino = true;
                this.estado_descripcion = "Gracias por tu información.";
            }
            this.termino = true;
        },
        async insertEmpleado_encuesta(validAnswers){
            return axios.post("../../models/bd/bd_answer_survey.php", {
                action:'inserEncuesta_empleado'
                ,id_encuesta: validAnswers[0].id_encuesta     
            }).then(function (response) {   
                return response.data; 
            }).catch(function (response) {  
                return response.data;
            })
        }, 
    },
    async mounted() {  
    },
    created: async function(){
        this.id_lider = document.getElementById("id_lider").value; 
        let id_indicador = document.getElementById("id_indicador").value;
        if (this.id_lider > 0 && id_indicador > 0) {
            const responce_evaluacion = await axios.post(
                "../../models/ev/bd_ev_evaluacion.php", 
                {
                    action:'select_puntos_evaluar'
                    ,id_lider: this.id_lider
                    ,id_indicador: id_indicador
                }
            ).then(function (response) {return response.data;}
            ).catch(function (response) {return response.data;});
            if (responce_evaluacion.status == "success") {
                this.puntos_evaluar = responce_evaluacion.data;
                this.cargando = false;
            }else{
                this.puntos_evaluar = [];
            }
        } else {
            location.href="showPoll.php"; 
        } 
    }
   }); 
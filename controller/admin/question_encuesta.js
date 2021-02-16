var application_poll = new Vue({ 
    el:'#crudPollQuestion',
    data:{
        poll:{"id":0,"company":null,"poll_name":"","poll_help":"","checked":true,"poll_validfrom":"","poll_validUntil":"","resp_direct_quest_value":""},
        allData_QuestionPoll: null,
        questionSelected : null,
        myModelPoll:false,
        myModelPoll2:false,
        actionButton:'Agregar',
        dynamicTitle:'',
        hiddenId  : null ,
        companys : null ,
        pollSelected : null,
        isDisabledSC:true,
        msgError:false,
        msg:false,
        alert: "",
        tipos : "",
        options: null,
        btePressed: false,
        disNewOption : true,
        resp_direct_quest_visible : false, 
        resp_direct_quest_type : "",
        options_resp_valid: [],
        formOption : false,
        option : {"id_opcion":0,"opcion":"","op_activo":true,action: "",id_pregunta: 0,"respuesta_extra":false}
        ,respaldo_QuestionPoll : []
    },
    methods:{ 
        async req_response_valid(origen){
            if (origen) {
                this.questionSelected.resp_direct_quest_value = "";
            }
            if (this.questionSelected.is_evaluated == "true" || this.questionSelected.is_evaluated == true) { 
                for (let index = 0; index < this.tipos.length; index++) {
                    const element = this.tipos[index];
                    if (element.id_tipo == this.questionSelected.id_tipo ) { 
                            this.resp_direct_quest_visible = true;
                            this.resp_direct_quest_type = element.tipo;
                            if (element.tipo == 'select' || element.tipo == 'radio') { 
                                this.options_resp_valid = await axios.post("../../models/bd/bd_option.php", { action:'fetchallOption',  
                                    idQuestion: this.questionSelected.id_pregunta }).then(function (response) { return response.data;   })  .catch(function (response) {    return response.data;  }) 
                            }
                            this.questionSelected.direct_data = element.direct_data
                            return; 
                    }else{
                        this.resp_direct_quest_visible = false;
                        this.resp_direct_quest_type = ""; 
                    }
                }       
            }else{
                console.log("No" + this.questionSelected.is_evaluated); 
                console.log(this.questionSelected);
                this.resp_direct_quest_visible = false;
                this.resp_direct_quest_type = "";
            } 
        }, 
        openModel(funct,id){
            this.msgError = false;
            this.msg = false;
            this.alert = ""; 
            if (funct == 'add') {
                this.actionButton = "Agregar";
                this.dynamicTitle = "Nueva pregunta."
                this.myModelPoll = true;
                this.questionSelected = {"id_pregunta":0,"id_encuesta":this.poll.id,"nombre_pregunta":"","activo":true,"id_tipo":0,"numero_pregunta":this.maxValuePosition(),"obligatoria":true,"is_evaluated":"false","resp_direct_quest_value":"",direct_data:true};
                this.req_response_valid(false);
            } else if(funct == 'mod'){
                this.actionButton = "Actualizar";
                if (id > 0) {  
                    this.allData_QuestionPoll.forEach(element => { 
                        element.id_pregunta == id ?  this.questionSelected =  element : console.log();
                    }); 
                   this.dynamicTitle = "Modificar pregunta." 
                   this.myModelPoll = true;   
                   this.req_response_valid(false);
                } else {
                    alert("La selección es incorrecta.");
                } 
            }
        },maxValuePosition(){
            let res = 0;
            this.allData_QuestionPoll.forEach(element => { 
                 if(res < element.numero_pregunta){
                    res = element.numero_pregunta;
                 }   
            }); 
            return res + 1;
        },async save(){
            let action = '';  
            this.questionSelected.is_evaluated = (this.questionSelected.is_evaluated == 'true' || this.questionSelected.is_evaluated == true ? 'true':'false');
            this.questionSelected.activo = (this.questionSelected.activo == 'true' || this.questionSelected.activo == true ? 'true':'false');
            this.questionSelected.obligatoria = (this.questionSelected.obligatoria == 'true' || this.questionSelected.obligatoria == true ? 'true':'false');
            
            if (  this.questionSelected.is_evaluated == 'false')  {
                this.questionSelected.resp_direct_quest_value = '';
            }
            if (this.validForm()) {
                this.questionSelected.id_pregunta > 0 ? action = 'update' : action = 'insert'; 
                console.log( this.questionSelected);
                const responseSave = await axios.post('../../models/bd/bd_question.php', {  action:action, model: this.questionSelected}).then(function(response){ return  response });
                this.questionSelected.id_pregunta > 0 ? application_poll.manageError(responseSave,'Data Updated','Actualizado.')   : application_poll.manageError(responseSave,'Data Inserted','Datos Guardados.'); 
                this.dynamicTitle = "" 
                this.myModelPoll = false;   
                await this.loadModelQuestion(); 
            } 
        },validForm(){
            try {
                if (this.questionSelected.nombre_pregunta != '')  
                {
                    if (this.questionSelected.id_tipo != 0){  
                        if (this.IsNumeric(this.questionSelected.numero_pregunta) ){
                            if (    
                                    (this.questionSelected.is_evaluated == true || this.questionSelected.is_evaluated == "true" ) &&  
                                    (this.questionSelected.direct_data == true || this.questionSelected.direct_data == "true") 
                                    || (this.questionSelected.id_tipo == 4 && (this.questionSelected.is_evaluated == true || this.questionSelected.is_evaluated == "true" ) )
                                ){
                                    if (this.questionSelected.id_tipo == 5 || this.questionSelected.id_tipo == 4 || this.questionSelected.id_tipo == 1) {
                                        return true;
                                    }else{
                                        if (this.questionSelected.resp_direct_quest_value.length > 0 ) {
                                            return true;
                                        } else {
                                            alert("Por favor ingresa la respuesta correcta.");
                                            return false;
                                        }  
                                    }  
                            }else{
                                console.log("Aqui se resetea  " + this.questionSelected.direct_data + "   " + this.questionSelected.is_evaluated );
                                this.questionSelected.resp_direct_quest_value = "";
                                return true;
                            }
                        }else{
                            alert("El Número de pregunta es obligatoría.");
                            return false;
                        }  
                    }else{
                        alert("El tipo es obligatorío.");
                        return false;
                    } 
                }
                else{
                    alert("La pregunta es obligatoría.");
                    return false;
                } 
            } catch (error) {
                alert(error);
                return false;
            } 
        },IsNumeric(input){
            var RE = /^-{0,1}\d*\.{0,1}\d+$/;
            return (RE.test(input));
        },async deleteQuestion(id){  
            const responseDelete = await axios.post('../../models/bd/bd_question.php', {  action:'delete',id: id, model: this.questionSelected}).then(function(response){ return  response });
            console.log(responseDelete);   
            application_poll.manageError(responseDelete,"Data Deleted",'Eliminado.');  
            await this.loadModelQuestion();  
        },async loadModelQuestion(){
            const questions = await axios.post('../../models/bd/bd_question.php', {  action:'fetchall',id_encuesta: this.poll.id }).then(function(response){ return  response.data });
            this.allData_QuestionPoll = questions;
            
        },manageError(result, origen,msg){
            try { 
              if(result.data.message == origen){  
                this.msg=true; 
                this.msgError=false; 
                this.alert = msg
              }else{
                this.msg=false; 
                this.msgError=true; 
                this.alert = result.data;
              }
            } catch (error) { 
              this.msg=false;  
              this.msgError=true; 
              this.alert = result.data;
            }  
          }, 
// ================================================================================================
        async showOpciones(id){ 
            this.formOption = false;
            this.msgError = false;
            this.msg = false;
            this.alert = "";
            this.btePressed = false;
            this.allData_QuestionPoll.forEach(element => { 
                element.id_pregunta == id ?  this.questionSelected =  element : console.log();
            });   
            if (this.questionSelected.opcion_multiple) {   
                this.dynamicTitle = this.questionSelected.nombre_pregunta;
                this.options = await axios.post("../../models/bd/bd_option.php", { action:'fetchallOption',  idQuestion: this.questionSelected.id_pregunta }).then(function (response) { return response.data;   })  .catch(function (response) {    return response.data;  }) 
                this.myModelPoll2 = true; 
            }   
        },newOption(){ 
            this.option = {"id_opcion":0,"opcion":"","op_activo":true
                            ,action: "",id_pregunta: this.questionSelected.id_pregunta, respuesta_extra : false
                            ,is_evaluated : this.questionSelected.is_evaluated, opcion_multiple : this.questionSelected.opcion_multiple
                            ,direct_data : this.questionSelected.direct_data , is_correct_answer : false,pocision:this.buscarPosicionOpcion()};
            this.formOption = true;
            console.log(this.option);
        },buscarPosicionOpcion(){
            let max = 0;
            for (let index = 0; index < this.options.length; index++) {
                const element = this.options[index];
                if (element.pocision > max) {
                    max = element.pocision;
                }
            }
            return max + 1;
        }
        ,async deleteOption(element){ 
            const result  = await axios.post("../../models/bd/bd_option.php", { action: 'delete' ,  model: element }).then(function (response) {    return response.data;   })  .catch(function (response) {    return response.data;  }) 
                try {
                    if (result.message == 'Data Deleted') {
                        this.options = await axios.post("../../models/bd/bd_option.php", { action:'fetchallOption',  idQuestion: this.questionSelected.id_pregunta }).then(function (response) {    return response.data;   })  .catch(function (response) {    return response.data;  }) 
                        // alert("Opción Eliminada: " + element.opcion);  
                    }else{ 
                        console.log(result);   
                        alert("Error Eliminando la opción: " + element.opcion);  
                    } 
                } catch (error) {  
                    alert("Error Eliminando la opción: " + element.opcion);  
                } 
        }

        ,async updateOption(element){   
            this.formOption = true; 
            this.option = element;
            console.log(this.option);
        }
        ,cancelarOption(){ this.formOption = false;  }

        ,async guardarOption(){  
            let action_ls = (this.option.id_opcion == 0 ? 'insert' : 'update' ) ;
   
            this.option.is_correct_answer = (this.option.is_correct_answer == "true" || this.option.is_correct_answer == true ? "true" : "false");
            this.option.respuesta_extra = (this.option.respuesta_extra == "true" || this.option.respuesta_extra == true ? "true" : "false" );
 
            const result  = await axios.post("../../models/bd/bd_option.php", { action: action_ls,  model: this.option })
            .then(function (response) { return response.data; }).catch(function (response) { return response.data; }) 
            try { 
                if (result.message == 'Data Updated' || result.message == 'Data Inserted' ) {
                    this.options = await axios.post("../../models/bd/bd_option.php", { action:'fetchallOption',  idQuestion: this.questionSelected.id_pregunta }).
                    then(function (response) {    return response.data;   })  .catch(function (response) {    return response.data;  }) 
                    // alert("Opción :" + this.option.opcion +  (action_ls == 'insert' ? ' Guardada.' : ' Actualizada.' ));  
                    this.formOption = false; 
                }else{  
                    alert("Error, Opción: " + this.option.opcion + "  Excepción: " + result.messageError  );
                } 
            } catch (error) {  
                alert("Error, Opcion: " + this.option.opcion + "  " + error);
            }  
        }
    }, 
    created:async function(){
        let id_encuesta = document.getElementById("id_encuesta").value; 
        if (id_encuesta > 0) {
            const encuesta = await axios.post('../../models/bd/bd_poll.php', {  action:'fetchSingle',id: id_encuesta }).then(function(response){ return  response.data });
            this.poll = encuesta;  
            await this.loadModelQuestion(); 
            this.respaldo_QuestionPoll = this.allData_QuestionPoll;
            const tiposResponce = await axios.post('../../models/bd/bd_question.php', {  action:'getTipos'}).then(function(response){ return  response.data });
            this.tipos = tiposResponce;   
        } else {
            location.href="p_poll.php"; 
        } 
    } 

 });
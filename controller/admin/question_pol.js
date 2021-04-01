var application_poll = new Vue({ 
    el:'#crudPollQuestion',
    data:{
        poll:{"id":0,"company":null,"poll_name":"","poll_help":"","checked":true,"poll_validfrom":"","poll_validUntil":""},
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
        disNewOption : true
    },
    methods:{ 
      openModel(funct,id){
            this.msgError = false;
            this.msg = false;
            this.alert = ""; 
            if (funct == 'add') {
                this.actionButton = "Agregar";
                this.dynamicTitle = "Nueva pregunta."
                this.myModelPoll = true;
                this.questionSelected = {"id_pregunta":0,"id_encuesta":this.poll.id,"nombre_pregunta":"","activo":true,"id_tipo":0,"numero_pregunta":this.maxValuePosition(),"obligatoria":true};
            } else if(funct == 'mod'){
                this.actionButton = "Actualizar";
                if (id > 0) {  
                    this.allData_QuestionPoll.forEach(element => { 
                        element.id_pregunta == id ?  this.questionSelected =  element : console.log();
                    }); 
                   this.dynamicTitle = "Modificar pregunta." 
                   this.myModelPoll = true;   
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
            if (this.validForm()) {
                this.questionSelected.id_pregunta > 0 ? action = 'update' : action = 'insert'; 
                const responseSave = await axios.post('../../models/bd/bd_question.php', {  action:action, model: this.questionSelected}).then(function(response){ return  response });
                console.log(responseSave); 
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
                            return true;
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
            this.msgError = false;
            this.msg = false;
            this.alert = "";
            this.btePressed = false;
            this.allData_QuestionPoll.forEach(element => { 
                element.id_pregunta == id ?  this.questionSelected =  element : console.log();
            });   
            if (this.questionSelected.opcion_multiple) {   
                this.dynamicTitle = this.questionSelected.nombre_pregunta;
                this.options = await axios.post("../../models/bd/bd_option.php", { action:'fetchallOption',  idQuestion: this.questionSelected.id_pregunta }).then(function (response) {    return response.data;   })  .catch(function (response) {    return response.data;  }) 
                this.myModelPoll2 = true; 
            }   
        },newOption(){ 
            this.options.push({"id_opcion":0,"opcion":"","op_activo":true,action: "insert",id_pregunta: this.questionSelected.id_pregunta,"respuesta_extra":false});
        },async completeOption(){  
            this.btePressed = true; 
            for (let index = 0; index < this.options.length; index++) {
                let element = this.options[index];  
                element.respuesta_extra = (element.respuesta_extra ? "true":"false") 
                const result  = await axios.post("../../models/bd/bd_option.php", { action: element.action ,  model: element }).then(function (response) {    return response.data;   })  .catch(function (response) {    return response.data;  }) 
                // console.log(result);
                try {
                    if (result.message == 'Data Updated' ||  result.message == 'Data Deleted' ||   result.message == 'Data Inserted') {
                        console.log(result); 
                    }else{ 
                        console.log(result); 
                        switch(element.action){
                            case 'update': 
                                alert("Error Actualizando la opción: " + element.opcion);
                            case 'insert': 
                                alert("Error al Guardar la opción: " + element.opcion);
                            case 'delete': 
                                alert("Error Eliminando la opción: " + element.opcion); 
                        } 
                    } 
                } catch (error) { 
                    switch(element.action){
                        case 'update': 
                            alert("Error Actualizando la opción: " + element.opcion);
                        case 'insert': 
                            alert("Error al Guardar la opción: " + element.opcion);
                        case 'delete': 
                            alert("Error Eliminando la opción: " + element.opcion); 
                    } 
                }
            } 
            this.myModelPoll2 = false;  
        }
    }, 
    created:async function(){
        let id_encuesta = document.getElementById("id_encuesta").value; 
        if (id_encuesta > 0) {
            const respuesta = await axios.post('../../models/bd/bd_poll.php', {  action:'fetchSingle',id: id_encuesta }).then(function(response){ return  response.data });
            this.poll = respuesta;  
            await this.loadModelQuestion(); 
            const tiposResponce = await axios.post('../../models/bd/bd_question.php', {  action:'getTipos'}).then(function(response){ return  response.data });
            this.tipos = tiposResponce;   
        } else {
            location.href="p_poll.php"; 
        } 
    } 

 });
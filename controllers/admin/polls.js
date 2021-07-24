var application_poll = new Vue({ 
    el:'#crudPoll',
    data:{
      allData_Poll:'',
      myModelPoll:false,
      myModelPoll2:false,
      modalCopy:false,
      actionButton:'Agregar',
      dynamicTitle:'Datos Encuesta',
      hiddenId  : null ,
      companys : null ,
      pollSelected : null,
      isDisabledSC:true,
      isDisabledBTEcopy:true,
      msgError:false,
      msg:false,
      alert: "", 
      copy_name: "",  copy_validfrom: "",   copy_validUntil: "", id_copy: 0, 
      modalIntento:false,in_inicio:'',in_fin:'',in_descripcion:'',in_id_encuesta:0,Intentos:[],pathIntentos:'../../models/bd/bd_enc_intentos_encuesta.php',

    },
    methods:{
        async deleteIntent(id_enc_intentos_encuesta){
          console.log(id_enc_intentos_encuesta); 
          const response = await this.request(this.pathIntentos,{model:{id_enc_intentos_encuesta:id_enc_intentos_encuesta},'action' : 'delete'});
          this.enc_intentos_encuestaCollection = response; 
          if(response.message == 'Data Deleted'){  
            let filtrarPor =  "(id_encuesta =" + this.pollSelected.id_encuesta + ")";  
            const response = await this.request(this.pathIntentos,{'order' : 'ORDER BY id_enc_intentos_encuesta DESC','action' : 'select','filter' : filtrarPor});
            console.log(response);
            try {
              if (response.length>0) {
                this.Intentos = response;
              }
            } catch (error) {this.Intentos =[];console.log(error);} 
          }else{
            console.log('Error'  + response.message); 
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
        async openModelIntent(encuesta){
          this.Intentos = [];
          this.pollSelected = encuesta;
          this.modalIntento = true; 
          let filtrarPor =  "(id_encuesta =" + this.pollSelected.id_encuesta + ")";  
          const response = await this.request(this.pathIntentos,{'order' : 'ORDER BY id_enc_intentos_encuesta DESC','action' : 'select','filter' : filtrarPor});
          try {
            if (response.length>0) {
              this.Intentos = response;
            }else{
              this.Intentos = [];
            }
          } catch (error) {console.log(error);
            this.Intentos = [];
          } 
        },
        validarIntento(){ 
          if (!this.pollSelected.id_encuesta > 1) {
            alert("Selecciona Una Encuesta.");
            return false;
          } 
          if (this.in_descripcion == '') {
            alert("La descripción es inválida.");
            return false;
          } 
          if (this.in_inicio == '') {
            alert("La fecha Incio es inválida.");
            return false;
          } 
          if (this.in_fin == '') {
            alert("La fecha Fin es inválida.");
            return false;
          }    
          console.log(new Date(this.in_fin));
          if (new Date(this.in_inicio) >= new Date(this.in_fin)) {
            alert("La fecha Inicio No puede ser mayor o igual que la fecha Fin.");
            return false;
          }  
          return true;
        },
        async saveIntent(){
          if (this.validarIntento()) {
            let enc_intentos_encuesta = {id_encuesta:this.pollSelected.id_encuesta, 
                                          descripcion:this.in_descripcion,inicio:this.in_inicio,fin:this.in_fin}
            const response = await this.request(this.pathIntentos,{model:enc_intentos_encuesta,'action' : 'insert'}); 
            if(response.message == 'Data Inserted'){ 
              this.in_inicio='';this.in_fin='';this.in_descripcion='';
              let filtrarPor =  "(id_encuesta =" + this.pollSelected.id_encuesta + ")";  
              const response = await this.request(this.pathIntentos,{'order' : 'ORDER BY id_enc_intentos_encuesta DESC','action' : 'select','filter' : filtrarPor});
              try {
              if (response.length>0) {  this.Intentos = response;  }
              } catch (error) {this.Intentos =[];console.log(error);} 
            }else{
              console.log(response.message);
            }  
          } 
        },
        fetchAllData:function(){
          axios.post('../../models/bd/bd_poll.php', {
            action:'fetchall' 
          }).then(function(response){
            application_poll.allData_Poll = response.data;
          });
        }, 
        openModel: function(){ 
          application_poll.poll_name = '';
          application_poll.poll_help = '';
          application_poll.poll_validfrom = '00-00-0000';
          application_poll.poll_validUntil = '00-00-0000'; 
          application_poll.company = '';
          application_poll.checked = true;
          application_poll.actionButton = "Agregar";
          application_poll.dynamicTitle = "Agregar Encuesta";
          application_poll.myModelPoll = true;  
        },
        submitData:function(){
            if(application_poll.poll_name != '' && application_poll.poll_help != '' && application_poll.poll_validfrom != '00-00-0000' && application_poll.poll_validUntil != '00-00-0000')
            {
              if(application_poll.actionButton == 'Agregar')
              {
                  axios.post('../../models/bd/bd_poll.php', {
                    action:'insert', 
                    poll_name:application_poll.poll_name, 
                    poll_help:application_poll.poll_help,
                    poll_validfrom:application_poll.poll_validfrom,
                    poll_validUntil : application_poll.poll_validUntil,
                    checked:application_poll.checked 
                  }).then(function(response){
                    console.log(response);
                    application_poll.myModelPoll = false;
                    application_poll.fetchAllData(); 
                    application_poll.poll_name = '';
                    application_poll.poll_validUntil = '';  
                    application_poll.poll_help = '';
                    application_poll.poll_validfrom = '01-01-2020';
                    application_poll.checked = ''; 
                    // alert(response.data.message);
                    application_poll.manageError(response,"Data Inserted",'Guardado');
                  });
              }
              if(application_poll.actionButton == 'Actualizar')
              {
                  axios.post('../../models/bd/bd_poll.php', {
                    action:'update', 
                    poll_name : application_poll.poll_name,
                    poll_help : application_poll.poll_help,
                    poll_validfrom : application_poll.poll_validfrom,
                    poll_validUntil : application_poll.poll_validUntil, 
                    checked : application_poll.checked,
                    hiddenId : application_poll.hiddenId
                  }).then(function(response){
                    console.log(response);
                    application_poll.myModelPoll = false;
                    application_poll.fetchAllData();  
                    application_poll.poll_validUntil = ''; 
                    application_poll.poll_name = '';
                    application_poll.poll_help = '';
                    application_poll.poll_validfrom = '';
                    application_poll.checked = '';
                    application_poll.hiddenId = '';
                    application_poll.manageError(response,"Data Updated",'Actualizado');
                    // alert(response.data.message);
                  });
              }
            }
            else
            {
              alert("Llena todos los campos por favor.");
            }
        },
        manageError(result, origen,msg){
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
        fetchData:function(id){
          axios.post('../../models/bd/bd_poll.php', {
            action:'fetchSingle',
            id:id
          }).then(function(response){  
            application_poll.poll_name = response.data.poll_name;
            application_poll.poll_help = response.data.poll_help;
            // application_poll.poll_validfrom =  response.data.poll_validfrom;
            //application_poll.poll_validUntil =  response.data.poll_validUntil; 
            application_poll.poll_validfrom = application_poll.formatDateTime(response.data.poll_validfrom);
            application_poll.poll_validUntil = application_poll.formatDateTime(response.data.poll_validUntil);
            application_poll.checked = response.data.checked
            application_poll.hiddenId = response.data.id;
            application_poll.myModelPoll = true;
            application_poll.actionButton = 'Actualizar';
            application_poll.dynamicTitle = 'Editar Encuesta';  
          });
        },
        formatDateTime(dateIn){
          dateIn = dateIn.toLocaleString("sv-SE", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
          }).replace(" ", "T") 
          return dateIn.substring(0,19)  
        },
        deleteData:async function(id){ 
          if(confirm("¿Seguro que deseas eliminar esta encuesta?"))
          {
            const result = await axios.post('../../models/bd/bd_poll.php', {  action:'delete',  id:id }).then(function(response){  return response;  });
            application_poll.manageError(result,  "Data Deleted","Eliminado.") 
            application_poll.fetchAllData();  
          }
        }, 
        question:function(id){ 
          if (id > 0) {
            location.href="p_question_poll.php?id_encuesta=" + id;  
          } 
        } ,
        showCopyPool(id,name){  
          console.log(this.getDate());
          this.copy_name = name + " (Copia)"; this.copy_validfrom = this.getDate();this.copy_validUntil = this.getDate(); this.id_copy = id;
          this.isDisabledBTEcopy = false;
          this.modalCopy = true;  
        },async copyPool(){
            if (this.id_copy > 0) {
              const response = await axios.post('../../models/bd/bd_poll.php', {  
                action:'copy',id_encuesta: this.id_copy,validfrom:this.copy_validfrom,validUntil:this.copy_validUntil,name:this.copy_name
               }).then(function(response){ return  response.data });
               try {
                if (response.results < 1) {
                  alert("Error: " + response);
                  return;
                }
               } catch (error) {
                alert("Error: " + error);
               }  
              console.log(response);
            }  
            this.fetchAllData();
            this.copy_name = ""; this.copy_validfrom = "";this.copy_validUntil = ""; this.id_copy = 0;
            this.isDisabledBTEcopy = true;
            this.modalCopy = false; 
        },getDate(){
          n =  new Date(); 
          y = n.getFullYear(); 
          m = n.getMonth() + 1;

          minute = n.getMinutes();
          hour = n.getHours(); 
          if (m < 10) {   m = '0' + m;  } 
          d = n.getDate(); 
          if (d < 10) {  d = '0' + m;  }
          return y + "-" + m + "-" + d + " " +hour+ ":" +minute+":00" ;
        },
        formatDate(date) {
          var hours = date.getHours();
          var minutes = date.getMinutes();
          var ampm = hours >= 12 ? 'pm' : 'am';
          hours = hours % 12;
          hours = hours ? hours : 12; // the hour '0' should be '12'
          minutes = minutes < 10 ? '0'+minutes : minutes;
          var strTime = hours + ':' + minutes + ' ' ;//+ ampm;
          return (date.getMonth()+1) + "-" + date.getDate() + "-" + date.getFullYear() + "  " + strTime;
        }  
       ,
        async asingCompany(poll){ 
          this.isDisabledSC = false;
          this.pollSelected = poll;
          const response = await axios.post('../../models/bd/bd_company_poll.php', {  action:'fetchall',id_encuesta:this.pollSelected.id_encuesta }).then(function(response){ return  response.data });
          this.companys = response; 
          this.dynamicTitle = this.pollSelected.nombre;
          this.myModelPoll2 = true;   
        },
        async saveCompanys(){ 
          this.isDisabledSC = true;
          const response = await axios.post('../../models/bd/bd_company_poll.php', {  action:'delete',id_encuesta: this.pollSelected.id_encuesta }).then(function(response){ return  response.data });
          // console.log(response); 
          for (let index = 0; index < this.companys.length; index++) {
            const element = this.companys[index];
            if (element.selected) { 
              const response2 = await axios.post('../../models/bd/bd_company_poll.php', {  action:'insert',id_encuesta: this.pollSelected.id_encuesta,id_empresa: element.id_empresa }).then(function(response){ return  response.data });
              // console.log(response2);
            } 
          } 
          this.pollSelected = null;
          this.myModelPoll2 = false;   
        }
    }, 
    created:function(){
     this.fetchAllData();
    //  this.fetchAllData_Company();
    } 
 });
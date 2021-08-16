

var account = new Vue({ 
    el:'#account',
    data:{
        account: '', 
        numsZapatoCollection:[],
        tallaCollection:[],
        modalRegistros:false,
        FechaI:'',
        FechaF:'',
        registros : [],
        msg : "",
        emailNOmodificado : "",
        isHello : false,
        isError:false,
    },
    methods:{ 
        async findRegister(){
            let fI = this.FechaI;
            let fF = this.FechaF;  
            let endPoint = `${configEP.EndPointCerberus}registros?FechaI=${fI}&FechaF=${fF}&operacion=es&IdEmpleado=${this.account.id_cerberus_empleado}`;
            const registros = await axios.get(endPoint,{
                    headers:{
                        "token" : localStorage.getItem("API_KEY_CERBERUS")
                    }
            }).then(function(response){ return  response.data });
            if (registros.status == "success") {
                this.formatearRegistros(registros.data);
            }
        },
        formatearRegistros(registros){
            let reg_temp = []; 
            registros.forEach(element => { 
                let miFechaActual  =  element.fechaRegistro.replace("T", " ").replace(".000Z", ""); 
                let fecha = miFechaActual.substr(0,miFechaActual.indexOf(" "));
                let estay = false;
                reg_temp.forEach(element => {
                    if(element.fecha == fecha ){ estay = true } 
                });
                if (estay == false) {
                    reg_temp.push({fecha : fecha,hora:[]});
                } 
            });
            reg_temp.forEach(reg => {
                registros.forEach(element => { 
                    let fechaTemp  =  element.fechaRegistro.replace("T", " ").replace(".000Z", ""); 
                    fechaTemp = fechaTemp.substr(0,fechaTemp.indexOf(" "));
                    if (reg.fecha == fechaTemp && element.esActivo ) {
                        let hora  =  element.fechaRegistro.replace("T", " ").replace(".000Z", ""); 
                        hora = hora.substr(hora.indexOf(" ")+1,hora.length);
                        reg.hora.push(hora);
                    }
                });
            });
            this.registros = reg_temp;
        },
        async save(){
            if (this.validForm()) {
                let actualizar = true;
                // let actualizar = false;
                // if (this.emailNOmodificado === this.account.correo && this.account.correo_verificado == true) {
                //     actualizar = true;
                // } else { 
                //     let email_valido = await this.validEmail(this.account.correo);
                //     if (email_valido) {
                //         actualizar = true;
                //         this.account.correo_verificado = true;
                //     } else {
                //         this.account.correo_verificado = false;
                //         document.getElementById("error_correo").innerHTML = "Este correo no esta permitido, por favor verificalo."
                //         this.msg = "El correo Ingresado no es correcto, recuerda ingresar tu correo personal";
                //         this.isError = true;
                //         this.isHello = false;
                //         $('#ModalMsg').modal('show');  
                //     } 
                // }
                this.account.correo_verificado = true;
                if (actualizar) {
                    const update_response = await axios.post('../../models/user/bd_account.php', {  action:'update',data:this.account })
                                            .then(function(response){ return  response.data });
                    if (update_response.message == 'data update') {
                        this.fetchData(); 
                        this.isHello = false;
                        this.isError = false;
                        this.msg = "Tus Datos Fueron actualizados.";
                        $('#ModalMsg').modal('show');  
                    }else{
                        this.fetchData(); 
                        console.log(update_response);
                        alert("Ocurrio un error:" + update_response.message);
                    } 
                } 
            }
        },
        async validEmail(email) {
            let response_email_valid = "";
            try {
                 response_email_valid =await axios.get('https://api.usebouncer.com/v1/email/verify?email='+email, 
                {headers: { 'x-api-key': "22pGlL2mHnG4juMeTUDvunGc8jtqaPlY74Hmk19Y" }})
                .then((res) => { return res.data })
                .catch((error) => {  return error });
                if (response_email_valid.reason == "accepted_email") {
                    console.log(response_email_valid);
                    return true;
                } else {
                    const update_response = await axios.post('../../models/user/bd_account.php', {  action:'updateStateEmailError',data:this.account,message: response_email_valid.reason })
                    .then(function(response){ return  response.data });
                    console.log(update_response);
                    return false;
                }   
            } catch (error) {
                console.log(response_email_valid);
                console.log(error);
                return false;
            } 
        },
        formatoNumero(n){  
            return n > 9 ? "" + n: "0" + n; 
        }, 

        fechaActual(){  
            let fechaActual = new Date();
            fechaActual = fechaActual.getFullYear() + "-" + this.formatoNumero(fechaActual.getMonth()+1) +"-"+ this.formatoNumero(fechaActual.getDate());
            return fechaActual; 
        }, 
        
        validarEmail(valor) {
            emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
            if (emailRegex.test(valor)){
                return true;
            } else {
                return false;
            }
        },ValidateNumber(phoneNumber) {
            return !isNaN(parseFloat(phoneNumber)) && isFinite(phoneNumber);   
        },validarFormatoFecha(campo) {
           try {
               let date = new Date(campo); 
               if(date.getFullYear() > 1920 && date.getFullYear() < 2012){
                return true;
               }else{
                return false;
               } 
           } catch (error) {
               console.log(error);
               return false;
           }
        },validForm(){ 
            let valido = true;

            if ( this.account.id_numero_zapato == 'null' || this.account.id_numero_zapato == null ){
                document.getElementById("error_zapato").innerHTML = "Ingresa el No. correcto."
                valido = false;
            }else{
                document.getElementById("error_zapato").innerHTML = "";
            }  

            if ( this.account.id_talla_playera == 'null' || this.account.id_talla_playera == null ){
                document.getElementById("error_playera").innerHTML = "Ingresa el talla correcta."
                valido = false;
            }else{
                document.getElementById("error_playera").innerHTML = "";
            }  

            if ( this.validarFormatoFecha(this.account.fecha_nacimiento) == false ){
                document.getElementById("error_fecha_naci").innerHTML = "Tu fecha de Nacimiento es Incorrecta."
                valido = false;
            }else{
                document.getElementById("error_fecha_naci").innerHTML = "";
            }  
            
            if (this.validarEmail(this.account.correo) == false 
                    || this.account.correo.includes(".@") == true  
                    || this.account.correo.includes(",") == true 
                    || this.account.correo.includes("refividrio.com") == true )
            {
                document.getElementById("error_correo").innerHTML = "Tu Correo es Incorrecto."
                valido = false;
            }else{
                document.getElementById("error_correo").innerHTML = "";
            }  
            
         //   if (this.ValidateNumber(this.account.celular) == false) {
          //      valido = false;
          //      document.getElementById("error_celular").innerHTML = "Tu Número celular es Incorrecto, Evita ingresar Paréntesis, espacios o cualquier Carácter que no sea un Número."
         //   }else{ 
          ///      if(this.account.celular.length == 10){
          //          document.getElementById("error_celular").innerHTML = ""
          //      }else{
          ///          document.getElementById("error_celular").innerHTML = "Tu Número celular es Incorrecto, Ingresar 10 Digitos sin espacios ni otro caracter."; 
           //         valido = false;
          //      }
          //  }
            return valido;
        },async fetchData(){
            await this.gettallas();
            const account_response = await axios.post('../../models/user/bd_account.php', {  action:'fetchAccount' }).then(function(response){ return  response.data });
            this.account = account_response[0]; 
            this.emailNOmodificado = this.account.correo;
            // console.log(this.account);
        },async gettallas(){
            this.tallaCollection = [];
            this.numsZapatoCollection = [];   
            let filtrarTallas = " activo = true AND id_tipo_producto IN (1)";
            let filtrarNumeros = " activo = true AND id_tipo_producto IN (3)";
            let response_tallas = await axios.post('../../models/un/bd_talla.php',{'order' : 'ORDER BY valor ASC','action' : 'select', 'filter' : filtrarTallas});
            let response_numsZapato = await axios.post('../../models/un/bd_talla.php',{'order' : 'ORDER BY valor ASC','action' : 'select', 'filter' : filtrarNumeros});
            response_numsZapato = response_numsZapato.data;
            response_tallas = response_tallas.data;  
            try{  
                if(response_tallas.length > 0 && response_tallas[0].id_talla > 0){  
                    this.tallaCollection = response_tallas; 
                } 
                if(response_numsZapato.length > 0 && response_numsZapato[0].id_talla > 0){  
                    this.numsZapatoCollection = response_numsZapato; 
                }  
            }catch(error){
                this.show_message('No hay productos.','info');
                console.log(response_tallas);
            }   
        } 
    }, 
    async created(){
        await this.fetchData();  
        try {
            let linkComprobate = "../../models/login.php";
            const reset = await axios.post(linkComprobate, {
                action:'isPass_default'
            }).then(function (response) { 
            return response;
            })
            .catch(function (response) {  
                return response;
            });  
            if(reset.data.data){
            }else{
                if (this.account.correo_verificado) {
                } else {
                    this.isHello = true;
                    this.msg =  "Tenemos un problema con tu correo electrónico por favor actualizalo.";
                    $('#ModalMsg').modal('show'); 
                }
                if (!this.validForm()) {
                    this.msg = "Algunos datos de tu cuenta no son correctos.";
                    this.isError = true;
                    this.isHello = false;
                    $('#ModalMsg').modal('show');  
                } 
            }
        } catch (error) {
            console.log(error);
        }
        this.FechaI = this.fechaActual();
        this.FechaF = this.fechaActual(); 
        this.findRegister();
    } 
 });

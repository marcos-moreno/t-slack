var login = new Vue({
    el:'#login',
    data:{  
        roles: null 
    },
    methods:{ 
        seachUser:async function(){ 
            let userParam = document.getElementById("user").value;
            let passwordParam = document.getElementById("password").value;
            let rolParam = document.getElementById("rol").value; 
            this.roles != null && this.roles != '' && rolParam != '' ?  rolParam = this.roles[rolParam]   : rolParam = 0;   
            try { 
                    const sig = await this.getRols(userParam,passwordParam ,rolParam); 
                    if (sig != null) { 
                            if (sig != "succes")  {  
                                if (sig.includes("¡Error!")) {  
                                    document.getElementById("msg").style.display = "none"; 
                                    document.getElementById("msgErro").innerHTML = sig; 
                                    document.getElementById("msgErro").style.display = "block"; 
                                    document.getElementById("buttonCancel").style.display = "none"; 
                                }else{  
                                    login.roles = sig;
                                    var x = document.getElementById("rol"); 
                                    x.innerHTML = "" 
                                    for (let index = 0; index <  this.roles.length; index++) { 
                                        const element =  this.roles[index];
                                        var option = document.createElement("option");
                                        option.text =element.rol;
                                        option.value = index;
                                        x.add(option);
                                        x.style.display = "block"; 
                                    } 
                                    // console.log(sig);
                                    document.getElementById("msg").innerHTML = "Selecciona un Rol"; 
                                    document.getElementById("msg").style.display = "block"; 
                                    document.getElementById("msgErro").style.display = "none";
                                    document.getElementById("user").disabled  = true; 
                                    document.getElementById("password").disabled  = true;
                                    document.getElementById("buttonCancel").style.display = "block";
                                } 
                            } else { 
                                if (rolParam.rol == 'admin') {
                                    location.href="views/admin/p_poll.php";  
                                } else if (rolParam.rol == 'user'){
                                    try {
                                        let user = await this.getUser(); 
                                        if (user.correo_verificado) {
                                            location.href="views/user/showPoll.php"; 
                                        } else { 
                                            location.href="views/user/account.php"; 
                                        }
                                    } catch (error) {
                                        this.reset();
                                    } 
                                } else if (rolParam.rol == 'soporte técnico'){
                                    location.href="views/sp/v_marca.php"; 
                                } else if (rolParam.rol == 'SuperAdmin'){
                                    location.href="views/generales/v_acceso_rol.php"; 
                                }  else if (rolParam.rol == 'Administracion'){
                                    location.href="views/un/v_almacen.php"; 
                                }  else if (rolParam.rol == 'Evaluaciones'){
                                    location.href="views/ev/v_ev_puesto.php";
                                } 
                            }     
                        }else{
                            document.getElementById("msg").style.display = "none"; 
                            document.getElementById("msgErro").innerHTML = "Comprueba tus credenciales "; 
                            document.getElementById("msgErro").style.display = "block"; 
                            document.getElementById("buttonCancel").style.display = "none"; 
                        }
                } catch (error) { 
                    document.getElementById("msg").style.display = "none"; 
                    document.getElementById("msgErro").innerHTML = "Comprueba tus credenciales " + error; 
                    document.getElementById("msgErro").style.display = "block"; 
                    document.getElementById("buttonCancel").style.display = "none"; 
                } 
        }, 
        reset(){  location.href="views/logout.php";   },
        async getRols(userParam,passwordParam,rolParam ){
            if (userParam != '' && passwordParam != '') { 
               return axios.post("models/login.php", {action:'login',user:userParam,password:passwordParam,rol:rolParam
                }).then(function (response) { 
                    return this.roles = response.data;   
                })
                .catch(function (response) {  
                    console.log(response); 
                })     
            } else {
                document.getElementById("msg").style.display = "none";  
                document.getElementById("msgErro").innerHTML = "Ingresa tus credenciales"; 
                document.getElementById("msgErro").style.display = "block"; 
                document.getElementById("buttonCancel").style.display = "none"; 
            } 
        } ,
        async getUser(){ 
               return axios.post("models/user/bd_account.php", {action:'fetchAccount'
                }).then(function (response) {  
                    return response.data[0];   
                })
                .catch(function (response) {  
                   return {};
                })      
        } 
    },
    async mounted() {   this.roles= null   },
    created:function(){  this.roles= null  }
   });  
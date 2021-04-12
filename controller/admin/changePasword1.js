var app_chagePassword = new Vue({
    el:'#chagePassword_DIV',
    data:{  
        modalchagePassword: false,
        pass_old : "",
        pass_new: "",
        pass_new_repeat:"",  
        disables_bte_save: false,
        isPass_default : false
    },
    methods:{  
        showModal(){   
            this.modalchagePassword= true;this.disables_bte_save = false;
        },
        async managePassword(){  
            let linkComprobate = "../../models/login.php"; 
           const isPassword_valid = await axios.post(linkComprobate, {
                action:'comparePassword',password_old:this.pass_old
            }).then(function (response) { 
                if(response.data == 'contraseña válida'){return true;}else{return false;}; 
            })
            .catch(function (response) {  
                console.log(response);
                return false;
            });   
            if (isPassword_valid) { 
                if (this.pass_new != '' && this.pass_new_repeat != '') {
                    if (this.pass_new != 'refividrio') {
                        if (this.pass_new === this.pass_new_repeat) {
                            this.disables_bte_save = true; 
                            const result = await this.changePassword();
                            if (result == true){  
                                    alert('¡Cambio de contraseña Exitoso!');  
                                    let linkLogout= "../logout.php";
                                    // if (admin) {
                                    //     linkLogout = "../logout.php";
                                    // }  
                                    location.href= linkLogout;   
                                }else
                                    alert('La Contraseña NO se puede Actualizar en estos momentos.');  
                        }else{
                            alert('La Nueva cotraseña No coincide.');  
                        }
                    }else{
                        alert('La cotraseña no se puede usar.');  
                    }
                }else{
                    alert('Ingresa la Nueva cotraseña y su comprobación.');  
                }
            }  else{
                alert('La contraseña anterior no coincide.');
            }
        },async changePassword(){
            let linkComprobate = "../../models/login.php"; 
            return await axios.post(linkComprobate, {
                action:'changePassword',password_old:this.pass_old, password_new: this.pass_new 
            }).then(function (response) { 
                if(response.data == 'Password Updated'){return true;}else{console.log(response.data);return false;}; 
            })
            .catch(function (response) {  
                console.log(response);
                return false;
            });
        } 
    },
    async mounted() {},
    created(){
        let reset = document.getElementById("resetPassword").value;
        if(reset === "reset"){
            console.log("YEa");
            this.modalchagePassword = true;
            this.isPass_default = true;
        }
    }
   });  
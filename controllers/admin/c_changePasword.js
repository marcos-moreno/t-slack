var app_chagePassword = new Vue({
    el:'#chagePassword_DIV',
    data:{  
        modalchagePassword: false,
        pass_old : "",
        pass_new: "",
        pass_new_repeat:"",  
        disables_bte_save: false,
        isPass_default : false,
        typeControl:"password",
        show_pass_v : false
        
    },
    methods:{  
        show_pass(){
            if(this.show_pass_v == 'true' || this.show_pass_v == true){
                this.typeControl='text' 
            }else{
                this.typeControl='password'  
            }
        },
        showModal(){
            this.modalchagePassword= true;this.disables_bte_save = false;
        },
        async managePassword(){   
            if (this.pass_new != '' && this.pass_new_repeat != '') {
                if (this.pass_new != 'refividrio') {
                    if (this.pass_new === this.pass_new_repeat) {
                        if (this.pass_new.length > 7) {
                            this.disables_bte_save = true; 
                            try {
                                const result = await this.changePassword();
                                switch (result.data.status) {
                                    case "success":
                                        alert('¡Cambio de contraseña Exitoso!'); 
                                        location.href="../logout.php";
                                        break; 
                                    case "errorOldPass":
                                        alert('La contraseña anterior es incorrecta');
                                        break; 
                                    case "errorChangePass":
                                        alert('Error actualizando la contraseña');
                                        break;  
                                    default:
                                        alert('La Contraseña NO se puede actualizar en estos momentos.');
                                        break;
                                }
                                this.disables_bte_save = false; 
                            } catch (error) {
                                this.disables_bte_save = false; 
                            } 
                        }else{
                            alert('La contraseña debé tener al menos 8 Caracteres.');  
                        }
                    }else{
                        alert('La nueva cotraseña NO coincide.');  
                    }
                }else{
                    alert('La cotraseña no se puede usar.');  
                }
            }else{
                alert('Ingresa la nueva cotraseña y su comprobación.');  
            }
        }
        
        ,async changePassword(){
            let linkComprobate = "../../models/login.php"; 
            return await axios.post(linkComprobate, {
                action:'changePassword'
                ,password_old:this.pass_old
                ,password_new: this.pass_new 
            }).then(function (response) { 
               return response;
            })
            .catch(function (response) {  
                return response;
            });
        } 
    },
    async mounted() {},
    async created(){ 
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
                this.modalchagePassword = true;
                this.isPass_default = true;
            }
        } catch (error) {
            console.log(error);
        } 
    }
   });  
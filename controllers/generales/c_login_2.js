var login = new Vue({
    el:'#login',
    data:{  
        roles: null,
        isRols : true,
        msgErro : "",
        msg : "",
        user: "",
        password : "",
        id_rol : ""
    },
    methods:{ 
        async getRoles(){ 
            if (this.user != '' && this.password != '') { 
                const rols = await axios.post("models/login.php", 
                {
                    action:'getRoles',user:this.user,password:this.password
                }).then(function (response) {
                    return  response.data;   
                }).catch(function (response) {
                    console.log(response); 
                }); 
                if (rols.status == "session_active") {
                    location.reload();
                    return;
                }
                if (rols.status == "success" && rols.data.length == 1 ) {
                    this.roles = rols.data; 
                    this.isRols = false;
                    this.id_rol = rols.data[0].id_rol;
                    this.login();
                }else if (rols.status == "success" && rols.data.length > 1 ) {
                    this.roles = rols.data; 
                    this.isRols = false;
                    if (this.roles.length > 1) {
                        this.msg = "Selecciona tu rol de acceso";  
                        this.msgErro = "";   
                    }  
                }else{ 
                    this.msg = "";
                    this.msgErro = "Usuario inválido";
                }
            } else {
                this.msg = "";  
                this.msgErro = "Ingresa tus credenciales";   
            } 
        }, 

        async login(){
            if (this.user != '' && this.password != '') { 
                const login = await axios.post("models/login.php", 
                {
                    action:'login',user:this.user,password:this.password,id_rol:this.id_rol
                }).then(function (response) {
                    return  response.data;   
                }).catch(function (response) {
                    console.log(response); 
                });    
                if (login.status == "success") {
                    try {
                        if (login.data.id_empleado > 0) {
                            if (login.data.api_key_cerberus != false) {
                                if (login.data.api_key_cerberus.status  == "success") {
                                    localStorage.setItem("API_KEY_CERBERUS",login.data.api_key_cerberus.data.token );
                                }
                            } 
                            location.href=login.data.pagina_inicio; 
                        }
                    } catch (error) {
                        console.log(error);
                    }
                }else{
                    this.msg = "";  
                    this.msgErro = "Usuario inválido";  
                }
            } else {
                this.msg = "";
                this.msgErro = "Ingresa tus credenciales";   
            } 
        },
        reset(){  
            location.href="views/logout.php";   
        }, 
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
var sinconizador = new Vue({ 
    el:'#sinconizador',
    data:{
      employesSlack:'',
      employesCerberus:'', 
      employeCerberus:'', 
      data_to_filter:"",
      filter_value:"",
      is_newEmployees : false ,
      typeMessage : '',
      msg:'', 
      id_cerberus : 0,
      array_duplicate : [],
      display_duplicate : false,
      view_modal : false,
      text_modal : ""
    },
    methods:{
        async completeSinc(){  
            for (let index = 0; index < this.employesCerberus.length; index++) {
                const element = this.employesCerberus[index];
                let employee = {}; 
                try { 
                    employee.id_empresa_cerberus = element.idEmpresa;
                    employee.nombre = element.nombreEmpleado;
                    employee.paterno = element.apPatEmpleado;
                    employee.materno = element.apMatEmpleado;
                    try {
                        employee.celular = (element.telcontacto != '' && element.telcontacto != '0' && !element.telcontacto.includes('--') ? element.telcontacto :
                        (element.telcasa != '' && element.telcasa != '0' && !element.telcasa.includes('--') ? element.telcasa : "" ));
                        employee.celular.replace(' ','');
                    } catch (error) {
                        employee.celular = null
                    }
                    employee.correo = element.correoPersonal;
                    employee.genero = (element.genero == 'MASCULINO' ? 'H' : 'M' );
                    let i = element.nombreEmpleado.indexOf(" ");
                    let usuario = (element.nombreEmpleado.substring(0,(i > 0 ? i : element.nombreEmpleado.length ) ))   + '.' + element.apPatEmpleado; 
                    usuario = usuario.toLowerCase(); 
                    employee.fecha_nacimiento = element.fechaNacimiento;
                    employee.nss = element.nss;
                    employee.rfc = element.rfc;
                    employee.fecha_alta_cerberus = element.fechaAlta;
                    employee.id_cerberus_empleado = element.idEmpleadoCerberus;
                    employee.perfilcalculo = element.perfilCalculo; 
                    employee.iddepartamento_cerbeus = element.idDepartamento; 
                    employee.idempresa_cerberus = element.idEmpresa; 
                    employee.idsucursal_cerberus = element.idSucursal; 
                    employee.id_compac = element.idConpaq;
                    let user_duplicate = false;
                    try {
                        if (employee.correo.length > 8 && employee.celular.length == 10) {
                            employee.correo_verificado = 'true';
                        } else {
                            employee.correo_verificado = 'false';
                        }
                    } catch (error) {
                        employee.correo_verificado = 'false';
                    }

                    if (this.validaUser(usuario)) {
                        employee.usuario =  usuario;
                    }else{
                        employee.usuario =  usuario + "_d";
                        user_duplicate = true;
                    }  
                    const res = await this.request('../../models/bd/bd_employee.php', {action:'insertSinc',model:employee}); 
                    if (res.message == 'sinc succes') {
                        this.show_message(this.msg + '\nSincronizado: ' + employee.id_cerberus_empleado,'success'); 
                        if (user_duplicate) {
                            this.array_duplicate.push(employee);  
                        }
                    }else if(res.message == 'error'){  
                        if(res.error.includes('SQLSTATE[23505]')){
                            this.show_message(this.msg  + ' Este empleado cuenta con una clave Duplucada. ' + res.error,'info'); 
                        }else if(res.error.includes('id_segmento') && res.error.includes('violates not-null constraint')){
                            this.show_message(this.msg  + ' || '+ employee.organization  +' Error: La organización No existe. ','info'); 
                        }else if(res.error.includes('already exists') && res.error.includes('unique constraint')){
                            this.show_message(this.msg  + '\nError: ' + employee.id_cerberus_empleado + "  Este ID cerberus,Nss o RFC ya estan en el sistema.",'info'); 
                        } else{
                            this.show_message(this.msg  + '\nError: ' + employee.id_cerberus_empleado +' '
                            + '\n'+ res.error,'info'); 
                        }  
                    }else{
                        this.show_message(res,'error');
                    } 
                    if (this.array_duplicate.length > 0) {
                        this.display_duplicate = true;
                    }
                } catch (error) {
                    this.show_message("Error al sincronizar el epleado: " + element.idEmpleadoCerberus + '\nError: ' + error +' '
                        + '\n'+ error,'error'); 
                }
            }
            this.employesCerberus = [];
            await this.fetchAllEmployees();  
        },validaUser(user){
            let valid = true;
            try { 
                sinconizador.employesSlack.forEach(element => {
                    if (element.usuario == user) {
                        valid = false;  
                    }
                }); 
            } catch (error) {
                console.log(error);
            }
            
            return valid;
        },async buscarCerberus(){
            const res = await this.get_data_cerberusByID(this.id_cerberus);
            this.employesCerberus = [];
            try {
                if (res.idEmpleadoCerberus != undefined) {
                    this.employesCerberus[0] = res; 
                }
            } catch (error) {
                console.log("No se encontró registro.");
            }
           
        },cancelSinc(){
            this.employesCerberus = []; 
            this.is_newEmployees = false;
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { sinconizador.typeMessage='' ;sinconizador.msg =''; }, 190000);
        },async searchNewEmployees(){
            this.text_modal = "estamos buscando empleados nuevos en Cerberus";
            this.view_modal = true; 
            const res = await this.get_data_new_employees_cerberus();
            this.employesCerberus = res;
            // console.log(this.employesCerberus);
            this.is_newEmployees = true;
            this.view_modal = false; 
        },DelNewEmployee(row){
            let array_result= [];
            for (let index = 0; index < this.employesCerberus.length; index++) {
                let element = this.employesCerberus[index]; 
                if (element.idEmpleadoCerberus != row.idEmpleadoCerberus) {
                    array_result.push(element);
                }
            }
            this.employesCerberus = array_result; 
        },async showDataCerberus(row){  
            const res = await this.get_data_cerberusByID(row.id_cerberus_empleado);
            try {
                if (res.idEmpleadoCerberus != undefined) {
                    this.employeCerberus = res;  
                }
            } catch (error) {
                this.employeCerberus = {};
                console.log(error);
            }

        },filter(){  
            let array_result= [];
            this.employesSlack.forEach(element => { 
                if (element.nombre.toUpperCase().includes(this.filter_value.toUpperCase()) || element.paterno.toUpperCase().includes(this.filter_value.toUpperCase()) 
                || element.materno.toUpperCase().includes(this.filter_value.toUpperCase()) || element.segmento.toUpperCase().includes(this.filter_value.toUpperCase()) 
                 || element.empresa_nombre.toUpperCase().includes(this.filter_value.toUpperCase()) || (parseInt(element.id_empleado) == parseInt(this.filter_value) ) 
                 ||  (parseInt(element.id_cerberus_empleado) == parseInt(this.filter_value) )   
                 ||  (element.nss == null?'':element.nss).toUpperCase().includes(this.filter_value.toUpperCase())
                 ||  (element.rfc == null ? '':element.rfc).toUpperCase().includes(this.filter_value.toUpperCase())
                ) { 
                    array_result.push(element);
                }  
            });
            this.data_to_filter = array_result;
        },
        async fetchAllEmployees(){
           const res = await this.request('../../models/bd/bd_employee.php', {  
            action:'fetchall'});
            sinconizador.employesSlack = res;
            sinconizador.data_to_filter = res; 
            //  console.log(sinconizador.employesSlack); 
        },async sync_IDs(){
            for (let index = 0; index <  sinconizador.employesSlack.length; index++) {
                let element =  sinconizador.employesSlack[index];
                try {
                    let employee_cerberus = await this.get_data_cerberusByName(element); 
                    employee_cerberus = employee_cerberus[0]; 
                    if (employee_cerberus.idEmpleadoCerberus > 0) {
                        element.id_cerberus_empleado = employee_cerberus.idEmpleadoCerberus;
                        element.nss = employee_cerberus.nss;
                        element.rfc = employee_cerberus.rfc; 
                        // console.log(element);
                        const res = await this.request('../../models/bd/bd_employee.php', { 
                            action:'updateSync',model:element});
                        // console.log(res);
                    }else{ 
                        console.log( "El modelo esta vacio IdEmpleado t-Slack:" +element.id_empleado);
                    } 
                } catch (error) {
                    console.log( error + " IdEmpleado t-Slack:" +element.id_empleado);
                } 
            }  
         }
         ,async get_data_new_employees_cerberus(){ 
            const response = await axios.get(configEP.EndPointCerberus + 'NewEmpleado' 
           ,{
            headers:{
                "token" : localStorage.getItem("API_KEY_CERBERUS")
            },
            params:{
            } 
            }).then(function (response) {
                if (response.data.status == "success") {
                    return response.data.data; 
                }else{
                    return [];
                }
            }).catch(function (response) {  
                console.log(response);
                return [];
            });
            return response;
        },async get_data_cerberusByName(employe){ 
            const response = await axios.get(configEP.EndPointCerberus + 'empleado' 
            ,{
                headers:{
                    "token" : localStorage.getItem("API_KEY_CERBERUS")
                },
                params:{
                'nombreEmpleado':employe.nombre,
                'apPatEmpleado':employe.paterno ,
                'apMatEmpleado':employe.materno ,
                'id_empresa_cerberus':employe.id_empresa_cerberus
            } 
            }).then(function (response) {   
                return response.data.recordset; 
            }).catch(function (response) {  
                return response;
            })  
            return response;
        },async get_data_cerberusByID(id){ 
            this.text_modal = "estamos recuperando la información de Cerberus";
            this.view_modal = true;
            const response = await axios.get(configEP.EndPointCerberus + 'empleadoById' 
            ,{
                headers:{
                    "token" : localStorage.getItem("API_KEY_CERBERUS")
                }
                ,params:{
                'id_cerberus_empleado':id, 
            } 
            }).then(function (response) {   
                if (response.data.status == "success") {
                    return response.data.data[0];
                }else{
                    [];
                }  
            }).catch(function (response) {  
                return response.data;
            })  
            this.view_modal = false;
            return response;
        },async request(path,jsonParameters){
            const response = await axios.post(path, jsonParameters).then(function (response) {   
                    return response.data; 
                }).catch(function (response) {  
                    return response.data;
                })
            return response; 
        },
        async fn_empleados_parametro(){
            let empleados_sin_fecha_alta = []; 
            empleados_sin_fecha_alta = await this.request('../../models/bd/bd_employee.php', {action:'empleados_parametro'});
            for (let index = 0; index < empleados_sin_fecha_alta.length; index++) {
                let empleado_slack = empleados_sin_fecha_alta[index];
                let empleadoCerberus = await this.get_data_cerberusByID(empleado_slack.id_cerberus_empleado);
                empleado_slack.perfilcalculo = empleadoCerberus != [] ? empleadoCerberus.perfilCalculo:""; 
                const resUpdate = await this.request('../../models/bd/bd_employee.php', {action:'updateParameter',model:empleado_slack});
                console.log(resUpdate);
            }
        }
        ,fn_format(value){
            try {
                if (value != 'NULL' && value != '' && value != 'undefined' && value != undefined) {
                    var d = new Date(value); 
                    var datestring = d.getDate()  + "/" + (d.getMonth()+1) + "/" + d.getFullYear()
                    return datestring;
                }else{
                    return "";
                }
            } catch (error) {
                return "";
            } 
        }

    }, 
    created:function(){
       this.fetchAllEmployees();  
    } 
 });
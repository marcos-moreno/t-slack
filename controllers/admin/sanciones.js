var sinconizador = new Vue({ 
    el:'#sinconizador',
    data:{ 
      typeMessage : '',
      msg:'',  
      incidenciasCollection : [],
      selectAll : 1,
      inProcess: false,
      sinconizado: true,
      fechaProceso:'',
      show_sincronizado:false
    },
    methods:{
        async searc_sincronizadas(){
            const Incidencias_creadas = await this.request('../../models/bd/bd_poll.php', {action:'getIncidencias_creadas',sincronizadas: "'"+ this.show_sincronizado + "'"});
            this.incidenciasCollection = Incidencias_creadas;
        },
        fn_selectAll(){ 
            if (this.selectAll == 1) {
                this.incidenciasCollection.forEach(element => {
                    element.sincronizar = true;
                });
            }else{ 
                this.incidenciasCollection.forEach(element => {
                    element.sincronizar = false;
                });  
            }  
        },
        async fn_sendCerberus(){ 
            if (this.fechaProceso == '') {
                // console.log('vacio');
                this.show_message('La fecha no puede estar vacia,'+
                ' recuerda que se encontrará la fecha para sancionar automaticamente pero debes indicar una fecha para conocer el Período.'
                ,'info')
            } else {  
                this.inProcess = true;
                for (let index = 0; index < this.incidenciasCollection.length; index++) {
                    const element = this.incidenciasCollection[index];
                    if (element.sincronizar == true) { 
                       await this.enviar_sancion(element);
                    }
                } 
                this.show_sincronizado = true;
                const Incidencias_creadas = await this.request('../../models/bd/bd_poll.php', {action:'getIncidencias_creadas',sincronizadas:'true'});
                this.incidenciasCollection = Incidencias_creadas;
                this.inProcess = false;
            }
        }, 
        async show_message(msg,typeMessage){ 
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { sinconizador.typeMessage='' ;sinconizador.msg =''; }, 30000);
        }, 
        async updateS(){
           const res = await this.request('../../models/bd/bd_poll.php', {action:'fn_incidencias'});
            if (res == 'success') {
                this.show_sincronizado = false;
                const Incidencias_creadas = await this.request('../../models/bd/bd_poll.php', 
                {action:'getIncidencias_creadas',sincronizadas:'false'});
                this.incidenciasCollection = Incidencias_creadas;
                // console.log(this.incidenciasCollection);
                this.show_message('Actualizado correctamente.','success');
            } else {
                this.show_message('Existe un error: ' + res,'error')
                console.log(res);
            } 
          }
        ,async request(path,jsonParameters){
            const response = await axios.post(path, jsonParameters).then(function (response) {   
                    return response.data; 
                }).catch(function (response) {  
                    return response.data;
                })
            return response; 
        }, 
        async enviar_sancion(sancion){   
            const response = await axios
            .get(configEP.EndPointCerberus + 'sanciones'
                ,{
                    headers:{
                        "token" : localStorage.getItem("API_KEY_CERBERUS")
                    },
                    params:{
                            idEmpleado: sancion.id_cerberus_empleado,
                            nivelSancion: sancion.nivel_sancion,
                            fecha: this.fechaProceso
                        }
                })
            .then(function(response){ return response.data;})
            .catch(function(response){ return response;});
            if (response.data[0].respuesta.includes('Éxito')) {
                const response_inicdencia = await this.request('../../models/bd/bd_poll.php',
                                                    {action:'updateIncidencia',
                                                    id_incidencias_creadas:sancion.id_incidencias_creadas
                                                    ,descripcion_cerberus:response.data[0].respuesta});
                if (response_inicdencia.message == 'Data Updated') {
                    return true;
                } else { 
                    this.show_message(this.msg + ' || ' +  response_inicdencia.messageError , 'error');
                    return false;
                }     
            } 
            if (response.data[0].respuesta.includes('Error')) { 
                this.show_message(this.msg + ' || ' + response.data[0].respuesta , 'error');
            }
        },
    }, 
    created: async function(){
        const Incidencias_creadas = await this.request('../../models/bd/bd_poll.php', {action:'getIncidencias_creadas',sincronizadas:'false'});
        this.incidenciasCollection = Incidencias_creadas;
    } 
 });
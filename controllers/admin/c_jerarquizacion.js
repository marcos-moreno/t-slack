 
var application = new Vue({
    el:'#app_jerarquizacion',
    data:{ 
        jerarquizacion : null,
        jerarquizacionCollection : [],
        isFormCrud: false,
        path : '../../models/admin/bd_jerarquizacion.php',
        typeMessage : '',
        msg:'', 
        ev_atributoCollection:[],
        empleadoCollection:[],
        empleadoCollectionfiltro:[], 
        superiorCollectionfiltro:[], 
        filtroEmpleado : "",
        filtroSuperior : "",
        //paginador
        numByPag : 15, 
        paginas : [],
        paginaCollection : [],
        paginaActual : 1,
        ////paginador
        departamentoCollection : [],

        filter : '',

        myHtmlCode : ""
    },
    methods:{
        async buscarValorEmpleado(filtro,isAD){
            let empleadoSelected_id = 0;
            if (isAD) {
                this.superiorCollectionfiltro = [];
            } else {
                this.empleadoCollectionfiltro = [];
            }
            let asignado = false;
            for (let index = 0; index < this.empleadoCollection.length; index++) {
                const element = this.empleadoCollection[index];
                let nomCompuesto = element.paterno + ' ' + element.materno + ' ' + element.nombre;
                try {
                    // if (this.segmento_id_filter == 0) {
                    //     if (nomCompuesto.toUpperCase().includes(filtro.toUpperCase())) {
                    //         if (isAD) {
                    //             this.superiorCollectionfiltro.push(element);
                    //         } else {
                    //             this.empleadoCollectionfiltro.push(element);
                    //         }
                    //         if (asignado==false) {
                    //             if (isAD) {
                    //                 this.jerarquizacion.id_superior = element.id_empleado;
                    //             } else {
                    //                 this.jerarquizacion.id_empleado = element.id_empleado;
                    //             }
                    //             // this.empleadoSelected_id = element.id_empleado;
                    //             // empleadoSelected_id = element.id_empleado;
                    //             asignado = true;
                    //         } 
                    //    } 
                    // }else{
                        if (nomCompuesto.toUpperCase().includes(filtro.toUpperCase())) {
                            if (isAD) {
                                this.superiorCollectionfiltro.push(element);
                            } else {
                                this.empleadoCollectionfiltro.push(element);
                            }
                            if (asignado==false) {
                                if (isAD) {
                                    this.jerarquizacion.id_superior = element.id_empleado;
                                } else {
                                    this.jerarquizacion.id_empleado = element.id_empleado;
                                }
                                // empleadoSelected_id = element.id_empleado;
                                asignado = true;
                            } 
                        }
                    // } 
                } catch (error) {
                    console.log(error);
                    if (isAD) {
                        this.superiorCollectionfiltro= [];
                    } else {
                        this.empleadoCollectionfiltro= [];
                    } 
                } 
            }
            return empleadoSelected_id;
        },
        async getjerarquizacions(){  
            this.jerarquizacionCollection  = [];
            this.paginaCollection = [];
            const response = await this.request(this.path,{'action' : 'select','filter' : this.filter});
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.jerarquizacionCollection = response;
                this.paginaCollection = response;
                this.paginator(1);  
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        builOrganigrama(){ 
                this.myHtmlCode += '<div class="row">';
                for (let index = 0; index < this.jerarquizacionCollection.length; index++) {
                    const element = this.jerarquizacionCollection[index];
                    if (0 == element.value) {
                        this.myHtmlCode += `
                        <div class="col-sm">
                            <ul>
                                <li> 
                                    <a>${element.nombre_empleado}</a>
                                    <input style="display:none" level=1_${element.id_empleado}>
                                </li>
                            </ul>
                        </div>
                        `;
                        this.printByLevel(element.id_empleado);
                    }
                }
                this.myHtmlCode += '</div>';
            //    console.log(this.myHtmlCode);
            // this.myHtmlCode = `<ul>
            //                         <li>
            //                         <a>Director</a>
            //                             <ul>
            //                                 <li>
            //                                     <a>Vicepresidente</a>
            //                                     <ul>
            //                                         <li><a>Vicepresidente</a></li>
            //                                     </ul>   
            //                                 </li>
            //                             </ul>
            //                         </li>
            //                     </ul>`; 
        },
        printByLevel(id_empleado){ 
            for (let index = 0; index < this.ev_atributoCollection.length; index++) {
                const nivel = this.ev_atributoCollection[index];
                if (nivel.value  > 0) { 
                    let result = this.recursividad(nivel.value,id_empleado);
                    // console.log(`<input style="display:none" level=${nivel.value}_${id_empleado}>`);
                    if (result == "") {
                        result += ` <ul>
                                        <li>
                                            <a>#</a>
                                        </li>
                                    </ul>`;
                    }
                    let valueRemplace = `<input style="display:none" level=${nivel.value}_${id_empleado}>`;
                    this.myHtmlCode.replace(valueRemplace, result);
                }
            }
        },
        recursividad(nivel,idSuperior){
            let resultado = "<ul>";
            for (let index = 0; index < this.jerarquizacionCollection.length; index++) {
                const element = this.jerarquizacionCollection[index];
                // ${this.recursividad(element.id_empleado)}
                if (element.id_superior == idSuperior && nivel == element.value) {
                    resultado += `<li> 
                                    <a>${element.nombre_empleado}</a>
                                </li>
                                <input style="display:none" level=${nivel}>
                                `;
                } 
            } 
            return resultado + '</ul>';
        },
        async delete_jerarquizacion(jerarquizacion_id){   
            if(jerarquizacion_id > 0){
                const response = await this.request(this.path,{model:{'jerarquizacion_id':jerarquizacion_id},'action' : 'delete'});
                if(response.message == 'Data Deleted'){
                    await this.getjerarquizacions();
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },   
        async save_jerarquizacion(){
            if(this.jerarquizacion.jerarquizacion_id > 0){
                const response = await this.request(this.path,{model:this.jerarquizacion,'action' : 'update'});
                if(response.message == 'Data Updated'){
                    await this.getjerarquizacions();
                    this.show_message('Registro Actualizado','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }else if(this.jerarquizacion.jerarquizacion_id == 0){ 
                const response = await this.request(this.path,{model:this.jerarquizacion,'action' : 'insert'}); 
                 if(response.message == 'Data Inserted'){
                    await this.getjerarquizacions();
                    this.show_message('Registro Guardado.','success');
                    this.model_empty();
                    this.isFormCrud = false;
                }else{
                    this.show_message(response.message,'error');
                }
            }
        },
        async update_jerarquizacion(jerarquizacion_id){ 
            if(jerarquizacion_id > 0){
                this.jerarquizacion = this.search_jerarquizacionByID(jerarquizacion_id);
                if(this.jerarquizacion.jerarquizacion_id > 0){
                    this.empleadoCollectionfiltro = this.empleadoCollection;
                    this.superiorCollectionfiltro = this.empleadoCollection;
                    this.isFormCrud = true;
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_jerarquizacion(){  
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_jerarquizacion(){  
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_jerarquizacionByID(jerarquizacion_id){
            for (let index = 0; index < this.jerarquizacionCollection.length; index++) {
                const element = this.jerarquizacionCollection[index];
                if (jerarquizacion_id == element.jerarquizacion_id) {
                    return element;
                }
            }
        },async show_message(msg,typeMessage){
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){ 
            this.filtroEmpleado = '';
            this.filtroSuperior = '';
            this.jerarquizacion = {jerarquizacion_id:0,id_empleado:''
                                    ,id_atributo_nivel:'',activo:true,id_superior:null,creado:''
                                    ,actualizado:'',creadopor:'',actualizadopor:''};
        },
        async request(path,jsonParameters){
            const response = await axios.post(path, jsonParameters).then(function (response) {   
                    return response.data; 
                }).catch(function (response) {  
                    return response.data;
                })
            return response; 
        },
        async fill_f_keys(){
            const response_departamento = await this.request('../../models/admin/bd_departamento.php'
            ,{'order' : 'ORDER BY id_empresa,id_segmento,nombre ASC','action' : 'select'});
            try{  
                if(response_departamento.length > 0){  
                    this.departamentoCollection = response_departamento;  
                }  
            }catch(error){
                this.show_message('No se encontrarón Departamentos.','info');
            } 
            const response_ev_atributo = await this.request('../../models/ev/bd_ev_atributo.php',
            {'valor' : 'Nivel Lider','action' : 'select'});
            try{  
                if(response_ev_atributo.length > 0){  
                    this.ev_atributoCollection = response_ev_atributo; 
                }  
            }catch(error){
                this.show_message('No hay ev_atributos.','info');
            }  
            const response_empleado = await this.request('../../models/admin/bd_empleado.php',{'action' : 'selectSimple'});
            try{
                if(response_empleado.length > 0){  
                    this.empleadoCollection = response_empleado; 
                }  
            }catch(error){
                this.show_message('No hay empleados.','info');
            } 
        },paginator(i){ 
            let cantidad_pages = Math.ceil(this.jerarquizacionCollection.length / this.numByPag);
            this.paginas = []; 
            if (i === 'Ant' ) {
                if (this.paginaActual == 1) {  i = 1;  }else{  i = this.paginaActual -1; }
            }else if (i === 'Sig') {
                if (this.paginaActual == cantidad_pages) {  i = cantidad_pages; } else { i = this.paginaActual + 1; }
            }else{ this.paginaActual = i; }
            this.paginaActual = i;
            this.paginas.push({'element':'Ant'});
            for (let indexI = 0; indexI < cantidad_pages; indexI++) {
                this.paginas.push({'element':(indexI + 1)});
                if (indexI == (i - 1) ) {
                    this.paginaCollection = [];
                    let inicio = ( i == 1 ? 0 : ((i-1) *  parseInt(this.numByPag)));
                    inicio = parseInt(inicio);
                    let fin = (cantidad_pages == i ? this.jerarquizacionCollection.length : (parseInt(inicio) + parseInt(this.numByPag)));
                    for (let index = inicio; index < fin; index++) {
                        const element = this.jerarquizacionCollection[index];
                        this.paginaCollection.push(element);
                    }
                }
            }
            this.paginas.push({'element':'Sig'});
        }

    },
    async mounted() {    
    },
    async created(){
       await this.getjerarquizacions();
       await this.model_empty();
       await this.fill_f_keys();
       this.builOrganigrama();
       this.paginator(1);
    }
}); 
        
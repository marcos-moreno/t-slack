 
var application = new Vue({
    el:'#app_solicitud_uniforme',
    data:{ 
        solicitud_uniforme : {},
        solicitud_uniformeCollection : [],
        isFormCrud: false,
        path : '../../models/un/bd_solicitud_uniforme.php',
        typeMessage : '',
        msg:'',
        empleadoCollection:[], 
        numByPag : 5, 
        paginas : [],
        paginaActual : 1, 
        cargando : false,
        //detalle
        isFormCrud_detalle: false,
        solicitud_uniforme_detalle:{},
        solicitud_uniforme_detalleCollection:[],
        productoCollection:[],
        tallaCollection:[],
        colorCollection:[],
        catalogoCollection:[],
        catalogo:{id_catalogo:0},
        productoSelected : {},

        // paquetes
        tipo_entregasCollection : [],
        tipo_entrega : {},
        paqueteCollection:[],
        id_paquete : 0,
        paquete_detalleCollection:[],

        // extras
        account : {},
        tallaPlayeraCollection : [],
        numsZapatoCollection : [], 
        id_talla:0,
        id_talla_zapato:0,
        myModalColors:false,
        colores : [],
        // Filtro
        filter:'',
        estado_filtro : 'estado',
        id_empresaSelected: 'emp.id_empresa',
        id_segmentoSelected: 's.id_segmento',
        companys: [],
        segments:[],
        empleados:[],

    },
    methods:{

// paquete 
    async add_paquete(){
        if (this.id_paquete > 0 ) {
            this.paquete_detalleCollection  = []; 
            let filtrarPor =  "(id_paquete =" + this.id_paquete + " )";  
            const response = await this.request('../../models/un/bd_paquete_detalle.php',{'order' : 'ORDER BY id_paquete DESC','action' : 'select','filter' : filtrarPor});
            
            try{   
                this.paquete_detalleCollection = response;     
                for (let index = 0; index < this.paquete_detalleCollection.length; index++) {
                    const element = this.paquete_detalleCollection[index]; 
                    let id_talla_valor = 0; 
                    switch (element.producto[0].id_tipo_producto) {
                        case 1: //Camiseta
                            try {
                                if (this.id_talla > 0 ) { 
                                    id_talla_valor =  this.id_talla; 
                                } else{
                                    this.show_message('Selecciona la talla','error');
                                };
                            } catch (error) {
                                id_talla_valor = 0;
                                this.show_message('Selecciona la talla','error');
                            }
                        break;
                        case 3: //Zapato
                            try {
                                if (this.id_talla_zapato > 0 ) {
                                    id_talla_valor =  this.id_talla_zapato; 
                                } else{
                                    this.show_message('Selecciona tu Número de Zapato','error'); 
                                };
                            } catch (error) {
                                id_talla_valor = 0;
                                this.show_message('Selecciona tu Número de Zapato','error');
                            } 
                        break; 
                    }  
                    if (id_talla_valor > 0) {
                        this.solicitud_uniforme_detalleCollection.push({
                            id_solicitud_uniforme_detalle:0,
                            id_solicitud_uniforme:0,
                            id_producto:element.id_producto,
                            id_talla: id_talla_valor,
                            total_linea:element.costo,
                            id_color:element.id_color,
                            cantidad:element.cantidad,
                            producto:element.producto,
                            id_paquete : this.id_paquete,
                            talla: [this.getTalla(id_talla_valor)],
                            color: [element.color[0]],
                            permitir_cambiar_color:element.permitir_cambiar_color
                        });
                        let monto = 0;
                        this.solicitud_uniforme_detalleCollection.forEach(element => {
                            monto = parseFloat(monto) + parseFloat(element.total_linea);  
                        });  
                        this.solicitud_uniforme.total = Number(monto).toFixed(2);
                        this.model_detalle_empty();      
                    } 
                } 
            }catch(error){ 
                this.show_message('Este paquete Presenta Falla, intenta Recargar esta Página.','error');
            } 
        } else {
            this.show_message('Selecciona un Paquete.','error');
        } 
    }, 
    getTalla(id){
        for (let index = 0; index < this.tallaPlayeraCollection.length; index++) {
            const element =  this.tallaPlayeraCollection[index];
            if (element.id_talla == id) {
                return element;
            }
        }
        for (let index = 0; index < this.numsZapatoCollection.length; index++) {
            const element =  this.numsZapatoCollection[index];
            if (element.id_talla == id) {
                return element;
            }
        }
    },
    async gettipo_entregass(){  
        this.tipo_entregasCollection  = []; 
        let filtrarPor =  " (activo = true) ";  
        const response = await this.request('../../models/un/bd_tipo_entregas.php',{'order' : 'ORDER BY id_tipo_entrega DESC','action' : 'select'});
        try{ 
            this.tipo_entregasCollection = response;  
        }catch(error){ 
            this.show_message('No hay Entregas disponibles.','info');
        } 
    }, 
    async getpaquetes(){
      console.log(this.tipo_entrega);
        this.paqueteCollection  = []; 
        let filtrarPor =  " (id_tipo_entrege = " + this.tipo_entrega.id_tipo_entrega + 
                        " AND  (genero = 'U' OR genero = (SELECT genero FROM empleado WHERE id_empleado = *requerid_session* ))) " ;  
        const response = await this.request('../../models/un/bd_paquete.php',{'order' : 'ORDER BY nombre_paquete DESC','action' : 'select','filter' : filtrarPor});
        try{ 
            this.show_message(response.length + ' Paquetes Encontrados.','success');
            this.paqueteCollection = response; 
            // console.log(response);
        }catch(error){
            this.show_message('No hay paquetes disponibles.','info');
        } 
    }, 
// paquete 

// detalle
        async guardarColor(){ 
            if (this.solicitud_uniforme_detalle.id_solicitud_uniforme > 0) {
                const response = await this.request('../../models/un/bd_solicitud_uniforme_detalle.php'
                ,{model:this.solicitud_uniforme_detalle,'action' : 'update'});  
                 await this.getsolicitud_uniforme_detalles();
                 this.show_message('Color Actualizado','success'); 
                 this.myModalColors = false;  
            }else{
                for (let index = 0; index < this.colores.length; index++) {
                    const color = this.colores[index];
                    if (color.id_color == this.solicitud_uniforme_detalle.id_color) {
                        this.solicitud_uniforme_detalle.color = [color];
                        this.myModalColors = false;  
                        return;
                    }
                }  
            }
        },
        async changeColor(solicitud_uniforme){ 
            this.solicitud_uniforme_detalle = solicitud_uniforme; 
            this.colores = []; 
            this.myModalColors = true; 
            let filtrarPor =  " (activo = true) AND id_color IN (SELECT id_color FROM Producto_color WHERE id_producto=" + this.solicitud_uniforme_detalle.id_producto + " ) ";  
            const colores_r = await this.request('../../models/un/bd_color.php',{'order' : 'ORDER BY nombre_color ASC','action' : 'select','filter' : filtrarPor});
            try { 
                if (colores_r.length > 0) {  this.colores =  colores_r;  }    
            }catch (error) {}   
        }, 
        async delete_solicitud_uniforme_detalle(valor){ 
            if (valor.id_paquete < 1 && valor.id_paquete != 'undefined' ) {
                if (valor.id_solicitud_uniforme_detalle == 0) {
                    let temp = [];
                    this.solicitud_uniforme_detalleCollection.forEach(element => {
                        if (element.index != valor.index) {
                            temp.push(element);
                        }
                    });
                    this.solicitud_uniforme_detalleCollection = temp; 
                }else{ 
                    this.cargando = true;
                    const response = await this.request('../../models/un/bd_solicitud_uniforme_detalle.php'
                                                        ,{model:valor,'action' : 'delete'}); 
                    if(response.message == 'Data Deleted'){
                        await this.getsolicitud_uniforme_detalles();
                        let monto = 0;
                        this.solicitud_uniforme_detalleCollection.forEach(element => {
                            monto = parseFloat(monto) + parseFloat(element.total_linea);  
                        }); 
                        this.solicitud_uniforme.total = Number(monto).toFixed(2);
                        const response = await this.request(this.path,{model:this.solicitud_uniforme,'action' : 'update'});
                        if(response.message == 'Data Updated'){  
                            this.cargando = false;
                            this.show_message('Producto Eliminado','success'); 
                        }else{
                            this.cargando = false;
                            this.show_message(response.message,'error');
                        } 
                    }else{
                        this.cargando = false;
                        this.show_message(response.message,'error');
                    } 
                } 
            }else{
                if (confirm("Este Producto pertenece a un Paquete al Eliminarlo se eliminará el paquete Completo")) {
                    if (valor.id_solicitud_uniforme_detalle == 0) {
                        let temp = [];
                        this.solicitud_uniforme_detalleCollection.forEach(element => {
                            if (valor.id_paquete != element.id_paquete) {
                                temp.push(element);
                            }
                        });
                        this.solicitud_uniforme_detalleCollection = temp; 
                    }else{ 
                        this.cargando = true;
                        const response = await 
                        this.request('../../models/un/bd_solicitud_uniforme_detalle.php'
                                    ,{model:valor,
                                    filter:" id_paquete=" + valor.id_paquete + " AND id_solicitud_uniforme=" + valor.id_solicitud_uniforme + " "
                                    ,'action' : 'delete'}); 
                        if(response.message == 'Data Deleted'){
                            await this.getsolicitud_uniforme_detalles();
                            let monto = 0;
                            this.solicitud_uniforme_detalleCollection.forEach(element => {
                                monto = parseFloat(monto) + parseFloat(element.total_linea);  
                            }); 
                            this.solicitud_uniforme.total = Number(monto).toFixed(2);
                            const response = await this.request(this.path,{model:this.solicitud_uniforme,'action' : 'update'});
                            if(response.message == 'Data Updated'){  
                                this.cargando = false;
                                this.show_message('Paquete Eliminado','success'); 
                            }else{
                                this.cargando = false;
                                this.show_message(response,'error');
                            } 
                        }else{
                            this.cargando = false;
                            this.show_message(response,'error');
                        } 
                    }  
                } 
            }
        },valid_form(){ 
            if ( this.solicitud_uniforme_detalle.id_producto == 0) {
                this.show_message('Seleccionar un Producto.','error');
                return false;
            }
            if ( this.solicitud_uniforme_detalle.id_talla == 0) {
                this.show_message('Seleccionar una Talla.','error');
                return false;
            } 
            if ( this.solicitud_uniforme_detalle.id_color == 0) {
                this.show_message('Seleccionar un Color.','error');
                return false;
            }
            if ( this.solicitud_uniforme_detalle.cantidad == 0) {
                this.show_message('Ingresa la Cantidad.','error');
                return false;
            } 
            if ( this.solicitud_uniforme_detalle.total_linea < 1) {
                this.show_message('El total es incorrecto.','error');
                return false;
            }
            return true;
        },async agrega_sol_uni_detalle(){
            if (this.valid_form()) {
                this.productoCollection.forEach(element => {
                    if (element.id_producto == this.solicitud_uniforme_detalle.id_producto) {
                        this.solicitud_uniforme_detalle.producto = [];
                        this.solicitud_uniforme_detalle.producto.push(element);
                    }
                });
                this.colorCollection.forEach(element => {
                    if (element.id_color == this.solicitud_uniforme_detalle.id_color) {
                        this.solicitud_uniforme_detalle.color = [];
                        this.solicitud_uniforme_detalle.color.push(element);
                    }
                });
                this.tallaCollection.forEach(element => {
                    if (element.id_talla == this.solicitud_uniforme_detalle.id_talla) {
                        this.solicitud_uniforme_detalle.talla = [];
                        this.solicitud_uniforme_detalle.talla.push(element);
                    }
                });
                this.solicitud_uniforme_detalle.permitir_cambiar_color = true;
                this.solicitud_uniforme_detalle.index = ( this.solicitud_uniforme_detalleCollection.length );
                this.solicitud_uniforme_detalleCollection.push(this.solicitud_uniforme_detalle);
                this.isFormCrud_detalle = false; 
                // --
                let monto = 0;
                this.solicitud_uniforme_detalleCollection.forEach(element => {
                    monto = parseFloat(monto) + parseFloat(element.total_linea);  
                });
                // --
                this.solicitud_uniforme.total = Number(monto).toFixed(2);
                this.model_detalle_empty(); 
            } 
        },
        calcularTotalDetalle(){    
            try {  
                if  ( this.solicitud_uniforme_detalle.cantidad == ''|| this.solicitud_uniforme_detalle.cantidad == '0'|| this.solicitud_uniforme_detalle.cantidad < 1) {
                    this.solicitud_uniforme_detalle.total_linea = "0.00";  
                }else{
                    if (this.productoSelected.id_producto != 0) {
                        this.solicitud_uniforme_detalle.cantidad = parseInt(this.solicitud_uniforme_detalle.cantidad); 
                        if (this.solicitud_uniforme_detalle.cantidad > 0) {
                            this.solicitud_uniforme_detalle.total_linea = Number(this.solicitud_uniforme_detalle.cantidad * this.productoSelected.costo).toFixed(2) ; 
                        }else{ 
                            this.solicitud_uniforme_detalle.total_linea = "0.00";  
                        }
                    }else{ 
                        this.solicitud_uniforme_detalle.total_linea = "0.00";  
                    } 
                }
            } catch (error) {  this.solicitud_uniforme_detalle.total_linea = "0.00";     } 

        }
        ,cancel_solicitud_uniforme_detalle(){
            this.productoCollection = [];
            this.tallaCollection = [];
            this.colorCollection = [];
            this.model_detalle_empty();
            this.isFormCrud_detalle = false;
        },
        add_solicitud_uniforme_detalle(){   
            this.productoCollection = [];
            this.tallaCollection = [];
            this.colorCollection = [];  
            this.model_detalle_empty(); 
            this.solicitud_uniforme_detalle.id_paquete = null;
            this.isFormCrud_detalle =true;
        },async buscarProductos(){ 
            this.productoCollection = [];
            this.tallaCollection = [];
            this.colorCollection = [];
            if (this.catalogo.id_catalogo != 0) {
                let filtrarPor = " activo = true AND id_catalogo = " + this.catalogo.id_catalogo;
                const response_producto = await this.request('../../models/un/bd_producto.php',{'order' : 'ORDER BY id_producto DESC','action' : 'select', 'filter' : filtrarPor});
                try{  
                    if(response_producto.length > 0 && response_producto[0].id_producto > 0){  
                        this.productoCollection = response_producto; 
                        // console.log( this.productoCollection);
                    }  
                }catch(error){
                    console.log(response_producto); //this.show_message('No hay productos.','info');
                   
                }  
            }
        },async buscarDatosProducto(){  
            if (this.solicitud_uniforme_detalle.id_producto != 0) {
                this.productoCollection.forEach(element => {
                if (element.id_producto == this.solicitud_uniforme_detalle.id_producto) {
                        this.productoSelected = element;
                    }
                });
            } 
            this.tallaCollection = [];
            if (this.productoSelected.id_producto != 0) {
                this.calcularTotalDetalle();   
                let filtrarPor = " activo = true AND id_tipo_producto IN ( SELECT id_tipo_producto FROM producto WHERE  id_producto = " + this.solicitud_uniforme_detalle.id_producto + ")";
                const response_tallas = await this.request('../../models/un/bd_talla.php',{'order' : 'ORDER BY valor ASC','action' : 'select', 'filter' : filtrarPor});
                try{  
                    if(response_tallas.length > 0 && response_tallas[0].id_talla > 0){  
                        this.tallaCollection = response_tallas; 
                    }  
                }catch(error){
                    this.show_message('No hay Tallas Disponibles.','info');
                    console.log(response_tallas);
                }  
            }
            this.colorCollection = [];
            if (this.productoSelected.id_producto != 0) {
                switch (this.productoSelected.id_tipo_producto) {
                    case 1: //Camiseta
                        this.solicitud_uniforme_detalle.id_talla = this.account.id_talla_playera;
                    break;
                    case 3: //Zapato
                        this.solicitud_uniforme_detalle.id_talla = this.account.id_numero_zapato;
                    break; 
                } 
                let filtrarPor = " id_color IN (SELECT id_color FROM producto_color WHERE id_producto = " + this.solicitud_uniforme_detalle.id_producto + " ) AND activo = true ";
                const response_color = await this.request('../../models/un/bd_color.php',{'order' : 'ORDER BY nombre_color ASC','action' : 'select', 'filter' : filtrarPor});
                try{  
                    if(response_color.length > 0 && response_color[0].id_color > 0){  
                        this.colorCollection = response_color; 
                    }  
                }catch(error){
                    this.show_message('No hay productos.','info');
                    console.log(response_color);
                }  
            }
         
        },async model_detalle_empty(){
            this.catalogo.id_catalogo = 0;
            this.solicitud_uniforme_detalle = {id_solicitud_uniforme_detalle:0,id_solicitud_uniforme:0,
                                                id_producto:0,id_talla:0,total_linea:'0.00',id_color:0,
                                                cantidad:0,id_paquete:0,solicitud_uniforme_detalle:false};
            this.productoSelected = 0;
        }, 
        async getsolicitud_uniforme_detalles(){  
            this.solicitud_uniforme_detalleCollection  = [];
            this.paginaCollection = [];
            let filtrarPor =  " ( id_solicitud_uniforme = " + this.solicitud_uniforme.id_solicitud_uniforme + ") ";  
           const response = await this.request('../../models/un/bd_solicitud_uniforme_detalle.php'
                                                ,{'order' : 'ORDER BY id_solicitud_uniforme_detalle DESC','action' : 'select','filter' : filtrarPor});
            try{ 
                if (response.length == 0 ) {
                    this.show_message('La Orden No Contiene Productos.','info');
                } else {
                    this.show_message('La Orden Contiene ' + response.length + ' Productos.','success');
                }
                this.solicitud_uniforme_detalleCollection = response;   
            }catch(error){
                this.show_message('La Orden No Contiene Productos.','info'); 
            } 
        }, 
//detalle  
        async getsolicitud_uniformes(){  
            //     this.solicitud_uniformeCollection  = [];
            //     let filtrarPor =  " emplea.id_empleado = _SESSION_id_empleado " ;  
            //    const response = await this.request(this.path,{'order' : ' ORDER BY fecha_creado DESC ','action' : 'select','filter' : filtrarPor});
            //   console.log(response);
            //    try{ 
            //         // this.show_message('Ordenes Creadas: ' + response.length + '.','success');
            //         this.solicitud_uniformeCollection = response; 
            //         this.isFormCrud=false;
            //     }catch(error){
            //         this.show_message('No hay datos Para Mostrar.','info');
            //         this.isFormCrud=false;
            //     }  
            this.solicitud_uniformeCollection  = [];
            let id_cerb = (Number.isInteger(Number.parseInt(this.filter)) ? " OR id_cerberus_empleado ='" + this.filter + "' " : "");
            let filtrarPor =  " estado = " + this.estado_filtro +
            " AND  concat(emplea.paterno,' ',emplea.materno,' ',emplea.nombre) ILIKE '%" + this.filter + "%' "+ id_cerb
            +" AND s.id_segmento = " + this.id_segmentoSelected +" AND emp.id_empresa = " + this.id_empresaSelected;  
            const response = await this.request(this.path,{'order' : ' ORDER BY e.fecha_creado DESC ','action' : 'select','filter' : filtrarPor});
            console.log(response);
            try{ 
                this.show_message(response.length + ' Registros Encontrados.','success');
                this.solicitud_uniformeCollection = response; 
                this.isFormCrud=false;
            }catch(error){
                this.show_message('No hay datos Para Mostrar.','info');
                this.isFormCrud=false;
            } 
        }, 
        async delete_solicitud_uniforme(id_solicitud_uniforme){
            this.cargando = true;  
            this.solicitud_uniforme = this.search_solicitud_uniformeByID(id_solicitud_uniforme);
            if(this.solicitud_uniforme.id_solicitud_uniforme > 0){
                const response = await this.request(this.path,{model:this.solicitud_uniforme,'action' : 'delete'});
                this.solicitud_uniformeCollection = response; 
                if(response.message == 'Data Deleted'){
                    await this.getsolicitud_uniformes();
                    this.cargando = false;
                    this.show_message('Registro Eliminado','success');
                }else{
                    this.cargando = false;
                    this.show_message(response.message,'error');
                }
            }else{ 
                this.cargando = false;
                this.show_message('Un ID 0 No es posible Eliminar.','info');
            } 
        },validar_solicitud(){ 
            if (this.solicitud_uniforme.id_empleado > 0 ) { 
            }else{
                this.show_message('Debes Seleccionar un Empleado','error')
                return false;
            } 
            if (this.solicitud_uniforme_detalleCollection.length > 0 ) { 
            }else{
                this.show_message('Debes ingresar Productos','error')
                return false;
            }  
            try {
                if (this.tipo_entrega.id_tipo_entrega > 0) { 
                }else{
                    this.show_message('Debes Seleccionar un Tipo de Entrega','error')
                    return false;
                }  
            } catch (error) {
                this.show_message('Debes Seleccionar un Tipo de Entrega','error')
            }
            return true;
        }, 
        async save_solicitud_uniforme(){ 
            if(this.validar_solicitud()){ 
                this.cargando = true;
                if(this.solicitud_uniforme.id_solicitud_uniforme > 0){
                    // --
                    let monto = 0;
                    this.solicitud_uniforme_detalleCollection.forEach(element => {
                        monto = parseFloat(monto) + parseFloat(element.total_linea);  
                    });
                    // --
                    this.solicitud_uniforme.total = Number(monto).toFixed(2);
                    console.log(this.solicitud_uniforme);
                    const response = await this.request(this.path,{model:this.solicitud_uniforme,'action' : 'update'});
                    if(response.message == 'Data Updated'){
                        for (let index = 0; index < this.solicitud_uniforme_detalleCollection.length; index++) {
                            let s_u_d = this.solicitud_uniforme_detalleCollection[index];
                            // console.log(this.solicitud_uniforme_detalleCollection);
                            s_u_d.id_solicitud_uniforme =this.solicitud_uniforme.id_solicitud_uniforme;
                            if (s_u_d.id_solicitud_uniforme_detalle == 0) {
                                const response_detalle = await this.request('../../models/un/bd_solicitud_uniforme_detalle.php' ,{model:s_u_d,'action' : 'insert'}); 
                                console.log(response_detalle);
                            } else {
                                const response_detalle = await this.request('../../models/un/bd_solicitud_uniforme_detalle.php' ,{model:s_u_d,'action' : 'update'}); 
                                console.log(response_detalle);
                            }
                        } 
                        // Stock 
                        const response_stock = await this.request('../../models/un/bd_movimiento_stock.php' ,
                        {model:{'id_movimiento':this.solicitud_uniforme.id_solicitud_uniforme,'id_tipo_movimiento':3},'action' : 'insert_stock'}); 
                        console.log(response_stock); 
                        //// stoc
                        this.solicitud_uniforme_detalleCollection = [];
                        this.productoCollection = [];
                        await this.getsolicitud_uniformes();
                        this.show_message('Registro Guardado.','success');
                        this.model_empty();
                        this.isFormCrud = false;
                        this.cargando = false;
                    }else{
                        this.cargando = false;
                        this.show_message(response.message,'error');
                    }
                }else if(this.solicitud_uniforme.id_solicitud_uniforme == 0){ 
                    this.solicitud_uniforme.id_tipo_entrega = this.tipo_entrega.id_tipo_entrega;
                    switch (this.tipo_entrega.id_tipo_entrega) {
                        case 4: //Semestral
                            this.solicitud_uniforme.fecha_entrega = this.tipo_entrega.fecha_aplicacion;
                        break;
                        case 3: //Extraordinaria     
                            var e = new Date() 
                            this.solicitud_uniforme.fecha_entrega = e.getFullYear() +"-"+ (e.getMonth()+1) +"-"+ e.getDate();
                        break;
                        case 2: //primer Entrega
                            var e = new Date(this.account.fecha_alta_cerberus)
                            e.setMonth(e.getMonth() + 1) 
                            this.solicitud_uniforme.fecha_entrega = e.getFullYear() +"-"+ (e.getMonth()+1) +"-"+ e.getDate();
                        break;
                        default:
                            break;
                    }
                    const response = await this.request(this.path,{model:this.solicitud_uniforme,'action' : 'insert',admin:true});
                    console.log(this.solicitud_uniforme);
                    console.log(response);
                    if(response.message == 'Data Inserted' && response.id_insert > 0 ){
                        try {
                            for (let index = 0; index < this.solicitud_uniforme_detalleCollection.length; index++) {
                                let s_u_d = this.solicitud_uniforme_detalleCollection[index]; 
                                s_u_d.id_solicitud_uniforme = response.id_insert;
                                const response_detalle = await this.request('../../models/un/bd_solicitud_uniforme_detalle.php' ,
                                {model:s_u_d,'action' : 'insert'}); 
                                console.log(response_detalle);
                            } 
                            // Stock
                            const response_stock = await this.request('../../models/un/bd_movimiento_stock.php' ,
                            {model:{'id_movimiento':response.id_insert,'id_tipo_movimiento':3},'action' : 'insert_stock'}); 
                            console.log(response_stock); 
                            //// stoc
                            this.solicitud_uniforme_detalleCollection = [];
                            this.productoCollection = [];
                            await this.getsolicitud_uniformes();
                            this.show_message('Registro Guardado.','success');
                            this.model_empty();
                            this.isFormCrud = false;
                            this.cargando = false;
                        } catch (error) {
                            console.log(error);
                            this.cargando = false;
                        } 
                    }else{
                        this.show_message(response,'error');
                        this.cargando = false;
                    }  
                }
            }    
        },
        async update_solicitud_uniforme(id_solicitud_uniforme){ 
            if(id_solicitud_uniforme > 0){
                this.solicitud_uniforme = this.search_solicitud_uniformeByID(id_solicitud_uniforme); 
                if(this.solicitud_uniforme.id_solicitud_uniforme > 0){
                    this.tipo_entrega = this.solicitud_uniforme.tipo_entregas[0];
                    this.getpaquetes();
                    this.getsolicitud_uniforme_detalles();
                    this.id_talla = this.account.id_talla_playera;
                    this.id_talla_zapato = this.account.id_numero_zapato;
                    this.isFormCrud = true; 
                }else{
                    this.show_message('Hay un problema con este Registro.','info');
                } 
            }else{
                this.show_message('Hay un problema con este Registro.','info');
            } 
        }, 
        add_solicitud_uniforme(){   
            this.model_empty();
            this.isFormCrud = true;
        },  
        cancel_solicitud_uniforme(){   
            this.productoCollection = [];
            this.tallaCollection = [];
            this.colorCollection = [];
            this.solicitud_uniforme_detalleCollection = [];
            this.model_detalle_empty();
            this.model_empty();
            this.isFormCrud = false;
        },  
        search_solicitud_uniformeByID(id_solicitud_uniforme){
            for (let index = 0; index < this.solicitud_uniformeCollection.length; index++) {
                const element = this.solicitud_uniformeCollection[index]; 
                if (id_solicitud_uniforme == element.id_solicitud_uniforme) { 
                    return element;
                }
            }  
        },async show_message(msg,typeMessage){
            // window.scrollTo(0,0);
            this.msg = msg;
            this.typeMessage = typeMessage;
            setTimeout(function() { application.typeMessage='' ;application.msg =''; }, 5000);
        },model_empty(){
            this.solicitud_uniforme = {id_solicitud_uniforme:0,id_empleado:0,estado:'CO',total:'0.00'};
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
            let filtrarPor = " activo=true ";
            const response_catalogo = await this.request('../../models/un/bd_catalogo.php',{'order' : 'ORDER BY nombre_catalogo ASC','action' : 'select','filter':filtrarPor});
            try{  
                if(response_catalogo.length > 0){  
                    this.catalogoCollection = response_catalogo; 
                }  
            }catch(error){
                this.show_message('No hay cátalogos.','info');
            } 
        },async fetchData(){
            await this.gettallas(); 
        },async gettallas(){
            this.tallaPlayeraCollection = [];
            this.numsZapatoCollection = [];   
            let filtrarTallas = " activo = true AND id_tipo_producto IN (1)";
            let filtrarNumeros = " activo = true AND id_tipo_producto IN (3)";
            let response_tallas = await axios.post('../../models/un/bd_talla.php',{'order' : 'ORDER BY valor ASC','action' : 'select', 'filter' : filtrarTallas});
            let response_numsZapato = await axios.post('../../models/un/bd_talla.php',{'order' : 'ORDER BY valor ASC','action' : 'select', 'filter' : filtrarNumeros});
            response_numsZapato = response_numsZapato.data;
            response_tallas = response_tallas.data;   
            try{  
                if(response_tallas.length > 0 && response_tallas[0].id_talla > 0){  
                    this.tallaPlayeraCollection = response_tallas; 
                } 
                if(response_numsZapato.length > 0 && response_numsZapato[0].id_talla > 0){  
                    this.numsZapatoCollection = response_numsZapato; 
                }  
            }catch(error){
                this.show_message('No hay productos.','info');
                console.log(response_tallas);
            }   
        } 
          // Filtros
        ,async getEmpleados(){
            this.cargando = true;
            this.empleados  = []; 
            let id_filtro = "";
            try {
                Number.isInteger(parseInt(this.filter)) ? id_filtro = " OR id_cerberus_empleado = " + this.filter:  id_filtro = ""; 
            } catch (error) {id_filtro = "";} 
            let filtrarPor = " (concat(paterno,' ',materno,' ',nombre) ILIKE '%" + this.filter + "%' " + id_filtro +")"
             " AND id_segmento = " + (this.id_segmentoSelected =="s.id_segmento" ? 'id_segmento': this.id_segmentoSelected);   
           const response = await this.request('../../models/generales/bd_empleado.php',
           {'order' : 'ORDER BY paterno,materno,nombre ASC','action' : 'select','filter' : filtrarPor});
            try{ 
                this.show_message(response.length + ' Empleados Encontrados.','success');
                this.empleados = response;   
                this.solicitud_uniforme.id_empleado = 0; 
                this.cargando = false;
            }catch(error){
                this.show_message('Ningun Empleado Encontrado.','info'); 
                this.cargando = false;
            }    
        },
        async getSegments(){
                if (this.id_empresaSelected != 'emp.id_empresa') {
                    const response = await this.request("../../models/bd/bd_organization.php",{'action' : 'fetchaByCompany',id_empresa:this.id_empresaSelected});
                    this.segments = response;  
                }else{
                    this.segments = [];
                    this.id_segmentoSelected = "s.id_segmento";
                }    
        },async getCompanys(){ 
            const response = await this.request("../../models/bd/bd_company.php",{'action' : 'fetchall'});
            this.companys = response; 
        },async changeEmployee(){  
            const account_response = await axios.post('../../models/user/bd_account.php', {  action:'fetchAccount' }).then(function(response){ return  response.data });
            this.account = account_response[0];   
            for (let index = 0; index < this.empleados.length; index++) {
                const element = this.empleados[index];
                if(element.id_empleado == this.solicitud_uniforme.id_empleado){
                    this.account = element;
                    console.log(this.account);
                    this.id_talla = this.account.id_talla_playera;
                    this.id_talla_zapato = this.account.id_numero_zapato;
                }
            }
        }
    },
    async mounted() {    
    },
    async created(){
        await this.fetchData();

       await this.getsolicitud_uniformes();
       await this.getCompanys();
       await this.model_empty();
       await this.fill_f_keys(); 
       await this. gettipo_entregass();
    }
}); 
        
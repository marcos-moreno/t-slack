var report = new Vue({
    el:'#reports',
    data:{ 
        empleados: "",
        companys : "",
        segments: "",
        pools: "", 
        empleadoSelected: 0,
        empresaSelected: 0,
        segmentSelected: 0,
        pollSelected: 0,
        typePoolSelected:0,
        reportSelected: "I",
        almacenCollection: [],
        filter : '',
        id_almacen : 0, 
        codigo : '',
        tomar_stock:true
    },
    methods:{ 
       
        async getEmployeesBySegment(){  
            await axios.post("../../models/bd/bd_employee.php", {   action:'fetchByDepartament',   id_segmento: this.segmentSelected  })
            .then(function (response) { report.empleados =  response.data;    })
            .catch(function (response) {    return response.data;  })   ;
               
        } 
        ,async getCompanys(){ 
            const response =  await 
            axios.post("../../models/bd/bd_company.php", {  action:'fetchall',  })
            .then(function (response) {         return  response.data;   })
            .catch(function (response) {     return response.data; }) ; 
            return response;
            
        },
         async getSegments(){   
            await 
            axios.post("../../models/bd/bd_organization.php", {   action:'fetchaByCompany',   id_empresa: this.empresaSelected,})
            .then(function (response) { report.segments = response.data;})
            .catch(function (response) { report.segments = response.data;  })   ;
              
        },
        async getalmacens(){ 
            await this.getEmployeesBySegment(); 
            this.almacenCollection  = []; 
            let filtrarPor =  "(nombre_almacen ILIKE '%" + this.filter + "%' AND id_segmento = " + this.segmentSelected +")";  
           const response = await this.request('../../models/un/bd_almacen.php',{'order' : 'ORDER BY activo DESC, nombre_almacen DESC','action' : 'select','filter' : filtrarPor});
            try{  
                this.almacenCollection = response;   
            }catch(error){
                this.show_message('No hay Almácenes Para Mostrar.','info'); 
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
        generateReport() {  
            const url = "../../models/generate_report.php";
            let params = url ; 
            params += "?uniformes=true&id_almacen=" + this.id_almacen;
            params += "&id_segmento=" + this.segmentSelected;  
            params += "&id_empresa=" + this.empresaSelected;
            params += "&name_report=" + this.nameReport(this.reportSelected); 
            params += "&codigo=" + this.codigo ;
            params += "&tomar_stock=" + this.tomar_stock ;
            console.log(params);
            document.getElementById("viewReport").innerHTML =  '<center><iframe src=' + params +' style="width:90%;height:1150px;"></iframe></center>'; 
            document.getElementById("bteConsRes").click();
        },
        nameReport(letter){
            switch (letter) {
                case 'I': return 'rpt_uniformes'; //Max    
                case 'II': return 'rpt_uniformes_1'; //Max                             
                default : return '';   
            }
        },
        closeFrameReportView(){
            document.getElementById("viewReport").innerHTML = "";
        }, 
    },
    async mounted() {     
    },
    created: async function(){   
        const response = await this.getCompanys(); 
        this.companys = response;  
    }
   }); 
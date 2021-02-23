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
        reportSelected: "A",
        mesSelected: "1"
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
        async getPools(){ 
            await axios.post("../../models/bd/bd_poll.php", {   action:'fetchByType',   typePoolSelected: this.typePoolSelected,})
            .then(function (response) { report.pools = response.data;})
            .catch(function (response) { report.pools = response.data;  }) ;   
        },  
        generateReport() {  
            const url = "../../models/generate_report.php";
            let params = url ;
            params += "?id_encuesta=" + this.pollSelected;
            params += "&id_empleado=" + this.empleadoSelected;
            params += "&id_segmento=" + this.segmentSelected;  
            params += "&id_empresa=" + this.empresaSelected;
            params += "&name_report=" + this.nameReport(this.reportSelected);

            if(this.reportSelected  == 'A'){ params += "&nivel=3";      }
            if(this.reportSelected  == 'B'){ params += "&nivel=2";      }
            if(this.reportSelected  == 'C'){ params += "&nivel=1";      } 
            if(this.reportSelected  == 'F'){ params += "&realizadas=0"; }
            if(this.reportSelected  == 'G'){ params += "&realizadas=1"; }  
            if(this.reportSelected  == 'M'){ params += "&num_mes=" + this.mesSelected ; } 

            params += "&tipo_encuesta=" + this.typePoolSelected; //0 = Todos los tipos de encuestas; 1 : Concluidas; 2 : En captura;
            console.log(params);
            document.getElementById("viewReport").innerHTML =  '<center><iframe src=' + params +' style="width:90%;height:1150px;"></iframe></center>'; 
            document.getElementById("bteConsRes").click();
        }, 
        nameReport(letter){
            switch (letter) {
                case 'A': return 'resultadoEncuesta_1_2'; //Max
                case 'B': return 'resultadoEncuesta_1_2'; //Alto
                case 'C': return 'resultadoEncuesta_1_2'; // Medio
                case 'D':  return 'resultadoEncuesta_2'; 
                case 'E': return 'resultadoEncuesta_1_1';   
                case 'F':  return 'resultadoEncuesta_1';
                case 'G': return 'resultadoEncuesta_1';
                case 'H': return 'resultadoEncuesta';
                case 'I': return 'Porcentaje_Segmento_Grafica_1';
                case 'J': return 'Porcentaje_Segmento_Grafica';    
                case 'K': return 'resultadoEncuesta_3'; 
                case 'L': return 'resultadoEncuesta_1_1_1';   
                case 'M': return 'cumple_mes';       
                case 'N': return 'resultadoEncuesta_1_1_1_1';                                
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
        const response = await this.getCompanys(); ///this.getEmployeesBySegment(1);
        await this.getPools();
        this.companys = response;  
    }
   }); 
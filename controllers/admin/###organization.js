var organization = new Vue({   
    el:'#crudOrg',
    data:{
     allData_Org:'',
     myModel:false,
     actionButton:'Agregar',
     dynamicTitle:'Datos Segmento',
     hiddenId: null,
     filterValue: ''
    },
    methods:{
     fetchAllData:function(){
      axios.post('../../models/bd/bd_organization.php', {
       action:'fetchall'
      }).then(function(response){
        organization.allData_Org = response.data;
      });
     },
     filtrar(){
      axios.post('../../models/bd/bd_organization.php', {
       action:'filterOrganization'
       ,filter:this.filterValue 
       }).then(function(response){
         organization.allData_Org = response.data;
       });
    },

     fetchAllData_Company:function(){
      axios.post('../../models/bd/bd_company.php', {
       action:'fetchall'
      }).then(function(response){
         organization.allDataCombo = response.data;
         //alert(response.data.message);
         //console.log(response.data);
      } , function(){
      alert('No se han podido recuperar las empresas.');


      });
     }
   ,
     openModel:function(){
        organization.first_name = '';
        organization.last_name = '';
        organization.company = '';
        organization.checked = true;
        organization.actionButton = "Agregar";
        organization.dynamicTitle = "Agregar Segmento";
        organization.myModel = true;
     },
     submitData:function(){
      if(organization.first_name != '' && organization.last_name != '')
      {
       if(organization.actionButton == 'Agregar')
       {
        axios.post('../../models/bd/bd_organization.php', {
         action:'Agregar',
         firstName:organization.first_name, 
         lastName:organization.last_name,
         company:organization.company,
         checked:organization.checked

        }).then(function(response){
            organization.myModel = false;
            organization.fetchAllData();
            organization.fetchAllData_Company();
            organization.first_name = '';
            organization.last_name = '';
            organization.company = '';
            organization.checked = '';

         alert(response.data.message);
        });
       }
       if(organization.actionButton == 'Modificar')
       {
        axios.post('../../models/bd/bd_organization.php', {
         action:'Modificar',
         firstName : organization.first_name,
         lastName : organization.last_name,
         company : organization.company,
         checked : organization.checked,
         hiddenId : organization.hiddenId
        }).then(function(response){
            organization.myModel = false;
            organization.fetchAllData();
            organization.first_name = '';
            organization.last_name = '';
            organization.company = '';
            organization.checked = '';
            organization.hiddenId = '';
         alert(response.data.message);
        });
       }
      }
      else
      {
       alert("Fill All Field");
      }
     },
     fetchData:function(id){
      axios.post('../../models/bd/bd_organization.php', {
       action:'fetchSingle',
       id:id
      }).then(function(response){
        organization.first_name = response.data.first_name;
        organization.last_name = response.data.last_name;
        organization.company = response.data.company
        organization.checked = response.data.checked
        organization.hiddenId = response.data.id;
       organization.myModel = true;
       organization.actionButton = 'Modificar';
       organization.dynamicTitle = 'Editar';
      });
     },
     deleteData:function(id){
      if(confirm("Are you sure you want to remove this data?"))
      {
       axios.post('../../models/bd/bd_organization.php', {
        action:'delete',
        id:id
       }).then(function(response){
        organization.fetchAllData();
        //organization.fetchAllData_Company();

        alert(response.data.message);
       });
      }
     }
    },

    created:function(){
     this.fetchAllData();
     this.fetchAllData_Company();
    }


   });

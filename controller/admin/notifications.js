
var notification = new Vue({
    el:'#notifications',
    data:{ 
        search_by: 0,
        allDataFilter: "",
        openModel:false,
        modalNotification:false,
        notificationSelected: '',
        allNotications:'',
        isCrud:false,
        actionButton:'Agregar',
        dynamicTitle:'Datos Empresa',
        hiddenId: null,
        countNotifications: "",  
        to_notify: [],
        data_to_filter:"",
        filter_value:"",
        sendEmail: true 
    },
    methods:{
    newNotification() {   
        this.notificationSelected = {msg:"",description:"",id_notification:0};
        this.isCrud=true;
        this.getAllData();
    },  
    filter(){ 
        this.getDataFilter();  
        let array_result= [];
        this.data_to_filter.forEach(element => { 
            if (element.value.toUpperCase().includes(this.filter_value.toUpperCase())  ) { 
                array_result.push(element);
            } 
        });
        this.data_to_filter = array_result;
    },
   async getAllData(){
        const responce =  await axios.post('../../models/notification/bd_notification.php', {  action:'getAllData' })
        .then(function(response){  return response.data;    });  
        this.allDataFilter = responce;
        // console.log(responce);
    },
    getDataFilter(){ 
        switch (this.search_by) {
            case 'empresa': 
                this.data_to_filter =  this.allDataFilter[2];
                break; 
            case 'emp': 
                this.data_to_filter =  this.allDataFilter[1];
                break; 
            case 'org': 
                this.data_to_filter =  this.allDataFilter[0];
                break; 
            case 'all': 
                this.data_to_filter = [];
                break; 
            default:
                break;
        }
    },
    moveToFilter(value){
        let valid = true;
        this.to_notify.forEach(element => {
            if (element.id == value.id) {
                valid = false;
            }
        }); 
        if (valid) {
            this.to_notify.push(value);
            this.to_notify.reverse(); 
        }  
    },
    deleteToFilter(value){
        let array_result= [];
        for (let index = 0; index < this.to_notify.length; index++) {
            let element = this.to_notify[index]; 
            if (element.id != value.id) {
                array_result.push(element);
            }
        }
        this.to_notify = array_result; 
    },
    async showData(id_notification){ 
        this.allNotications.forEach(element => {
            if (element.id_notification == id_notification) { 
                this.notificationSelected= element ;  
                return;
            }
        });
        this.isCrud=true;
    },  
    async save(){ 
        if (this.notificationSelected.id_notification > 0) {
            await this.updateData();
        } else {
           await this.createData();
        }
        this.data_to_filter = [];
        this.to_notify = [];
        this.search_by = 'all';
        this.filter_value = '';
    } ,
    async createData(){  
        const responce_nt =  await axios.post('../../models/notification/bd_notification.php', {  action:'insertData', data: this.notificationSelected })
                            .then(function(response){  return response.data;});   
        if (responce_nt.message == "Data Inserted") { 
            const responces_nt_detalil = await axios.post('../../models/notification/bd_notification.php', { action:'insertNotification',  id_notification: responce_nt.id 
                                        ,filter: this.to_notify ,type: this.search_by })
                                        .then(function(response){  return response.data;});  
            if (responce_nt.id > 0 && this.sendEmail) {
                await this.send_Email(responce_nt.id);
            }   
            this.fetchAllNotifications();  
            this.isCrud=false;
        }  
     
    },
    async send_Email(id_notifiation){ 
        const responce_email =  await axios.post('../../models/notification/bd_notification.php', {  action:'getUsersEmail',  id_notifiation: id_notifiation})
        .then(function(response){    return response.data;    }); 
        console.log(responce_email);
        for (let index = 0; index < responce_email.length; index++) {
            const element = responce_email[index];
            const result_email =  await axios.post('../../models/notification/send_email.php', { data : element,subject: element.msg })
            .then(function(response){ return response.data;    });
            console.log(result_email);
        }
    },
    async updateData(){ 
        const responce =  await axios.post('../../models/notification/bd_notification.php', {  action:'updateData', 
                                data: this.notificationSelected })
        .then(function(response){  return response.data;    });   
        if (responce.message == "Data Updated") { 
            this.fetchAllNotifications();  
            this.isCrud=false;
        }   
    }, 
    async deleteData(id_notification){ 
        const responce =  await axios.post('../../models/notification/bd_notification.php', {  action:'deleteData',  id_notification: id_notification })
        .then(function(response){ return response.data; });   
        if (responce.message == "Data Deleteted") {  
        }  
        this.fetchAllNotifications();  
        this.isCrud=false;
    }, 
     async fetchAllNotifications(){
        const responce = await axios.post('../../models/notification/bd_notification.php', {  action:'fetchallNotificationsAndmin'  }).then(function(response){   return response.data;   });   
        if (responce.length > 0 ) {
            notification.allNotications = responce;  
        } else{
            notification.allNotications = [];
        } 
     },  
    }, 
 
    mounted:function(){
        this.fetchAllNotifications(); 
        this.search_by = ""; 
    }
   });
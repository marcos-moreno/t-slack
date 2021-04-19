
var notification = new Vue({
    el:'#notification',
    data:{
        modalNotification:false,
        notificationSelected: '',
        allNotications:'',
        myModel:false,
        actionButton:'Agregar',
        dynamicTitle:'Datos Empresa',
        hiddenId: null,
        countNotifications: "",  
    },
    methods:{
    showNotifications() {  
        this.myModel ?  this.myModel = false : this.myModel = true; 
    },      
    async viewNotification(_id_notification_detail,viewed){
        if (viewed == false) {
            const responce =  await axios.post('../../models/notification/bd_notification.php', {  action:'updateViewed',  id_notification_detail: _id_notification_detail })
            .then(function(response){  return response.data;    });  
            if (responce.message == "Data Updated") { 
                this.fetchAllNotifications();  
            }  
        }  
        this.allNotications.forEach(element => {
            if (element.id_notification_detail == _id_notification_detail) { 
                this.notificationSelected= element ;
                this.modalNotification = true;
                this.showNotifications();
                return;
            }
        });
    }, 
     async fetchAllNotifications(){
        const responce = await axios.post('../../models/notification/bd_notification.php', {  action:'fetchallNotifications'  }).then(function(response){   return response.data;   });   
        if (responce.length > 0 ) {
            this.allNotications = responce;  
            let NotifiactionPending = 0;
            for (let index = 0; index < this.allNotications.length; index++) {
                const element = this.allNotications[index];
                if (element.viewed == false  && element.display_start == true) { 
                    const responce =  await axios.post('../../models/notification/bd_notification.php', 
                    { action:'updateViewed', id_notification_detail: element.id_notification_detail })
                    .then(function(response){  return response.data;    });  
                    if (responce.message == "Data Updated") { 
                        this.allNotications[index].viewed = true;
                        this.notificationSelected= element ;
                        this.modalNotification = true;
                    }  
                    break;
                }
            }  
            this.allNotications.forEach(element => { element.viewed == false ?  NotifiactionPending ++ : NotifiactionPending = NotifiactionPending  });
            NotifiactionPending > 0 ? notification.countNotifications = NotifiactionPending : notification.countNotifications = "" ;
        }  
     },  
    }, 
 
    mounted:function(){
        let reset = document.getElementById("resetPassword").value;
        if(reset != "reset"){ 
            this.fetchAllNotifications(); 
        }
    }
   });
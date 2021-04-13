<?php 
require_once '../../models/postgres.php';
require_once '../../models/auth/access_session.php';

$archivo_actual = basename($_SERVER['PHP_SELF']); $valido = false;
foreach ($elementos as $key) {  
  $path =str_replace("../", "", $key->path); 
  if (substr($_SERVER['PHP_SELF'], strlen($_SERVER['PHP_SELF']) - strlen($path) , strlen($_SERVER['PHP_SELF']) ) == $path ) {
    $valido = true;break;
  }
}  
if ($valido == false) {  header('location: ../logout.php'); } 

?>
<head> 
  <meta charset="utf-8">  
  <html lang="es">
  <title>Refividrio</title> 
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../../css/bootstrap.min.css"> 
  <script src="../../css/js/jquery.min.js"></script>
  <script src="../../css/js/popper.min.js"></script>
  <script src="../../css/js/bootstrap.min.js"></script>
  <script src="../../css/js/vue.js"></script>
  <script src="../../css/js/axios.min.js"></script>     
  <link href="../../css/modal.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="../../css/notificaciones_model.css"> 
</head>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="overflow: auto;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button> 
    <a class="navbar-brand" href="#"><img src="../../img/logo.png" style="width:70%">
      <?php echo '<br/><font size="1.5">Rol: '.$_SESSION['rol'].'</font>' ?> 
    </a>  
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <?php   echo '<br/><font size="2.5" color="#B1C6CD" >' . $nombre. '</font>' ?>  
      <ul class="navbar-nav mr-auto">  
        <?php echo $elementos_string; ?> 

          <div id='chagePassword_DIV'> 
            <input id="resetPassword" style="display:none" value=  
                   <?php 
                  session_start(); 
                  if ($_SESSION['password'] == 'af8d96655446891992fa92383ae70b02') {
                    echo("reset");
                  }else{echo("0");} 
                  ?>>  
            
            <?php if($_SESSION['rol'] == 'user'){?> 
            <li class="nav-item active">
              <button @click="showModal()" style="background:none;border:none;" > 
              <a class="nav-link">Cambiar Contraseña<span class="sr-only">(current)</span></a>
              </button>
            </li> 
            <?php } ?> 

            <div v-if="modalchagePassword" >  
              <transition name="model" >
                <div class="modal-mask" > 
                  <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                      <div class="modal-header">
                        <p class="alert alert-warning"  v-if="isPass_default==true || isPass_default=='true'" 
                        class="modal-title" >
                          Es necesario que personalices tu Contraseña
                        </p>
                        <button v-if="isPass_default==false || isPass_default=='false'" type="button" class="close" @click="modalchagePassword=false">
                        <span aria-hidden="true">&times;</span></button>
                      </div>  
                        <div class="modal-body"> 
                          <div class="card-body">   
                              <div class="custom-control custom-checkbox">
                                <h5 >Cambio de Contraseña.</h5> 
                                <div class="form-group">
                                  <label>Contraseña Anterior</label>
                                  <input :type="typeControl" class="form-control" v-model="pass_old" />
                                </div> 
                                <div class="md-form md-outline input-with-post-icon datepicker">
                                  <label for="example">Nueva Contraseña</label>
                                  <input :type="typeControl" id="password_old" class="form-control" v-model="pass_new"  />
                                </div> 
                                <div class="md-form md-outline input-with-post-icon datepicker">
                                  <label for="example">Comprueba Contraseña</label>
                                  <input :type="typeControl" id="copy_validUntil" class="form-control" v-model="pass_new_repeat" />
                                </div> 
                                <br/>

                                <div class="md-form md-outline input-with-post-icon datepicker">
                                  <label for="example">
                                      <input type='checkbox' class='custom-control-input' 
                                      id='show_pass' @change="show_pass()" 
                                        v-model='show_pass_v'
                                          false-value=false true-value=true >
                                    <label class='custom-control-label' for='show_pass'  >
                                      <img src="../../img/ojo.png" width="7%" />
                                    </label>
                                  </label>
                                </div> 
 
                                <br/>
                                <div align="center"> 
                                  <input type="button" class="btn btn-success btn-xs" value="Guardar" :disabled="disables_bte_save"  @click="managePassword()" />
                                </div>
                              </div>  
                          </div>
                        </div>
                    </div> 
                  </div>
                </div>
              </transition>
            </div>
          </div>  
        <li class="nav-item active">
          <a class="nav-link" href="../logout.php">Salir <span class="sr-only">(current)</span></a>
        </li> 
      </ul> 
    </div>
  </nav>   
  <!-- <li class="nav-item active">  -->
        <div > 
          <div id="notification">  
            <div class="demo-content">
              <div id="notification-header" >
                <div style="position:relative" >
                  <button id="notification-icon" name="button"  @click="showNotifications()" class="dropbtn"  style="border-radius: 50%;background: #00BFFF;"><span id="notification-count">{{ countNotifications }}</span><img src="../../img/notificacions.png" width=30px; /></button>
                    <div id="notification-latest" v-if="myModel" >  
                      <div v-for="item in allNotications">  
                        <div class='notification-item container-fluid'  v-bind:class="[item.viewed ? '': 'viewed']" 
                              @click="viewNotification(item.id_notification_detail,item.viewed)" >
                          <h4>{{ item.msg }}</h4> 
                          <div  style="max-height:100px;overflow:auto;" v-html="item.description"></div> 
                        </div> 
                      </div> 
                    </div>
                </div>
              </div>          
            </div>  <!-- notificaciones --> 
            
            <div v-if="modalNotification" >  <!-- Modal notificaciones -->
              <transition name="model"  >
                <div class="modal-mask" > 
                  <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title">{{ notificationSelected.msg }}</h4>
                        <button type="button" class="close" @click="modalNotification=false;notificationSelected='';"><span aria-hidden="true">&times;</span></button>
                      </div>  
                      <div class="modal-body"> 
                        <div class="card-body">   
                          <div class="custom-control custom-checkbox"> 
                            <div v-html="notificationSelected.description"></div>
                          </div> 
                        </div>
                        <button type="button" class="btn btn-info" @click="modalNotification=false;notificationSelected='';">
                          Cerrar
                        </button>
                      </div>
                    </div> 
                  </div>
                </div>
              </transition>
            </div>
          </div>   
        </div>

        <div> 
          <div id="leccion">  
            <div v-if="modalLeccion" >  <!-- Modal Lecciones -->
              <transition name="model"  >
                <div class="modal-mask" > 
                  <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" @click="modalLeccion=false;lecciones=[];"><span aria-hidden="true">&times;</span></button>
                      </div>  
                      <div class="modal-body"> 
                        <div class="card-body">   
                          <div class="custom-control custom-checkbox">
                          </div> 
                        </div>
                      </div>
                    </div> 
                  </div>
                </div>
              </transition>
            </div>
          </div>
        </div>

<script>
  async function hover() {
    httpGet( '../../models/auth/check_session.php', 
      function(response) { 
        if (response == 1) { 
        }else{
          location.href="../";
        }
      }); 
  }  
  async function  httpGet(theUrl, callback){
      var xmlHttp = new XMLHttpRequest();
      xmlHttp.onreadystatechange = function() { 
          if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
              callback(xmlHttp.responseText);
      }
      xmlHttp.open("GET", theUrl, true); 
      xmlHttp.send(null);
  }
</script>

<body  onclick="hover()" style="background-color: <?php session_start();   echo $_SESSION['color_back'] ?>" >
<script type="text/javascript" src="../../controller/user/c_leccion.js"></script>
<script type="text/javascript" src="../../controller/user/notifications_u.js"></script>
<script type="text/javascript" src="../../controller/admin/changePasword2js"></script>  
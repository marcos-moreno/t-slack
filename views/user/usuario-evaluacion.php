<?php 
    require '../header.php';   
    if(isset($_GET['id_lider'])){
        echo '<input id="id_lider" value="'.$_GET['id_lider'].'" style="display:none" >';
    }else{ header('location: showPoll.php');}
    if(isset($_GET['lider'])){
        echo '<center><h4>EVALUANDO A '.$_GET['lider'].'</h4></center>';
    }else{ header('location: showPoll.php');}
    if(isset($_GET['id_indicador'])){
        echo '<input id="id_indicador" value="'.$_GET['id_indicador'].'" style="display:none" >';
    }else{ header('location: showPoll.php');}
?>  
<div class="container">
    <div id="usuarioencuesta">
        <center v-if="cargando == false && termino">
            <img v-if="cargando == false && status_termino == false" src="../../img/cancelar.png" class="img-fluid" width="200">
            <img v-else src="../../img/cheque.png" class="img-fluid" width="200">
            <br>
            <h5>{{estado_descripcion}}</h5>
            <br>
            <a href="../">
                <img src="../../img/regresar.png" width="28px"  /> Regresar
            </a>
        </center>
        <center v-if="cargando">
            <img src="../../img/cargando.gif" class="img-fluid" width="500">
            <h5>Por favor no cierres ni recargues la página.</h5>
            <h4>Recuerda que es importante contar con una conexión estable a Internet.</h4>
        </center>
        <center v-if="cargando == false && is_upload">
            <img src="../../img/subiendo.gif" class="img-fluid" width="500" alt="">
            <h5>Espera por favor, estamos guardando tus Respuestas.</h5>
        </center>  
        <div v-if="is_upload==false && cargando==false && termino==false"  > 
            <button @click="valueDefault()"  class="btn btn-info" style="margin-top:15px;" :disabled=btePressed  v-if="cargando==false" > 
                        valueDefault
            </button>
            <div v-for="row in puntos_evaluar" class="card" > 
                <div  class="container">
                    <br>
                    <div :id="'label_'+ row.id_pregunta"   class="alert alert-info" >
                        <span v-if="row.respuesta !='' || intent_save == false" style="color:black;"><strong> {{ row.nombre }}</strong></span> 
                        <span v-if="row.respuesta ==''  && intent_save == true" style="color:red;"><strong> {{ row.nombre }}</strong></span> 
                    </div>
                    <div v-if="row.tipo_captura == 'RADIO' && row.es_evaluado == true" class="form-check">
                        <div v-for="r in row.ev_punto_evaluar_ln">      
                            <label>
                                <input type='radio' v-model="row.respuesta"
                                    :value="r.valor"
                                    :id="row.ev_punto_evaluar_id + '_' + r.ev_punto_evaluar_ln_id" :name=row.ev_punto_evaluar_id
                                    style="margin-left:15px;"> {{ r.nombre }} 
                            </label>
                        </div>
                    </div>
                    <div v-if="row.tipo_captura == 'TEXTO'" >
                        <input v-model="row.respuesta" class="form-control" type='text' class="form-control"  >
                    </div>
                </div>  
                <br> 
                </div>
                <center> 
                    <button @click="getRespuestas()"  class="btn btn-info" style="margin-top:15px;" :disabled=btePressed  v-if="cargando==false" > 
                    <img src="../../img/send.png" width="18px" >  Completar evaluación
                    </button>
                </center><br><br><br>
            </div>
        </div> 
    </div> 
</div>
<script type="text/javascript" src="../../controllers/user/ctl_evaluacion.js"></script>

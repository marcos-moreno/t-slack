<?php 
     require '../header.php';   
    if(isset($_GET['ev_ticket_ln_id'])){
        echo '<input id="ev_ticket_ln_id" value="'.$_GET['ev_ticket_ln_id'].'" style="display:none" >';
    }else{header('location: v_ev_ticket.php');}
    if(isset($_GET['departamento'], $_GET['nombre'], $_GET['paterno'],  $_GET['materno'],  $_GET['comentario_solucion'], $_GET['situacion'])){
            echo '<div class="container"><center><h3>Evaluando el departamento de: '.$_GET['departamento'].'</h3></center></div><br>';
            echo '<div class="container"><center><h6>Solucionado por: '.$_GET['nombre'].' '.$_GET['paterno'].' '.$_GET['materno'].'.<br>
             Comentario de Soluci&oacute;n: '.$_GET['comentario_solucion'].' <br>
             Situaci&oacute;n Ticket: '.$_GET['situacion'].' </h6></center><br>';


    }else{ header('location: v_ev_ticket.php');}
    if(isset($_GET['id_indicador'])){
        echo '<input id="id_indicador" value="'.$_GET['id_indicador'].'" style="display:none" >';
    }else{ header('location: v_ev_ticket.php');}
    if(isset($_GET['solucionadopor'])){
        echo '<input id="solucionadopor" value="'.$_GET['solucionadopor'].'" style="display:none" >';
    }else{ header('location: v_ev_ticket.php');}
?>  


<div class="container">
    <div id="ticketencuesta">
        <center v-if="cargando == false && termino">
            <img v-if="cargando == false && status_termino == false" src="../../img/cancelar.png" class="img-fluid" width="200">
            <img v-else src="../../img/cheque.png" class="img-fluid" width="200">
            <br>
            <h5>{{estado_descripcion}}</h5>
            <br>
            <a href="v_ev_ticket.php">
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
            <!-- <button @click="valueDefault()"  class="btn btn-info" style="margin-top:15px;" :disabled=btePressed  v-if="cargando==false" > 
                        valueDefault
            </button> -->
            <div v-for="row in puntos_evaluar" class="card" > 
                <div  class="container">
                    <br>
                    
                    <div :id="'label_'+ row.id_pregunta"   class="alert alert-info" >
                        <span v-if="row.respuesta !='' || intent_save == false" style="color:black;"><strong>{{ row.orden }}.- {{ row.nombre }}</strong></span> 
                        <span v-if="row.respuesta ==''  && intent_save == true" style="color:red;"><strong>{{ row.orden }}.- {{ row.nombre }}</strong></span> 
                    </div>
                    <div v-if="row.tipo_captura == 'RADIO' && row.es_evaluado == true" class="form-check">
                        <div v-for="r in row.ev_punto_evaluar_ln"> 
                            <label>     
                                <input type='radio' v-model="row.respuesta"
                                    :value="r.valor"
                                    :id="row.ev_punto_evaluar_id + '_' + r.ev_punto_evaluar_ln_id" :name=row.ev_punto_evaluar_id
                                    style="margin-left:15px;"> 
                                   
                                    <span v-for="(e, i) in  parseInt(r.valor)" name="raiting" 
                                    style="width: 1em; font-size: 1vw; color: black; position: relative; cursor: pointer;" >
                                    <!-- ☆ -->
                                    <img src="../../img/start.png" height="30" width="30">
                                </span>                                    
                                    
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
<script type="text/javascript" src="../../controllers/ev/c_ev_ticket_evaluacion.js"></script>


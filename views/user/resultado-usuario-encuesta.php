<?php 
    require '../header.php'; 
?>
<?php   
    if(isset($_GET['id_encuesta'])){
        echo '<input id="id_encuesta" value="'.$_GET['id_encuesta'].'" style="display:none" >';
    }else{
        header('location: showPoll.php');
    }
?>   
<div class="container" style="width:100%">  
    <div id="usuarioencuesta" style="margin-top:1px;"> 
        <center v-if=cargando> 
            <img src="../../img/cargando2colores.gif" class="img-fluid" width="700"  >  
            <h5>Estamos Corroborando tus Respuestas.</h5>
        </center>  
        <div  v-if="cargando==false">
            <div class="alert alert-info" style="background:#004276;color:#fff" >
                    <h3  >{{poll.nombre}}  </h3> 
                Nombre: <strong> {{datosdetermino.nomempleado}} </strong><br>
                Fecha de Respuesta:  <strong> {{datosdetermino.respuesta}} </strong>  <br>
                Folio de termino: <strong> {{datosdetermino.id_empleado_encuesta}} </strong> 
                <h6>Respuestas Correctas: <strong> {{datosdetermino.respuestas_correctas}}/{{datosdetermino.total}} </strong> </h6>
        </div>
        
            <div v-for="row in questions">  
                <!-- {{ row }} -->
                <div v-if="row.is_evaluated"   v-bind:class="[(row.estado_respuesta == 'Correcta' ) ? 'alert alert-success' : 'alert alert-danger']"  >
                    <span>
                    <!-- <strong> {{ row.numero_pregunta }}-:</strong> -->
                    <strong> {{ row.nombre_pregunta }}</strong></span>
                        *Esta Pregunta se Evaluará* <img src="../../img/alertad.svg" width="18px" > 
                    <div v-if="row.estado_respuesta == 'Correcta'"> 
                        <div v-for="r in row.respuestas" ><div class="col-md-12"><span>R: <strong>{{ r }}</strong></span></div></div> 
                        <img src="../../img/cheque.png" width="18px" >La respuesta es Correcta </div> 
                    <div v-else >
                        <div v-for="r in row.respuestas" ><div class="col-md-12"><span>R: <strong>{{ r }}</strong></span></div></div> 
                        <img src="../../img/cancelar.png" width="18px" > La respuesta es Incorrecta   
                        <div class="alert alert-success" v-if="row.id_tipo != 5" >La Respuesta Correcta es: {{row.resp_direct_quest_value}}</div>
                        <div class="alert alert-success" v-else >Las Respuestas Correctas son: 
                            <div v-for="r in row.valid_options" ><div class="col-md-12"><span>- <strong>{{ r }}</strong></span></div></div> 
                        </div>
                    </div>
                </div> 
                <div  v-else   class="alert alert-info" >
                <!-- <strong> {{ row.numero_pregunta }}-:</strong> -->
                <span > <strong> {{ row.nombre_pregunta }}</strong> 
                <div class="alert alert-warning"  >    
                    <div  v-for="r in row.respuestas">    
                        <div class="col-md-12"> 
                            <span>
                                {{ r }}   
                            </span>
                        </div>  
                    </div> 
                    </div>  
                </div>    
            </div> 
            <center> 
               <a href="showPoll.php"> <button  class="btn btn-success" style="margin-top:15px;"> 
                    Todas las encuestas
                </button></a>
            </center>
            <br><br><br>
        </div> 
    </div> 
</div>
<script type="text/javascript" src="../../controller/user/ctl_answer_survey_show.js"></script>

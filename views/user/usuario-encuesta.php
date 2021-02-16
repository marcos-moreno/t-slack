<?php 
    require '../header.php'; 
?>
<?php   
    if(isset($_GET['id_encuesta'])){
        echo '<input id="id_encuesta" value="'.$_GET['id_encuesta'].'" style="display:none" >';
    }else{ header('location: showPoll.php');}
?>  
<div class="container" >  
    <div id="usuarioencuesta"  > 
    <h3 class="alert alert-info"  >{{poll.nombre}}</h3> 
    <center v-if=cargando> 
        <img src="../../img/cargando.gif" class="img-fluid" 
         >  
        <h5>Por favor no cierres ni recargues la página.</h5>
    </center>  

    <center v-if=is_upload> 
        <img src="../../img/subiendo.gif" style='max-width:100%;max-heigth:100%;'
        alt=""  >  
        <h5>Espera por favor, estamos Guardando tus Respuestas.</h5>
    </center>  

        <div v-if="is_upload==false && cargando==false"  > 
            <div v-for="row in questions" class="card" > 
                <div  class="container"   > 
                    <br>
                        <div :id="'label_'+ row.id_pregunta"   class="alert alert-info" >
                            <span><strong> {{ row.nombre_pregunta }}</strong></span> 
                        </div>  
                        

                        <!-- <div v-if="row.tipo == 'checkbox'"   >
                        <div v-for="r in row.options" >    
                                <input style="margin-left:5px;"   :type=row.tipo :id="row.id_pregunta + '_' + r.id_opcion" :value=r.id_opcion > 
                                    <span>
                                        {{ r.opcion }}  
                                        <input v-if="r.respuesta_extra"   style="width:50%;  border-top: none;  border-left: none;
                                          border-right: none;  border-bottom: 1px solid #03a8f45e;"
                                          type="text" :id="r.id_opcion + '_respuesta_extra'" placeholder="Especifique cual" > 
                                    </span>
                        </div> 
                    </div>  -->


                        <div v-if="row.tipo == 'checkbox'"  >
                            <div v-for="r in row.options" >    
                                <div>
                                <input style="margin-left:5px;"   :type=row.tipo :id="row.id_pregunta + '_' + r.id_opcion" :value=r.id_opcion > 
                                    <span>
                                        {{ r.opcion }}  
                                        <input v-if="r.respuesta_extra"   style="width:50%;  border-top: none;  border-left: none;
                                          border-right: none;  border-bottom: 1px solid #03a8f45e;"
                                          type="text" :id="r.id_opcion + '_respuesta_extra'" placeholder="Especifique cual" > 
                                    </span>
                                    <!-- <div v-if="r.respuesta_extra==true" class="form-inline">
                                        <input style="margin-left:5px;" :type=row.tipo :id="row.id_pregunta + '_' + r.id_opcion" :value=r.id_opcion > 
                                        <span>{{ r.opcion }}</span>
                                        <input style="margin-left:5px;" type="text" :id="r.id_opcion + '_respuesta_extra'" placeholder="Por favor Especifique..." class="form-control form-control-sm" > 
                                    </div>

                                    <div v-else >
                                        <input style="margin-left:5px;" :type=row.tipo :id="row.id_pregunta + '_' + r.id_opcion" :value=r.id_opcion > 
                                        <span style="margin-left:5px;">{{  r.opcion }}</span>
                                    </div>  -->
                                </div>  
                            </div> 
                        </div>  

                        <div v-if="row.tipo == 'radio'" class="form-check">
                            <div v-for="r in row.options">     
                                <input :type=row.tipo style="margin-left:15px;" :id="row.id_pregunta + '_' + r.id_opcion" :name=row.id_pregunta >  <span>{{ r.opcion }} </span> 
                                <input v-if="r.respuesta_extra"   style="width:50%;  border-top: none;  border-left: none;
                                          border-right: none;  border-bottom: 1px solid #03a8f45e;"
                                          type="text" :id="r.id_opcion + '_respuesta_extra'" placeholder="Especifique cual" > 
                            </div>
                            <!-- <div v-for="r in row.options" >  
                            <input style="margin-left:5px;"   :type=row.tipo :id="row.id_pregunta + '_' + r.id_opcion" :value=r.id_opcion > 
                                   

                                    <div v-if="r.respuesta_extra==true" class="form-inline">
                                        <input class="form-check-input" :type=row.tipo :id="row.id_pregunta + '_' + r.id_opcion" :name=row.id_pregunta>
                                        <label  class="form-check-label" >{{ r.opcion }}</label > 
                                        <input v-if="r.respuesta_extra" type="text" :id="r.id_opcion + '_respuesta_extra'" placeholder="Por favor Especifique..." class="form-control form-control-sm" > 
                                    </div>
                                    <div v-else >
                                        <input  style="margin-left:5px;" :type=row.tipo :id="row.id_pregunta + '_' + r.id_opcion" :name=row.id_pregunta>
                                        <label  style="margin-left:5px;" >{{ r.opcion }}</label > 
                                    </div>
                            </div> -->
                        </div> 

                        <div v-if="row.tipo == 'select'"   >   
                            <select :id="'res_' + row.id_pregunta" class="form-control">
                                <option v-for="r in row.options" v-bind:value="r.opcion">{{ r.opcion }}</option> 
                            </select> 
                        </div> 
                        <div v-if="row.tipo == 'date'"  >
                                <input  class="form-control" :type=row.tipo :id="'res_' + row.id_pregunta" >
                        </div>
                        <div v-if="row.tipo == 'text' || row.tipo == 'number'"  >
                                <input  class="form-control" :type=row.tipo :id="'res_' + row.id_pregunta" class="form-control"  >
                        </div>

                       
                </div>  
                <br>
            </div>  

                <center> 
                    <button @click="getRespuestas()"  class="btn btn-info" style="margin-top:15px;" :disabled=btePressed  v-if="cargando==false" > 
                    <img src="../../img/send.png" width="18px" >  Completar Encuesta
                    </button>
                </center>
                <br>
                <br>
                <br>
            </div> 
        </div> 
    </div> 
</div>
<script type="text/javascript" src="../../controller/user/ctl_answer_survey_m.js"></script>

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

<div class="container" style="width:90%">  
    <div id="usuarioencuesta" style="margin-top:15px;"> 
    <center v-if=cargando> 
        <img src="../../img/cargando.gif" style="width:50%" alt=""  >  
        <h5>Por favor no cierres ni recargues la página.</h5>
    </center>  
        <div >
            <div v-for="row in questions">  
                <div :id="'label_'+ row.id_pregunta" style="margin-top:15px;" ><span><strong> {{ row.nombre_pregunta }}</strong></span> </div>  
                    <div v-if="row.tipo == 'checkbox'"   >
                        <div v-for="r in row.options" >    
                            <div class="col-md-12">
                                <input style="margin-left:5px;"   :type=row.tipo :id="row.id_pregunta + '_' + r.id_opcion" :value=r.id_opcion > 
                                    <span>
                                        {{ r.opcion }}  
                                        <input v-if="r.respuesta_extra"   style="width:50%;  border-top: none;  border-left: none;  border-right: none;  border-bottom: 1px solid #03a8f45e;"
                                          type="text" :id="r.id_opcion + '_respuesta_extra'" placeholder="Especifique cual" > 
                                    </span>
                            </div>  
                        </div> 
                    </div>   
                    <div v-if="row.tipo == 'select'"  >   
                        <select   style="margin-left:15px;" :id="'res_' + row.id_pregunta" class="form-control">
                            <option v-for="r in row.options" v-bind:value="r.opcion">{{ r.opcion }}</option> 
                        </select> 
                    </div> 
                    <div v-if="row.tipo == 'date'" >
                            <input :type=row.tipo style="margin-left:15px;"  :id="'res_' + row.id_pregunta" class="form-control" >
                    </div>
                    <div v-if="row.tipo == 'text' || row.tipo == 'number'"  >
                            <input :type=row.tipo style="margin-left:15px;" :id="'res_' + row.id_pregunta" class="form-control"  >
                    </div> 
                    <div v-if="row.tipo == 'radio'"  >
                        <div v-for="r in row.options">     
                            <input :type=row.tipo style="margin-left:15px;" :id="row.id_pregunta + '_' + r.id_opcion" :name=row.id_pregunta >  <span>{{ r.opcion }} </span> 
                        </div>
                    </div> 
                </div>   
                <center> 
                    <button @click="getRespuestas()"  class="btn btn-success" style="margin-top:15px;" :disabled=btePressed  v-if="cargando==false" > 
                        Enviar
                    </button>
                </center>
            </div> 
        </div> 
    </div> 
</div>
<script type="text/javascript" src="../../controller/user/ctl_answer_survey.js"></script>

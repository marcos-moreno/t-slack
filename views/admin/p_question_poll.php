<?php require '../header.php'; ?>
<?php   
    if(isset($_GET['id_encuesta'])){
        echo '<input id="id_encuesta" value="'.$_GET['id_encuesta'].'" style="display:none" >';
    }else{ 
?>  
<script> location.href="p_poll.php";</script>  
<?php } ?> 
<div class="container" id="crudPollQuestion"> 
 
    <h2>{{ poll.poll_name }}</h2><h4>{{ poll.poll_help }}</h>  <h6>Período {{ poll.poll_validfrom }} {{ poll.poll_validUntil }}</h6>  
    
    <div class="alert alert-danger" role="alert"   v-if="msgError" > {{alert}}
    </div>
    <div class="alert alert-success" role="alert"  v-if="msg" > {{alert}}
    </div> 

    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row">
          <div class="col-md-6">
          <h3 class="panel-title">Datos</h3>
          </div>
          <div class="col-md-6" align="right">
            <input type="button" class="btn btn-success btn-xs" @click="openModel('add',0)" value="Agregar" /> 
          </div>
        </div>
      </div> 
      <div class="panel-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <tr>
            <th>ID</th>
            <th>No.</th>
            <th>Pregunta</th> 
            <th>obligatoría</th>
            <th>Activo</th> 
            <th>Tipo</th>  
            <th>Será evaluada</th>  
            <th></th> 
            <th></th>   
            <th></th>   
          </tr>
          <tr v-for="row in allData_QuestionPoll">
            <td>{{ row.id_pregunta }}</td>    
            <td>{{ row.numero_pregunta }}</td>    
            <td>{{ row.nombre_pregunta }}</td>
            <td>
              <div v-if="row.obligatoria">Si</div>
              <div v-else >No</div>
            </td>
            <td>
              <div v-if="row.activo">Si</div>
              <div v-else >No</div>
            </td>
            <td>{{ row.tipodesc }} ({{ row.tipo }})</td>   

            <td>
            <!-- {{row}} -->
              <div v-if="row.is_evaluated">
                  <div v-if="row.resp_direct_quest_value.length < 1 && row.id_tipo != 5 "  >
                      <img src="../../img/cancelar.png" width="40" ><br>
                      No hay Respuesta
                  </div>
                  <div v-else  >
                    Si  
                  </div>
              </div>
              <div v-else >No</div>
            </td>
            
            <td><button type="button" name="question" class="btn btn-info" @click="openModel('mod',row.id_pregunta)" >Modificar</button></td> 
            <td><button type="button" name="delete" class="btn btn-danger" @click=deleteQuestion(row.id_pregunta) >Eliminar</button></td> 
            <td><button type="button" name="options" class="btn btn-secondary btn-xs question" @click="showOpciones(row.id_pregunta)" v-if="row.opcion_multiple" >Opciones</button></td> 
          </tr>
        </table>
      </div>
      </div>  
 
      <div v-if="myModelPoll" >  
            <transition name="model" >
              <div class="modal-mask" >
                  <!-- <div class="modal-wrapper"> -->
                      <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title">{{ dynamicTitle }}</h4>
                            <button type="button" class="close" @click="myModelPoll=false"><span aria-hidden="true">&times;</span></button>
                          </div>  
                          <div class="modal-body"> 
                            <div class="card-body">  
                                <div class="form-group">
                                  <label>Pregunta</label>
                                  <textarea  type="text" class="form-control" v-model="questionSelected.nombre_pregunta"></textarea>
                                </div> 
                                <div class="form-group">
                                  <label>numero pregunta</label>
                                  <input type="number" class="form-control" v-model="questionSelected.numero_pregunta" />
                                </div>  

                                <div class="custom-control custom-checkbox">
                                  <input type="checkbox" class="custom-control-input" id="checked" v-model="questionSelected.activo"  false-value="false" true-value="true" >
                                  <label class="custom-control-label" for="checked">Activo</label>
                                </div> 
                                <br /> 

                                <div class="form-group">  
                                    <label>Tipo</label> 
                                    <select v-model="questionSelected.id_tipo" class="form-control" @change="req_response_valid()" >
                                        <option v-for="r in tipos" v-bind:value="r.id_tipo"> {{ r.descripcion }} ({{ r.tipo }}) </option> 
                                    </select>  
                                </div> 
                                
                                <div class="custom-control custom-checkbox">
                                  <input type="checkbox" class="custom-control-input" id="obligatoria" v-model="questionSelected.obligatoria"  false-value="false" true-value="true" >
                                  <label class="custom-control-label" for="obligatoria">¿Es obligatoría?</label>
                                </div> 

                                <div class="custom-control custom-checkbox">
                                  <input type="checkbox"  @change="req_response_valid()"  class="custom-control-input" id="is_evaluated" v-model="questionSelected.is_evaluated"  false-value="false" true-value="true" >
                                  <label class="custom-control-label" for="is_evaluated">¿Será Evaluada?</label>
                                </div>
                                
                                <br />   
                                <div class="form-group alert alert-success" v-if="resp_direct_quest_visible" >
                                
                                  <div v-if="resp_direct_quest_type == 'text' || resp_direct_quest_type == 'number' || resp_direct_quest_type == 'date'" >
                                    <label>Respuesta Correcta</label> 
                                    <input :type="resp_direct_quest_type" class="form-control" v-model="questionSelected.resp_direct_quest_value" /> 
                                  </div> 

                                  <div v-if="resp_direct_quest_type == 'select' || resp_direct_quest_type == 'radio'" >
                                    <label>Respuesta Correcta</label>  
                                    <select   style="margin-left:15px;" class="form-control" v-model="questionSelected.resp_direct_quest_value" >
                                        <option v-for="r in options_resp_valid" v-bind:value="r.opcion">{{ r.opcion }}</option> 
                                    </select> 
                                  </div>

                                  <div v-if="resp_direct_quest_type == 'checkbox'" >
                                    <label>Ingresa las Respuestas Correctas en el apartado de opciones</label> 
                                  </div>
                                </div>   
                    
                                <br />  
                                <br/>    
                                <div align="center">
                                  <input type="hidden" v-model="hiddenId" />
                                  <input type="button" class="btn btn-success btn-xs" v-model="actionButton" @click="save" />
                                </div>

                                </br> 
                            </div>
                          </div>
                      </div>
                    <!-- </div> -->
                </div>
              </div>
            </transition>
          </div> 

          <div v-if="myModelPoll2" >  
            <transition name="model" style="width:40%">
              <div class="modal-mask" > 
                      <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                          <div class="modal-header">
                            <p class="modal-title">{{ dynamicTitle }}</p>
                            <button type="button" class="close" @click="myModelPoll2=false"><span aria-hidden="true">&times;</span></button>
                          </div>  


                          <div class="modal-body"> 
                            <div class="card-body"> 
                               <div v-if="formOption == false" >
                                <ul id="Opciones" >
                                  <li v-for="r in options">
                                  ID: {{ r.id_opcion }} - Opción: <strong> {{ r.opcion }} </strong>
                                      <ul>  
                                        <table border=1  BORDERCOLOR=#D6DBDF >
                                          <tr>
                                            <button style="color:#5DADE2;background:none;border:none;" @click="updateOption(r)" >Modificar</button> 
                                            <button style="color:#D98880;background:none;border:none;"  @click="deleteOption(r)" >Eliminar</button> 
                                            <td>Activo</td>
                                            <td style="background: #D6DBDF">
                                              <div v-if="r.op_activo" ><strong>Si</strong></div> 
                                              <div v-else ><strong>No</strong></div> 
                                            </td>   
                                          </tr>
                                          <tr>
                                            <td>¿Permitir Respuesta del Usuario?</td>
                                            <td style="background: #D6DBDF" > 
                                              <div v-if="r.respuesta_extra" ><strong>Si</strong></div>  
                                              <div v-else ><strong>No</strong></div> 
                                            </td>  
                                          </tr>

                                          <tr>
                                            <td>Respuesta Correcta</td>
                                            <td style="background: #D6DBDF" > 
                                              <div v-if="r.is_correct_answer" ><strong>Si</strong></div>  
                                              <div v-else ><strong>No</strong></div> 
                                            </td>  
                                          </tr>

                                          <tr>
                                            <td>Posición: </td>
                                            <td style="background: #D6DBDF" > 
                                              {{r.pocision}} 
                                            </td>  
                                          </tr>
                                        </table>   
                                      </ul>    
                                    </li>
                                  </ul>
                                  <input type="button" class="btn btn-info btn-xs" value="+" @click="newOption()" />
                               </div>
                               
                              <div v-else > 

                                  <div class="col-xs-12 col-sm-6 col-md-12"> 
                                  </br>                                   
                                      <textarea class="form-control" type="text" v-model=option.opcion  ></textarea> 
                                  </div>
                                  <div class="col-xs-12 col-sm-6 col-md-12">  
                                  </br>
                                      <input type="number" class="form-control" v-model="option.pocision" /> </div>
                                  </br>  

                                  <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="option_activo" v-model="option.op_activo"  false-value="false" true-value="true" >
                                    <label   class="custom-control-label" for="option_activo" >Activo</label>
                                  </div>  

                                  <div class="custom-control custom-checkbox" v-if="option.is_evaluated == false && (questionSelected.id_tipo == 5 || questionSelected.id_tipo == 4)">
                                    <input type="checkbox" class="custom-control-input" id="res_extra" v-model="option.respuesta_extra"  false-value="false" true-value="true"  >
                                    <label   class="custom-control-label" for="res_extra" >Permitir Valor de Usuario</label>
                                    <!-- {{ option.respuesta_extra }} -->
                                  </div>  
                                  <div v-else style="display:none" >
                                    {{ option.respuesta_extra = false }}
                                  </div>  
                                  
                                  <div class="custom-control custom-checkbox" v-if="option.is_evaluated && option.opcion_multiple && option.direct_data == false && questionSelected.id_tipo != 4">
                                    <input type="checkbox" class="custom-control-input" id="is_correct_answer" v-model="option.is_correct_answer" false-value="false" true-value="true"  >
                                    <label class="custom-control-label" for="is_correct_answer" >¿Es la respuesta Correcta?</label>
                                    <!-- {{ option.is_correct_answer }} -->
                                  </div>   

                                  <div>   
                                </div>  
                                <br><br> 
                                <input type="button" class="btn btn-success btn-xs" value="Guardar" @click="guardarOption()" />
                                <input type="button" class="btn btn-danger" value="Cancelar" @click="cancelarOption()" />

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
<script type="text/javascript" src="../../controller/admin/question_encuesta.js"></script> 
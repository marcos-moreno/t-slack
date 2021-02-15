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
                                    {{questionSelected.id_tipo}}
                                    <select v-model="questionSelected.id_tipo" class="form-control">
                                        <option v-for="r in tipos" v-bind:value="r.id_tipo"> {{ r.descripcion }} ({{ r.tipo }}) </option> 
                                    </select>  
                                </div> 
                                
                                <div class="custom-control custom-checkbox">
                                  <input type="checkbox" class="custom-control-input" id="obligatoria" v-model="questionSelected.obligatoria"  false-value="false" true-value="true" >
                                  <label class="custom-control-label" for="obligatoria">¿Es obligatoría?</label>
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

                                <div v-for="r in options" >   
                                    <div v-if="r.action == 'update'||r.action == 'insert'" class="row" style="border-style: dashed; border-width: 1px;">
                                    </br>
                                        <div class="col-xs-12 col-sm-6 col-md-12"> 
                                        </br>                                   
                                            <textarea class="form-control" type="text" v-model=r.opcion  ></textarea> 
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-12">  
                                        </br>
                                            <input type="number" class="form-control" v-model="r.pocision" /> </div>
                                        </br>
                                        <div class="col-xs-12 col-sm-6 col-md-12">  
                                            </br> 
                                            <center>
                                                <button style="background:none;" class="form"  @click="r.action = 'delete'" > <img src="../../img/borrar.png" /> </button>
                                            </center> 
                                        </div> 
                                        <div class="custom-control custom-checkbox">
                                          <input type="checkbox"  v-if=" r.id_opcion != 0" class="custom-control-input" :id="'op_activo' + r.id_opcion" v-model="r.op_activo"  false-value="false" true-value="true" >
                                          <label  v-if=" r.id_opcion != 0" class="custom-control-label" :for="'op_activo' + r.id_opcion" >Activo</label>
                                        </div>  
                                        <div class="custom-control custom-checkbox">
                                          <input v-if=" r.id_opcion != 0" type="checkbox" class="custom-control-input" :id="'op_respuesta_extra' + r.id_opcion" v-model="r.respuesta_extra"  >
                                          <label  v-if=" r.id_opcion != 0" class="custom-control-label" :for="'op_respuesta_extra' + r.id_opcion" >Permitir Valor de Usuario</label>
                                        </div> 
                                    </div> 
                                    </br>
                                </div> 
                                <div> 
                                  <input type="button" class="btn btn-info btn-xs" value="+" @click="newOption()" />
                                </div>  
                                <div> 
                                  <input type="button" class="btn btn-success btn-xs" value="Completar" @click="completeOption()" :disabled=btePressed />
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
<script type="text/javascript" src="../../controller/admin/question_pol.js"></script> 
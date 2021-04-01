<?php require '../header.php'; ?>  
 
<!-- <div class="container" id="crudPoll">  -->
<div class="container" style="width:100%" id="crudPoll">
    <h3>Encuesta</h3><br/> 
    <div class="alert alert-danger" role="alert"   v-if="msgError" > {{alert}}
    </div>
    <div class="alert alert-success" role="alert"  v-if="msg" > {{alert}}
    </div> 
    <div >
      <div >
        <div class="row">
          <div class="col-md-6">
          <h3 class="panel-title">Datos</h3>
          </div>
          <div class="col-md-6" align="right">
           <input type="button" class="btn btn-success btn-xs" @click="openModel" value="Agregar" />
          </div>
        </div>
      </div> 
      <div class="panel-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Activo</th>
            <th>Valido desde</th>
            <th>Valido hasta</th>
            <th></th>
            <th>Empresas</th>
            <th>Lecciones</th>
            <th>Preguntas</th>
            <th></th>
            <th></th>
            <th></th> 
          </tr>
          <tr v-for="row in allData_Poll">
            <td>{{ row.id_encuesta }}</td>
            <td>{{ row.nombre }}</td>
            <td>
              <div v-if="row.activo">Si</div>
              <div v-else >No</div>
            </td>
            <td>{{ row.validodesde }}</td>
            <td>{{ row.validohasta }}</td> 
            <td><a type="button" name="company" class="btn-xs delete" @click="openModelIntent(row)">Nuevo Intento</a></td>
            <td><a type="button" name="company" class="btn-xs delete" @click="asingCompany(row)">Asignar Empresa</a></td>
            <td><a type="button" v-bind:href="'v_enc_leccion.php?id_encuesta=' + row.id_encuesta " name="company" class="btn-xs delete" h>Lecciones</a></td>
            <td><center><img src="../../img/cuestionario.svg"   @click="question(row.id_encuesta)"  width="30px" height="30px" /></center></td>
            <td><img src="../../img/lapiz.svg" @click="fetchData(row.id_encuesta)" width="30px" height="30px" /></td>
            <td><img src="../../img/basura.svg" @click="deleteData(row.id_encuesta)" width="30px" height="30px" /> </td>
            <td><img src="../../img/copiar.svg" @click="showCopyPool(row.id_encuesta,row.nombre)" width="30px" height="30px" /></td>
          </tr>
        </table>
      </div>
      </div>  
 
      <div v-if="modalCopy" >  
            <transition name="model" >
              <div class="modal-mask" > 
                      <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title">{{ dynamicTitle }}</h4>
                            <button type="button" class="close" @click="modalCopy=false"><span aria-hidden="true">&times;</span></button>
                          </div>  
                          <div class="modal-body"> 
                            <div class="card-body">   
                                <div class="custom-control custom-checkbox">
                                  <h5 >Datos para la Nueva Encuesta.</h5> 

                                  <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" v-model="copy_name" />
                                  </div> 

                                  <div class="md-form md-outline input-with-post-icon datepicker">
                                    <label for="example">Válido Desde</label>
                                    <input type="datetime-local" id="copy_validfrom" class="form-control" v-model="copy_validfrom"  />
                                  </div> 

                                  <div class="md-form md-outline input-with-post-icon datepicker">
                                    <label for="example">Válido Hasta</label>
                                    <input type="datetime-local" id="copy_validUntil" class="form-control" v-model="copy_validUntil" />
                                  </div> 
                                  <br/><br/>
                                  
                                  <div align="center"> 
                                    <input type="button" class="btn btn-success btn-xs" value="Guardar" :disabled='isDisabledBTEcopy' @click="copyPool()" />
                                  </div>
                                 
                                </div>    
                                </br> 
                            </div>
                          </div>
                      </div> 
                </div>
              </div>
            </transition>
          </div>

<!-- INTENTO -->
          <div v-if="modalIntento" >  
            <transition name="model" >
              <div class="modal-mask" > 
                      <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title">Intento Para Responder la Encuesta: {{pollSelected.nombre}}</h4>
                            <button type="button" class="close" @click="modalIntento=false"><span aria-hidden="true">&times;</span></button>
                          </div>
 
                          <div class="modal-body"> 
                            <ul class="list-group">
                              <li v-for="item in Intentos" class="list-group-item d-flex justify-content-between align-items-center">
                              No. Intento: {{ item.no_intento }}  {{ item.descripcion }}  <br> 
                              Desde: {{ item.inicio }} <br> Hasta: {{ item.fin }}     
                              <img src="../../img/basura.svg" @click="deleteIntent(item.id_enc_intentos_encuesta)" width="30px" height="30px" />
                              </li>
                            </ul>
                            <div class="card-body">   
                                <div class="custom-control custom-checkbox">
                                  <h5 >Datos para El Nuevo Intento.</h5> 

                                  <div class="form-group">
                                    <label>Descripcion</label>
                                    <input type="text" class="form-control" v-model="in_descripcion" />
                                  </div> 

                                  <div class="md-form md-outline input-with-post-icon datepicker">
                                    <label for="example">Válido Desde</label>
                                    <input type="datetime-local" class="form-control" v-model="in_inicio"  />
                                  </div> 

                                  <div class="md-form md-outline input-with-post-icon datepicker">
                                    <label for="example">Válido Hasta</label>
                                    <input type="datetime-local"  class="form-control" v-model="in_fin" />
                                  </div> 
                                  <br/><br/>
                                  
                                  <div align="center"> 
                                    <input type="button" class="btn btn-info btn-xs" value="Guardar"  @click="saveIntent()" />
                                  </div> 

                                </div>    
                                </br> 
                            </div>
                          </div>
                      </div> 
                </div>
              </div>
            </transition>
          </div>
<!-- INTENTO -->


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
                                  <label>Nombre</label>
                                  <input type="text" class="form-control" v-model="poll_name" />
                                </div> 
                                <div class="form-group">
                                  <label>Observaciones</label>
                                  <input type="text" class="form-control" v-model="poll_help" />
                                </div> 
                                <div class="md-form md-outline input-with-post-icon datepicker">
                                  <label for="example">Válido Desde</label>
                                  <input type="datetime-local" id="example" class="form-control" v-model="poll_validfrom" placeholder="01-01-2020" />
                                </div> 

                                <div class="md-form md-outline input-with-post-icon datepicker">
                                  <label for="example">Válido Hasta</label>
                                  <input type="datetime-local" id="example" class="form-control" v-model="poll_validUntil" placeholder="01-01-2020" />
                                </div> 

                                <div class="custom-control custom-checkbox">
                                  <input type="checkbox" class="custom-control-input" id="checked" v-model="checked"  false-value="false" true-value="true" >
                                  <label class="custom-control-label" for="checked">Activo</label>
                                </div> 
                                <br /> 
                                
                                <div align="center">
                                  <input type="hidden" v-model="hiddenId" />
                                  <input type="button" class="btn btn-success btn-xs" v-model="actionButton" @click="submitData" />
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
            <transition name="model" >
              <div class="modal-mask" > 
                      <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title">{{ dynamicTitle }}</h4>
                            <button type="button" class="close" @click="myModelPoll2=false"><span aria-hidden="true">&times;</span></button>
                          </div>  
                          <div class="modal-body"> 
                            <div class="card-body">   
                                <div class="custom-control custom-checkbox">
                                  <h5 >La encuesta estará disponible para las siguientes Empresas.</h5>
                                  <div v-for="r in companys" >   
                                        <input style="margin-left:5px;" type='checkbox' v-model=r.selected  :id="'check_' + r.id_empresa"  > <span>{{ r.empresa_nombre }}</span>  
                                  </div> 
                                </div>   
                                <div align="center">
                                  <input type="hidden" v-model="hiddenId" />
                                  <input type="button" class="btn btn-success btn-xs" value="Guardar"    :disabled='isDisabledSC'
                                  @click="saveCompanys()" />
                                </div>
                                </br> 
                            </div>
                          </div>
                      </div> 
                </div>
              </div>
            </transition>
          </div>

    


          
    </div> 
  </div> 



<script type="text/javascript" src="../../controller/admin/polls.js"></script>  
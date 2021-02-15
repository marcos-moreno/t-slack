<?php

require '../header.php';

?>
           <!-- TABLA INICIO -->
  <div class="container" id="crudEmp">
             
  
   <br />
   <h3 align="center">Empleado</h3>
   
   <label>Filtrar</label> 
  
   <div class="panel panel-default">
    <div class="panel-heading">

    <table>
      <tr>
        <td>
         <input type="text" class="form-control" v-model="filterValue" />
        </td> 
        <td>
          <button type="button" name="filter" class="btn btn-info btn-xs" @click="filtrar"> filtrar</button>
        </td> 
      </tr>
    </table>
    
    
    </br></br>
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
        <th>ID Cerberus</th>
        <th>Empresa</th>
        <th>Segmento</th> 
        <th>Empleado</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
       </tr>
       <tr v-for="row in allData_Emp">
       <td>{{ row.id_empleado }}</td>
       <td>{{ row.id_cerberus_empleado }}</td>
       <td>{{ row.empresa_nombre }}</td>
       <td>{{ row.segmento }}</td>
        <td>{{row.paterno +' '+ row.materno + ' ' + row.nombre  }}</td>
        <td><button type="button" name="company" class="btn btn-info btn-xs edit" @click="asingRols(row)">Rol</button></td>
        <td><button type="button" name="edit" class="btn btn-secondary btn-xs edit" @click="fetchData(row.id_empleado)">Editar</button></td>
        <td><button type="button" name="delete" class="btn btn-danger btn-xs delete" @click="deleteData(row.id_empleado)">Eliminiar</button></td>
        <td><button @click="resetPassword(row.id_empleado)" style="border:none;background:none;color:blue" :disabled=disbledResetPass  >Restablecer Contraseña</button></td>
       </tr>
      </table>
     </div>
    </div>





    
   </div>


   <div v-if="myModel">
    <transition name="model">
     <div class="modal-mask">
       <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
         <div class="modal-header">
          <h4 class="modal-title">{{ dynamicTitle }}</h4>
          <button type="button" class="close" @click="myModel=false"><span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body">

         <div class="card-body">

<!--          <div class="form-group">
            <label>Empresa</label>
            <select class='form-control' v-model="company">
            <option value='0' >Selecciona Empresa</option>
            <option v-for="rows in allDataComboCompany" v-bind:value='rows.id_empresa'>{{ rows.empresa_nombre }}</option>
            </select>
        </div>  -->

        <div class="form-group">
            <label>Segmento</label>
            <select class='form-control'  v-model="organization">
            <option value='0' >Selecciona Segmento</option>
            <option v-for="rows in allDataCombo" v-bind:value='rows.id_segmento'>{{ rows.nombre }} ({{ rows.empresa_nombre }})</option>
            </select>
        </div>

          <div class="form-group">
           <label>Nombre</label>
           <input type="text" class="form-control" v-model="first_name" />
          </div>
          <div class="form-group">
           <label>Apellido Paterno</label>
           <input type="text" class="form-control" v-model="paternal_name" />
          </div>
          <div class="form-group">
           <label>Apellido Materno</label>
           <input type="text" class="form-control" v-model="maternal_name" />
          </div>
          <div class="form-group">
           <label>Telef&oacute;no Celular</label>
           <input type="tel" class="form-control" v-model="cellphone" />
          </div> 
          <div class="form-group">
           <label>Correo Electronico</label>
           <input type="email" class="form-control" v-model="emp_email" />
          </div>

          <div class="form-group">
           <label>Fecha Nacimiento</label>
           <input type="date" class="form-control" v-model="age" min="18"/>
          </div>

          <div class="form-group">
           <label>ID Cerberus</label>
           <input type="number" class="form-control" v-model="id_cerberus_empleado" min="18"/>
          </div>

          <div class="form-group">
           <label>RFC</label>
           <input type="text" class="form-control" v-model="rfc" min="18"/>
          </div>

          <div class="form-group">
           <label>NSS</label>
           <input type="text" class="form-control" v-model="nss" min="18"/>
          </div>

          <div class="form-group">
           <label>G&eacute;nero</label><br>
            <input type="radio" id="male" name="gender" value="H" v-model="picked">  
           <label for="male">Hombre</label><br>
            <input type="radio" id="female" name="gender" value="M" v-model="picked"> 
            <label for="female">Mujer</label><br>
           <input type="radio" id="other" name="gender" value="O" v-model="picked">
            <label for="other">Otro</label><br>
          </div> 

          <div class="form-group">
           <label>Usuario</label>
           <input type="usu" class="form-control" v-model="user" v-bind:disabled="actionButton == 'Agregar' ? true : false" />
          </div> 

          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="checked" v-model="checked"  false-value="false" true-value="true" >
            <label class="custom-control-label" for="checked">Activo</label>
        </div>

        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="checked_poll" v-model="checked_poll"  false-value="false" true-value="true" >
            <label class="custom-control-label" for="checked_poll">Enviar Encuesta</label>
        </div>

          <br />
          <div align="center">
           <input type="hidden" v-model="hiddenId" />
           <input type="button" class="btn btn-success btn-xs" v-model="actionButton" @click="submitData" />
          </div>
         </div>
        </div>
       </div>
      </div>
      </div>
    </transition>
   </div>

   <div v-if="myModelRol" >  
            <transition name="model" >
              <div class="modal-mask" > 
                      <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title">{{ dynamicTitle }}</h4>
                            <button type="button" class="close" @click="myModelRol=false"><span aria-hidden="true">&times;</span></button>
                          </div>  
                          <div class="modal-body"> 
                            <div class="card-body">   
                                <div class="custom-control custom-checkbox">
                                  <h5 >El Usuario cuenta con las siguientes roles:</h5>
                                  <div v-for="r in rols" >   
                                        <input style="margin-left:5px;" type='checkbox' v-model=r.selected  :id="'check_' + r.id_rol"  > <span>{{ r.rol }}</span>  
                                  </div> 
                                </div>   
                                <div align="center">
                                  <input type="hidden" v-model="hiddenId" />
                                  <input type="button" class="btn btn-success btn-xs" value="Guardar" :disabled='isDisabledSC' @click="saveRols()" />
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
    <!-- End of Content Wrapper -->

<script type="text/javascript" src="../../controller/admin/employe.js"></script>
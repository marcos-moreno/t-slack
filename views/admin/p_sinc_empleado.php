<?php require '../header.php'; ?>  
 
<div class="container-fluid" style="width:90%;" id="sinconizador">
<button type="button"  class="btn btn-info btn-xs" @click="searchNewEmployees">Buscar Empleados Nuevos</button>

  <br/> 
  <div >
      <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg}}</div>
      <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg}}</div>
      <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg}}</div>
  </div> 
  <!-- <button type="button"  class="btn btn-info btn-xs" @click="sync_IDs"> Sincro IDs</button> -->
  <!-- <button type="button"  class="btn btn-info btn-xs" @click="fn_empleados_parametro">Parametro</button> -->
  <br>
    <div v-if="is_newEmployees">
     
      <div class="table-responsive"  v-if="display_duplicate == false">
        <nav class="navbar navbar-light bg-light justify-content-between">
          <label>Bucar por ID en Cerberus</label> 
          <form class="form-inline">
            <input type="text" class="form-control" v-model="id_cerberus" />  
            <input type="button" class="btn btn-info btn-xs" @click="buscarCerberus()" value="Buscar" /> 
          </form>
          <button type="button"  class="btn btn-success btn-xs" @click="completeSinc">Sincronizar</button>
          <button type="button"  class="btn btn-danger btn-xs" @click="cancelSinc">Cancelar</button>
          <p>Da doble Click para quitar de la lista.</p> 
        </nav> 

        <table class="table table-bordered table-striped"    > 
          <tr> 
            <th>ID Cerberus</th>
            <th>ID Contpaqi</th>
            <th>Perfil C.</th>
            <th>Nombre</th>
            <th>fecha Alta</th>  
            <th>rfc</th> 
            <th>segmento</th> 
            <!-- <th>usuario</th>  -->
            <th>nss</th> 
            <th>empresa_nombre</th> 
            <th>fecha_nacimiento</th> 
          </tr>
          <tr v-for="row in employesCerberus" v-on:dblclick="DelNewEmployee(row)" style="cursor: pointer" > 
            <td>{{ row.idEmpleadoCerberus }}</td> 
            <td>{{ row.idConpaq }}</td> 
            <td>{{ row.perfilCalculo }}</td>
            <!-- <td> 
              <div v-if="row.esActivo" >Si</div> 
              <div v-else >No</div> 
            </td>  -->
            <td>{{ row.nombreEmpleado }} {{ row.apPatEmpleado }} {{ row.apMatEmpleado }}</td>
            <td>{{ fn_format(row.fechaAlta) }}</td> 
            <td>{{ row.rfc }}</td> 
            <td>{{ row.nombreSucursal }}</td> 
            <!-- <td>{{ row.usuario }}</td>  -->
            <td>{{ row.nss }}</td> 
            <td>{{ row.nombreEmpresa }}</td> 
            <td>{{ fn_format(row.fechaNacimiento) }}</td> 
          </tr>
        </table>    
      </div>  

      <div  v-if="display_duplicate == true">
        <h1>Usuarios Duplicados</h1> 
        <table class="table table-bordered table-striped"> 
          <tr> 
            <th>ID Cerberus</th> 
            <th>Nombre</th>   
            <th>usuario</th>    
          </tr>
          <tr v-for="row in array_duplicate" > 
            <td>{{ row.id_cerberus_empleado }}</td> 
            <td>{{ row.nombre }} {{ row.paterno }} {{ row.materno }}</td> 
            <td>{{ row.usuario }}</td> 
          </tr>
        </table> 
      </div>  
      <br/>
      <br/> 
    </div> 

  <div class="row" v-if="is_newEmployees==false" >
        <div class="col"> 
          <div>
            <div class="row"> 
              <div class="col">  
                <input type="text" class="form-control" v-model="filter_value" placeholder="Buscar Empleado" /> 
              </div>
              <div class="col"> 
                <input type="button" class="btn btn-info btn-xs" @click="filter()" value="Buscar" />  
              </div>
            </div>
          </div>
          <br/>
          <div class="table-responsive" style="height:600px;" >
            <table class="table table-bordered table-striped" >
              <tr>
                <th>ID</th>
                <th>ID Cerberus</th>
                <th>activo</th>
                <th>Nombre</th>
                <th>rfc</th> 
                <th>segmento</th> 
                <th>usuario</th> 
                <th>nss</th> 
                <th>empresa_nombre</th> 
                <th>fecha_nacimiento</th>  
              </tr>
              <tr v-for="row in data_to_filter" v-on:dblclick="showDataCerberus(row)" style="cursor: pointer" >
                <td>{{ row.id_empleado }}</td> 
                <td>{{ row.id_cerberus_empleado }}</td> 
                <td> 
                  <div v-if="row.activo" >Si</div> 
                  <div v-else >No</div> 
                </td> 
                <td>{{ row.nombre }} {{ row.paterno }} {{ row.materno }}</td>
                <td>{{ row.rfc }}</td> 
                <td>{{ row.segmento }}</td> 
                <td>{{ row.usuario }}</td> 
                <td>{{ row.nss }}</td> 
                <td>{{ row.empresa_nombre }}</td> 
                <td>{{ fn_format(row.fecha_nacimiento) }}</td>   
              </tr>
            </table>
          </div>  
        </div>

        <div class="col" v-if="is_newEmployees==false">
          <h1>Datos En Cerberus</h1>
          <div class="table-responsive" style=" max-height: 400px; overflow-y: auto;" >
          <font  color="green" >nombreEmpleado: </font >  {{ employeCerberus.nombreEmpleado }} 
            {{ employeCerberus.apPatEmpleado }} 
            {{ employeCerberus.apMatEmpleado }} <br/>
            <font  color="green" > esActivo: </font > {{ employeCerberus.esActivo }} <br/>
            <font  color="green" > nss:  </font >{{ employeCerberus.nss }} <br/>
            <font  color="green" > curp: </font > {{ employeCerberus.curp }} <br/>
            <font  color="green" > rfc:  </font >{{ employeCerberus.rfc }} <br/>
            <font  color="green" > nombreDepartamento: </font > {{ employeCerberus.nombreDepartamento }} <br/>
            <font  color="green" >nombreHorario: </font > {{ employeCerberus.nombreHorario }} <br/>
            <font  color="green" > idSucursal: </font > {{ employeCerberus.idSucursal }} <br/>
            <font  color="green" >fechaAlta: </font > {{ fn_format(employeCerberus.fechaAlta) }} <br/>
            <font  color="green" > fechaBaja:  </font >{{employeCerberus.fechaBaja}} {{ fn_format(employeCerberus.fechaBaja) }} <br/>
            <font  color="green" > puesto: </font > {{ employeCerberus.puesto }} <br/>
            <font  color="green" >fechaNacimiento:  </font >{{ fn_format(employeCerberus.fechaNacimiento) }} <br/>
            <font  color="green" > genero:  </font >{{ employeCerberus.genero }} <br/> 
          </div>
        </div> 
      </div>
</div> 
<script type="text/javascript" src="../../controller/admin/sinc3_cerberus.js"></script>  
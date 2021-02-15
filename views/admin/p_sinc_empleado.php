<?php require '../header.php'; ?>  
 
<div class="container" id="sinconizador">
<br/> 
<div style="height: 80px;" >
    <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg}}</div>
    <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg}}</div>
    <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg}}</div>
</div> 
  <!-- <button type="button"  class="btn btn-info btn-xs" @click="sync_IDs"> Sincro IDs</button> -->
  <!-- <button type="button"  class="btn btn-info btn-xs" @click="fn_empleados_parametro">Parametro</button> -->
  <button type="button"  class="btn btn-info btn-xs" @click="searchNewEmployees">Buscar Empleado Nuevos</button>
  <br/> 
  <div v-if="is_newEmployees">
          <br/><br/>
            <table>
              <tr>
                  <td> 
                    <label>Bucar por ID en Cerberus</label>
                  </td>
                  <td> 
                    <input type="text" class="form-control" v-model="id_cerberus" /> 
                  </td>
                  <td> 
                    <input type="button" class="btn btn-info btn-xs" @click="buscarCerberus()" value="Buscar" /> 
                  </td>
              </tr>
            </table>
          <p>Da doble Click para quitar de la lista.</p> 
          <div class="table-responsive" style=" max-height: 500px; max-width: 1000px; overflow-y: auto;"  v-if="is_newEmployees">
            <table class="table table-bordered table-striped"  >
              <tr> 
                <th>ID Cerberus</th>
                <th>activo</th>
                <th>Nombre</th>
                <th>fecha Alta</th>  
                <th>rfc</th> 
                <th>segmento</th> 
                <th>usuario</th> 
                <th>nss</th> 
                <th>empresa_nombre</th> 
                <th>fecha_nacimiento</th> 
              </tr>
              <tr v-for="row in employesCerberus" v-on:dblclick="DelNewEmployee(row)" style="cursor: pointer" > 
                <td>{{ row.idEmpleadoCerberus }}</td> 
                <td> 
                  <div v-if="row.esActivo" >Si</div> 
                  <div v-else >No</div> 
                </td> 
                <td>{{ row.nombreEmpleado }} {{ row.apPatEmpleado }} {{ row.apMatEmpleado }}</td>+
                <td>{{ fn_format(row.fechaAlta) }}</td> 
                <td>{{ row.rfc }}</td> 
                <td>{{ row.nombreSucursal }}</td> 
                <td>{{ row.usuario }}</td> 
                <td>{{ row.nss }}</td> 
                <td>{{ row.nombreEmpresa }}</td> 
                <td>{{ fn_format(row.fechaNacimiento) }}</td> 
              </tr>
            </table>
          </div>
          <button type="button"  class="btn btn-success btn-xs" @click="completeSinc">Sincronizar</button>
          <button type="button"  class="btn btn-danger btn-xs" @click="cancelSinc">Cancelar</button>
          <br/>
          <br/> 
    </div> 
  <div class="row"  style="height: 450px;" v-if="is_newEmployees==false" >
        <div class="col">
            <table>
              <tr>
                  <td> 
                    <label>Filtro</label>
                  </td>
                  <td> 
                    <input type="text" class="form-control" v-model="filter_value" /> 
                  </td>
                  <td> 
                    <input type="button" class="btn btn-info btn-xs" @click="filter()" value="Buscar" /> 
                  </td>
              </tr>
            </table>
          <div class="table-responsive" style=" max-height: 400px; max-width: 600px; overflow-y: auto;" >
            <table class="table table-bordered table-striped"  >
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
<script type="text/javascript" src="../../controller/admin/sincro_cerberus.js"></script>  
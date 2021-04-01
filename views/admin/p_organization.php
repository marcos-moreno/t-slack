<?php

require '../header.php';

?>
 
 
<div class="container-fluid">

<div class="container" id="crudOrg">
   <br />
   <h3 align="center">Organizaci&oacute;n</h3>
   <br />
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
        <th>Empresa</th>
        <th>Segmento</th>
        <th>Editar</th>
        <th>Eliminar</th>
       </tr>
       <tr v-for="row in allData_Org">
       <td>{{ row.id_segmento }}</td>
       <td>{{ row.empresa_nombre }}</td>
        <td>{{ row.nombre }}</td>
        <td><button type="button" name="edit" class="btn btn-primary btn-xs edit" @click="fetchData(row.id_segmento)">Editar</button></td>
        <td><button type="button" name="delete" class="btn btn-danger btn-xs delete" @click="deleteData(row.id_segmento)">Eliminar</button></td>
       </tr>
      </table>
     </div>
    </div>
   </div>

    <div v-if="myModel">
      <transition name="model">
      <div class="modal-mask">
        <div class="modal-dialog">
          <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{ dynamicTitle }}</h4>
            <button type="button" class="close" @click="myModel=false"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
          <div class="card-body">

            <div class="form-group">
                <label>Selecciona Empresa:</label>
                <select class='form-control'  v-model="company">
                  <option value='0' >Selecciona Empresa</option>
                  <option v-for="rows in allDataCombo" v-bind:value='rows.id_empresa'>{{ rows.empresa_nombre }}</option>
                </select>
            </div>
            <div class="form-group">
            <label>Segmento</label>
            <input type="text" class="form-control" v-model="first_name" />
            </div>
            <div class="form-group">
            <label>Observaciones</label>
            <input type="text" class="form-control" v-model="last_name" />
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
          </div>
          </div>
        </div>
        </div>
      </transition>
    </div>
    </div>
      </div> 
    </div> 
  </div> 
</div> 

 
 
</body>
<script type="text/javascript" src="../../controller/admin/organization.js"></script>
 
 
 
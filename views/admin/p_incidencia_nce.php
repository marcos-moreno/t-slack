<?php require '../header.php'; ?>  
 
<div class="container-fluid" style="width:90%;"  id="sinconizador">
<br/> 
<div style="height: 80px;" >
    <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg}}</div>
    <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg}}</div>
    <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg}}</div>
</div> 

  <div  class="container">
    <div class="row">
      <div class="col-lg-3">
        <div class='custom-control custom-checkbox'>
          <input type='checkbox' class='custom-control-input' id='show_sincronizados' 
            @change='searc_sincronizadas' :disabled="inProcess" v-model="show_sincronizado" 
             >
          <label class='custom-control-label' for='show_sincronizados'  >Sincronizado</label>
        </div>
      </div> 
      <div class="col-lg-3">
        <button type="button"  class="btn btn-info btn-xs" @click="updateS" :disabled="inProcess">Buscar Sanciones</button>
      </div> 
      <div class="col-lg-3">
        <button type="button"  class="btn btn-info btn-xs" @click="fn_sendCerberus" v-if="inProcess==false" :disabled="show_sincronizado"><img src="../../img/send.png" width="18px" />Enviar a Cerberus</button>
      </div> 
      <div class="col-lg-3"> 
        <input type='date' class='form-control' v-model='fechaProceso' />
      </div> 
    </div> 
  </div> 
  </br>
  <div>   
      <div class="table-responsive" style=" max-height: 500px; max-width: 2500px; overflow-y: auto;" >
        <table class="table table-bordered table-striped"  >
          <tr>  
            <th>
              <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='sinc_id'  
                  false-value=0 true-value=1 @change='fn_selectAll' 
                  :disabled="show_sincronizado" v-model="selectAll" >
                <label class='custom-control-label' for='sinc_id'  ></label>
              </div>   
              </th>
            <th>ID Cerberus</th>
            <th>id empleado</th>
            <th>Nombre</th> 
            <th>segmento</th> 
            <th>Empresa</th> 
            <th>id Sanción</th> 
            <th>Tipo Sanción</th> 
            <th>Encuesta</th>  
            <th>Resultado_Cerberus</th> 
          </tr>
          <tr v-for="row in incidenciasCollection" style="cursor: pointer" > 
            <td>   
                <div class='custom-control custom-checkbox'>
                  <input type='checkbox' class='custom-control-input' :id='row.id_cerberus_empleado'
                   v-model="row.sincronizar" :disabled="row.sincronizado" >
                  <label class='custom-control-label' :for='row.id_cerberus_empleado'  ></label>
                </div>  
                <!-- <input type="checkbox" class="custom-control" id="checked" v-model="row.sincronizar">    -->
            </td>
            <td>{{ row.id_cerberus_empleado }}</td> 
            <td>{{ row.id_empleado }}</td> 
            <td>{{ row.paterno }} {{ row.materno }} {{ row.nombre }}</td>
            <td>{{ row.segmento }}</td> 
            <td>{{ row.empresa_nombre.substring(0,8) }}...</td> 
            <td>{{ row.id_incidencias_creadas }}</td> 
            <td>{{ row.nivel_sancion }}</td>  
            <td>{{row.ids_encuestas_sancionadas}}</td>  
            <td>{{ row.descripcion_cerberus }}</td>  
          </tr>
        </table>
      </div> 
      <br/>
      <br/> 
    </div> 
</div> 
<script type="text/javascript" src="../../controllers/admin/incidencia_nce.js"></script>  
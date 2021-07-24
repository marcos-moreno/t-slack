<?php require '../header.php';?>
<div class="container-fluid" style="width:90%;"  id="notifications"> 
   <h3 align="center">Notificaciones</h3>
   <div class="panel panel-default" v-if="isCrud==false">
    <div class="panel-heading">
     <div class="row">
      <div class="col-md-6"> 
      </div>
      <div class="col-md-6" align="right">
       <input type="button" class="btn btn-success btn-xs" @click="newNotification()" value="Nueva Notificación" />
      </div>
     </div>
    </div>
    <div class="panel-body">
     <div class="table-responsive">
      <table class="table table-bordered table-striped">
       <tr>
       <th>ID</th>
        <th style="width:350px" >Mensaje</th>
        <th>Descripcion</th>
        <th>Editar</th>
        <th>Eliminar</th>
       </tr>
       <tr v-for="row in allNotications">
       <td>{{ row.id_notification }}</td>
        <td>{{ row.msg }}</td>
        <td>{{ row.description.substring(0,260) }}</td>
        <td><button type="button" name="edit" class="btn btn-primary btn-xs edit" @click="showData(row.id_notification)">Editar</button></td>
        <td><button type="button" name="delete" class="btn btn-danger btn-xs delete" @click="deleteData(row.id_notification)">Eliminar</button></td>
       </tr>
      </table>
     </div>
    </div>
   </div>
   <div v-if="isCrud">  
      <div class="form-group">
        <label>ID: {{notificationSelected.id_notification}}</label> 
      </div> 
      <div class="form-group">
        <label>¿Mostrar al Iniciar Sesión?</label> 
        <input type='checkbox' v-model="notificationSelected.display_start"  false-value='false' true-value='true' /> 
      </div>  
      <div class="form-group">
        <label>Mensaje</label>
        <input type="text" class="form-control" v-model="notificationSelected.msg" />
      </div> 
      <div class="form-group">
        <label>Descripcion.</label>
        <textarea  type="textbox" class="form-control" v-model="notificationSelected.description" rows="4" cols="50"></textarea>
      </div>  
      <div class="row">
        <div class="col">
          <label>Filtro</label>
            <select class='form-control'  v-model="search_by"  @change='getDataFilter()'>
              <option value='all' >Enviar a Todos</option>
              <option value='empresa'>Enviar Por Empresa</option>
              <option value='org' >Enviar Por Organización</option> 
              <option value='emp' >Enviar Por Empleado</option> 
            </select>
        </div> 
        <div class="col">
          <div class="form-group">
          <label>Filtro</label>
          <input type="text" class="form-control" v-model="filter_value" />
          <input type="button" class="btn btn-info btn-xs" @click="filter()" value="Buscar" /> 
          </div> 
        </div> 
      </div> 
      <div class="row"  style="height: 450px;">
        <div class="col">
          <div class="table-responsive" style=" max-height: 400px; overflow-y: auto;">
            <table class="table table-bordered table-striped"  >
              <tr>
                <th>ID</th>
                <th style="width:350px" >Valor</th> 
              </tr>
              <tr v-for="row in data_to_filter" v-on:dblclick="moveToFilter(row)" style="cursor: pointer" >
                <td>{{ row.id }}</td>
                <td>{{ row.value }}</td> 
              </tr>
            </table>
          </div>
        </div>
        <div class="col">
          <div class="table-responsive" style=" max-height: 400px; overflow-y: auto;" >
            <table class="table table-bordered table-striped">
              <tr>
                <th>ID</th>
                <th style="width:350px" >Valor</th> 
              </tr>
              <tr v-for="row in to_notify" v-on:dblclick="deleteToFilter(row)" style="cursor: pointer">
                <td>{{ row.id }}</td>
                <td>{{ row.value }}</td> 
              </tr>
            </table>
          </div>
        </div> 
      </div>
      <!-- <input type="button" class="btn btn-danger btn-xs" @click="isCrud=false" value="Cancelar" /> -->
      <!-- <input type="button" class="btn btn-success btn-xs" @click="save()" value="Enviar" />  -->
      <button @click="isCrud=false" class="btn btn-danger btn-xs" ><img src="../../img/cancelar.png" width="25px" />Cancelar</button> 
      <button @click="save()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="15%" />Enviar</button>
      <div  style="height: 450px;"></div>
   </div>
  </div>
</div>

<script type="text/javascript" src="../../controllers/admin/notifications_a.js"></script>



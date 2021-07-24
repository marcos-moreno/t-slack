<?php require '../header.php'; ?> 
 
<div  class="container" id="reports"> 
  <h2>Reportes</h2>
  <p>Uniformes.</p> 
  <p> 
    <button data-toggle="collapse" class="btn btn-link" href="#encuestaAcord"    aria-expanded="false"   >
      <div align="left" > 
        <img src="../../img/uniforme.svg" width="10%"   />Uniformes
      </div> 
    </button>  
  </p>
  <div class="collapse" id="encuestaAcord">
    <div class="card card-body"> 
     
        <button  data-toggle="collapse" href="#encuestaCompletAcord" class="btn btn-link" id="bteConsRes" aria-expanded="false"  >   Consultar Resultados  </button> 

      <div class="collapse" id="encuestaCompletAcord">
        <div class="card card-body">
            <!-- content --> 
                <div class="container" id="contentForm">  
                  <div class="row">
                      <div class="col-sm-12">

                        <div class="modal-body"> 
                          <div class="card-body">  
                        <!-- @change='getCountryStates()' --> 
                            <label>Empresa:</label>
                              <select class='form-control'  v-model="empresaSelected" @change='getSegments()'>
                                <option value='0' >Selecciona Empresa</option>
                                <option v-for="rows in companys" v-bind:value='rows.id_empresa'>{{ rows.empresa_nombre }}</option>
                              </select> 

                              <label>Segmento:</label>
                              <select class='form-control'  v-model="segmentSelected" @change='getalmacens()' >
                                <option value='0' >Todos los Segmentos</option>
                                <option v-for="rows in segments" v-bind:value='rows.id_segmento'>{{ rows.nombre }}</option>
                              </select>

                               
                              <label>Almácenes:</label>
                              <select class='form-control'  v-model="id_almacen">
                                <option value='0' >Todas los Almácenes</option>
                                <option v-for="rows in almacenCollection" v-bind:value='rows.id_almacen'>{{ rows.nombre_almacen }}</option>
                              </select>

                              <label>Código:</label>
                              <input class='form-control'  v-model="codigo" type="text" > 

                              <label>empleado:</label>
                              <select class='form-control'  v-model="empleadoSelected">
                                <option value='0' >Todos los empleado</option>
                                <option v-for="rows in empleados" v-bind:value='rows.id_empleado'>{{ rows.nom_largo }}</option>
                              </select>
                              
                              <div class='custom-control custom-checkbox'>
                                  <input type='checkbox' class='custom-control-input' id='tomar_stock'   v-model='tomar_stock'  false-value='false' true-value='true' >
                                  <label class='custom-control-label' for='tomar_stock'  >tomar stock</label>
                              </div> 

                            </div>
                          </div> 
                      </div> 
                    </div> 
                  </div><!-- container -->
             <!-- content --> 
           </div>
            <div>
              <center>
                <label>REPORTE:</label>
                <select class='form-control'  v-model="reportSelected">
                  <option value='I' >I.STOCK POR ALMÁCEN</option> 
                  <option value='II' >II.COMPRAS</option> 
                </select>  
                </br>
                <button type="button" class="btn btn-info float-right"  @click='generateReport()' >Consultar</button>       
                <button type="button" class="btn btn-danger float-right"   @click='closeFrameReportView()'>Cerrar Reporte</button>  
              </center> 
            </div>
      </div>
    </div> 
  </div> 
</div>  

  <div id="viewReport"> </div>   
  
<script type="text/javascript" src="../../controllers/admin/report_uniforme.js" ></script>
<?php require '../header.php'; ?> 
 
<div  class="container" id="reports"> 
  <h2>Reportes</h2>
  <p>Encuestas Refividrio.</p> 
  <p> 
    <button   data-toggle="collapse" class="btn btn-link" href="#encuestaAcord"    aria-expanded="false"   >
      <div align="left" > 
        <img src="../../img/encuesta.svg" width="10%"   />Encuestas
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
                              <select class='form-control'  v-model="segmentSelected" @change='getEmployeesBySegment()' >
                                <option value='0' >Todos los Segmentos</option>
                                <option v-for="rows in segments" v-bind:value='rows.id_segmento'>{{ rows.nombre }}</option>
                              </select>

                              <label>Tipo encuestas:</label>
                              <select class='form-control'  v-model="typePoolSelected" @change='getPools()'  >
                                <option value='0' >Todos los tipos de encuestas</option>
                                <option value='1' >Concluidas</option>
                                <option value='2' >En captura</option> 
                              </select>  
                              <label>Encuesta:</label>
                              <select class='form-control'  v-model="pollSelected">
                                <option value='0' >Todas las Encuesta</option>
                                <option v-for="rows in pools" v-bind:value='rows.id_encuesta'>{{ rows.nombre }}</option>
                              </select>

                              <label>empleado:</label>
                              <select class='form-control'  v-model="empleadoSelected">
                                <option value='0' >Todos los empleado</option>
                                <option v-for="rows in empleados" v-bind:value='rows.id_empleado'>{{ rows.paterno }} {{ rows.materno }} {{ rows.nombre }}</option>
                              </select>

                              <div v-if="reportSelected=='M'">
                                <label>Mes:</label>
                                <select class='form-control'  v-model="mesSelected"  > 
                                  <option value="1">Enero</option>
                                  <option value="2">Febrero</option>
                                  <option value="3">Marzo</option>
                                  <option value="4">Abril</option>
                                  <option value="5">Mayo</option>
                                  <option value="6">Junio</option>
                                  <option value="7">Julio</option>
                                  <option value="8">Agosto</option>
                                  <option value="9">Septiembre</option>
                                  <option value="10">Octubre</option>
                                  <option value="11">Noviembre</option>
                                  <option value="12">Diciembre</option>
                                </select>
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
                <select class='form-control'  v-model="reportSelected" >
                  <option value='A' >A.CASOS DE RIESGO MÁXIMO COVID-19</option>
                  <option value='B' >B.CASOS DE RIESGO ALTO COVID-19</option>
                  <option value='C' >C.CASOS DE RIESGO MEDIO COVID-19</option>
                  <option value='D' >D.PERSONAL VULNERABLE POR SEGMENTO (Listado y gráfica)</option>
                  <option value='E' >E.PERSONAL CON FACTOR DE RIESGO POR TRASLADO EN TRANSPORTE PÚBLICO (Listado y gráfica)</option>
                  <option value='F' >F.PERSONAL QUE NO HAN CONTESTADO EL INSTRUMENTO </option>
                  <option value='G' >G.PERSONAL QUE YA HAN CONTESTADO EL INSTRUMENTO </option>
                  <option value='H' >H.RESPUESTAS</option> 
                  <option value='I' >I.LISTADO DE PORCENTAJES</option>
                  <option value='J' >J.LISTADO DE GRÁFICA</option>   
                  <option value='K' >K.MEDIDAS DE PREVENCIÓN A VALIDAR POR ÁREA</option>        
                  <option value='L' >L.REINCIDENCIA EN INCUMPLIMIENTO DE CUESTIONARIO COVID-19</option> 
                  <option value='M'  >M.CUMPLEAÑOS DE COLABORADORES POR MES</option> 
                  <option value='N'  >N.GRÁFICAS POR ENCUESTA</option>                 
                  <option value='Ñ'  >Ñ.ENCUESTAS SANCIONADAS</option>       
                  <option value='O'  >O.GRÁFICA DE PREGUNTAS EVALUADAS</option>                               
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
  
<script type="text/javascript" src="../../controllers/admin/reportes.js" ></script>
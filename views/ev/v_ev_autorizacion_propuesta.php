<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_propuesta" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30%" >
                        <table>
                            <tr>
                            <div class="form-group" >
                                <td v-if='isFormCrud2 ==false'>
                                    <button type="button" class="btn btn btn-xs" @click="cancel_ev_propuesta()"><img src="../../img/regresar.png" width="28px" /> Regresar</button>
                                </td>
                                <td v-if='isFormCrud2 ==false'>
                                    <label>Estado</label> 
                                    <select class='form-control' size='1' @change="getevConsulEstado()" v-model='filterestado' >
                                        <option v-for='rows in estadoCollection2'  v-bind:value='rows.value' :selected="rows.value">({{ rows.value }}) {{ rows.descripcion }}</option>
                                    </select>
                                </td> 
                                
                            </div>
                            
                            </tr>
                        </table>
                    </td>
                    <td >
                        <div class="pre-scrollable" >
                            <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg}}</div>
                            <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg}}</div>
                            <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg}}</div>
                        </div> 
                    </td> 
                </tr>
            </table> 
            </div>   
        <br>
        <div class="panel-body"  v-if="isFormCrud2">
            <h4>Propuestas</h4>
            <div class='form-group'>
                <label>Selecciona Empleado </label> 
                <select class='form-control' size='1' v-model="ev_propuesta.id_empleado" @change="get_empleadoFilter()">
                    <optgroup v-for="(depa, i) in departamentoCollection" :label="depa.nombre" >  
                        <option v-for='rows in empleadoCollection' v-if="rows.departamento_id==depa.departamento_id" v-bind:value='rows.id_empleado' >
                            {{ rows.paterno }} {{ rows.materno }} {{ rows.nombre }}
                        </option>
                    </optgroup>
                </select>
            </div> 
           
        </div>  
            
        <div v-if="isFormCrud2==false" >   
            <br>
            <br>

            <h4>SOLUCI&Oacute;N DE PROBLEMAS</h4>
            <div class="table-responsive">
            <nav aria-label="Page navigation example">
                        <ul class="pagination">  
                            <li>
                                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="numByPag" @change="paginator(1)" > 
                                    <option value=5  >5</option>
                                    <option value=10 >10</option>
                                    <option value=15 >15</option>
                                    <option value=20 >20</option>
                                </select>
                            </li>
                            <li v-for="li in paginas" class="page-item">
                                <a class="page-link" @click="paginator(li.element)" >{{ li.element }} <div v-if="li.element == paginaActual" >_</div></a> 
                            </li>
                        </ul>  
                    </nav>
            <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th>
                                    
                        <th>Fecha de Creaci&oacute;n</th>
                                    
                        <th>Empleado</th>
                                    
                        <th>Descripci&oacute;n</th>
                                    
                        <th>estado</th>
                                    
                        <th>propuesta</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="ev_propuesta in paginaCollection" >
                        
                    <td>{{ ev_propuesta.propuesta_id}}</td>
            
            <td>{{ ev_propuesta.fecha_creado}}</td>

            <td>({{ ev_propuesta.id_empleado}}) {{ ev_propuesta.nombre }} {{ ev_propuesta.paterno }} {{ ev_propuesta.materno }}</td>

            <td>{{ ev_propuesta.texto}}</td>

            <td v-if="ev_propuesta.estado == 'PA'">
                ({{ ev_propuesta.estado}}) Pendiente a Autorizar
            </td>

            <td v-if="ev_propuesta.estado == 'AU'">
                ({{ ev_propuesta.estado}}) Autorizada
            </td>

            <td v-if="ev_propuesta.estado == 'NA'">
                ({{ ev_propuesta.estado}}) No Autorizada
            </td>

            <td>{{ ev_propuesta.propuesta}}</td>
               
                        <td style="width:150px" >
                            <div class='form-check'>
                                <input type='checkbox' class='form-check-input' id='ev_propuesta.propuesta_id'   v-model='ev_propuesta.estado' false-value='NA' true-value='AU' @change="update_ev_propuesta(ev_propuesta.propuesta_id)">
                                <label class='form-check-label' for='ev_propuesta.propuesta_id'  >Autorizar</label>
                            </div> 
                            
                            <!--<button type="button" class="btn btn" @click="delete_ev_propuesta(ev_propuesta.propuesta_id)"><img src="../../img/borrar.png" width="25px" /></button>-->
                        </td>
                        
                    </tr>
                </table>
                </div>
                <br><br>

                <h4>PROACTIVIDAD</h4>
                <div class="table-responsive">
                <nav aria-label="Page navigation example">
                        <ul class="pagination">  
                            <li>
                                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="numByPag" @change="paginator2(1)" > 
                                    <option value=5  >5</option>
                                    <option value=10 >10</option>
                                    <option value=15 >15</option>
                                    <option value=20 >20</option>
                                </select>
                            </li>
                            <li v-for="li in paginas" class="page-item">
                                <a class="page-link" @click="paginator(li.element)" >{{ li.element }} <div v-if="li.element == paginaActual" >_</div></a> 
                            </li>
                        </ul>  
                    </nav>
            <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th>
                                    
                        <th>Fecha de Creaci&oacute;n</th>
                                    
                        <th>Empleado</th>
                                    
                        <th>texto</th>
                                    
                        <th>Descripci&oacute;n</th>
                                    
                        <th>propuesta</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="ev_propuesta in paginaCollection2" >
                        
                        <td>{{ ev_propuesta.propuesta_id}}</td>
            
                        <td>{{ ev_propuesta.fecha_creado}}</td>
            
                        <td>({{ ev_propuesta.id_empleado}}) {{ ev_propuesta.nombre }} {{ ev_propuesta.paterno }} {{ ev_propuesta.materno }}</td>
            
                        <td>{{ ev_propuesta.texto}}</td>
            
                        <td v-if="ev_propuesta.estado == 'PA'">
                            ({{ ev_propuesta.estado}}) Pendiente a Autorizar
                        </td>

                        <td v-if="ev_propuesta.estado == 'AU'">
                            ({{ ev_propuesta.estado}}) Autorizada
                        </td>

                        <td v-if="ev_propuesta.estado == 'NA'">
                            ({{ ev_propuesta.estado}}) No Autorizada
                        </td>
            
                        <td>{{ ev_propuesta.propuesta}}</td>
               
                        <td style="width:150px" >
                            <div class='form-check'>
                                <input type='checkbox' class='form-check-input' id='ev_propuesta.propuesta_id'   v-model='ev_propuesta.estado' false-value='NA' true-value='AU' @change="update_ev_propuesta2(ev_propuesta.propuesta_id)">
                                <label class='form-check-label' for='ev_propuesta.propuesta_id'  >Autorizar</label>
                            </div> 
                           
                            <!--<button type="button" class="btn btn" @click="delete_ev_propuesta(ev_propuesta.propuesta_id)"><img src="../../img/borrar.png" width="25px" /></button>-->
                        </td> 
                    </tr>
                </table>
                </div>
                <br><br>
           
          
            <br>
            <br>
        </div>

    </div>  
<script type="text/javascript" src="../../controllers/ev/c_ev_propuesta.js"></script>

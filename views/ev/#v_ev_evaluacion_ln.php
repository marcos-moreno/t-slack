<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_evaluacion_ln" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30%" v-if="isFormCrud_ln==false">
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" v-model="filter_ln" />
                                </td> 
                                <td>
                                    <button type="button" name="filter_ln" class="btn btn-info btn-xs" @click="getev_evaluacion_lns()"> filtrar</button>
                                </td> 
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
        <div class="panel-body"  v-if="isFormCrud_ln==false">
            <h4>ev_evaluacion_ln</h4>
            <br>
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  
                        <li>
                            <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="numByPag" @change="paginator_ln(1)" > 
                                <option value=5  >5</option>
                                <option value=10 >10</option>
                                <option value=15 >15</option>
                                <option value=20 >20</option>
                            </select>
                        </li>
                        <li v-for="li in paginas" class="page-item">
                            <a class="page-link" @click="paginator_ln(li.element)" >{{ li.element }} <div v-if="li.element == paginaActual" >_</div></a> 
                        </li>
                    </ul>  
                </nav>
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_evaluacion_ln()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ev_evaluacion_ln_id</th>
                                    
                        <th>ev_evaluacion_id</th>
                                    
                        <th>id_empleado</th>
                                    
                        <th>ev_puesto_id</th>
                                    
                        <th>caalificacion</th>
                                    
                        <th>estado_atributo</th>
                                    
                        <th>creado</th>
                                    
                        <th>actualizado</th>
                                    
                        <th>creadopor</th>
                                    
                        <th>actualizadopor</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="ev_evaluacion_ln in paginaCollection" >
                        
                        <td>{{ ev_evaluacion_ln.ev_evaluacion_ln_id}}</td>
            
                        <td>{{ ev_evaluacion_ln.ev_evaluacion_id}}</td>
            
                        <td>{{ ev_evaluacion_ln.id_empleado}}</td>
            
                        <td>{{ ev_evaluacion_ln.ev_puesto_id}}</td>
            
                        <td>{{ ev_evaluacion_ln.caalificacion}}</td>
            
                        <td>{{ ev_evaluacion_ln.estado_atributo}}</td>
            
                        <td>{{ ev_evaluacion_ln.creado}}</td>
            
                        <td>{{ ev_evaluacion_ln.actualizado}}</td>
            
                        <td>{{ ev_evaluacion_ln.creadopor}}</td>
            
                        <td>{{ ev_evaluacion_ln.actualizadopor}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_evaluacion_ln(ev_evaluacion_ln.ev_evaluacion_ln_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_evaluacion_ln(ev_evaluacion_ln.ev_evaluacion_ln_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud_ln" >   
            <div class="form-group">
                <label>ID: {{ ev_evaluacion_ln.ev_evaluacion_ln_id }}</label>  
            </div> 
                                    <div class='form-group'>
                                        <label>ev evaluacion id</label> 
                                        <select class='form-control' size='1'  v-model='ev_evaluacion_ln.ev_evaluacion_id' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in ev_evaluacionCollection' v-bind:value='rows.ev_evaluacion_id'>{{ rows }}</option>
                                        </select>
                                    </div>  
                                    <div class='form-group'>
                                        <label>empleado</label> 
                                        <select class='form-control' size='1'  v-model='ev_evaluacion_ln.id_empleado' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in empleadoCollection' v-bind:value='rows.id_empleado'>{{ rows }}</option>
                                        </select>
                                    </div>  
                                    <div class='form-group'>
                                        <label>ev puesto id</label> 
                                        <select class='form-control' size='1'  v-model='ev_evaluacion_ln.ev_puesto_id' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in ev_puestoCollection' v-bind:value='rows.ev_puesto_id'>{{ rows }}</option>
                                        </select>
                                    </div> 
            <div class='form-group'>
                <label>caalificacion</label>
                <input type='number' class='form-control' v-model='ev_evaluacion_ln.caalificacion' />
            </div>  
            <div class='form-group'>
                <label>estado atributo</label>
                <input type='number' class='form-control' v-model='ev_evaluacion_ln.estado_atributo' />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_evaluacion_ln()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_evaluacion_ln()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_evaluacion_ln.js"></script>

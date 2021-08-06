<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_escala_evaluacion" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30%" v-if="isFormCrud==false">
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" v-model="filter" />
                                </td> 
                                <td>
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_escala_evaluacions()"> filtrar</button>
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
        <div class="panel-body"  v-if="isFormCrud==false">
            <h4>ev_escala_evaluacion</h4>
            <br>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_escala_evaluacion()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>Id</th>
                        <th>ev_indicador_general_id</th>
                        <th>porcentaje</th>
                        <th>parametro_menor</th>
                        <th>parametro_mayor</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="ev_escala_evaluacion in paginaCollection" >
                        <td>{{ ev_escala_evaluacion.ev_escala_evaluacion_id}}</td>
                        <td>{{ ev_escala_evaluacion.ev_indicador_general}}</td>
                        <td>{{ ev_escala_evaluacion.porcentaje}}%</td>
                        <td>{{ ev_escala_evaluacion.parametro_menor}}</td>
                        <td>{{ ev_escala_evaluacion.parametro_mayor}}</td>
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_escala_evaluacion( ev_escala_evaluacion.ev_escala_evaluacion_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_escala_evaluacion( ev_escala_evaluacion.ev_escala_evaluacion_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ev_escala_evaluacion.ev_escala_evaluacion_id }}</label>  
            </div> 
            <div class='form-group'>
                <label>Indicador</label> 
                <select class='form-control' size='1'  v-model='ev_escala_evaluacion.ev_indicador_general_id' >
                    <option value='0' >-</option>
                    <option v-for='rows in ev_indicador_generalCollection' v-bind:value='rows.ev_indicador_general_id'>{{ rows.nombre }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>porcentaje</label>
                <input type='number' class='form-control' v-model='ev_escala_evaluacion.porcentaje' />
            </div>  
            <div class='form-group'>
                <label>parametro menor</label>
                <input type='number' class='form-control' v-model='ev_escala_evaluacion.parametro_menor' />
            </div>  
            <div class='form-group'>
                <label>parametro mayor</label>
                <input type='number' class='form-control' v-model='ev_escala_evaluacion.parametro_mayor' />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_escala_evaluacion()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_escala_evaluacion()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_escala_evaluacion.js"></script>

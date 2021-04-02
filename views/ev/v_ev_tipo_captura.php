<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_tipo_captura" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_tipo_capturas()"> filtrar</button>
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

        
        <br><br>
        <div class="panel-body"  v-if="isFormCrud==false">
            <h4>ev_tipo_captura" </h4>
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  
                        <li><h3> EV_TIPO_CAPTURA </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_tipo_captura()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ev_tipo_captura_id</th>
                                    
                        <th>nombre</th>
                                    
                        <th>es_capturado</th>
                                    
                        <th>creado</th>
                                    
                        <th>creadopor</th>
                                    
                        <th>actualizado</th>
                                    
                        <th>actualizadopor</th>
                                    
                        <th>direct_data</th>
                                    
                        <th>opcion_multiple</th>
                                    
                        <th>dato</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="ev_tipo_captura in paginaCollection" >
                        
                        <td>{{ ev_tipo_captura.ev_tipo_captura_id}}</td>
            
                        <td>{{ ev_tipo_captura.nombre}}</td>
            
                        <td>{{ ev_tipo_captura.es_capturado}}</td>
            
                        <td>{{ ev_tipo_captura.creado}}</td>
            
                        <td>{{ ev_tipo_captura.creadopor}}</td>
            
                        <td>{{ ev_tipo_captura.actualizado}}</td>
            
                        <td>{{ ev_tipo_captura.actualizadopor}}</td>
            
                        <td>{{ ev_tipo_captura.direct_data}}</td>
            
                        <td>{{ ev_tipo_captura.opcion_multiple}}</td>
            
                        <td>{{ ev_tipo_captura.dato}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_tipo_captura(ev_tipo_captura.ev_tipo_captura_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_tipo_captura(ev_tipo_captura.ev_tipo_captura_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ev_tipo_captura.ev_tipo_captura_id }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='ev_tipo_captura.nombre' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='ev_tipo_capturaes_capturado _id'   v-model='ev_tipo_captura.es_capturado'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='ev_tipo_capturaes_capturado _id'  >es capturado</label>
            </div>  
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='ev_tipo_capturadirect_data _id'   v-model='ev_tipo_captura.direct_data'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='ev_tipo_capturadirect_data _id'  >direct data</label>
            </div>  
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='ev_tipo_capturaopcion_multiple _id'   v-model='ev_tipo_captura.opcion_multiple'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='ev_tipo_capturaopcion_multiple _id'  >opcion multiple</label>
            </div> 
            <div class='form-group'>
                <label>dato</label>
                <input type='text' class='form-control' v-model='ev_tipo_captura.dato' />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_tipo_captura()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_tipo_captura()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/ev/c_ev_tipo_captura.js"></script>

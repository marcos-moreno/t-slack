<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_propuesta" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30%" v-if="isFormCrud==false">
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                <!-- <label>Descripci&oacute;n</label> 
                                    <input type="text" class="form-control" v-model="filter" />
                                </td>  -->
                                <td>
                                    <!-- <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_propuestas()"> filtrar</button> -->
                                </td> 
                                <td>
                                    <label>Estado</label> 
                                    <select class='form-control' size='1' @change="getevConsulEstadoUser()" v-model='filterestado' >
                                        <option v-for='rows in estadoCollection2'  v-bind:value='rows.value' :selected="rows.value">({{ rows.value }}) {{ rows.descripcion }}</option>
                                    </select>
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
            <h4>Propuesta</h4>

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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_propuesta()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>Id</th>
                                    
                        <th>Fecha de Creaci&oacute;n</th>
                                    
                        <th>Descripci&oacute;n</th>
                                    
                        <th>estado</th>
                                    
                        <th>propuesta</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="ev_propuesta in paginaCollection" >
                        
                        <td>{{ ev_propuesta.propuesta_id}}</td>
            
                        <td>{{ ev_propuesta.fecha_creado}}</td>
            
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

                        <td>{{ ev_propuesta.propuesta}} </td>
               
                        <td style="width:150px" >
                            <!-- <button type="button" class="btn btn" @click="update_ev_propuesta(ev_propuesta.propuesta_id)"><img src="../../img/lapiz.svg" width="25px" /></button> -->
                            <button type="button" class="btn btn" @click="delete_ev_propuesta(ev_propuesta.propuesta_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>          
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class='form-group'>
                <label>Selecciona Propuesta </label> 
                <select class='form-control' size='1'  v-model='ev_propuesta.propuesta' >
                    <option value='0' >-</option>
                    <option v-for='rows in propuestaCollection' v-bind:value='rows.value'>{{ rows.value }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <textarea class='form-control' v-model='ev_propuesta.texto'  rows="4" placeholder="Escribe Aqu&iacute;"></textarea>
            </div> 
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs btn-danger" @click="cancel_ev_propuesta()"> Cancelar</button></td> 
                <button @click="save_ev_propuesta()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>
            
            </div>
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_propuesta_detalle.js"></script>

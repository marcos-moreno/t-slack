<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_ticket" style="margin-top:15px;"> 

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30%" v-if="isFormCrud==false">
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                <label>Situaci&oacute;n</label> 
                                    <input type="text" class="form-control" v-model="filter" />
                                </td> 
                                <td>
                                    
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_tickets()"> filtrar</button>
                                </td> 
                                <td></td>
                                <td>
                                <div class='form-group'>
                                    <label>Estado</label> 
                                    <select class='form-control' size='1' @change="getevConsulEstado()" v-model='filterestado' >
                                        <option v-for='rows in estadoCollection2'  v-bind:value='rows.value' :selected="rows.value">({{ rows.value }}) {{ rows.descripcion }}</option>
                                    </select>
                                 </div>
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
            <h4>Levantamiento de Ticket</h4>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_ticket()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th>
                        
                        <th>Departamento</th>

                        <th>Situaci&oacute;n</th>
                                    
                        <th>Fecha Creaci&oacute;n</th>
                                    
                        <th>Estado</th>

                        <th>Comentario Realizado</th>
                                    
                        <th>Comentario de Soluci&oacute;n</th>

                        <th>Fecha Soluci&oacute;n</th>
                        <th></th>
                
                    </tr>
                    <tr v-for="ev_ticket in paginaCollection" >
                        
                        <td>{{ ev_ticket.ev_ticket_id}}</td>

                        <td>{{ev_ticket.nombre}}</td>

                        <td>{{ ev_ticket.situacion }}</td>
            
                        <td>{{ ev_ticket.fechacreacion}}</td>
            
                        <td>{{ ev_ticket.estado}}</td>

                        <td>{{ ev_ticket.comentario }}</td>

                        <td>{{ ev_ticket.comentario_solucion }}</td>

                        <td>{{ ev_ticket.fechasolucion}}</td>
            
               
                        <td >
                            <button v-if="ev_ticket.estado == 'AB' || ev_ticket.estado == 'CA'" type="button" class="btn btn" @click="update_ev_ticket(ev_ticket.ev_ticket_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <!-- <button type="button" class="btn btn" @click="delete_ev_ticket(ev_ticket.ev_ticket_id)"><img src="../../img/borrar.png" width="25px" /></button> -->
                        </td>
                        
                        
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >
            <div v-show="isForm">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-4">
                            <div class='form-group'>
                                <label>Departamento</label> 
                                <select class='form-control' size='1'  @change="get_catalogo()" v-model='ev_ticket.departamento_id'  >
                                    <option value='0' >-</option>
                                    <option v-for='rows in departamentoCollection' v-bind:value='rows.departamento_id'>{{ rows.nombre }}</option>
                                </select>
                            </div>
                        </div>  
                    </div>
                </div>

                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-4">
                            <div class='form-group'>
                                <label>Situaci&oacute;n</label> 
                                <select class='form-control' size='1'  v-model='ev_ticket.ev_catalogo_ticket_id'  >
                                    <option v-for='rows in ev_catalogo_ticketCollection' v-bind:value='rows.ev_catalogo_ticket_id'>{{ rows.situacion }}</option>
                                    <option value=0>Otro</option>
                                </select>
                            </div>
                        </div>  
                    </div>
                </div>
                

                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-4">
                            <div class='form-group'>
                                <button type="button" class="btn btn-info btn-xs edit" @click="add_ev_ticketF()">Agregar Ticket</button>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>

            <div v-show="isFormInsert">
                <div class="form-group">
                    <label>ID: {{ ev_ticket.ev_ticket_id }}</label>  
                </div>
                <!-- <div class='form-group'>
                    <label>Problema</label>
                    <input type='text' class='form-control' v-model='ev_ticket.problema' :disabled= "disabled == 0"/>
                </div>  
                <div class='form-group'>
                    <label>Observacion</label>
                    <input type='text' class='form-control' v-model='ev_ticket.observacion' :disabled= "disabled == 0" />
                </div> -->
                <div v-show="fechas">
                <div class='form-group' >
                    <label>Fecha Creaci&oacute;n</label>
                    <input type='datetime' class='form-control' v-model='ev_ticket.fechacreacion' disabled/>
                </div>  
                <div class='form-group'>
                    <label>Fecha Soluci&oacute;n</label>
                    <input type='datetime' class='form-control' v-model='ev_ticket.fechasolucion' disabled/>
                </div>
                
                <div class='form-group'>
                    <label>Estado</label> 
                    <div v-if="ev_ticket.estado == 'CO' ">
                        <input type="text" class='form-control' value="(CO) Completo" disabled>
                    </div>

                    <div v-if="ev_ticket.estado == 'SSO'">
                        <input type="text" class='form-control' value="(SSO) Sin Solución" disabled>
                    </div>
                    <select v-if="ev_ticket.estado != 'SSO' && ev_ticket.estado != 'CO'" class='form-control' size='1'  v-model='ev_ticket.estado' >
                        <option v-for='rows in estadoCollection'  v-bind:value='rows.value' :selected= "rows.id_atributo" >({{ rows.value }}) {{ rows.descripcion }}</option>
                    </select>
                </div>    
                </div>  

                <div class='form-group'>
                    <label>Comentario</label>
                    <input type='text' class='form-control' v-model='ev_ticket.comentario' />
                </div>

                <div class='form-group' v-show="com">
                    <label>Comentario de soluci&oacute;n</label>
                    <input type='text' class='form-control' v-model='ev_ticket.comentario_solucion' disabled/>
                </div> 

                <div class="form-group">
                    <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_ticket()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                    <button @click="save_ev_ticket()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
                </div> 
            </div>
            <div  v-show="isFormInsert2">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Ticket Existente!</h4>
                    <p>El ticket ya a sido creado por otro usuario.</p><hr>
                    <p class="mb-0">Ingresa un comentario adicional para mejora de este ticket.</p>
                </div>
                <!-- <div class='form-group'>
                    <label>Problema</label>
                    <input type='text' class='form-control' v-model='ev_ticket.problema' :disabled= "disabled == 0"/>
                </div>  
                <div class='form-group'>
                    <label>Observacion</label>
                    <input type='text' class='form-control' v-model='ev_ticket.observacion' :disabled= "disabled == 0" />
                </div> -->
                <div class='form-group'>
                    <label>Comentario</label>
                    <input type='text' class='form-control' v-model='ev_ticket.comentario' />
                </div>
                <div class="form-group">
                    <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_ticket()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                    <button @click="save_ev_ticket()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
                </div> 
            </div>
            
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_ticket.js"></script>

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
                                    <input type="text" class="form-control" v-model="filter" />
                                </td> 
                                <td>
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_tickets()"> filtrar</button>
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
            <h4>ev_ticket</h4>
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
                        <th>ev_ticket_id</th>
                                    
                        <th>problema</th>
                                    
                        <th>observacion</th>
                                    
                        <th>fechacreacion</th>
                                    
                        <th>fechasolucion</th>
                                    
                        <th>estado</th>
                                    
                        <th>ev_catalogo_ticket_id</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="ev_ticket in paginaCollection" >
                        
                        <td>{{ ev_ticket.ev_ticket_id}}</td>
            
                        <td>{{ ev_ticket.problema}}</td>
            
                        <td>{{ ev_ticket.observacion}}</td>
            
                        <td>{{ ev_ticket.fechacreacion}}</td>
            
                        <td>{{ ev_ticket.fechasolucion}}</td>
            
                        <td>{{ ev_ticket.estado}}</td>
            
                        <td>{{ ev_ticket.ev_catalogo_ticket_id}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_ticket(ev_ticket.ev_ticket_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_ticket(ev_ticket.ev_ticket_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ev_ticket.ev_ticket_id }}</label>  
            </div>
            <div class='form-group'>
                <label>problema</label>
                <input type='text' class='form-control' v-model='ev_ticket.problema' />
            </div>  
            <div class='form-group'>
                <label>observacion</label>
                <input type='text' class='form-control' v-model='ev_ticket.observacion' />
            </div>  
            <div class='form-group'>
                <label>fechacreacion</label>
                <input type='datetime-local' class='form-control' v-model='ev_ticket.fechacreacion' />
            </div>  
            <div class='form-group'>
                <label>fechasolucion</label>
                <input type='datetime-local' class='form-control' v-model='ev_ticket.fechasolucion' />
            </div>  
            <div class='form-group'>
                <label>estado</label>
                <input type='text' class='form-control' v-model='ev_ticket.estado' />
            </div>   
                                    <div class='form-group'>
                                        <label>ev catalogo ticket id</label> 
                                        <select class='form-control' size='1'  v-model='ev_ticket.ev_catalogo_ticket_id' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in ev_catalogo_ticketCollection' v-bind:value='rows.ev_catalogo_ticket_id'>{{ rows }}</option>
                                        </select>
                                    </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_ticket()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_ticket()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/../ev/ev_ticket/c_ev_ticket.js"></script>

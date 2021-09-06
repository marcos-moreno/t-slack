<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_catalogo_ticket" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_catalogo_tickets()"> filtrar</button>
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
            <h4>Cat&aacute;logo Ticket </h4>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_catalogo_ticket()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th>
                                    
                        <th>situaci&oacute;n</th>
                                    
                                    
                        <th>departamento_id</th>
                        
                        <th>activo</th>
                        <th>creado</th>

                        <th></th> 
                    </tr>
                    <tr v-for="ev_catalogo_ticket in paginaCollection" >
                        
                        <td>{{ ev_catalogo_ticket.ev_catalogo_ticket_id}}</td>
            
                        <td>{{ ev_catalogo_ticket.situacion}}</td>

                        <td>{{ ev_catalogo_ticket.nombre}}</td>
            
                        <td>{{ ev_catalogo_ticket.activo}}</td>
            
                        <td>{{ ev_catalogo_ticket.creado}}</td>
            
                        
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_catalogo_ticket(ev_catalogo_ticket.ev_catalogo_ticket_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_catalogo_ticket(ev_catalogo_ticket.ev_catalogo_ticket_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ev_catalogo_ticket.ev_catalogo_ticket_id }}</label>  
            </div>
            <div class='form-group'>
                <label>situacion</label>
                <input type='text' class='form-control' v-model='ev_catalogo_ticket.situacion' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='ev_catalogo_ticketactivo _id'   v-model='ev_catalogo_ticket.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='ev_catalogo_ticketactivo _id'  >activo</label>
            </div> 
            <!--<div class='form-group'>
                <label>modificadopor</label>
                <input type='number' class='form-control' v-model='ev_catalogo_ticket.modificadopor' />
            </div> -->  
                                    <div class='form-group'>
                                        <label>departamento id</label> 
                                        <select class='form-control' size='1'  v-model='ev_catalogo_ticket.departamento_id' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in departamentoCollection' v-bind:value='rows.departamento_id'>{{ rows.nombre }}</option>
                                        </select>
                                    </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_catalogo_ticket()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_catalogo_ticket()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_catalogo_ticket.js"></script>

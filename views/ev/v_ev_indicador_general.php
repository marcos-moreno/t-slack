<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_indicador_general" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_indicador_generals()"> filtrar</button>
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
            <h4>Indicador General</h4>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_indicador_general()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th>
                        <th>nombre</th>
                        <th>descripcion</th>
                        <th>tendencia</th>
                        <th>activo</th> 
                        <th>origen</th>
                        <th></th> 
                    </tr>
                    <tr v-for="ev_indicador_general in paginaCollection" >
                        <td>{{ ev_indicador_general.ev_indicador_general_id}}</td>
                        <td>{{ ev_indicador_general.nombre}}</td>
                        <td>{{ ev_indicador_general.descripcion}}</td>
                        <td>{{ ev_indicador_general.tendencia}}</td>
                        <td><div v-if="ev_indicador_general.activo" >Si</div><div v-else>No</div></td> 
                        <td>{{ ev_indicador_general.origen}}</td>
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_indicador_general(ev_indicador_general.ev_indicador_general_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_indicador_general(ev_indicador_general.ev_indicador_general_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ev_indicador_general.ev_indicador_general_id }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='ev_indicador_general.nombre' />
            </div>  
            <div class='form-group'>
                <label>descripcion</label>
                <input type='text' class='form-control' v-model='ev_indicador_general.descripcion' />
            </div>  
            <div class='form-group'>
                <label>Tendencia</label>
                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model='ev_indicador_general.tendencia' > 
                    <option value="CRECIENTE" >CRECIENTE</option> 
                    <option value="DECRECIENTE"  >DECRECIENTE</option> 
                </select> 
            </div>  
            <div class='form-group'>
                <label>origen</label>
                    <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model='ev_indicador_general.origen' > 
                        <option value="Adempiere" >Adempiere</option> 
                        <option value="Cerberus"  >Cerberus</option>
                        <option value="Basecamp"  >Basecamp</option>
                        <option value="Surver"  >Surver</option>
                        <option value="GM Transport"  >GM Transport</option>
                    </select> 
            </div> 
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='ev_indicador_generalactivo _id'  
                 v-model='ev_indicador_general.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='ev_indicador_generalactivo _id'  >activo</label>
            </div>  
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='ev_indicador_generalactivo_id'  
                 v-model='ev_indicador_general.allowrepor'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='ev_indicador_generalactivo_id'  >Permitir Reportes</label>
            </div>     
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_indicador_general()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_indicador_general()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_indicador_general.js"></script>

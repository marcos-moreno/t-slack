<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_periodo" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getperiodos()"> filtrar</button>
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
            <h4>periodo</h4>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_periodo()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>Id</th>
                        <th>Período</th> 
                        <th>Inicio</th> 
                        <th>Fin</th> 
                        <th>Ejercicio</th> 
                        <th>Empresa</th>  
                        <th>No. Período</th>  
                        <th>Activo</th> 
                        <th>ES</th> 
                        <th></th> 
                    </tr>
                    <tr v-for="periodo in paginaCollection" >
                        <td>{{ periodo.periodo_id}}</td>
                        <td>{{ periodo.nombre_periodo}}</td>
                        <td>{{ periodo.inicio_periodo}}</td>
                        <td>{{ periodo.fin_periodo}}</td>
                        <td>{{ periodo.ejercicio}}</td>
                        <td>{{ periodo.empresa[0].empresa_observaciones}}</td> 
                        <td>{{ periodo.numero_periodo}}</td>
                        <td v-if="periodo.activo">Si</td>
                        <td v-else>No</td>
                        <td>{{ periodo.ev_atributo_es[0].descripcion}} ({{ periodo.ev_atributo_es[0].value}})</td>
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_periodo(periodo.periodo_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_periodo(periodo.periodo_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ periodo.periodo_id }}</label>  
            </div>
            <div class='form-group'>
                <label>Nombre</label>
                <input type='text' class='form-control' v-model='periodo.nombre_periodo' />
            </div>  
            <div class='form-group'>
                <label>Inicio Período</label>
                <input type='datetime-local' class='form-control' v-model='periodo.inicio_periodo' />
            </div>  
            <div class='form-group'>
                <label>Fin Período</label>
                <input type='datetime-local' class='form-control' v-model='periodo.fin_periodo' />
            </div>  
            <div class='form-group'>
                <label>Ejercicio</label>
                <input type='number' class='form-control' v-model='periodo.ejercicio' />
            </div>   
            <div class='form-group'>
                <label>Empresa</label> 
                <select class='form-control' size='1'  v-model='periodo.id_empresa' >
                    <option value='0' >-</option>
                    <option v-for='rows in empresaCollection' v-bind:value='rows.id_empresa'>{{ rows.empresa_observaciones }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>Número periodo</label>
                <input type='number' class='form-control' v-model='periodo.numero_periodo' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='periodoactivo _id'   v-model='periodo.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='periodoactivo _id'  >activo</label>
            </div>  
            <div class='form-group'>
                <label>Elemento sistema</label> 
                <select class='form-control' size='1'  v-model='periodo.elemento_sistema_atributo' >
                    <option value='0' >-</option>
                    <option v-for='rows in ev_atributoCollection' v-bind:value='rows.id_atributo'>{{ rows.descripcion }} ({{ rows.value }})</option>
                </select>
            </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_periodo()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_periodo()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/admin/c_periodo.js"></script>

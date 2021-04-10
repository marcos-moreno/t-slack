<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_departamento" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getdepartamentos()"> filtrar</button>
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
            <h4>Departamento</h4>
            <br>
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  
                        <li>
                            <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="numByPag" @change="paginator(1)" > 
                                <option value=10 >10</option>
                                <option value=15 >15</option>
                                <option value=20 >20</option>
                                <option value=30  >30</option>

                            </select>
                        </li>
                        <li v-for="li in paginas" class="page-item">
                            <a class="page-link" @click="paginator(li.element)" >{{ li.element }} <div v-if="li.element == paginaActual" >_</div></a> 
                        </li>
                    </ul>  
                </nav>
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-info btn-xs edit" @click="add_departamento()">Agregar</button>
                    </div> 
                </div>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th>
                        <th>nombre</th>
                        <th>activo</th>
                        <th>empresa</th>
                        <th>segmento</th>
                        <th>id Cerberus</th>
                        <th></th> 
                        <th></th> 
                    </tr>
                    <tr v-for="departamento in paginaCollection" >
                        <td>{{ departamento.departamento_id}}</td>
                        <td>{{ departamento.nombre}}</td>
                        <td>{{ departamento.activo}}</td>
                        <td>{{ departamento.empresa[0].empresa_observaciones}}</td>
                        <td>{{ departamento.segmento[0].nombre}}</td>
                        <td>{{ departamento.id_cerberus}}</td>
                        <td><a :href="'v_lider_departamento.php?departamento_id='+departamento.departamento_id" >Lider</a></td>
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_departamento(departamento.departamento_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_departamento(departamento.departamento_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ departamento.departamento_id }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='departamento.nombre' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='departamentoactivo _id'   v-model='departamento.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='departamentoactivo _id'  >activo</label>
            </div>  
            <div class='form-group'>
                <label>empresa</label> 
                <select class='form-control' size='1'  v-model='departamento.id_empresa' >
                    <option value='0' >-</option>
                    <option v-for='rows in empresaCollection' v-bind:value='rows.id_empresa'>{{ rows.empresa_observaciones }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>segmento</label> 
                <select class='form-control' size='1'  v-model='departamento.id_segmento' >
                    <option value='0' >-</option>
                    <option v-for='rows in segmentoCollection' v-bind:value='rows.id_segmento'>{{ rows.nombre }} ({{ rows.empresa[0].empresa_observaciones }})</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>cerberus</label>
                <input type='number' class='form-control' v-model='departamento.id_cerberus' />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_departamento()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_departamento()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/admin/c_departamento.js"></script>

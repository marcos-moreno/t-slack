<?php require "../header.php";?> 
<div class="container-fluid" style="width:90%;" >  
    <div id="app_segmento" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30%">
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" v-model="filter" />
                                    <div class='form-group'>
                                        <label>empresa</label> 
                                        <select class='form-control' size='1'  v-model='filterCompany' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in empresaCollection' v-bind:value='rows.id_empresa'>{{ rows.empresa_observaciones }}</option>
                                        </select>
                                    </div> 
                                </td> 
                                <td>
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getsegmentos()"> filtrar</button>
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
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  
                        <li><h3> SEGMENTO </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_segmento()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_segmento</th>
                                    
                        <th>empresa</th>
                                    
                        <th>Cerberus</th>
                                    
                        <!-- <th>fecha_creado</th> -->
                                    
                        <th>nombre</th>
                                    
                        <!-- <th>observaciones</th> -->
                                    
                        <th>activo</th>
                                    
                        <!-- <th>id_actualizadopor</th> -->
                                    
                        <!-- <th>fecha_actualizado</th> -->
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="segmento in paginaCollection" >
                        
                        <td>{{ segmento.id_segmento}}</td>
            
                        <td>{{ segmento.empresa[0].empresa_nombre}}</td>
            
                        <td>{{ segmento.id_cerberus}}</td>
            
                        <!-- <td>{{ segmento.fecha_creado}}</td> -->
            
                        <td>{{ segmento.nombre}}</td>
            
                        <!-- <td>{{ segmento.observaciones}}</td> -->
            
                        <td>  
                            <div v-if="segmento.activo">Si</div>
                            <div v-else >No</div>
                        </td>  
                        <!-- <td>{{ segmento.id_actualizadopor}}</td> -->
            
                        <!-- <td>{{ segmento.fecha_actualizado}}</td> -->
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_segmento(segmento.id_segmento)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_segmento(segmento.id_segmento)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ segmento.id_segmento }}</label>  
            </div> 
                                    <div class='form-group'>
                                        <label>empresa</label> 
                                        <select class='form-control' size='1'  v-model='segmento.id_empresa' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in empresaCollection' v-bind:value='rows.id_empresa'>{{ rows.empresa_nombre }}</option>
                                        </select>
                                    </div> 
            <!-- <div class='form-group'>
                <label>creadopor</label>
                <input type='number' class='form-control' v-model='segmento.id_creadopor' />
            </div>  
            <div class='form-group'>
                <label>fecha creado</label>
                <input type='datetime-local' class='form-control' v-model='segmento.fecha_creado' />
            </div>   -->
            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='segmento.nombre' />
            </div>  
            <div class='form-group'>
                <label>ID Cerberus</label>
                <input type='text' class='form-control' v-model='segmento.id_cerberus' />
            </div>  
            <div class='form-group'>
                <label>observaciones</label>
                <input type='text' class='form-control' v-model='segmento.observaciones' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='segmentoactivo _id'   v-model='segmento.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='segmentoactivo _id'  >activo</label>
            </div> 
            <!-- <div class='form-group'>
                <label>actualizadopor</label>
                <input type='number' class='form-control' v-model='segmento.id_actualizadopor' />
            </div>  
            <div class='form-group'>
                <label>fecha actualizado</label>
                <input type='datetime-local' class='form-control' v-model='segmento.fecha_actualizado' />
            </div>     -->
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_segmento()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_segmento()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/admin/c_segmento.js"></script>

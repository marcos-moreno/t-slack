<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_almacen" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30%">
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" v-model="filter" />
                                </td> 
                                <td>
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getalmacens()"> filtrar</button>
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
                        <li><h3> ALMACEN </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_almacen()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_almacen</th>
                                    
                        <th>nombre_almacen</th>
                                    
                        <th>id_segmento</th>
                                    
                        <th>activo</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="almacen in paginaCollection" >
                        
                        <td>{{ almacen.id_almacen}}</td>
            
                        <td>{{ almacen.nombre_almacen}}</td>
            
                        <td>(ID:{{ almacen.segmento[0].id_segmento}}) {{  almacen.segmento[0].nombre }} -> empresa: {{ almacen.segmento[0].id_empresa}}
                        </td>
            
                        <td>  
                            <div v-if="almacen.activo">Si</div>
                            <div v-else >No</div>
                        </td> 
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_almacen(almacen.id_almacen)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_almacen(almacen.id_almacen)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ almacen.id_almacen }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre almacen</label>
                <input type='text' class='form-control' v-model='almacen.nombre_almacen' />
            </div>   
            <div class='form-group'>
                <label>segmento</label> 
                <select class='form-control'size='1'  v-model='almacen.id_segmento' >
                    <option value='0' >-</option>
                    <option v-for='rows in segmentoCollection' v-bind:value='rows.id_segmento'>(ID: {{ rows.id_segmento }}) {{ rows.nombre }} ( empresa: {{ rows.empresa[0].empresa_nombre }}) </option>
                </select>
            </div>  
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='almacenactivo _id'   v-model='almacen.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='almacenactivo _id'  >activo</label>
            </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_almacen()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_almacen()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/un/c_almacen.js"></script>

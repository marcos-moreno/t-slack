<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_compra" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getcompras()"> filtrar</button>
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
                        <li><h3> COMPRA </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_compra()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_compra</th>
                                    
                        <th>id_proveedor</th>
                                    
                        <th>total</th>
                                    
                        <th>fecha_compra</th>
                                    
                        <th>nombre</th>
                                    
                        <th>estado</th>
                                     
                        <th></th>

                        <th></th> 
                    </tr>
                    <tr v-for="compra in paginaCollection" >
                        
                        <td>{{ compra.id_compra}}</td>
            
                        <td>{{ compra.proveedor[0].nombre_proveedor}}</td>
            
                        <td>{{ compra.total}}</td>
            
                        <td>{{ compra.fecha_compra}}</td>
            
                        <td>{{ compra.nombre}}</td>
            
                        <td>{{ compra.estado}}</td>

                        <td >
                            <div class="pre-scrollable" >
                                <a v-bind:href="'../un/v_compra_detalle.php?id_compra='+compra.id_compra" >Productos</a>
                            </div> 
                        </td>

                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_compra(compra.id_compra)" v-if="compra.estado=='BO'" ><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_compra(compra.id_compra)"  ><img src="../../img/borrar.png" width="25px" /></button>
                            <button type="button" class="btn btn" @click="completar(compra.id_compra)" v-if="compra.estado=='BO'"><img src="../../img/confirmar.svg" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ compra.id_compra }}</label>  
            </div> 
            <div class='form-group'>
                <label>proveedor</label> 
                <select class='form-control' size='1'  v-model='compra.id_proveedor' >
                    <option value='0' >-</option>
                    <option v-for='rows in proveedorCollection' v-bind:value='rows.id_proveedor'>{{ rows.nombre_proveedor }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>total</label>
                <input type='number' class='form-control' v-model='compra.total' :disabled="true"  />
            </div>  
            <div class='form-group'>
                <label>fecha compra</label>
                <input type='date' class='form-control' v-model='compra.fecha_compra' />
            </div>  
            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='compra.nombre' />
            </div>  
            <div class='form-group'>
                <label>estado</label>
                <input type='text' class='form-control' v-model='compra.estado'  :disabled="true" />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_compra()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_compra()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/un/c_compra.js"></script>

<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_movimiento_stock" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getmovimiento_stocks()"> filtrar</button>
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
                        <li><h3> MOVIMIENTO_STOCK </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_movimiento_stock()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th>
                                    
                        <th>almacen</th>

                        <th>Fecha</th>

                        <th>Código</th>
                        
                        <th>producto</th>

                        <th>cantidad</th>
                                    
                        <th>movimiento</th>
                                    
                        <th>movimiento</th>
                                    
                        <th>descripcion</th>
                                    
                        <th>activo</th>
                                    
                        <th>color</th>
                                    
                        <th>talla</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="movimiento_stock in paginaCollection" >
                        
                        <td>{{ movimiento_stock.id_movimiento_stock}}</td>
            
                        <td>{{ movimiento_stock.almacen[0].nombre_almacen}}</td>

                        <td>{{ movimiento_stock.fecha_movimiento}}</td>
            
                        <td>{{ movimiento_stock.codigo}}</td>

                        <td>{{ movimiento_stock.producto[0].nombre_producto}}</td>
            
                        <td>{{ movimiento_stock.cantidad}}</td>
            
                        <td>{{ movimiento_stock.tipo_movimiento[0].nombre_tipo_movimiento}}</td>
            
                        <td>{{ movimiento_stock.id_movimiento}}</td>
            
                        <td>{{ movimiento_stock.descripcion}}</td>
            
                        <td>{{ movimiento_stock.activo}}</td>
            
                        <td>{{ movimiento_stock.color[0].nombre_color}}</td>
            
                        <td>{{ movimiento_stock.talla[0].valor}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_movimiento_stock(movimiento_stock.id_movimiento_stock)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_movimiento_stock(movimiento_stock.id_movimiento_stock)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ movimiento_stock.id_movimiento_stock }}</label>  
            </div> 
            <div class='form-group'>
                <label>almacen</label> 
                <select class='form-control' size='1'  v-model='movimiento_stock.id_almacen' >
                    <option value='0' >-</option>
                    <option v-for='rows in almacenCollection' v-bind:value='rows.id_almacen'>{{ rows.nombre_almacen }} (Segmento: {{ rows.segmento[0].nombre}})</option>
                </select>
            </div>  
            <div class='form-group'>
                <label>producto</label> 
                <select class='form-control' size='1'  v-model='movimiento_stock.id_producto' >
                    <option value='0' >-</option>
                    <option v-for='rows in productoCollection' v-bind:value='rows.id_producto'>{{ rows.nombre_producto }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>cantidad</label>
                <input type='number' class='form-control' v-model='movimiento_stock.cantidad' />
            </div>   
            <div class='form-group'>
                <label>tipo movimiento</label> 
                <select class='form-control' size='1'  v-model='movimiento_stock.id_tipo_movimiento' >
                    <option value='0' >-</option>
                    <option v-for='rows in tipo_movimientoCollection' v-bind:value='rows.id_tipo_movimiento'>{{ rows.nombre_tipo_movimiento }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>movimiento</label>
                <input type='number' class='form-control' v-model='movimiento_stock.id_movimiento' />
            </div>  
            <div class='form-group'>
                <label>descripcion</label>
                <input type='text' class='form-control' v-model='movimiento_stock.descripcion' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='movimiento_stockactivo _id'   v-model='movimiento_stock.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='movimiento_stockactivo _id'  >activo</label>
            </div>  
            <div class='form-group'>
                <label>color</label> 
                <select class='form-control' size='1'  v-model='movimiento_stock.id_color' >
                    <option value='0' >-</option>
                    <option v-for='rows in colorCollection' v-bind:value='rows.id_color'>{{ rows.nombre_color }}</option>
                </select>
            </div>  
            <div class='form-group'>
                <label>talla</label> 
                <select class='form-control' size='1'  v-model='movimiento_stock.id_talla' >
                    <option value='0' >-</option>
                    <option v-for='rows in tallaCollection' v-bind:value='rows.id_talla'>{{ rows.valor }}</option>
                </select>
            </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_movimiento_stock()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_movimiento_stock()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/un/c_movimiento_stock.js"></script>

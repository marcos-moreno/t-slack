<?php require "../header.php";?> 

<?php 
    if(isset($_GET['id_compra'])){ 
        echo "<input id='id_compra'  value=".$_GET['id_compra']." style='display:none' />"; 
    }else{
        echo "<input value=0 id='id_compra' style='display:none' />";
    }    ?> 
<div class="container" style="width:90%">  
    <div id="app_compra_detalle" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr> 
                    <td>
                        <div class='form-group' style="width:500px">
                            <label>compra</label> 
                            <select class='form-control' size='1'  v-model='id_compra' @change="getcompra_detalles" >
                                <option value='0' >-</option>
                                <option v-for='rows in compraCollection' v-bind:value='rows.id_compra'>{{ rows.nombre }} {{ rows.fecha_compra }}</option>
                            </select>
                        </div>  
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
                        <li><h3> COMPRA_DETALLE </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_compra_detalle()" v-if="valido" >Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_compra_detalle</th>
                                    
                        <th>Almácen</th>
                                    
                        <th>color</th>
                                    
                        <th>talla</th>
                                    
                        <th>producto</th>
                                    
                        <th>total_linea</th>
                                    
                        <th>cantidad</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="compra_detalle in paginaCollection" >
                        
                        <td>{{ compra_detalle.id_compra_detalle}}</td>
            
                        <td>{{ compra_detalle.almacen[0].nombre_almacen}}</td>  
            
                        <td>{{ compra_detalle.color[0].nombre_color }}</td>
            
                        <td>{{ compra_detalle.talla[0].valor }}</td>
            
                        <td>{{ compra_detalle.producto[0].nombre_producto }}</td>
            
                        <td>${{ compra_detalle.total_linea}}</td>
            
                        <td>{{ compra_detalle.cantidad}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_compra_detalle(compra_detalle.id_compra_detalle)" v-if="valido" ><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_compra_detalle(compra_detalle.id_compra_detalle)" v-if="valido" ><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ compra_detalle.id_compra_detalle }}</label>  
            </div>  
            <div class='form-group'>
                <label>Almácen</label> 
                <select class='form-control' size='1'  v-model='compra_detalle.id_almacen' >
                    <option value='0' >-</option>
                    <option v-for='rows in almacenCollection' v-bind:value='rows.id_almacen'>{{ rows.nombre_almacen }} (Segmento: {{ rows.segmento[0].nombre}})</option>
                </select>
            </div>
            <div class='form-group'>
                <label>producto</label> 
                <select class='form-control' size='1'  v-model='compra_detalle.id_producto' @change="getDataProd()" >
                    <option value='0' >-</option>
                    <option v-for='rows in productoCollection' v-bind:value='rows.id_producto'> 
                      {{ rows.nombre_producto }} {{ rows.descripcion_producto }} ${{ rows.costo_proveedor }} </option>
                </select>
            </div> 
            <div class='form-group'>
                <label>color</label> 
                <select class='form-control' size='1'  v-model='compra_detalle.id_color' >
                    <option value='0' >-</option>
                    <option v-for='rows in colorCollection' v-bind:value='rows.id_color'>{{ rows.nombre_color }}</option>
                </select>
            </div>  
            <div class='form-group'>
                <label>talla</label> 
                <select class='form-control' size='1'  v-model='compra_detalle.id_talla' >
                    <option value='0' >-</option>
                    <option v-for='rows in tallaCollection' v-bind:value='rows.id_talla'>{{ rows.valor }}</option>
                </select>
            </div>   
            <div class='form-group'>
                <label>cantidad</label>
                <input type='number' class='form-control' v-model='compra_detalle.cantidad'  @input="calcularTotalDetalle()"  />
            </div>
            <div class='form-group'>
                <label>total linea</label>
                <input type='number' class='form-control' v-model='compra_detalle.total_linea' />
            </div>  
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_compra_detalle()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_compra_detalle()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/un/c_compra_detalle.js"></script>

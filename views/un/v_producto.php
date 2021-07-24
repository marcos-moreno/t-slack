<?php require "../header.php"; ?> 
<div class="container" style="width:90%">  
    <div id="app_producto" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getproductos()"> filtrar</button>
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
                        <li><h3> PRODUCTO </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_producto()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID producto</th>
                                    
                        <th>Cátalogo</th>
                                    
                        <th>Nombre Producto</th>
                                    
                        <th>Descripcion</th>
                                    
                        <th>Precio Colaborador</th>

                        <th>Costo Proveedor</th>
                                    
                        <th>Tipo Producto</th>
                                    
                        <th>Activo</th>
                                     
                        <th></th> 

                        <th></th> 
                    </tr>
                    <tr v-for="producto in paginaCollection" >
                        
                        <td>{{ producto.id_producto}}</td>
            
                        <td>{{ producto.catalogo[0].nombre_catalogo}}</td>
            
                        <td>{{ producto.nombre_producto}}</td>
            
                        <td>{{ producto.descripcion}}</td>
            
                        <td>${{ producto.costo}}</td> 
                        
                        <td>${{ producto.costo_proveedor}}</td>
            
                        <td>{{ producto.tipo_producto[0].nombre_tipo_producto}}</td>
            
                        <td>  
                            <div v-if="producto.activo">Si</div>
                            <div v-else >No</div>
                        </td> 

                        <td><a type="button" name="company" class="btn-xs delete" @click="asingColor(producto)">Colores Disponibles</a></td>
 
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_producto(producto.id_producto)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_producto(producto.id_producto)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  


        <div v-if="myModalColors" >  
            <transition name="model" >
              <div class="modal-mask" > 
                      <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                          <div class="modal-header"> 
                            <button type="button" class="close" @click="myModalColors=false"><span aria-hidden="true">&times;</span></button>
                          </div>  
                          <div class="modal-body"> 
                            <div class="card-body">   
                                <div class="custom-control custom-checkbox">
                                  <h5 >Colores Disponibles.</h5>
                                  <div v-for="r in colores" >   
                                        <input style="margin-left:5px;" type='checkbox' v-model=r.selected  > <span>{{ r.nombre_color }}</span>  
                                  </div> 
                                </div>   
                                <div align="center"> 
                                  <input type="button" class="btn btn-success btn-xs" value="Guardar"  
                                  @click="guardarColores()" />
                                </div>
                                </br> 
                            </div>
                          </div>
                      </div> 
                </div>
              </div>
            </transition>
        </div>

            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ producto.id_producto }}</label>  
            </div> 
            <div class='form-group'>
                <label>catalogo</label> 
                <select class='form-control' size='1'  v-model='producto.id_catalogo' >
                    <option value='0' >-</option>
                    <option v-for='rows in catalogoCollection' v-bind:value='rows.id_catalogo'>({{ rows.id_catalogo }}) {{ rows.nombre_catalogo }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>nombre producto</label>
                <input type='text' class='form-control' v-model='producto.nombre_producto' />
            </div>  
            <div class='form-group'>
                <label>descripcion</label>
                <input type='text' class='form-control' v-model='producto.descripcion' />
            </div>  
            <div class='form-group'>
                <label>precio al colaborador</label>
                <input type='number' class='form-control' v-model='producto.costo' />
            </div>  
            <div class='form-group'>
                <label>Costo de Proveedor</label>
                <input type='number' class='form-control' v-model='producto.costo_proveedor' />
            </div>   
            <div class='form-group'>
                <label>tipo producto</label> 
                <select class='form-control' size='1'  v-model='producto.id_tipo_producto' >
                    <option value='0' >-</option>
                    <option v-for='rows in tipo_productoCollection' v-bind:value='rows.id_tipo_producto'>({{ rows.id_tipo_producto }}) {{ rows.nombre_tipo_producto }}</option>
                </select>
            </div>  
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='productoactivo _id'   v-model='producto.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='productoactivo _id'  >activo</label>
            </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_producto()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_producto()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/un/c_producto.js"></script>

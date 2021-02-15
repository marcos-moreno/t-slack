<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_tipo_producto" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="gettipo_productos()"> filtrar</button>
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
                        <li><h3> TIPO DE PRODUCTO </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_tipo_producto()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_tipo_producto</th>
                                    
                        <th>nombre_tipo_producto</th>
                                    
                        <th>descripcion</th>
                                    
                        <th>activo</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="tipo_producto in paginaCollection" >
                        
                        <td>{{ tipo_producto.id_tipo_producto}}</td>
            
                        <td>{{ tipo_producto.nombre_tipo_producto}}</td>
            
                        <td>{{ tipo_producto.descripcion}}</td>
            
                        
                        <td>  
                            <div v-if="tipo_producto.activo">Si</div>
                            <div v-else >No</div>
                        </td> 
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_tipo_producto(tipo_producto.id_tipo_producto)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_tipo_producto(tipo_producto.id_tipo_producto)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ tipo_producto.id_tipo_producto }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre tipo producto</label>
                <input type='text' class='form-control' v-model='tipo_producto.nombre_tipo_producto' />
            </div>  
            <div class='form-group'>
                <label>descripcion</label>
                <input type='text' class='form-control' v-model='tipo_producto.descripcion' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='tipo_productoactivo _id'   v-model='tipo_producto.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='tipo_productoactivo _id'  >activo</label>
            </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_tipo_producto()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_tipo_producto()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/un/c_tipo_producto.js"></script>

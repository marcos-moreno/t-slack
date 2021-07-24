<?php require "../header.php";?> 
<?php 
    if(isset($_GET['id_paquete'])){ 
        echo "<input id='id_paquete'  value=".$_GET['id_paquete']." style='display:none' />"; 
    }else{
        echo "<input value=0 id='id_paquete' style='display:none' />";
    }    ?> 
<div class="container" style="width:90%">  
    <div id="app_paquete_detalle" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getpaquetes()"> filtrar</button>
                                </td> 
                            </tr>

                            <tr> 
                                <td>
                                    <div class='form-group'>
                                        <label>Paquete</label> 
                                        <select class='form-control' size='1'  v-model='id_paquete_seleted' @change="getpaquete_detalles()" >
                                            <option value='0' >Paquete No seleccionado</option>
                                            <option v-for='rows in paqueteCollection' v-bind:value='rows.id_paquete'>({{ rows.id_paquete }}) {{ rows.nombre_paquete }}</option>
                                        </select>
                                    </div> 
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
                        <li><h3> PAQUETE_DETALLE </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_paquete_detalle()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id detalle</th>
                                    
                        <th>paquete</th>
                                    
                        <th>producto</th>
                                    
                        <th>cantidad</th>

                        <th>color</th>
                                    
                        <th>Precio al Colaborador</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="paquete_detalle in paginaCollection" >
                        
                        <td>{{ paquete_detalle.id_paquete_detalle}}</td>
            
                        <td>({{ paquete_detalle.paquete[0].id_paquete }}) {{ paquete_detalle.paquete[0].nombre_paquete }}
                            <br> <div v-if="paquete_detalle.paquete[0].genero == 'H'">para: Hombre</div> <div v-else>para: Mujer</div> </td>
            
                        <td>({{ paquete_detalle.producto[0].id_producto }}) {{ paquete_detalle.producto[0].nombre_producto }}</td>
            
                        <td>{{ paquete_detalle.cantidad}}</td>

                        <td>{{ paquete_detalle.color[0].nombre_color }}</td>
            
                        <td>{{ paquete_detalle.costo}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_paquete_detalle(paquete_detalle.id_paquete_detalle)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_paquete_detalle(paquete_detalle.id_paquete_detalle)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ paquete_detalle.id_paquete_detalle }}</label>  
            </div> 
          
            <div class='form-group'>
                <label>producto</label> 
                <select class='form-control' size='1'  v-model='paquete_detalle.id_producto' @change="getColoresDisponibles()" >
                    <option value='0' >-</option>
                    <option v-for='rows in productoCollection' v-bind:value='rows.id_producto'>({{ rows.id_producto }}){{ rows.nombre_producto }}  Colaborador: ${{ rows.costo }} | Proveedor: ${{ rows.costo_proveedor }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>Color</label> 
                <select class='form-control' size='1'  v-model='paquete_detalle.id_color' >
                    <option value='0' >-</option>
                    <option v-for='rows in colorCollection' v-bind:value='rows.id_color'>{{ rows.nombre_color }}</option>
                </select>
            </div>    
            <div class='form-group'>
                <label>cantidad</label>
                <input type='number' class='form-control' v-model='paquete_detalle.cantidad' />
            </div>  
            <div class='form-group'>
                <label>Precio al Colaborador</label>
                <input type='number' class='form-control' v-model='paquete_detalle.costo' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='permitir_cambiar_color _id'   v-model='paquete_detalle.permitir_cambiar_color'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='permitir_cambiar_color _id'  >permitir al Usuario cambiar color</label>
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_paquete_detalle()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_paquete_detalle()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/un/c_paquete_detalle.js"></script>

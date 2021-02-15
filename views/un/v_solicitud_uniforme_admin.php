<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_solicitud_uniforme" style="margin-top:15px;"> 

    <div v-if="cargando">
    <transition name="model">
        <div class="modal-mask">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" @click="cargando=false"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <img src="../../img/cargando.gif" > 
                </div>
            </div>
        </div>
    </transition>
   </div>  
   <div class="table-responsive" >
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30%"> 
                        <table>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" v-model="filter" /> 
                                    <select class='form-control' size='1'  v-model='estado_filtro' v-if="isFormCrud == false" >
                                        <option value='estado' >Todos Los Estados</option>
                                        <option value="'BO'" >Borrador</option>
                                        <option value="'CO'" >Completo</option>
                                        <option value="'EN'" >Entregado</option> 
                                    </select>
                                </td> 
                                <td>
                                    <select class='form-control' size='1'  v-model='id_empresaSelected' @change="getSegments()" >
                                        <option value='emp.id_empresa' >Todas Las Empresas</option>  
                                        <option v-for='rows in companys' v-bind:value='rows.id_empresa'>{{ rows.empresa_nombre.substring(0,17)  }}</option>
                                    </select>
                                    <select class='form-control' size='1'  v-model='id_segmentoSelected'>
                                        <option value='s.id_segmento' >Todos Los Segmentos</option> 
                                        <option v-for='rows in segments' v-bind:value='rows.id_segmento'>{{ rows.nombre.substring(0,17) }}</option>
                                    </select>
                                </td> 
                                <td>
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getsolicitud_uniformes()" v-if="isFormCrud == false" > filtrar</button>
                                   <br><div  v-if="isFormCrud == true"> <button type="button" name="filter" class="btn btn" @click="getEmpleados()" ><img src="../../img/buscar.svg" width="22px" /></button></div>
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
        <div class="panel-body"  v-if="isFormCrud==false">
            <div class="table-responsive"> 
                <button type="button" class="btn btn-info btn-xs edit" @click="add_solicitud_uniforme()">Crear Una Solicitud</button>
                <br><br> 
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr> 
                            <th>id</th> 
                            <th>estado</th>
                            <th>Empleado</th>
                            <th>total</th>
                            <th>Fecha Aplica</th>
                            <th>tipo entrega</th>
                            <th></th> 
                        </tr>
                    </thead>
                    <tr v-for="solicitud_uniforme in solicitud_uniformeCollection" >
                        <td>{{ solicitud_uniforme.id_solicitud_uniforme}}</td> 
                        <td>
                            <div v-if="solicitud_uniforme.estado=='BO'">Borrador</div>
                            <div v-if="solicitud_uniforme.estado=='CO'">Completa</div>
                            <div v-if="solicitud_uniforme.estado=='EN'">Entregado</div>
                        </td>
                        <td>{{ solicitud_uniforme.empleado[0].paterno}} {{ solicitud_uniforme.empleado[0].materno}} {{ solicitud_uniforme.empleado[0].nombre}}
                        (ID Cerberus: {{ solicitud_uniforme.empleado[0].id_cerberus_empleado}})</td>
                        <td>{{ solicitud_uniforme.total}}</td>
                        <td>{{ solicitud_uniforme.fecha_entrega}}</td>
                        <td>{{ solicitud_uniforme.tipo_entregas[0].tipo}}</td>
                        <td style="width:150px" > 
                            <button type="button" class="btn btn" @click="update_solicitud_uniforme(solicitud_uniforme.id_solicitud_uniforme)" ><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_solicitud_uniforme(solicitud_uniforme.id_solicitud_uniforme)" ><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
        <div v-if="isFormCrud" >    
            <div class='form-group'>
                <label>Empleado</label>
                <select class='form-control' size='1'  v-model='solicitud_uniforme.id_empleado' @change="changeEmployee()" >
                    <option value='0' >No Seleccionado</option> 
                    <option v-for='rows in empleados' v-bind:value='rows.id_empleado'>(ID Cerberus: {{ rows.id_cerberus_empleado }}) {{ rows.paterno }} {{ rows.materno }} {{ rows.nombre }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>Estado de la Solicitud</label>
                <select class='form-control' size='1'  v-model='solicitud_uniforme.estado'> 
                    <option value="BO" >Borrador</option>
                    <option value="CO" >Completo</option>
                    <option value="EN" >Entegado</option> 
                </select>
            </div> 
            <div class="form-group">
                <label>ID: {{ solicitud_uniforme.id_solicitud_uniforme }}</label>  
            </div> 
            <div class='form-group'>
                <label>total</label>
                <input type='number' class='form-control' v-model='solicitud_uniforme.total' :disabled=true />
            </div> 
        
            <div class='form-group' v-if="isFormCrud_detalle == false"> 
            <br> <div style='background:#0677C1;height:3px;'></div> <br>

            <div class='form-group'>
                <label>Tipo de Entrega</label> 
                <select class='form-control' size='1'  v-model='tipo_entrega' @change="getpaquetes" :disabled="solicitud_uniforme.id_solicitud_uniforme != 0" >
                    <option value='0' >No seleccionada</option>
                    <option v-for='rows in tipo_entregasCollection' v-bind:value='rows'>({{ rows.id_tipo_entrega }}) {{ rows.tipo }}</option>
                </select>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <label>Paquete</label> 
                    <select class='form-control' size='1'  v-model='id_paquete'>
                        <option value='0' >No seleccionado</option>
                        <option v-for='rows in paqueteCollection' v-bind:value='rows.id_paquete'>({{ rows.id_paquete }}) {{ rows.nombre_paquete }}</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label>talla</label> 
                    <select class='form-control' size='1'  v-model='id_talla' >
                        <option value=0 >Talla No seleccionada</option>
                        <option v-for='rows in tallaPlayeraCollection' v-bind:value='rows.id_talla'  >{{ rows.valor }}</option>
                    </select>
                </div>  
                <div class="col-sm-4">
                    <label>Número Zapato</label> 
                    <select class='form-control' size='1'  v-model='id_talla_zapato' >
                        <option value=0 >Número Zapato No seleccionado</option>
                        <option v-for='rows in numsZapatoCollection' v-bind:value='rows.id_talla'  > {{ rows.valor }}</option>
                    </select>
                </div>   
                <div class="col-sm-4">
                    <br> 
                    <button type="button" class="btn btn-info btn-xs edit" @click="add_paquete()"> Agregar Paquete</button>
                </div>  
            </div>
            </div> <br> 
            <div style='background:#0677C1;height:3px;'></div> <br>

<!-- detalle -->
        <div class="panel-body" v-if="isFormCrud_detalle == false" >
            <div class="table-responsive"> 
                <button type="button" class="btn btn btn-xs edit" @click="add_solicitud_uniforme_detalle()"><img src="../../img/mas.svg" width="18px" /> Añadir productos</button>
                <br><br>
                <table class="table table-bordered table-striped ">
                          <tr> 
                            <!-- <th>id_solicitud_uniforme_detalle</th> -->
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Cantidad</th>
                            <th>Color</th>
                            <th>total linea</th>
                            <th></th> 
                        </tr>  
                    <tr v-for="solicitud_uniforme_detalle in solicitud_uniforme_detalleCollection" >
                        <!-- <td>{{ solicitud_uniforme_detalle.id_solicitud_uniforme_detalle}}</td> --> 
                        <td>{{ solicitud_uniforme_detalle.producto[0].nombre_producto}} {{ solicitud_uniforme_detalle.producto.descripcion}}</td>
                        <td>{{ solicitud_uniforme_detalle.talla[0].valor}}</td>
                        <td>{{ solicitud_uniforme_detalle.cantidad}}</td>
                        <td>{{ solicitud_uniforme_detalle.color[0].nombre_color}} 
                        <button type="button" class="btn btn" @click="changeColor(solicitud_uniforme_detalle)" v-if="solicitud_uniforme_detalle.permitir_cambiar_color==true" >
                            <img src="../../img/elige-color.svg"  width="25px" /></button>
                        </td>  
                        <td>{{ solicitud_uniforme_detalle.total_linea}}</td>
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="delete_solicitud_uniforme_detalle(solicitud_uniforme_detalle)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
        <div v-if="isFormCrud_detalle" >   
            <div class="form-group">
                <label>ID: {{ solicitud_uniforme_detalle.id_solicitud_uniforme_detalle }}</label>  
            </div>  
            <div class='form-group'>
                <label>Cátalogo</label> 
                <select class='form-control' size='1'  v-model='catalogo.id_catalogo' @change="buscarProductos" >
                    <option value='0' >No seleccionado</option>
                    <option v-for='rows in catalogoCollection' v-bind:value='rows.id_catalogo'>({{ rows.id_catalogo }}) {{ rows.nombre_catalogo }} Proveedor: {{ rows.proveedor[0].nombre_proveedor }}</option>
                </select>
            </div>
            <div class='form-group'>
                <label>producto</label> 
                <select class='form-control' size='1'  v-model='solicitud_uniforme_detalle.id_producto'  @change="buscarDatosProducto" >
                    <option value='0' >Producto No seleccionado</option>
                    <option v-for='rows in productoCollection' v-bind:value='rows.id_producto'> ${{ rows.costo }} 
                     {{ rows.ndescripcion_producto }} {{ rows.nombre_producto }}</option>
                </select>
            </div>  
            <div class='form-group'>
                <div class="row">
                    <div class="col-sm">
                        <label>talla</label> 
                        <select class='form-control' size='1'  v-model='solicitud_uniforme_detalle.id_talla' >
                            <option value='0' >Talla No seleccionada</option>
                            <option v-for='rows in tallaCollection' v-bind:value='rows.id_talla'>{{ rows.valor }}</option>
                        </select>
                    </div>  
                    <div class="col-sm">
                        <label>color</label> 
                        <select class='form-control' size='1'  v-model='solicitud_uniforme_detalle.id_color' >
                            <option value='0' >Color No seleccionado</option>
                            <option v-for='rows in colorCollection' v-bind:value='rows.id_color'> {{ rows.nombre_color }}</option>
                        </select>
                    </div>  
                </div>  
            </div>   
            <div class='form-group'>
                <div class="row">
                    <div class="col-sm">
                        <label>Cantidad</label>
                        <input type='number' class='form-control' v-model='solicitud_uniforme_detalle.cantidad'  @input="calcularTotalDetalle()"  />
                    </div> 
                    <div class="col-sm">
                        <label>total linea</label>
                        <input type='number' class='form-control' v-model='solicitud_uniforme_detalle.total_linea'  :disabled=true   />
                    </div> 
                </div>  
            </div>  
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_solicitud_uniforme_detalle()"><img src="../../img/del-icon-compra.svg" width="28px" />Cancelar Producto</button></td> 
                <button @click="agrega_sol_uni_detalle()" class="btn btn btn-xs" ><img src="../../img/add-icono.svg" width="18px" /> *Agregar Producto</button>
            </div>   
        </div>   
<!-- Detalle -->



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
                            <h5 >Color</h5>
                            <label>Selección Color</label>  
                            <select class='form-control' size='1'  v-model='solicitud_uniforme_detalle.id_color' >
                                <option v-for='rows in colores' v-bind:value='rows.id_color'>{{ rows.nombre_color }}</option>
                            </select>
                        </div>   <br><br>
                        <div align="center"> 
                            <input type="button" class="btn btn-success btn-xs" value="Guardar"  
                            @click="guardarColor()" />
                        </div>
                        </br> 
                    </div>
                    </div>
                </div> 
        </div>
        </div>
    </transition>
</div>




            <br>
            <br>
            <div class="form-group" v-if="isFormCrud_detalle==false">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_solicitud_uniforme()"><img src="../../img/regresar.png" width="28px" />Descartar</button></td> 
                <button @click="save_solicitud_uniforme()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> Guardar Solicitud</button>
            </div>   
        </div>  
    </div>
</div> 
<script type="text/javascript" src="../../controller/un/c_solicitud_uniforme_admin.js"></script>

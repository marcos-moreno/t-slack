<?php require "../header.php";?> 

<div class="container-fluid" style="width:80%;">  
    <div id="app_empleado" style="margin-top:5px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped" >
                <tr>
                    <td style="weight: 30%"  v-if="isFormCrud==false">
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" v-model="filter" />
                                </td> 
                                <td>
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getempleados()"> filtrar</button>
                                </td>
                                <td class='form-group'>
                                    <input type='checkbox' class='custom-control-input' id='active_filter' @change="getempleados()"   v-model='activos_filter'  false-value=false true-value=true >
                                    <label class='custom-control-label' for='active_filter'  >activo</label>
                                </td>
                            </tr>
                            <tr> 
                                <td> 
                                    <select class='form-control'  v-model='empresa_id_filter' style="width:150px" @change="segmento_id_filter=0;get_segmentosFilter()" > 
                                        <option value=0>-</option>
                                        <option v-for='rows in empresas' v-bind:value='rows.id_empresa'>{{ rows.empresa_observaciones }}</option>
                                    </select>
                                </td> 
                                <td>
                                <!-- {{segmentoFilterCollection}} -->
                                    <select class='form-control'  v-model='segmento_id_filter' style="width:150px" @change="getempleados()" > 
                                        <option value=0 >Todo los Segmentos</option>
                                        <option v-for='rows in segmentoFilterCollection' v-bind:value='rows.id_segmento'>{{ rows.nombre }}</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td >
                        <div class="pre-scrollable" >
                            <h3> EMPLEADO </h3> 
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
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  
                        
                        <li>
                            <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="numByPag" @change="paginator(1)" > 
                                <option value=5  >5</option>
                                <option value=10 >10</option>
                                <option value=15 >15</option>
                                <option value=25 >25</option>
                            </select>
                        </li>
                        <li v-for="li in paginas" class="page-item">
                            <a class="page-link" @click="paginator(li.element)" >{{ li.element }} <div v-if="li.element == paginaActual" >_</div></a> 
                        </li>
                    </ul>  
                </nav>
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_empleado()">Agregar</button></td>
                <table class="table table-bordered table-striped"
                style='font-size:90%'
                >
                    <tr> 
                        <th>id</th>
                        <th>id cerberus</th> 
                        <th>Empresa</th>   
                        <th>Segmento</th>
                        <th>Departamento</th>
                        <th>nombre</th>        
                        <th>Perfil-Calculo</th>   
                        <th></th> 
                    </tr>
                    <tr v-for="empleado in paginaCollection" > 
                        <td>{{ empleado.id_empleado}}</td>
                        <td>{{ empleado.id_cerberus_empleado}}</td> 
                        <td>{{ empleado.empresa_observaciones }}</td>
                        <td>{{ empleado.segmento}}</td>  
                        <td>
                            <div  v-if="empleado.departamento.length>0">
                                {{ empleado.departamento}}
                            </div>
                            <div v-else></div>
                        </td> 
                        <td>{{ empleado.paterno + ' ' + empleado.materno  + ' ' + empleado.nombre }}</td>
                        <td>{{ empleado.perfilcalculo}}</td> 
                        <td style="width:250px" >
                            <button type="button" class="btn btn" @click="resetPassword(empleado.id_empleado)"><img src="../../img/synchronize.png" width="18px" /></button>
                            <button type="button" class="btn btn" @click="update_empleado(empleado.id_empleado)"><img src="../../img/ojo.png" width="18px" /></button>
                            <button type="button" class="btn btn" @click="delete_empleado(empleado.id_empleado)"><img src="../../img/borrar.png" width="18px" /></button>
                            <button type="button" class="btn btn-link"  @click="asingRols(empleado)">Rol</button> 
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>   
            
        <div v-if="isFormCrud" >  
            <div class='form-group'>
                <div class="row">
                    <div  class="col-sm">
                        <label>ID: {{ empleado.id_empleado }}</label>  
                    </div> 
                    <div class="col-sm">
                        <input type='checkbox' class='custom-control-input' id='empleadoactivo _id'   v-model='empleado.activo'  false-value='false' true-value='true' >
                        <label class='custom-control-label' for='empleadoactivo _id'  >activo</label>
                    </div> 
                    <div class='col-sm'>
                        <input type='checkbox' class='custom-control-input' id='empleadocorreo_verificado _id'   v-model='empleado.correo_verificado'  false-value='false' true-value='true' >
                        <label class='custom-control-label' for='empleadocorreo_verificado _id'  >correo verificado</label>
                    </div>  
                </div>
            </div> 

            <div class='form-group'>
                <label>segmento</label> 
                <select class='form-control' size='1'  v-model='empleado.id_segmento' >
                    <optgroup v-for="(empresa, i) in empresas" :label="empresa.empresa_observaciones" > 
                        <option v-if="empresa.id_empresa==rows.id_empresa" v-for='rows in segmentoCollection' v-bind:value='rows.id_segmento'>{{ rows.nombre }}</option>
                    </optgroup>
                </select>
            </div>   

            <div class='form-group'>
                <div class="row">
                    <div class="col-5">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" >Puesto</label>
                            </div>
                            <input class="form-control" type="search"  v-model="filtroPuesto" 
                                v-on:keyup ="buscarValorPuesto"
                                placeholder="Buscar Puesto" aria-label="Search"> 
                        </div>
                    </div>
                    <div class="col-7"> 
                        <select class='form-control' size='1'  v-model='empleado.ev_puesto_id' >    
                            <option value='null' >No asignado</option>
                            <option v-for='rows in ev_puestoCollectionFiltro' v-bind:value='rows.ev_puesto_id'>
                                {{ rows.nombre_puesto }} {{ rows.tipo }} ({{ rows.ev_nivel_p[0].nombre_nivel_puesto }})
                            </option>
                        </select>
                    </div>
                </div>
            </div>  
 
            <div class='form-group'>
                <div class="row">
                    <div class="col-sm">
                        <div class="input-group mb-3">
                            <label  class="input-group-text">nombre</label>
                            <input type='text' class='form-control' v-model='empleado.nombre' />
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="input-group mb-3">
                            <label  class="input-group-text">paterno</label>
                            <input type='text' class='form-control' v-model='empleado.paterno' />
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="input-group mb-3">
                            <label  class="input-group-text">materno</label>
                            <input type='text' class='form-control' v-model='empleado.materno' />
                        </div>
                    </div>
                </div>
            </div>  

            <div class='form-group'>
                <div class="row">
                    <div class="col-sm">
                        <div class="input-group mb-3">
                            <label  class="input-group-text" >celular</label>
                            <input type='number'  class='form-control' v-model='empleado.celular' />
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="input-group mb-3">
                            <label  class="input-group-text">correo</label>
                            <input type='email' class='form-control' v-model='empleado.correo' />
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="input-group mb-3">
                            <label  class="input-group-text">usuario</label>
                            <input type='text' class='form-control' v-model='empleado.usuario' />
                        </div>
                    </div>
                </div>
            </div>   
 
            <div class='form-group'>
                <div class="row">
                    <div class="col-sm">
                        <div class="input-group mb-3">
                            <label  class="input-group-text">Nacimiento</label>
                            <input type='date' class='form-control' v-model='empleado.fecha_nacimiento' />
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="input-group mb-3">
                            <label  class="input-group-text">nss</label>
                            <input type='text' class='form-control' v-model='empleado.nss' />
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="input-group mb-3">
                            <label  class="input-group-text">rfc</label>
                            <input type='text' class='form-control' v-model='empleado.rfc' />
                        </div>
                    </div>
                </div>
            </div>   

            <div class='form-group'>
                <div class="row">
                    <div class="col-sm">
                        <div class="input-group mb-3">
                            <label  class="input-group-text">Cerberus</label>
                            <input type='number' class='form-control' v-model='empleado.id_cerberus_empleado' />
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="input-group mb-3">
                            <label  class="input-group-text">CONTPAQi</label>
                            <input type='text' class='form-control' v-model='empleado.id_compac' />
                        </div>
                    </div>
                    <div class='col-sm'>
                        <div class="input-group mb-3">
                            <label  class="input-group-text">Alta Cerb</label>
                            <input type='date' class='form-control' v-model='empleado.fecha_alta_cerberus' />
                        </div>
                    </div>  
                    
                </div>
            </div>  

            <div class='form-group'>
            <div class="row">
                <div class='col-sm'>
                    <div class="input-group mb-3">
                        <label class="input-group-text">perfilcalculo</label>
                        <select class='form-control' v-model='empleado.perfilcalculo'>
                            <option value="Tabulador">Tabulador</option>
                            <option  value="Estadia">Estadia</option>
                            <option value="Destajista">Destajista</option>
                            <option value="X Horas">X Horas</option>
                        </select> 
                    </div>  
                </div>   

                <div class='col-sm'>
                    <div class="input-group mb-3">
                        <label class="input-group-text">Departamento</label>
                        <select class='form-control' v-model='empleado.departamento_id'>
                            <optgroup  v-for="(segmento, i) in segmentoCollection"
                                         :label="'(' + segmento.empresa[0].empresa_observaciones +') ' + segmento.nombre.replace(/\(([^)]*)\)/,'')" >
                                <option v-if="segmento.id_segmento==depa.id_segmento" 
                                        v-for='depa in departamentoCollection' 
                                        v-bind:value='depa.departamento_id'>
                                        {{depa.nombre}}
                                </option> 
                            </optgroup>  
                        </select> 
                    </div> 
                </div>   
            </div> 
            </div>  
            <div class="row">
                <div class='col-sm'>
                    <div class="input-group mb-3">
                        <label  class="input-group-text">Número zapato</label>
                        <select disabled class='form-control' size='1'  v-model='empleado.id_numero_zapato' >
                            <option value='0' >-</option>
                            <option v-for='row in un_tallaCollection' v-bind:value='row.id_talla'>{{ row.valor }}</option>
                        </select>
                    </div>  
                </div>  
                <div class='col-sm'>
                    <div class="input-group mb-3">
                        <label  class="input-group-text">Talla playera</label>
                        <select disabled class='form-control' size='1'  v-model='empleado.id_talla_playera' >
                            <option value='0' >-</option>
                            <option v-for='row in un_tallaCollection' v-bind:value='row.id_talla'>{{ row.valor }}</option>
                        </select>
                    </div>
                </div>      
            </div>  
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_empleado()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_empleado()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>   

        <!-- Modal Roles -->
        <div v-if="myModelRol" >  
                    <transition name="model" >
                    <div class="modal-mask" > 
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">{{ dynamicTitle }}</h4>
                                    <button type="button" class="close" @click="myModelRol=false"><span aria-hidden="true">&times;</span></button>
                                </div>  
                                <div class="modal-body"> 
                                    <div class="card-body">   
                                        <div class="custom-control custom-checkbox">
                                        <h5 >El Usuario cuenta con las siguientes roles:</h5>
                                        <div v-for="r in rols" >   
                                                <input style="margin-left:5px;" type='checkbox' v-model=r.selected  :id="'check_' + r.id_rol"  > <span>{{ r.rol }}</span>  
                                        </div> 
                                        </div>   
                                        <div align="center">
                                        <input type="hidden" v-model="empleado.id_empleado" />
                                        <input type="button" class="btn btn-success btn-xs" value="Guardar" :disabled='isDisabledSC' @click="saveRols()" />
                                        </div>
                                        </br> 
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                    </transition>
                </div>
            </div>
        <!-- Modal Roles --> 
    </div>



</div>
<script type="text/javascript" src="../../controllers/admin/c_empleado_1.js"></script>

  







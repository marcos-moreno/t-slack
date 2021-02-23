<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_empleado" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getempleados()"> filtrar</button>
                                </td>
                                <td class='form-group'>
                                    <input type='checkbox' class='custom-control-input' id='active_filter' @change="getempleados()"   v-model='activos_filter'  false-value=false true-value=true >
                                    <label class='custom-control-label' for='active_filter'  >activo</label>
                                </td>
                            </tr>
                            <tr> 
                                <td> 
                                    <select class='form-control'  v-model='empresa_id_filter' style="width:150px" @change="get_segmentos()" > 
                                        <option value='todo'>Todas las Empresas</option>
                                        <option v-for='rows in empresas' v-bind:value='rows.id_empresa'>{{ rows.empresa_observaciones }}</option>
                                    </select>
                                </td> 
                                <td>
                                    <select class='form-control'  v-model='segmento_id_filter' style="width:150px" @change="getempleados()" > 
                                        <option value='todo'>Todo los Segmentos</option>
                                        <option v-for='rows in segmentoCollection' v-bind:value='rows.id_segmento'>{{ rows.nombre }}</option>
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

        
        <br><br>
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
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id</th>
                        <th>id cerberus</th> 
                        <th>Empresa</th>   
                        <th>Segmento</th>
                        <th>nombre</th>        
                        <th>Perfil-Calculo</th>   
                        <th></th> 
                    </tr>
                    <tr v-for="empleado in paginaCollection" >
                        
                        <td>{{ empleado.id_empleado}}</td>
                        <td>{{ empleado.id_cerberus_empleado}}</td> 
                        <td>{{ empleado.empresa[0].empresa_observaciones }}</td>
                        <td>{{ empleado.segmento[0].nombre}}</td>
                        <td>{{ empleado.paterno + ' ' + empleado.materno  + ' ' + empleado.nombre }}</td>
                        <td>{{ empleado.perfilcalculo}}</td> 
                        <td style="width:250px" >
                            <button type="button" class="btn btn" @click="resetPassword(empleado.id_empleado)"><img src="../../img/synchronize.png" width="25px" /></button>
                            <button type="button" class="btn btn" @click="update_empleado(empleado.id_empleado)"><img src="../../img/ojo.png" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_empleado(empleado.id_empleado)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ empleado.id_empleado }}</label>  
            </div> 

            <div class='form-group'>
                <label>segmento</label> 
                <select class='form-control' size='1'  v-model='empleado.id_segmento' >
                    <option value='0' >-</option>
                    <option v-for='rows in segmentoCollection' v-bind:value='rows.id_segmento'>{{ rows.nombre }}</option>
                </select>
            </div>   

            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='empleado.nombre' />
            </div>  
            <div class='form-group'>
                <label>paterno</label>
                <input type='text' class='form-control' v-model='empleado.paterno' />
            </div>  
            <div class='form-group'>
                <label>materno</label>
                <input type='text' class='form-control' v-model='empleado.materno' />
            </div>   
           
            <div class='form-group'>
                <label>celular</label>
                <input type='number'  class='form-control' v-model='empleado.celular' />
            </div>  
            <div class='form-group'>
                <label>correo</label>
                <input type='email' class='form-control' v-model='empleado.correo' />
            </div>       
            <div class='form-group'>
                <label>usuario</label>
                <input type='text' class='form-control' v-model='empleado.usuario' />
            </div>  
            <!-- <div class='form-group'>
                <label>password</label>
                <input type='text' class='form-control' v-model='empleado.password' />
            </div>   -->
            <div class='form-group'>
                <label>fecha nacimiento</label>
                <input type='date' class='form-control' v-model='empleado.fecha_nacimiento' />
            </div>  
            <div class='form-group'>
                <label>nss</label>
                <input type='text' class='form-control' v-model='empleado.nss' />
            </div>  
            <div class='form-group'>
                <label>rfc</label>
                <input type='text' class='form-control' v-model='empleado.rfc' />
            </div>  
            <div class='form-group'>
                <label>ID cerberus empleado</label>
                <input type='number' class='form-control' v-model='empleado.id_cerberus_empleado' />
            </div>   
                                    <!-- <div class='form-group'>
                                        <label>talla playera</label> 
                                        <select class='form-control' size='1'  v-model='empleado.id_talla_playera' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in un_tallaCollection' v-bind:value='rows.id_talla'>{{ rows }}</option>
                                        </select>
                                    </div>  
                                    <div class='form-group'>
                                        <label>numero zapato</label> 
                                        <select class='form-control' size='1'  v-model='empleado.id_numero_zapato' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in un_tallaCollection' v-bind:value='rows.id_talla'>{{ rows }}</option>
                                        </select>
                                    </div>  -->
            <div class='form-group'>
                <label>fecha alta cerberus</label>
                <input type='date' class='form-control' v-model='empleado.fecha_alta_cerberus' />
            </div>  
            <div class='form-group'>
                <label>perfilcalculo</label>
                <select class='form-control' v-model='empleado.perfilcalculo'>
                    <option value="Tabulador">Tabulador</option>
                    <option  value="Estadia">Estadia</option>
                    <option value="Destajista">Destajista</option>
                    <option value="X Horas">X Horas</option>
                </select> 
            </div>
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='empleadoactivo _id'   v-model='empleado.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='empleadoactivo _id'  >activo</label>
            </div>  
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='empleadocorreo_verificado _id'   v-model='empleado.correo_verificado'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='empleadocorreo_verificado _id'  >correo verificado</label>
            </div>  
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_empleado()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_empleado()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/admin/c_empleado.js"></script>

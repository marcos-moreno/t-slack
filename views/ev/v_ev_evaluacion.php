<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_evaluacion" style="margin-top:15px;"> 
        <div v-if="is_evaluacion">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>
                        <td style="weight: 30%" v-if="isFormCrud==false">
                            <label>Filtrar</label>  
                            <table>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" v-model="filter" />
                                    </td>
                                    <td>
                                        <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_evaluacions()"> filtrar</button>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <div class="pre-scrollable" >
                                <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg}}</div>
                                <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg}}</div>
                                <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg}}</div>
                            </div> 
                        </td> 
                    </tr>
                </table> 
            </div><br>
            <div class="panel-body"  v-if="isFormCrud==false">
                <h4>Evaluación</h4>
                <br>
                <div class="table-responsive">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">  
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
                    <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_evaluacion()">Agregar Evaluación</button></td>
                    <table class="table table-bordered table-striped">
                        <tr> 
                            <th>Id</th>
                            <th>Líder</th>
                            <th>Período</th> 
                            <th>nombre</th>
                            <th></th>  
                        </tr>
                        <tr v-for="ev_evaluacion in paginaCollection" >
                            <td>{{ ev_evaluacion.ev_evaluacion_id}}</td>
                            <td>{{ ev_evaluacion.empleado[0].usuario}}</td>
                            <td>{{ ev_evaluacion.periodo[0].nombre_periodo }} ({{ formatDate(ev_evaluacion.periodo[0].inicio_periodo)}} Al {{ formatDate(ev_evaluacion.periodo[0].fin_periodo)}})</td>
                            <td>{{ ev_evaluacion.nombre}}</td>
                            <td>
                                <button type="button" class="btn-link btn" @click="display_line(ev_evaluacion)">Empleados</button>
                                <button type="button" class="btn btn" @click="update_ev_evaluacion(ev_evaluacion.ev_evaluacion_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                                <button type="button" class="btn btn" @click="delete_ev_evaluacion(ev_evaluacion.ev_evaluacion_id)"><img src="../../img/borrar.png" width="25px" /></button>
                            </td> 
                        </tr>
                    </table>
                    <br>
                    <br>
                </div>
            </div>  
            <div v-if="isFormCrud" >   
                <div class="form-group">
                    <label>ID: {{ ev_evaluacion.ev_evaluacion_id }}</label>  
                </div> 
                <div class='form-group'>
                    <label>Líder</label>  
                    <input class='form-control' disabled type='text'  :value='`${evaluador.nombre} ${evaluador.paterno} ${evaluador.materno}`' />
                </div>  
                <div class='form-group'>
                    <label>Período</label> 
                    <select class='form-control' size='1'  v-model='ev_evaluacion.periodo_id' >
                        <option value='0' >-</option>
                        <option v-for='rows in periodoCollection' v-bind:value='rows.periodo_id'>{{ rows.nombre_periodo }} ({{ formatDate(rows.inicio_periodo)}} Al {{ formatDate(rows.fin_periodo)}})</option>
                    </select>
                </div> 
                <div class='form-group'>
                    <label>Descripción</label>
                    <input type='text' class='form-control' v-model='ev_evaluacion.nombre' />
                </div>    
                <br>
                <br>
                <div class="form-group">
                    <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_evaluacion()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                    <button @click="save_ev_evaluacion()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
                </div>   
            </div>  
        </div> 
        <div v-if="is_evaluacion_ln">
            <h5>({{ev_evaluacion.ev_evaluacion_id}}) {{ev_evaluacion.nombre}}</h5> 
            <p>Evaluado por: {{ev_evaluacion.empleado[0].usuario}}</p>
            <button type="button" class="btn btn-link" @click="is_evaluacion_ln=false;is_evaluacion=true;" >Evaluaciones</button> 
                <div id="app_ev_evaluacion_ln" style="margin-top:15px;"> 
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td style="weight: 30%" v-if="isFormCrud_ln==false">
                                <label>Filtrar</label>  
                                <table>
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" v-model="filter_ln" />
                                        </td> 
                                        <td>
                                            <button type="button" name="filter_ln" class="btn btn-info btn-xs" @click="getev_evaluacion_lns()"> filtrar</button>
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
                <br>
                <div class="panel-body"  v-if="isFormCrud_ln==false">
                    <div class="table-responsive">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">  
                                <li>
                                    <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="numByPag_ln" @change="paginator_ln(1)" > 
                                        <option value=5  >5</option>
                                        <option value=10 >10</option>
                                        <option value=15 >15</option>
                                        <option value=20 >20</option>
                                    </select>
                                </li>
                                <li v-for="li in paginas_ln" class="page-item">
                                    <a class="page-link" @click="paginator_ln(li.element)" >{{ li.element }} <div v-if="li.element == paginaActual_ln" >_</div></a> 
                                </li>
                            </ul>  
                        </nav>
                        <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_evaluacion_ln()">Agregar empleado</button></td>
                        <table class="table table-bordered table-striped">
                            <tr> 
                                <th>Id.</th>
                                <th>Empleado</th> 
                                <th>Puesto</th> 
                                <th>Calificación</th> 
                                <th>Estado</th>  
                                <th></th> 
                            </tr>
                            <tr v-for="ev_evaluacion_ln in paginaCollection_ln" >
                                <td>{{ ev_evaluacion_ln.ev_evaluacion_ln_id}}</td> 
                                <td>({{ ev_evaluacion_ln.empleado[0].id_empleado}}) {{ ev_evaluacion_ln.empleado[0].paterno}} {{ ev_evaluacion_ln.empleado[0].materno}} {{ ev_evaluacion_ln.empleado[0].nombre}}</td>
                                <td>({{ ev_evaluacion_ln.ev_puesto[0].codigo}}) {{ ev_evaluacion_ln.ev_puesto[0].nombre_puesto}}</td>
                                <td>{{ ev_evaluacion_ln.calificacion}}</td>
                                <td>({{ ev_evaluacion_ln.estado[0].value}}) {{ ev_evaluacion_ln.estado[0].descripcion}}</td>
                                <td style="width:150px" >
                                    <button type="button" class="btn-link btn" @click="show_indicadores(ev_evaluacion_ln)">Indicadores</button>
                                    <button type="button" class="btn btn" @click="delete_ev_evaluacion_ln(ev_evaluacion_ln.ev_evaluacion_ln_id)"><img src="../../img/borrar.png" width="25px" /></button>
                                </td> 
                            </tr>
                        </table>
                        <br>
                        <br>
                    </div>
                </div>  
                <div v-if="isFormCrud_ln" >   
                    <div class="form-group">
                        <label>ID: {{ ev_evaluacion_ln.ev_evaluacion_ln_id }}</label>  
                    </div>  
                    <!-- <div class='form-group'>
                        <label>Empleado</label> 
                        <select @change="seleccionEmpleado()" class='form-control' size='1'  v-model='ev_evaluacion_ln.id_empleado' >
                            <option value='0' >-</option>
                            <option v-for='rows in empleadoCollection' v-bind:value='rows.id_empleado'>
                                ({{ rows.id_empleado }}) {{ rows.paterno }} {{ rows.materno }} {{ rows.nombre }}
                            </option>
                        </select>
                    </div>   -->
                    <div class='form-group'>
                        <div class="row">
                            <div class="col-5">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" >Empleado</label>
                                    </div>
                                    <input class="form-control" type="search"  v-model="filtroEmpleado" 
                                        v-on:keyup ="buscarValorEmpleado"
                                        placeholder="Buscar Empleado" aria-label="Search"> 
                                </div>
                            </div>
                            <div class="col-7">
                                <select @change="seleccionEmpleado()" class='form-control' size='1'  v-model='ev_evaluacion_ln.id_empleado' >
                                <!-- v-if="depa.id_segmento==segmento_id_filter" -->
                                        <optgroup v-for="(depa, i) in departamentos" :label="depa.nombre" >  
                                            <option v-for='rows in empleadoCollectionfiltro' 
                                                v-if="rows.departamento_id==depa.departamento_id" 
                                                v-bind:value='rows.id_empleado'>
                                                    {{ rows.paterno }} {{ rows.materno }} {{ rows.nombre }}
                                            </option>
                                        </optgroup>
                                </select>
                            </div>
                        </div>
                    </div> 
                    <div class='form-group'>
                        <label>Puesto</label>   
                        <div v-if="empleadoByln.id_empleado > 0">
                            <input disabled type='text' class='form-control' v-if="empleadoByln.ev_puesto.length > 0"
                            :value='empleadoByln.ev_puesto[0].ev_puesto_id + " - (" +
                            empleadoByln.ev_puesto[0].codigo + ") " + empleadoByln.ev_puesto[0].nombre_puesto + 
                            " " + (empleadoByln.ev_puesto[0].tipo!=null?empleadoByln.ev_puesto[0].tipo:"")' />
                        </div>
                    </div> 
                    <div class='row'>
                        <div class="col-sm">
                            <label>Calificación</label>
                            <input disabled type='number' class='form-control' v-model='ev_evaluacion_ln.calificacion' />
                        </div>  
                        <div class="col-sm">
                            <label>Estado de la evaluación</label> 
                            <select class='form-control' size='1'  v-model='ev_evaluacion_ln.estado_atributo' >
                                <option value='0' >-</option>
                                <option v-for='rows in estados' v-bind:value='rows.id_atributo'>
                                    ({{ rows.value }}) {{ rows.descripcion }}
                                </option>
                            </select>
                        </div>  
                    </div><br><br>
                    <div class="form-group">
                        <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_evaluacion_ln()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                        <button @click="save_ev_evaluacion_ln()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
                    </div>   
                </div>  
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_evaluacion.js"></script>

<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_cumplimiento_obj" style="margin-top:15px;"> 
        <h4>Cumplimiento de Objetivos</h4>
      
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>   
                        <td>
                            <div class="pre-scrollable" >
                                <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg}}</div>
                                <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg}}</div>
                                <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg}}</div>
                            </div> 
                        </td> 
                    </tr>
                </table>                   
                
            
                        
                
               
            </div> 

            <div class="panel-body" v-if="tabla">
                <div class="col-6">
                    <div class="input-group mb-3">
                        <button type="button" class="btn btn-info btn-xs edit" @click="abre()">Agregar</button>
                    </div>
                </div> 
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
                    <table class="table table-bordered table-striped">
                        <tr> 
                            <th>ID</th>
                            <th>Indicador</th>
                            <th>Empleado</th>
                            <th>EstadoIndicador</th>
                            <th>Nombre del Objetivo</th>
                            <th></th> 
                        </tr>
                        <tr v-for="contenido in paginaCollection" >
                            <td>{{ contenido.ev_cumplimiento_obj_id}}</td>
                            <td>{{ contenido.nombre}}</td>
                            <td>({{ contenido.id_empleado }} ) {{ contenido.fullname}}</td>
                            <td>{{ contenido.estado}}</td>
                            <td>{{ contenido.nombre_objetivo }}</td>
                            <td style="width:150px" >
                                <button type="button" class="btn btn" @click="update_ev_cumpli(contenido.ev_cumplimiento_obj_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                                <button type="button" class="btn btn" @click="delete_ev_cumpli(contenido.ev_cumplimiento_obj_id)"><img src="../../img/borrar.png" width="25px" /></button>
                            </td> 
                        </tr>
                    </table>
                    <br>
                    <br>
                </div>
            </div>
            
            <div v-if="tabla==false">
                ID: {{ ev_cumplimiento.ev_cumplimiento_obj_id }}
               
                    <div class="container" >
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" >Empleados</label>
                                    </div>
                                    <select class='form-control' size='1' @change="get_empleadoFilter()"  v-model='ev_cumplimiento.id_empleado' >
                                        <optgroup v-for="(depa, i) in departamentos" :label="depa.nombre" >  
                                            <option v-for='rows in empleadoCollectionfiltro' v-if="rows.departamento_id==depa.departamento_id" v-bind:value='rows.id_empleado' >
                                                {{ rows.paterno }} {{ rows.materno }} {{ rows.nombre }}
                                            </option>
                                        </optgroup>
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>

                 

                    <div class="container" >
                        
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" >Indicador</label>
                                    </div>
                                    <select class='form-control'  style="width:150px" @change="get_estado()" v-model='ev_cumplimiento.id_indicador'  > 
                                        <option > ------</option>
                                        <option v-for='rows in indicador' v-bind:value='rows.ev_indicador_general_id'>{{ rows.nombre }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div><br><br><br>

                    <div class="container" >
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" >Fecha Inicio</label>
                                        </div>
                                        <input class="form-control" type="datetime-local"  v-model='ev_cumplimiento.fechainicio'> 
                                    </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" >Fecha Termino</label>
                                    </div>
                                    <input class="form-control" type="datetime-local" v-model='ev_cumplimiento.fechatermino'> 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container" >
                        <div class="form-group">
                            <div class="row">
                                <div class="col-5">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" >Objetivo</label>
                                        </div>
                                        <input class="form-control" type="text" v-model='ev_cumplimiento.nombre_objetivo'> 
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" >Descripci&oacute;n</label>
                                        </div>
                                        <input class="form-control" type="text" v-model='ev_cumplimiento.descripcion'> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" >Estado</label>
                                        </div>
                                        <select class='form-control'  style="width:150px"   v-model='ev_cumplimiento.estado'> 
                                            <option v-for='rows in estados' v-bind:value='rows.value'> ({{ rows.value }}) {{ rows.descripcion }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div v-show="select2">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" >Estado</label>
                                        </div>
                                        <select class='form-control' style="width:150px"   v-model='ev_cumplimiento.estado'> 
                                            <option value='NS-NO seleccionado '>--------</option>
                                            <option>(NE) Negociaci&oacute;n &Eacute;xitosa</option>
                                            <option>(NNE) Negociaci&oacute;n no &Eacute;xitosa</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                
                    <div class="form-group" >
                        <td>
                            <button type="button" class="btn btn btn-xs" @click="cancel_ev_cumpli()"><img src="../../img/regresar.png" width="28px" /> Cancelar</button>
                        </td> 
                            <button @click="save()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
                    </div>
                 
                
            </div>
        


        
        </div>        
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_cumplimiento_obj.js"></script>

<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_reporte" style="margin-top:15px;"> 
    <h4>Reportes</h4>
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
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" >Empresa</label>
                            </div>
                            <select class='form-control'  v-model='empresa_id_filter' style="width:150px" @change="get_segmentosFilter()" > 
                                <option value='0'>Todas las Empresas</option>
                                <option v-for='rows in empresas' v-bind:value='rows.id_empresa'>{{ rows.empresa_observaciones }}</option>
                            </select> 
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" >Segmentos</label>
                            </div>
                            <select class='form-control'  v-model='segmento_id_filter' style="width:150px" @change="buscarValorEmpleado()" > 
                                <option value='0'>Todo los Segmentos</option>
                                <option v-for='rows in segmentoFilterCollection' v-bind:value='rows.id_segmento'>{{ rows.nombre }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>  

        <div class='form-group'>
            <div class="row">
                <div class="col-5">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" >Empleado</label>
                        </div>
                        <input class="form-control" type="search"  v-model="filtroLider" 
                            v-on:keyup ="buscarValorEmpleado"
                            placeholder="Buscar Empleado" aria-label="Search"> 
                    </div>
                </div>
                <div class="col-7">
                    <select @change="getev_reportes()" class='form-control' size='1'  v-model='empleadoSelected_id' >
                            <optgroup v-if="depa.id_segmento==segmento_id_filter" 
                                        v-for="(depa, i) in depas" :label="depa.nombre" >  
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

        <div class="panel-body"  v-if="isFormCrud==false && empleadoSelected_id > 0"> 
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_reporte()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th>
                        <th>Descripcion</th>
                        <th>Fecha</th>
                        <th>Indicador</th>
                        <th></th> 
                    </tr>
                    <tr v-for="ev_reporte in paginaCollection" >
                        <td>{{ ev_reporte.ev_reporte_id}}</td>
                        <td>{{ ev_reporte.descripcion}}</td>
                        <td>{{ ev_reporte.fecha}}</td>
                        <td>{{ ev_reporte.nombre_indicador}}</td> 
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_reporte(ev_reporte.ev_reporte_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_reporte(ev_reporte.ev_reporte_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <button  type="button" class="close" @click="adjunto_dialog=true">
                <img src="../../img/adjuntar.svg" width="7%" />
                Adjuntar Evidencia
            </button>
            <button  type="button" class="close" @click="getfiles_adjuntos()">
                <img src="../../img/evidencias.svg" width="7%" />
                Evidencias
            </button>
            <div class="form-group">
                <label>ID: {{ ev_reporte.ev_reporte_id }}</label>  
            </div>
         
            <div class='form-group'>
                <label>Fecha</label>{{ev_reporte.fecha}}
                <input type='date' class='form-control' v-model='ev_reporte.fecha' />
            </div>    
            <div class='form-group'>
                <label>Indicador</label> 
                <select class='form-control' size='1'  v-model='ev_reporte.ev_indicador_puesto_id' >
                    <option value='0' >-</option>
                    <option v-for='rows in ev_indicador_puestoCollection' v-bind:value='rows.ev_indicador_id'>
                        {{ rows.ev_indicador_general[0].nombre }}
                    </option>
                </select>
            </div>  
            <div class='form-group'>
                <label >descripcion</label>
                <textarea class='form-control' v-model='ev_reporte.descripcion' >  </textarea>
            </div>  
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_reporte()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_reporte()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div> 
 
            <div v-if="adjunto_dialog" >  
                <transition name="model" >
                    <div class="modal-mask" > 
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <p class="alert alert-warning" class="modal-title">
                                        Adjuntar Evidencia  
                                    </p>
                                    <div>
                                        <button  type="button" class="close" @click="adjunto_dialog=false">
                                        <span aria-hidden="true">&times;</span></button>
                                    </div>
                                </div>  
                                <div class="modal-body"> 
                                    <div class="card-body">   
                                        <div class="custom-control custom-checkbox">
                                            <div class='form-group'>
                                                <label>Archivo imagen,pdf,word,excel</label>
                                                <input type='file' class='form-control' id="file" 
                                                multiple="multiple" 
                                                accept="application/pdf,.doc,.docx,.xls,.xlsx,image/png,image/jpeg,image/jpg"  />
                                            </div>    
                                            <div align="center"> 
                                                <input type="button" @click="save_file()" 
                                                class="btn btn-success btn-xs" value="Guardar" />
                                            </div>
                                        </div>  
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </transition>
            </div>  
        </div>  

        <div v-if="view_adjunto_dialog" >  
            <transition name="model" > 
                <div class="modal-mask" > 
                    <div  class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <p class="modal-title">
                                    Evidencias del reporte
                                </p> 
                                <div>
                                    <button  type="button" class="close" @click="view_adjunto_dialog=false">
                                    <span aria-hidden="true">&times;</span></button>
                                </div>
                            </div>  
                            <div class="modal-body"> 
                                <div class="card-body">   
                                    <div class="modal-body"> 
                                        <ul class="list-group">
                                            <li v-for="item in files_adjuntos" 
                                            class="list-group-item d-flex justify-content-between align-items-center">
                                                <button
                                                    style="background:none;color:blue;border:none
                                                    ;max-width:30px
                                                    "
                                                    @click="get_file(item)"
                                                >{{ item.name.substring(0,50) }}</button>  

                                                 <button
                                                    style="background:none;color:blue;border:none"
                                                    @click="delete_file(item.id_file_adjunto)"
                                                > 
                                                    <img src="../../img/borrar.png" width="28px" />
                                                </button>   
                                            </li>
                                        </ul>
                                    </div> 
                                </div>
                            </div>
                        </div> 
                    </div>
                </div> 
            </transition>
        </div>

        <div v-if="load_dialog" >  
            <transition name="model" > 
                <div class="modal-mask" > 
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <p class="modal-title">
                                    Cargando Archivos, por favor se paciente...
                                </p> 
                            </div>  
                            <div class="modal-body"> 
                                <div class="card-body">   
                                    <div class="modal-body">
                                        <img width="400"  src="../../img/progress.gif">
                                    </div> 
                                </div>
                            </div>
                        </div> 
                    </div>
                </div> 
            </transition>
        </div>

      


        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/ev/c_ev_reporte.js"></script>

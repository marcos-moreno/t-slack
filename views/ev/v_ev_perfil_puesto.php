<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_perfil_puesto" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_perfil_puestos()"> filtrar</button>
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
        </div> 
        <br>
        <div class="panel-body"  v-if="isFormCrud==false">
            <h4>Perfil Puesto</h4>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_perfil_puesto()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>Id</th>
                        <th>Código</th>
                        <th>genero</th>
                        <th>puesto</th>
                        <th>tabulador mínimo</th>
                        <th>tabulador máximo</th> 
                        <th>Sueldo Promedio</th>
                        <!-- <th>media salarial mes</th> -->
                        <!-- <th>media salarial zona</th> -->
                        <th>nivel estudios</th>
                        <th>idioma</th>
                        <th></th> 
                    </tr>
                    <tr v-for="ev_perfil_puesto in paginaCollection" >
                        <td>{{ ev_perfil_puesto.ev_perfil_puesto_id}}</td>
                        <td>{{ ev_perfil_puesto.ev_puesto[0].codigo}}</td>
                        <td>{{ ev_perfil_puesto.genero[0].value}}</td>
                        <td>{{ ev_perfil_puesto.ev_puesto[0].nombre_puesto}}</td>
                        <td>{{ ev_perfil_puesto.tabulador_minimo[0].tabulador}} {{ formatMXN(ev_perfil_puesto.tabulador_minimo[0].sueldo)}}</td>
                        <td>{{ ev_perfil_puesto.tabulador_maximo[0].tabulador}} {{ formatMXN(ev_perfil_puesto.tabulador_maximo[0].sueldo)}}</td>
                        <td>{{ ev_perfil_puesto.sueldo_promedio}}</td>
                        <!-- <td>{{ formatMXN(ev_perfil_puesto.media_salarial_mes)}}</td> -->
                        <!-- <td>{{ formatMXN(ev_perfil_puesto.media_salarial_zona)}}</td> -->
                        <td>{{ ev_perfil_puesto.nivel_estudios[0].value}}</td>
                        <td>{{ ev_perfil_puesto.idioma[0].value}}</td>
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_perfil_puesto(ev_perfil_puesto.ev_perfil_puesto_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_perfil_puesto(ev_perfil_puesto.ev_perfil_puesto_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ev_perfil_puesto.ev_perfil_puesto_id }}</label>  
            </div> 
            <div class="row"> 
                <div class="col-sm-8">
                    <label>Puesto</label> 
                    <select class='form-control'  size='1'  v-model='ev_perfil_puesto.ev_puesto_id' >
                        <option value='0' >-</option>
                        <option v-for='rows in ev_puestoCollection' v-bind:value='rows.ev_puesto_id'>{{ rows.codigo }}_{{ rows.nombre_puesto }} {{ rows.tipo }} ({{ rows.ev_nivel_p[0].nombre_nivel_puesto }})</option>
                    </select>
                </div>    
                <div class="col-sm-2">
                    <label>Edad mínima</label>
                    <input type='number' class='form-control' v-model='ev_perfil_puesto.edad_minima' />
                </div>
                <div class="col-sm-2">
                    <label>Edad máxima</label>
                    <input type='number' class='form-control' v-model='ev_perfil_puesto.edad_maxima' />
                </div> 
            </div>
            <br>
            <div class="row">
                <div class="col-sm">
                    <label>Genero</label> 
                    <select class='form-control' size='1'  v-model='ev_perfil_puesto.genero_atributo' >
                        <option value='0' >-</option>
                        <option v-for='rows in ev_atr_genero_Collection' v-bind:value='rows.id_atributo'>{{ rows.value }}</option>
                    </select>
                </div>
                <div class="col-sm">
                    <label>Estado civil</label> 
                    <select class='form-control' size='1'  v-model='ev_perfil_puesto.estado_civil_atributo' >
                        <option value='0' >-</option>
                        <option v-for='rows in ev_atr_estado_civilCollection' v-bind:value='rows.id_atributo'>{{ rows.value }}</option>
                    </select>
                </div>  

                <div class="col-sm">
                    <label>Mínimo experiencia años</label>
                    <input type='number' class='form-control' v-model='ev_perfil_puesto.minimo_experiencia_anios' />
                </div>  
                <div class="col-sm">
                    <label>Máximo experiencia meses</label>
                    <input type='number' class='form-control' v-model='ev_perfil_puesto.minimo_experiencia_meses' />
                </div>  

            </div>  
            <br>
            <div class="row"> 
                <div class="col-sm">
                    <label>Tabulador mínimo</label>  
                    <select @change="calcularPromedios()" class='form-control' size='1'  v-model='ev_perfil_puesto.ev_tabulador_id_minimo' >
                        <option value='0' >-</option>
                        <option v-for='rows in tabuladorCollection' v-bind:value='rows.id_tabulador'>{{ rows.tabulador }} - {{ formatMXN(rows.sueldo) }} ({{ rows.ev_nivel_p[0].nombre_nivel_puesto }})</option>
                    </select>
                </div>  
                <div class="col-sm">
                    <label>Tabulador máximo</label> 
                    <select @change="calcularPromedios()"  class='form-control' size='1'  v-model='ev_perfil_puesto.ev_tabulador_id_maximo' >
                        <option value='0' >-</option>
                        <option v-for='rows in tabuladorCollection' v-bind:value='rows.id_tabulador'>
                            {{ rows.tabulador }} - {{ formatMXN(rows.sueldo) }} ({{ rows.ev_nivel_p[0].nombre_nivel_puesto }})
                        </option>
                    </select>
                </div>   
            </div> 
            <br>
            <div class="row">   
                <div class="col-sm">
                    <label>Sueldo promedio</label>
                    <input disabled type='text' class='form-control' v-model='ev_perfil_puesto.sueldo_promedio' />
                </div>  
                <div class="col-sm">
                    <label>Media salarial(mes)</label>
                    <input type='number' v-on:keyup ="calcularMediaSalarialZona()" class='form-control' v-model='ev_perfil_puesto.media_salarial_mes' />
                </div>  
                <div class="col-sm">
                    <label>Media salarial(zona)</label>
                    <input disabled type='text' class='form-control' v-model='ev_perfil_puesto.media_salarial_zona' />
                </div>  
            </div>
            <br> 
            <div class="row">   
                <div class="col-sm">
                    <label>Nivel estudios</label> 
                    <select class='form-control' size='1'  v-model='ev_perfil_puesto.nivel_estudios_atributo' >
                        <option value='0' >-</option>
                        <option v-for='rows in ev_atrnivel_estudioCollection' v-bind:value='rows.id_atributo'>{{ rows.value }}</option>
                    </select>
                </div>  
                <div class="col-sm">
                    <label>Idioma</label> 
                    <select class='form-control' size='1'  v-model='ev_perfil_puesto.idioma_atributo' >
                        <option value='0' >-</option>
                        <option v-for='rows in ev_atr_idiomaCollection' v-bind:value='rows.id_atributo'>{{ rows.value }}</option>
                    </select>
                </div> 
                <div class="col-sm">
                    <label>Grado de avance</label> 
                    <select class='form-control' size='1'  v-model='ev_perfil_puesto.grado_avance_atributo' >
                        <option value='0' >-</option>
                        <option v-for='rows in ev_atr_grado_avanceCollection' v-bind:value='rows.id_atributo'>{{ rows.value }}</option>
                    </select>
                </div>
            </div> 
            <br>
            <div class='form-group'>
                <label>Áreas de conocimiento</label>
                <textarea type='text' class='form-control' v-model='ev_perfil_puesto.areas_conocimiento'></textarea>
            </div>  
            <div class='form-group'>
                <label>Áreas de experiencia</label>
                <textarea type='text' class='form-control' v-model='ev_perfil_puesto.areas_experiencia'></textarea>
            </div>  
            <div class='form-group'>
                <label>Conocimientos específicos</label>
                <textarea type='text' class='form-control' v-model='ev_perfil_puesto.conocimientos_especificos'></textarea>
            </div>  
            <div class='form-group'>
                <label>Equipo,software y/o herramientas</label>
                <textarea type='text' class='form-control' v-model='ev_perfil_puesto.equipo_software_herramientas'></textarea>
            </div>    
            
            <div class='form-group'>
                <label>Competencias</label>
                <textarea type='text' class='form-control' v-model='ev_perfil_puesto.competencias'></textarea>
            </div>  
            <div class='form-group'>
                <label>Aptitudes</label>
                <textarea type='text' class='form-control' v-model='ev_perfil_puesto.aptitudes'></textarea>
            </div>  
            <div class='form-group'>
                <label>Observaciones adicionales</label>
                <textarea type='text' class='form-control' v-model='ev_perfil_puesto.observaciones_adicionales'></textarea>
            </div>  
            <div class='form-group'>
                <label>Actividades del puesto</label>
                <textarea type='text' class='form-control' v-model='ev_perfil_puesto.actividades_puesto'></textarea>
            </div> 
           
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_perfil_puesto()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_perfil_puesto()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_perfil_puesto.js"></script>
<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_puesto" style="margin-top:15px;"> 
 
        <div v-if="modalPerfil">
        <transition name="model">
        <div class="modal-mask"> 
          <div  class="modal-dialog modal-dialog-scrollable" >
            <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">{{titleModalPerfil}}</h4> 
              <button type="button" class="close" @click="modalPerfil=false"><span aria-hidden="true">&times;</span></button>
                </div>  
                    <div class="modal-body"> 
                        <table border="1">
                            <tr> <td>GÉNERO:</td><td>{{perfil.genero[0].value}}</td></tr>
                            <tr><td>EDAD MÍNIMA:</td><td>{{perfil.edad_minima}}</td></tr>
                            <tr><td>EDAD MÁXIMA:</td><td>{{perfil.edad_maxima}}</td></tr>
                            <tr><td>ESTADO CIVIL:</td><td>{{perfil.estado_civil[0].value}}</td></tr>
                            <tr><td>NIVEL DE ESTUDIOS<br>MÍNIMO REQUERIDO:</td><td>{{perfil.nivel_estudios[0].value}}</td></tr>
                            <tr><td>GRADO DE AVANCE:</td><td>{{perfil.grado_avance[0].value}}</td></tr> 
                            <tr><td>ÁREAS DE CONOCIMIENTO<br> O ESPECIALIDAD:</td><td>{{perfil.areas_conocimiento}}</td></tr>
                            <tr><td>MÍNIMO EXPERIENCIA (AÑOS):</td><td>{{perfil.minimo_experiencia_anios}}</td></tr> 
                            <tr><td>MÍNIMO EXPERIENCIA (MESES):</td><td>{{perfil.minimo_experiencia_meses}}</td></tr> 
                            <tr><td>ÁREAS DE EXPERIENCIA:</td><td>{{perfil.observaciones_adicionales}}</td></tr> 
                            <tr><td>CONOCIMIENTOS ESPECÍFICOS:</td><td>{{perfil.conocimientos_especificos}}</td></tr> 
                            <tr><td>IDIOMAS:</td><td>{{perfil.idioma[0].value}}</td></tr> 
                            <tr><td>EQUIPO, SOFTWARE<br> Y/O HERRAMIENTA:</td><td>{{perfil.equipo_software_herramientas}}</td></tr> 
                            <tr><td>SUELDO MÍNIMO:</td><td>{{perfil.tabulador_minimo[0].tabulador}} {{formatMXN(perfil.tabulador_minimo[0].sueldo)}}</td></tr> 
                            <tr><td>SUELDO MÁXIMO:</td><td>{{perfil.tabulador_maximo[0].tabulador}} {{formatMXN(perfil.tabulador_maximo[0].sueldo)}}</td></tr> 
                            <tr><td>SUELDO PROMEDIO:</td><td>{{formatMXN(perfil.sueldo_promedio)}}</td></tr> 
                            <tr><td>MEDIA SALARIAL (MES):</td><td>{{formatMXN(perfil.media_salarial_mes)}}</td></tr> 
                            <tr><td>MEDIA SALARIAL (ZONA):</td><td>{{formatMXN(perfil.media_salarial_zona)}}</td></tr> 
                            <tr><td>COMPETENCIAS:</td><td>{{perfil.competencias}}</td></tr> 
                            <tr><td>APTITUDES:</td><td>{{perfil.aptitudes}}</td></tr> 
                            <tr><td>OBSERVACIONES ADICIONALES:</td><td>{{perfil.observaciones_adicionales}}</td></tr> 
                            <tr><td>ACTIVIDADES DEL PUESTO:</td><td>{{perfil.actitudes_puesto}}</td></tr> 
                        </table>   
                    </div> 
                </div>
            </div>
        </div>
        </transition>
      </div>


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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_puestos()"> filtrar</button>
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
            <h4>Puesto</h4>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_puesto()">Agregar</button></td>
                <td><a type="button" class="btn btn-secondary btn-xs edit" href="v_ev_nivel_p.php">Niveles</a></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ev_puesto_id</th>
                        <th>codigo</th> 
                        <th>nombre_puesto</th> 
                        <th>tipo</th>
                        <th>nivel</th>
                        <th></th> 
                        <th></th> 
                    </tr>
                    <tr v-for="ev_puesto in paginaCollection" >
                        
                        <td>{{ ev_puesto.ev_puesto_id}}</td>
                        <td>{{ ev_puesto.codigo}}</td>
                        <td>{{ ev_puesto.nombre_puesto}}</td> 
                        <td>{{ ev_puesto.tipo}}</td>
                        <td>{{ ev_puesto.ev_nivel_p[0].nombre_nivel_puesto}}</td>
                        <td><button type="button" class="btn btn-link" @click="getperfil(ev_puesto)">Perfil</button></td>
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_puesto(ev_puesto.ev_puesto_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_puesto(ev_puesto.ev_puesto_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ev_puesto.ev_puesto_id }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre puesto</label>
                <input type='text' class='form-control' v-model='ev_puesto.nombre_puesto' />
            </div>  
            <div class='form-group'>
                <label>decripcion puesto</label>
                <input type='text' class='form-control' v-model='ev_puesto.decripcion_puesto' />
            </div>  
            <div class='form-group'>
                <label>codigo</label>
                <input type='text' class='form-control' v-model='ev_puesto.codigo' />
            </div>  
            <div class='form-group'>
                <label>tipo</label>
                <input type='text' class='form-control' v-model='ev_puesto.tipo' />
            </div>   
            <div class='form-group'>
                <label>nivel</label> 
                <select class='form-control' size='1'  v-model='ev_puesto.ev_nivel_p_id' >
                    <option value='0' >-</option>
                    <option v-for='rows in ev_nivel_pCollection' v-bind:value='rows.ev_nivel_p_id'>{{ rows.nombre_nivel_puesto }}</option>
                </select>
            </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_puesto()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_puesto()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/ev/c_ev_puesto.js"></script>

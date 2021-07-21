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
        <div class="panel-body"  v-if="isFormCrud==false">
            <h4>ev_perfil_puesto</h4>
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
                        <th>ev_perfil_puesto_id</th>
                                    
                        <th>genero</th>
                                    
                        <th>edad minima</th>
                                    
                        <th>edad maxima</th>
                                    
                        <th>estado civil</th>
                                    
                        <th>grado_avance</th>
                                    
                        <th>areas conocimiento</th>
                                    
                        <th>minimo experiencia años</th>
                                    
                        <th>minimo experiencia meses</th>
                                    
                        <th>areas experiencia</th>
                                    
                        <th>conocimientos especificos</th>
                                    
                        <th>equipo software herramientas</th>
                                    
                        <th>tabulador mínimo</th>
                                    
                        <th>tabulador máximo</th>
                                    
                        <th>sueldo promedio</th>
                                    
                        <th>media salarial mes</th>
                                    
                        <th>media salarial zona</th>
                                    
                        <th>competencias</th>
                                    
                        <th>aptitudes</th>

                        <th>puesto</th>
                                    
                        <th>observaciones adicionales</th>
                                    
                        <th>actitudes puesto</th>
                                    
                        <th>nivel estudios</th>
                                    
                        <th>idioma</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="ev_perfil_puesto in paginaCollection" >
                        <td>{{ ev_perfil_puesto.ev_puesto_id}}</td>
                        
                        <td>{{ ev_perfil_puesto.ev_perfil_puesto_id}}</td>
            
                        <td>{{ ev_perfil_puesto.genero_atributo}}</td>
            
                        <td>{{ ev_perfil_puesto.edad_minima}}</td>
            
                        <td>{{ ev_perfil_puesto.edad_maxima}}</td>
            
                        <td>{{ ev_perfil_puesto.estado_civil_atributo}}</td>
            
                        <td>{{ ev_perfil_puesto.grado_avance_atributo}}</td>
            
                        <td>{{ ev_perfil_puesto.areas_conocimiento}}</td>
            
                        <td>{{ ev_perfil_puesto.minimo_experiencia_anios}}</td>
            
                        <td>{{ ev_perfil_puesto.minimo_experiencia_meses}}</td>
            
                        <td>{{ ev_perfil_puesto.areas_experiencia}}</td>
            
                        <td>{{ ev_perfil_puesto.conocimientos_especificos}}</td>
            
                        <td>{{ ev_perfil_puesto.equipo_software_herramientas}}</td>
            
                        <td>{{ ev_perfil_puesto.ev_tabulador_id_minimo}}</td>
            
                        <td>{{ ev_perfil_puesto.ev_tabulador_id_maximo}}</td>
            
                        <td>{{ ev_perfil_puesto.sueldo_promedio}}</td>
            
                        <td>{{ ev_perfil_puesto.media_salarial_mes}}</td>
            
                        <td>{{ ev_perfil_puesto.media_salarial_zona}}</td>
            
                        <td>{{ ev_perfil_puesto.competencias}}</td>
            
                        <td>{{ ev_perfil_puesto.aptitudes}}</td>
            
                        <td>{{ ev_perfil_puesto.observaciones_adicionales}}</td>
            
                        <td>{{ ev_perfil_puesto.actitudes_puesto}}</td>
            
                        <td>{{ ev_perfil_puesto.nivel_estudios_atributo}}</td>
            
                        <td>{{ ev_perfil_puesto.idioma_atributo}}</td>
               
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
                                  

                                    <!-- <div class="container"> -->
  <div class="row">

    <div class="col-sm">
        <label>puesto</label> 
        <select class='form-control' size='1'  v-model='ev_perfil_puesto.ev_puesto_id' >
            <option value='0' >-</option>
            <option v-for='rows in ev_puestoCollection' v-bind:value='rows.ev_puesto_id'>{{ rows.nombre_puesto }}</option>
        </select>
    </div>   

    <div class="col-sm">
        <label>genero</label> 
        <select class='form-control' size='1'  v-model='ev_perfil_puesto.genero_atributo' >
            <option value='0' >-</option>
            <option v-for='rows in ev_atr_genero_Collection' v-bind:value='rows.id_atributo'>{{ rows.value }}</option>
        </select>
    </div>
    <div class="col-sm">
        <label>edad mínima</label>
        <input type='number' class='form-control' v-model='ev_perfil_puesto.edad_minima' />
    </div>
    <div class="col-sm">
        <label>edad máxima</label>
        <input type='number' class='form-control' v-model='ev_perfil_puesto.edad_maxima' />
    </div> 
  </div>
<!-- </div>  -->


 

    <div class="row">
            <div class="col-sm">
                <label>estado civil</label> 
                <select class='form-control' size='1'  v-model='ev_perfil_puesto.estado_civil_atributo' >
                    <option value='0' >-</option>
                    <option v-for='rows in ev_atr_estado_civilCollection' v-bind:value='rows.id_atributo'>{{ rows.value }}</option>
                </select>
            </div>  
            <div class="col-sm">
                <label>grado avance</label> 
                <select class='form-control' size='1'  v-model='ev_perfil_puesto.grado_avance_atributo' >
                    <option value='0' >-</option>
                    <option v-for='rows in ev_atr_grado_avanceCollection' v-bind:value='rows.id_atributo'>{{ rows.value }}</option>
                </select>
            </div>
    </div>

    <div class="row">
                <div class="col-sm">
                    <label>tabulador mínimo</label> 
                    <select class='form-control' size='1'  v-model='ev_perfil_puesto.ev_tabulador_id_minimo' >
                        <option value='0' >-</option>
                        <option v-for='rows in tabuladorCollection' v-bind:value='rows.id_tabulador'>{{ rows.tabulador }} - {{ rows.sueldo }}</option>
                    </select>
                </div>  
                <div class="col-sm">
                    <label>tabulador máximo</label> 
                    <select class='form-control' size='1'  v-model='ev_perfil_puesto.ev_tabulador_id_maximo' >
                        <option value='0' >-</option>
                        <option v-for='rows in tabuladorCollection' v-bind:value='rows.id_tabulador'>{{ rows.tabulador }} - {{ rows.sueldo }}</option>
                    </select>
                </div>  
            </div> 

    <div class="row">
            <div class="col-sm">
                <label>mínimo experiencia años</label>
                <input type='number' class='form-control' v-model='ev_perfil_puesto.minimo_experiencia_anios' />
            </div>  
            <div class="col-sm">
                <label>máximo experiencia meses</label>
                <input type='number' class='form-control' v-model='ev_perfil_puesto.minimo_experiencia_meses' />
            </div>  
    </div>
    
            <div class='form-group'>
                <label>areas conocimiento</label>
                <input type='text' class='form-control' v-model='ev_perfil_puesto.areas_conocimiento' />
            </div>  
        
            <div class='form-group'>
                <label>areas experiencia</label>
                <input type='text' class='form-control' v-model='ev_perfil_puesto.areas_experiencia' />
            </div>  
            <div class='form-group'>
                <label>conocimientos especificos</label>
                <input type='text' class='form-control' v-model='ev_perfil_puesto.conocimientos_especificos' />
            </div>  
            <div class='form-group'>
                <label>equipo software herramientas</label>
                <input type='text' class='form-control' v-model='ev_perfil_puesto.equipo_software_herramientas' />
            </div>   
            
            


       
            
        <div class="row">   
            <div class="col-sm">
                <label>sueldo promedio</label>
                <input type='number' class='form-control' v-model='ev_perfil_puesto.sueldo_promedio' />
            </div>  
            <div class="col-sm">
                <label>media salarial(mes)</label>
                <input type='number' class='form-control' v-model='ev_perfil_puesto.media_salarial_mes' />
            </div>  
            <div class="col-sm">
                <label>media salarial(zona)</label>
                <input type='number' class='form-control' v-model='ev_perfil_puesto.media_salarial_zona' />
            </div>  
        </div>

            <div class='form-group'>
                <label>competencias</label>
                <input type='text' class='form-control' v-model='ev_perfil_puesto.competencias' />
            </div>  
            <div class='form-group'>
                <label>aptitudes</label>
                <input type='text' class='form-control' v-model='ev_perfil_puesto.aptitudes' />
            </div>  
            <div class='form-group'>
                <label>observaciones adicionales</label>
                <input type='text' class='form-control' v-model='ev_perfil_puesto.observaciones_adicionales' />
            </div>  
            <div class='form-group'>
                <label>actitudes puesto</label>
                <input type='text' class='form-control' v-model='ev_perfil_puesto.actitudes_puesto' />
            </div> 

            <div class="row">   
                <div class="col-sm">
                    <label>nivel estudios</label> 
                    <select class='form-control' size='1'  v-model='ev_perfil_puesto.nivel_estudios_atributo' >
                        <option value='0' >-</option>
                        <option v-for='rows in ev_atrnivel_estudioCollection' v-bind:value='rows.id_atributo'>{{ rows.value }}</option>
                    </select>
                </div>  
                <div class="col-sm">
                    <label>idioma</label> 
                    <select class='form-control' size='1'  v-model='ev_perfil_puesto.idioma_atributo' >
                        <option value='0' >-</option>
                        <option v-for='rows in ev_atr_idiomaCollection' v-bind:value='rows.id_atributo'>{{ rows.value }}</option>
                    </select>
                </div> 
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
<script type="text/javascript" src="../../controller/ev/c_ev_perfil_puesto.js"></script>

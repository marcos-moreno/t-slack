<?php require '../header.php'; ?>
<div class="container" >  
    <div id="showPoll" style="margin-top:15px;">
        
        <div v-if="poll.length > 0 || evaluaciones_lider.length > 0" >  
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Lo que tienes pendiente</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tr v-for="row in poll"> 
                        <td>
                            <strong>{{ row.nombre }}</strong>
                            <br><div style="font-size: 10;">Disponible desde: ({{ row.validodesde }}) hasta ({{ row.validohasta }})</div>
                        </td>
                        <td>
                            <center>
                                <button  v-if="row.totallecciones > 0"   @click="getenc_leccions(row)" class="btn btn-info">
                                    Lecciones
                                </button>
                                <button type="button" class="btn btn-success" @click="openPoll(row)">
                                    Responder
                                </button>
                            </center>
                        </td>
                    </tr> 
                    <tr v-for="row in evaluaciones_lider" v-if="evaluaciones_lider.length > 0"> 
                        <td>
                            <strong>{{ row.nombre_lider }}</strong>
                            <br><div style="font-size: 10;">{{ row.departamento }}</div>
                        </td>
                        <td>
                            <center>
                                <button type="button" class="btn btn-info" @click="openEvaluacion(row)">
                                    Evaluar líder
                                </button>
                            </center> 
                        </td>
                    </tr> 
                </table> 
            </div> 
        </div>
        
        
        <div class="table-responsive">
            <div>
                <nav>
                    <ul class="pagination" >  
                        <li>
                            <select class="custom-select mb-4 mr-sm-4 mb-sm-0" v-model="numByPag" @change="paginator(1)" > 
                                <option value=5 >5</option>
                                <option value=10 >10</option>
                                <option value=15 >15</option>
                                <option value=20 >20</option>
                                <option value=30  >30</option>
                            </select>
                        </li>
                        <li v-for="li in paginas" class="page-item">
                            <a v-if="li.element == paginaActual || li.element == 'Sig' || li.element == 'Ant'" class="page-link" @click="paginator(li.element)" >
                                {{ li.element }}
                            </a> 
                        </li>
                    </ul>  
                </nav>
            </div>
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>Encuestas realizadas</th>
                        <th>Hora respuesta</th>
                        <th>Estado respuesta</th>
                    </tr>
                </thead>       
                <tr v-for="row in paginaCollection">
                    <td>{{ row.no_enc }}</td>
                    <td>
                        <a v-if="row.estado=='Contestada Fuera de Tiempo' || row.estado=='Correcto'"
                            v-bind:href="'resultado-usuario-encuesta.php?id_encuesta=' + row.id_encuesta ">  
                            {{ row.nombre }}
                        </a>   
                        <div v-else>
                            {{ row.nombre }}
                        </div> 
                        <button v-if="row.totallecciones > 0 && row.estado != 'En Captura' " @click="getenc_leccions(row)" class="btn btn-link" width="18px" >
                            <div v-if="row.estado=='Aún No Disponible'" style="color:green">
                                <img src="../../img/notificacion.png">Ya puedes consultar las Lecciones de esta encuesta.
                            </div>
                            <div v-else>Recordar lecciones</div>
                        </button>  

                        <br><div style="font-size: 10;">Disponible desde: ({{ row.validodesde }}) hasta ({{ row.validohasta }})</div>
                    </td>
                    <td>{{ row.respuesta }}</td>
                    <td> 
                        <div :class="clase_x_status(row.estado)">
                            <center>{{ row.estado }}</center>
                        </div>
                    </td>
                </tr>
            </table> 
        </div> 
 
        <div v-if="modalLeccion" class="container">  <!-- Modal Lecciones -->
            <div v-if="leccion.id_enc_leccion > 0"> 
                <transition name="model"  >
                <div class="modal-mask" > 
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>{{leccion.nombre}}</h4> 
                            <!-- <button type="button" class="close" @click="modalLeccion=false;lecciones=[];"><span aria-hidden="true">&times;</span></button> -->
                        </div>  
                        <div class="modal-header"> 
                            <button v-if="leccion.index > 0" @click="iterarLeccion('ant',leccion.index)" class="btn btn-info" >
                                        << Anterior
                            </button>
                            <button v-if="leccion.index < (lecciones.length - 1)" @click="iterarLeccion('sig',leccion.index)" class="btn btn-info" >
                                Siguiente >>
                            </button>

                            <button v-if="leccion.index == (lecciones.length - 1)" @click="terminar_lecciones(leccion.id_encuesta)" class="btn btn-success">
                                terminar
                            </button> 
                        </div> 
                        <div class="modal-body"> 
                        <p><strong>{{leccion.descipcion}}</strong></p>  
                            <div >     
                                    <div v-if="leccion.tipo == 'text'" >
                                    <!-- <span v-once v-html="leccion.valor"></span>  -->
                                    <div v-html="leccion.valor"></div>
                                    </div>

                                    <div v-if="leccion.tipo == 'link'"> 
                                        <a target="_blank" v-bind:href="leccion.link" >
                                            <button  class="btn btn-warning" >Ir al Enlace</button>
                                        </a>
                                    </div>

                                    <div v-if="leccion.tipo == 'video'">
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <iframe  
                                            width="560" height="315" v-bind:src="leccion.link" allowfullscreen></iframe> 
                                        </div>
                                    </div>

                                    <div v-if="leccion.tipo == 'image'">
                                        <image style="max-width: 100%; max-height: 100%;" v-bind:src="leccion.link"  />
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
<script type="text/javascript" src="../../controllers/user/ctl_vista_encuesta1.js"></script>

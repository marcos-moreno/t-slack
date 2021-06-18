<?php require '../header.php'; ?>
<div class="container" >  
    <div id="showPoll" style="margin-top:15px;"> 
        
        <div v-if="poll.length > 0" >
            <h3>LO QUE TIENES PENDIENTE</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr> 
                                <th>Nombre</th>
                                <th>Observaciones</th>
                                <th>Válido</th> 
                                <th></th> 
                            </tr>     
                        </thead>    
                        <tr v-for="row in poll"> 
                            <td>{{ row.nombre }}</td>
                            <td>
                                <!-- {{ row }} -->
                                {{ row.observaciones }}
                            </td>
                            <td>({{ row.validodesde }})<br><br>({{ row.validohasta }})</td> 
                            <td>
                                <button  v-if="row.totallecciones > 0"   @click="getenc_leccions(row)" class="btn btn-info">
                                    Lecciones 
                                </button>
                                <button type="button" class="btn btn-success" @click="openPoll(row)">
                                    Responder
                                </button> 
                            </td>
                        </tr>
                    </table> 
                </div> 
            </div>

            <h3>Encuestas</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th>nombre</th>
                            <th>Estado Respuesta</th>  
                            <th>Hora Respuesta</th>
                            <th>Válido</th> 
                        </tr>
                    </thead>       
                    <tr v-for="row in pollComplete">
                        <td>{{ row.no_enc }}</td>
                        <td>  
                        {{ row.nombre }} 
                        <!-- {{ row }}  -->
                        <button v-if="row.totallecciones > 0 && row.estado != 'En Captura' " @click="getenc_leccions(row)" class="btn btn-link" width="18px" >
                            <div v-if="row.estado=='Aún No Disponible'" style="color:green">
                                <img src="../../img/notificacion.png">Ya puedes consultar las Lecciones de esta encuesta.
                            </div>
                            <div v-else>Lecciones</div>
                        </button>  
                        </td>
                        <td >
                             
                            <div v-if="row.estado=='Correcto'" class="alert alert-success" role="alert">
                            {{ row.estado }} 
                                <a v-bind:href="'resultado-usuario-encuesta.php?id_encuesta=' + row.id_encuesta ">
                                    <button class="alert alert-info" width="15px" ><img src="../../img/ojo.png" width="18px" ></button>
                                </a> 
                            </div>

                            <div  v-if="row.estado=='En Captura'"  class="alert alert-primary" role="alert">
                            {{ row.estado }}  
                            </div>

                            <div v-if="row.estado=='Contestada Fuera de Tiempo'"   class="alert alert-warning" role="alert">
                            {{ row.estado }} 
                                <a v-bind:href="'resultado-usuario-encuesta.php?id_encuesta=' + row.id_encuesta ">
                                    <button class="alert alert-info" width="18px" ><img src="../../img/ojo.png" width="18px" ></button>
                                </a> 
                            </div>

                            <div v-if="row.estado=='No se Respondió'"  class="alert alert-danger" role="alert">
                            {{ row.estado }} 
                            </div> 

                            <div v-if="row.estado=='Aún No Disponible'"  class="alert alert-secondary" role="alert">
                            {{ row.estado }} 
                            </div> 
                        </td>  
                        <td>{{ row.respuesta }}</td>
                        <td>({{ row.validodesde }})<br><br>({{ row.validohasta }})</td>
                    </tr>
                </table> 
            </div> 



        <div id="modalPDFs" v-if="modalPDFv2" >  
        <transition name="model" >
            <div class="modal-mask" > 
                    <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h4 class="modal-title">Actualiza tu Direcci&oacute;n</h4>
                                        <!-- <button type="button" class="close" @click="modalPDFv2=false"><span>&times;</span></button> -->
                                    </div>  

                                    <div class="modal-body">
                                    <strong>Estimado Colaborador</strong>
                                    <p>Solicitamos de tu apoyo para actualizar, en caso de que hayan cambiado al día de hoy, los siguientes datos.
                                        Nota: Al ingresar el código postal, da ENTER para que accedas a los demás campos. Dudas o aclaraciones envíanos un whatsapp al 55 7610 6686.</p>

                                            <div class="card-body">

                                            <div class="form-group">
                                                <label>Tel&eacute;fono Celular: </label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <input type="text" class="form-control" v-model="celular" onkeypress="return isNumberKey(event);" />
                                            </div>

                                            <div class="form-group">
                                                <label>Tel&eacute;fono de Casa</label>
                                                <input type="text" class="form-control" v-model="casa" onkeypress="return isNumberKey(event);" />
                                            </div>

                                            <div class="form-group">
                                                <label>Correo Electr&oacute;nico: </label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <input type="email" class="form-control" v-model="correo" />
                                            </div>


                                            <div class="form-group">
                                                <label>Estado Civil: </label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <select class='form-control'  v-model="estadocivil">
                                                <option v-for="elemento in elementos2" v-bind:value="elemento.value">{{ elemento.text }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Escolaridad: </label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <select class='form-control'  v-model="escolaridad" >
                                                <option v-for="elemento in elementos" v-bind:value="elemento.value">{{ elemento.text }}</option>
                                                </select>
                                                
                                            </div>

                                            <div class="form-group">
                                                <label>Calle:</label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <input type="text" class="form-control" v-model="calle" />
                                            </div>

                                            <div class="form-group">
                                                <label>No. Interior: </label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <input type="text" class="form-control" v-model="no_interior" />
                                            </div>

                                            <div class="form-group">
                                                <label>No. Exterior: </label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <input type="text" class="form-control" v-model="no_exterior" />
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>C&oacute;digo Postal: </label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <input type="text" id="cp" class="form-control" v-model="codigo_postal" maxlength="5" v-on:keyup="clean()" v-on:onkeypress="buscarEstado(),buscarCiudad(),buscarMunicipio(),buscarAsentamiento(),seachaddress5()" onkeypress="return isNumberKey(event);"/>
                                                <h6></h6>
                                            </div>

                                            <div class="form-group">
                                                <label>Selecciona Estado:</label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <select class='form-control'  v-model="estado">
                                                <option v-for="rows in comboEstado" v-bind:value='rows.estado'>{{ rows.estado }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Selecciona Municipio:</label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <select class='form-control'  v-model="municipio">
                                                <option v-for="rows in comboMunicipio" v-bind:value='rows.municipio'>{{ rows.municipio }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Selecciona Ciudad:</label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <select class='form-control' v-model="ciudad">
                                                <option v-for="rows in comboCuidad" v-bind:value='rows.cuidad'>{{ rows.cuidad }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Selecciona Tipo Asentamiento:</label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <select class='form-control'  v-model="tipo_asentamiento" @change='seachaddress5()' >
                                                <option v-for="rows in comboTipoAsentamiento" v-bind:value='rows.tipo_asentamiento'>{{ rows.tipo_asentamiento }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Selecciona Asentamiento:</label><label style="color: #FF0000;">(Obligatorio)</label>
                                                <select class='form-control'  v-model="asentamiento">
                                                <option v-for="rows in comboAsentamiento" v-bind:value='rows.id_codigo_postal'>{{ rows.asentamiento }}</option>

                                                </select><span>Seleccionado: {{ asentamiento }}</span>
                                            </div>

                                            </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-success" @click="editarE">Guardar</button>

                                    </div>
                            </div> 
                    </div>
             </div>
        </transition>
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
                                            <span v-once v-html="leccion.valor"></span> 
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
<script type="text/javascript" src="../../controller/user/ctl_vista_encuest_1.js"></script>

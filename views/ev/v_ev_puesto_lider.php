<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_puesto" style="margin-top:15px;"> 
 
        <div v-if="modalPerfil">
            <transition name="model">
                <div class="modal-mask"> 
                    <div>
                        <div class="modal-content" >
                            <div class="modal-header">
                                <h5 class="modal-title"  v-html="titleModalPerfil"></h5> 
                                <button type="button" class="close" @click="modalPerfil=false"><span aria-hidden="true">&times;</span></button>
                            </div>  
                        <iframe :src='"data:application/pdf;base64,"+dataPDF' height="850" width="100%"></iframe>
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
                <!-- <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_puesto()">Agregar</button></td>
                <td><a type="button" class="btn btn-secondary btn-xs edit" href="v_ev_nivel_p.php">Niveles</a></td> -->
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ev_puesto_id</th>
                        <th>codigo</th>
                        <th>nombre_puesto</th>
                        <th>tipo</th>
                        <th>nivel</th>
                        <th></th>
                        <!-- <th></th> 
                        <th></th>  -->
                    </tr>
                    <tr v-for="ev_puesto in paginaCollection" >
                        
                        <td>{{ ev_puesto.ev_puesto_id}}</td>
                        <td>{{ ev_puesto.codigo}}</td>
                        <td>{{ ev_puesto.nombre_puesto}}</td> 
                        <td>{{ ev_puesto.tipo}}</td>
                        <td>{{ ev_puesto.ev_nivel_p[0].nombre_nivel_puesto}}</td>
                        <td><button type="button" class="btn btn-link" @click="getperfil(ev_puesto)">Perfil</button></td>
                        <!-- <td><a :href="'./v_ev_indicador_puesto.php?ev_puesto_id=' + ev_puesto.ev_puesto_id" >Indicadores</a></td> 
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_puesto(ev_puesto.ev_puesto_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_puesto(ev_puesto.ev_puesto_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td>  -->
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>   
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_puesto.js"></script>

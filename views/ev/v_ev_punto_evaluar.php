<?php   
    require "../header.php";
    if(isset($_GET['ev_indicador_id'])){
        echo '<input id="ev_indicador_id" value="'.$_GET['ev_indicador_id'].'" style="display:none" >';
    }else{ 
?>  
<script> location.href="v_ev_indicador_puesto.php";</script>  
<?php } ?> 

<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_punto_evaluar" style="margin-top:15px;"> 


        <div v-if="display_modal_rubros"  class="modal-mask" style="height:100%" > 
            <div class="modal-dialog modal-dialog-centered"  >
                <div class="modal-content"  >  
                    <div class="modal-header">
                        <h5 class="modal-title">{{ev_punto_evaluar.nombre}}</h5>
                        <button type="button" class="close" @click="display_modal_rubros=false" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">  
                        <div v-if="load">    
                            <img width="100%"  src="../../img/progress.gif">
                            Espera por favor...
                        </div>
                        <div v-else> 
                            <div v-if="isFormCrud" >   
                                <div class="form-group">
                                    <label>ID: {{ ev_punto_evaluar_ln.ev_punto_evaluar_ln_id }}</label>  
                                </div>  
                                <div class='form-group'>
                                    <label>nombre</label>
                                    <input type='text' class='form-control' v-model='ev_punto_evaluar_ln.nombre' />
                                </div>  
                                <div class='form-group'>
                                    <label>icon</label>
                                    <input type='text' class='form-control' v-model='ev_punto_evaluar_ln.icon' />
                                </div>  
                                <div class='form-group'>
                                    <label>valor</label>
                                    <input type='number' class='form-control' v-model='ev_punto_evaluar_ln.valor' />
                                </div>    
                                <br>
                                <br>
                                <div class="form-group">
                                    <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_punto_evaluar_ln()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                                    <button @click="save_ev_punto_evaluar_ln()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
                                </div>   
                            </div>
                            <div v-else> 

                                <div v-if="isFormCrudRubro==false" >
                                    <button type="button" class="btn btn-info" @click="add_ev_punto_evaluar_ln()">Agregar</button>
                                    <ul>
                                        <li v-for="item in ev_punto_evaluar_lnCollection" class="border-top border-bottom border-primary" style=" margin: 20px;">
                                            {{ item.nombre }} = {{ item.valor }}
                                            <br>
                                            <button type="button" class="btn btn" @click="update_ev_punto_evaluar_ln(item)"><img src="../../img/lapiz.svg" width="25px" /></button>
                                            <button type="button" class="btn btn" @click="delete_ev_punto_evaluar_ln(item)"><img src="../../img/borrar.png" width="25px" /></button>
                                        </li>
                                    </ul>
                                </div> 

                                <div v-else >   
                                    <div class="form-group">
                                        <label>ID: {{ ev_punto_evaluar_ln.ev_punto_evaluar_ln_id }}</label>  
                                    </div>  
                                    <div class='form-group'>
                                        <label>nombre</label>
                                        <textarea class='form-control' rows="5" v-model='ev_punto_evaluar_ln.nombre'></textarea>
                                    </div>  
                                    <!-- <div class='form-group'>
                                        <label>icon</label>
                                        <input type='text' class='form-control' v-model='ev_punto_evaluar_ln.icon' />
                                    </div>   -->
                                    <div class='form-group'>
                                        <label>valor</label>
                                        <input type='number' class='form-control' v-model='ev_punto_evaluar_ln.valor' />
                                    </div>    
                                    <br>
                                    <br>
                                    <div class="form-group">
                                        <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_punto_evaluar_ln()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                                        <button @click="save_ev_punto_evaluar_ln()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
                                    </div>   
                                </div>    
                            
                            </div> 

                        </div>
                    </div>
                </div> 
            </div>
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_punto_evaluars()"> filtrar</button>
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
                        <a type="button" class="btn btn btn-xs" :href="'./v_ev_indicador_puesto.php?ev_puesto_nivel_id='+ev_indicador.ev_puesto_nivel_id">
                        <img src="../../img/regresar.png" width="28px" /> Ir a los Indicadores</a>

                    </td> 
                </tr>
            </table> 
        </div> 

        
        <br>
        <div class="panel-body"  v-if="isFormCrud==false">  
            <div class="col-md-auto" style="color:#858C8A">
                <a href="v_ev_puesto_nivel.php" >{{ev_indicador.puesto}}</a> > 
                <a :href="'v_ev_indicador_puesto.php?ev_puesto_nivel_id='+ev_indicador.ev_puesto_nivel_id">{{ev_indicador.ev_indicador_general[0].nombre}}</a> 
                 > PUNTOS A EVALUAR
            </div>   
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_punto_evaluar()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th> 
                        <th>tipo captura</th>
                        <th>nombre</th> 
                        <th>%</th> 
                        <th></th> 
                        <th></th> 
                    </tr>
                    <tr v-for="ev_punto_evaluar in paginaCollection" >
                        <td>{{ ev_punto_evaluar.ev_punto_evaluar_id}}</td>
                        <td>
                            {{ ev_punto_evaluar.ev_tipo_captura[0].nombre}} 
                            {{ ev_punto_evaluar.ev_tipo_captura[0].ev_tipo_captura_id == 1?ev_punto_evaluar.min_escala+' al '+ev_punto_evaluar.max_escala:''}}
                        </td>
                        <td>{{ ev_punto_evaluar.nombre}}</td>
                        <td>{{ ev_punto_evaluar.porcentaje_tl}}</td> 
                        <td style="width:150px">
                            <button type="button" class="btn btn-link" @click="openModalRubros(ev_punto_evaluar)">Rubros</button>
                        </td> 
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_punto_evaluar(ev_punto_evaluar.ev_punto_evaluar_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_punto_evaluar(ev_punto_evaluar.ev_punto_evaluar_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   

            <div class="col-md-auto" >
                <h4>Punto para Evaluar: <font style="color:#858C8A">{{ev_indicador.nombre}}</font></h4>
            </div> 

            <div class="form-group">
                <label>ID: {{ ev_punto_evaluar.ev_punto_evaluar_id }}</label>  
            </div>  

            <div class='form-group'>
                <label>Tipo de Captura</label> 
                <select class='form-control' size='1'  v-model='ev_punto_evaluar.ev_tipo_captura_id' >
                    <option value='0' >-</option>
                    <option v-for='rows in ev_tipo_capturaCollection' v-bind:value='rows.ev_tipo_captura_id'>{{ rows.nombre }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='ev_punto_evaluar.nombre' />
            </div>  
            <div class='form-group'>
                <label>descripcion</label>
                <input type='text' class='form-control' v-model='ev_punto_evaluar.descripcion' />
            </div>  
            <div class='form-group'>
                <label>porcentaje tl</label>
                <input type='number' class='form-control' v-model='ev_punto_evaluar.porcentaje_tl' />
            </div> 

            <div class="row" v-if="ev_punto_evaluar.ev_tipo_captura_id == 1" > 
                <div class="col-md-auto">
                    <label>Incremento</label>
                    <input type='number' class='form-control' v-model='ev_punto_evaluar.incremento' />
                </div> 
                <div class="col-md-auto">
                    <label>Valor Inicio</label>
                    <input type='number' class='form-control' v-model='ev_punto_evaluar.min_escala' />
                </div> 
                <div class="col-md-auto">
                    <label>Valor Fin</label>
                    <input type='number' class='form-control' v-model='ev_punto_evaluar.max_escala' />  
                </div> 
            </div>
            
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_punto_evaluar()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_punto_evaluar()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_punto_evaluar.js"></script>

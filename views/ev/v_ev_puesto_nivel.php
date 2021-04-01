<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_ev_puesto_nivel" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30%">
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" v-model="filter" />
                                </td> 
                                <td>
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_puesto_nivels()"> filtrar</button>
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

        
        <br><br>
        <div class="panel-body"  v-if="isFormCrud==false">
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  
                        <li><h3> EV_PUESTO_NIVEL </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_puesto_nivel()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th>
                                    
                        <th>Puesto</th>
                                    
                        <th>Nivel</th> 
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="ev_puesto_nivel in paginaCollection" >
                        
                        <td>{{ ev_puesto_nivel.ev_puesto_nivel_id}}</td>
            
                        <td>{{ ev_puesto_nivel.ev_puesto[0].nombre_puesto}}</td>
            
                        <td>{{ ev_puesto_nivel.ev_nivel_p[0].nombre_nivel_puesto}}</td> 
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_puesto_nivel(ev_puesto_nivel.ev_puesto_nivel_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_puesto_nivel(ev_puesto_nivel.ev_puesto_nivel_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ev_puesto_nivel.ev_puesto_nivel_id }}</label>  
            </div> 
                                    <div class='form-group'>
                                        <label>Puesto</label> 
                                        <select class='form-control' size='1'  v-model='ev_puesto_nivel.ev_puesto_id' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in ev_puestoCollection' v-bind:value='rows.ev_puesto_id'>{{ rows.nombre_puesto }}</option>
                                        </select>
                                    </div>  
                                    <div class='form-group'>
                                        <label>Nivel</label> 
                                        <select class='form-control' size='1'  v-model='ev_puesto_nivel.ev_nivel_p_id' >
                                            <option value='0' >-</option>
                                            <option v-for='rows in ev_nivel_pCollection' v-bind:value='rows.ev_nivel_p_id'>{{ rows.nombre_nivel_puesto }}</option>
                                        </select>
                                    </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_puesto_nivel()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_puesto_nivel()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/ev/c_ev_puesto_nivel.js"></script>

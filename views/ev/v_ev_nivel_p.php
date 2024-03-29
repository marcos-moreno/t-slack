<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;">  
    <div id="app_ev_nivel_p" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_nivel_ps()"> filtrar</button>
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
                        <li><h3>NIVEL PUESTO </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_nivel_p()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ev_nivel_p_id</th>
                                    
                        <th>nivel puesto</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="ev_nivel_p in paginaCollection" >
                        
                        <td>{{ ev_nivel_p.ev_nivel_p_id}}</td>
            
                        <td>{{ ev_nivel_p.nombre_nivel_puesto}}</td>
             
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_nivel_p(ev_nivel_p.ev_nivel_p_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_nivel_p(ev_nivel_p.ev_nivel_p_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ev_nivel_p.ev_nivel_p_id }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre nivel puesto</label>
                <input type='text' class='form-control' v-model='ev_nivel_p.nombre_nivel_puesto' />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_nivel_p()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_nivel_p()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_nivel_p.js"></script>

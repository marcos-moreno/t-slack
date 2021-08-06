<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_atributo" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_atributos()"> filtrar</button>
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
            <h4>Listas de Catalogos</h4>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_atributo()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_atributo</th>
                                    
                        <th>value</th>
                                    
                        <th>activo</th>
                                    
                        <th>descripcion</th>
                                    
                        <th>tabla</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="ev_atributo in paginaCollection" >
                        
                        <td>{{ ev_atributo.id_atributo}}</td>
            
                        <td>{{ ev_atributo.value}}</td>
            
                        <td>{{ ev_atributo.activo}}</td>
            
                        <td>{{ ev_atributo.descripcion}}</td>
            
                        <td>{{ ev_atributo.tabla}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_atributo(ev_atributo.id_atributo)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_atributo(ev_atributo.id_atributo)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ev_atributo.id_atributo }}</label>  
            </div>
            <div class='form-group'>
                <label>value</label>
                <input type='text' class='form-control' v-model='ev_atributo.value' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='ev_atributoactivo _id'   v-model='ev_atributo.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='ev_atributoactivo _id'  >activo</label>
            </div> 
            <div class='form-group'>
                <label>descripcion</label>
                <input type='text' class='form-control' v-model='ev_atributo.descripcion' />
            </div>  
            <div class='form-group'>
                <label>tabla</label>
                <input type='text' class='form-control' v-model='ev_atributo.tabla' />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_atributo()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_ev_atributo()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_atributo.js"></script>

<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_tabulador" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="gettabuladors()"> filtrar</button>
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
            <h4>tabulador</h4>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_tabulador()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_tabulador</th>
                                    
                        <th>tabulador</th>
                                    
                        <th>id_empresa</th>
                                    
                        <th>activo</th>
                                    
                        <th>sueldo</th>
                                    
                        <th>costo_hora</th>
                                    
                        <th>septimo_dia</th>
                                    
                        <th>costo_hora_extra</th>
                                    
                        <th>orden</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="tabulador in paginaCollection" >
                        
                        <td>{{ tabulador.id_tabulador}}</td>
            
                        <td>{{ tabulador.tabulador}}</td>
            
                        <td>{{ tabulador.id_empresa}}</td>
            
                        <td>{{ tabulador.activo}}</td>
            
                        <td>{{ tabulador.sueldo}}</td>
            
                        <td>{{ tabulador.costo_hora}}</td>
            
                        <td>{{ tabulador.septimo_dia}}</td>
            
                        <td>{{ tabulador.costo_hora_extra}}</td>
            
                        <td>{{ tabulador.orden}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_tabulador(tabulador.id_tabulador)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_tabulador(tabulador.id_tabulador)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ tabulador.id_tabulador }}</label>  
            </div>
            <div class='form-group'>
                <label>tabulador</label>
                <input type='text' class='form-control' v-model='tabulador.tabulador' />
            </div>  
            <div class='form-group'>
                <label>empresa</label>
                <input type='number' class='form-control' v-model='tabulador.id_empresa' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='tabuladoractivo _id'   v-model='tabulador.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='tabuladoractivo _id'  >activo</label>
            </div> 
            <div class='form-group'>
                <label>sueldo</label>
                <input type='number' class='form-control' v-model='tabulador.sueldo' />
            </div>  
            <div class='form-group'>
                <label>costo hora</label>
                <input type='number' class='form-control' v-model='tabulador.costo_hora' />
            </div>  
            <div class='form-group'>
                <label>septimo dia</label>
                <input type='number' class='form-control' v-model='tabulador.septimo_dia' />
            </div>  
            <div class='form-group'>
                <label>costo hora extra</label>
                <input type='number' class='form-control' v-model='tabulador.costo_hora_extra' />
            </div>  
            <div class='form-group'>
                <label>orden</label>
                <input type='text' class='form-control' v-model='tabulador.orden' />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_tabulador()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_tabulador()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/admin/c_tabulador.js"></script>

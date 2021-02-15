<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_marca" style="margin-top:15px;"> 


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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getmarcas()"> filtrar</button>
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
        
        <div class="panel-body"  v-if="isFormCrud==false">
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  <li><h3><div style="width: 155px;"  >MARCA</div></h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_marca()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_marca</th>
                                    
                        <th>nombre</th>
                                    
                        <th>descripcion</th>
                        
                        <th></th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="marca in paginaCollection" >
                        
                        <td>{{ marca.id_marca}}</td>
            
                        <td>{{ marca.nombre}}</td>
            
                        <td>{{ marca.descripcion}}</td>

                        <td><a type="button" name="company" class="btn-xs delete" @click="asingGroup(marca)">Asignar grupo</a></td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_marca(marca.id_marca)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_marca(marca.id_marca)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ marca.id_marca }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='marca.nombre' />
            </div>  
            <div class='form-group'>
                <label>descripcion</label>
                <input type='text' class='form-control' v-model='marca.descripcion' />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_marca()"><img src="../../img/regresar.png" width="28$" /> Regresar</button></td> 
                <button @click="save_marca()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div> 


        <div v-if="isAsingGroup" >  
            <transition name="model" >
                <div class="modal-mask" > 
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">La marca {{ this.marca.nombre }} estará disponible para los siguientes Grupos.</h4>
                                <button type="button" class="close" @click="isAsingGroup=false"><span aria-hidden="true">&times;</span></button>
                            </div>  
                            <div class="modal-body"> 
                                <div class="card-body">   
                                    <div class="custom-control custom-checkbox">
                                        <h5 ></h5>
                                        <div v-for="r in marca_grupos_marca_collection" >   
                                            <input style="margin-left:5px;" type='checkbox' v-model=r.selected > <span>{{ r.nombre_grupo_marca }}</span>  
                                        </div> 
                                    </div>   
                                    <div align="center"> 
                                        <input type="button" class="btn btn-success btn-xs" value="Guardar"  
                                        @click="saveGroups()" />
                                    </div>
                                    </br> 
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </transition>
        </div>




    </div>
</div>
<script type="text/javascript" src="../../controller/sp/c_marca.js"></script>

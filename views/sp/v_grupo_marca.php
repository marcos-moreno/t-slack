<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_grupo_marca" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getgrupo_marcas()"> filtrar</button>
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
                        <li><h3> GRUPO MARCA </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_grupo_marca()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_grupo_marca</th>
                                    
                        <th>nombre_grupo_marca</th>
                                    
                        <th>descripcion_grupo_marca</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="grupo_marca in paginaCollection" >
                        
                        <td>{{ grupo_marca.id_grupo_marca}}</td>
            
                        <td>{{ grupo_marca.nombre_grupo_marca}}</td>
            
                        <td>{{ grupo_marca.descripcion_grupo_marca}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_grupo_marca(grupo_marca.id_grupo_marca)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_grupo_marca(grupo_marca.id_grupo_marca)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ grupo_marca.id_grupo_marca }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre grupo marca</label>
                <input type='text' class='form-control' v-model='grupo_marca.nombre_grupo_marca' />
            </div>  
            <div class='form-group'>
                <label>descripcion grupo marca</label>
                <input type='text' class='form-control' v-model='grupo_marca.descripcion_grupo_marca' />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_grupo_marca()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_grupo_marca()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/sp/c_grupo_marca.js"></script>

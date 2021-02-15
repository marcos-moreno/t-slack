<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_campo" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30$">
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" v-model="filter" />
                                </td> 
                                <td>
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getcampos()"> filtrar</button>
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
                        <li><h3> CAMPO </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_campo()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_campo</th>
                                    
                        <th>nombre</th>
                                    
                        <th>descripcion</th>
                                    
                        <th>obligatorio</th>
                                    
                        <th>activo</th>
                                    
                        <th>id_grupo_marca</th>
                                    
                        <th>id_tipo</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="campo in paginaCollection" >
                        
                        <td>{{ campo.id_campo}}</td>
            
                        <td>{{ campo.nombre}}</td>
            
                        <td>{{ campo.descripcion}}</td>
            
                        <td>{{ campo.obligatorio}}</td>
            
                        <td>{{ campo.activo}}</td>
            
                        <td>{{ campo.id_grupo_marca}}</td>
            
                        <td>{{ campo.id_tipo}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_campo(campo.id_campo)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_campo(campo.id_campo)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ campo.id_campo }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='campo.nombre' />
            </div>  
            <div class='form-group'>
                <label>descripcion</label>
                <input type='text' class='form-control' v-model='campo.descripcion' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='campoobligatorio _id'   v-model='campo.obligatorio'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='campoobligatorio _id'  >obligatorio</label>
            </div>  
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='campoactivo _id'   v-model='campo.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='campoactivo _id'  >activo</label>
            </div>  
            <div class='form-group'>
                <label>grupo marca</label> 
                <select class='form-control'size='1'  v-model='campo.id_grupo_marca' >
                    <option value='0' >-</option>
                    <option v-for='rows in grupo_marcaCollection' v-bind:value='rows.id_grupo_marca'>{{ rows }}</option>
                </select>
            </div>  
            <div class='form-group'>
                <label>tipo</label> 
                <select class='form-control'size='1'  v-model='campo.id_tipo' >
                    <option value='0' >-</option>
                    <option v-for='rows in tipoCollection' v-bind:value='rows.id_tipo'>{{ rows }}</option>
                </select>
            </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_campo()"><img src="../../img/regresar.png" width="28$" /> Regresar</button></td> 
                <button @click="save_campo()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/sp/c_campo.js"></script>

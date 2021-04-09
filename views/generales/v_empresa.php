<?php require "../header.php";?> 
<div class="container-fluid" style="width:90%;" >  
    <div id="app_empresa" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getempresas()"> filtrar</button>
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
                        <li><h3> EMPRESA </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_empresa()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th> 
                                    
                        <th>Nombre</th>
                                    
                        <th>RFC</th>
                                      
                        <th>activo</th> 
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="empresa in paginaCollection" >
                        
                        <td>{{ empresa.id_empresa}}</td>
              
                        <td>{{ empresa.empresa_nombre}}</td>
            
                        <td>{{ empresa.empresa_rfc}}</td>
              
                        <td>  
                            <div v-if="empresa.empresa_activo">Si</div>
                            <div v-else >No</div>
                        </td> 
             
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_empresa(empresa.id_empresa)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_empresa(empresa.id_empresa)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ empresa.id_empresa }}</label>  
            </div>
            <div class='form-group'>
                <label>empresa nombre</label>
                <input type='text' class='form-control' v-model='empresa.empresa_nombre' />
            </div>  
            <div class='form-group'>
                <label>empresa rfc</label>
                <input type='text' class='form-control' v-model='empresa.empresa_rfc' />
            </div>  
            <div class='form-group'>
                <label>empresa observaciones</label>
                <input type='text' class='form-control' v-model='empresa.empresa_observaciones' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='empresaempresa_activo _id'   v-model='empresa.empresa_activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='empresaempresa_activo _id'  >empresa activo</label>
            </div> 
            <div class='form-group'>
                <label>empresa cerberus</label>
                <input type='number' class='form-control' v-model='empresa.id_empresa_cerberus' />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_empresa()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_empresa()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/generales/c_empresa.js"></script>

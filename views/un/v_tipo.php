<?php require "../header.php";?> 
<div class="container" style="width:90$">  
    <div id="app_tipo" style="margin-top:15px;"> 
        <div class="pre-scrollable" style="max-height: 75vh" >
            <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg}}</div>
            <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg}}</div>
            <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg}}</div>
        </div> 

        
        <div class="panel-body"  v-if="isFormCrud==false">
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  <li><h3><div style="width: 155px;"  >TIPO</div></h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_tipo()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_tipo</th>
                                    
                        <th>tipo</th>
                                    
                        <th>descripcion</th>
                                    
                        <th>direct_data</th>
                                    
                        <th>opcion_multiple</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="tipo in paginaCollection" >
                        
                        <td>{{ tipo.id_tipo}}</td>
            
                        <td>{{ tipo.tipo}}</td>
            
                        <td>{{ tipo.descripcion}}</td>
            
                        <td>{{ tipo.direct_data}}</td>
            
                        <td>{{ tipo.opcion_multiple}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_tipo(tipo.id_tipo)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_tipo(tipo.id_tipo)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ tipo.id_tipo }}</label>  
            </div>
            <div class='form-group'>
                <label>tipo</label>
                <input type='text' class='form-control' v-model='tipo.tipo' />
            </div>  
            <div class='form-group'>
                <label>descripcion</label>
                <input type='text' class='form-control' v-model='tipo.descripcion' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='tipodirect_data _id'   v-model='tipo.direct_data'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='tipodirect_data _id'  >direct data</label>
            </div>  
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='tipoopcion_multiple _id'   v-model='tipo.opcion_multiple'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='tipoopcion_multiple _id'  >opcion multiple</label>
            </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_tipo()"><img src="../../img/regresar.png" width="28$" /> Regresar</button></td> 
                <button @click="save_tipo()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/un/c_tipo.js"></script>

<?php require "../header.php";?> 
<div class="container" style="width:90$">  
    <div id="app_dispositivo" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getdispositivos()"> filtrar</button>
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
                        <li><h3> DISPOSITIVO </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_dispositivo()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_dispositivo</th>
                                    
                        <th>nombre</th>
                                    
                        <th>descripcion</th>
                                    
                        <th>codigo</th>
                                    
                        <th>mac</th>
                                    
                        <th>num_serie</th>
                                    
                        <th>id_grupo_marca</th>
                                    
                        <th>id_marca</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="dispositivo in paginaCollection" >
                        
                        <td>{{ dispositivo.id_dispositivo}}</td>
            
                        <td>{{ dispositivo.nombre}}</td>
            
                        <td>{{ dispositivo.descripcion}}</td>
            
                        <td>{{ dispositivo.codigo}}</td>
            
                        <td>{{ dispositivo.mac}}</td>
            
                        <td>{{ dispositivo.num_serie}}</td>
            
                        <td>{{ dispositivo.grupo_marca[0].nombre_grupo_marca}}</td>
            
                        <td>{{ dispositivo.marca[0].nombre}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_dispositivo(dispositivo.id_dispositivo)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_dispositivo(dispositivo.id_dispositivo)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ dispositivo.id_dispositivo }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='dispositivo.nombre' />
            </div>  
            <div class='form-group'>
                <label>descripcion</label>
                <input type='text' class='form-control' v-model='dispositivo.descripcion' />
            </div>  
            <div class='form-group'>
                <label>codigo</label>
                <input type='text' class='form-control' v-model='dispositivo.codigo' />
            </div>  
            <div class='form-group'>
                <label>mac</label>
                <input type='text' class='form-control' v-model='dispositivo.mac' />
            </div>  
            <div class='form-group'>
                <label>num serie</label>
                <input type='text' class='form-control' v-model='dispositivo.num_serie' />
            </div>   
            <div class='form-group'>
                <label>Clasificación</label> 
                <select class='form-control'size='1'  v-model='dispositivo.id_grupo_marca' @change="getFieldsByGroup()" >
                    <option value='0' >-</option>
                    <option v-for='rows in grupo_marcaCollection' v-bind:value='rows.id_grupo_marca'>{{ rows }}</option>
                </select>
            </div>  
            <div class='form-group'>
                <label>marca</label> 
                <select class='form-control'size='1'  v-model='dispositivo.id_marca' >
                    <option value='0' >-</option>
                    <option v-for='rows in marcaCollection' v-bind:value='rows.id_marca'>{{ rows }}</option>
                </select>
            </div>   


            <div v-for="row in fields">       
                <div class="col-md-12">
                    <input style="margin-left:5px;"   :type=row.tipo :id="row.id_campo" :value=r.id_opcion > 
                        <span>
                            {{ r.opcion }}  
                            <input v-if="r.respuesta_extra"   style="width:50%;  border-top: none;  border-left: none;  border-right: none;  border-bottom: 1px solid #03a8f45e;"
                                type="text" :id="r.id_opcion + '_respuesta_extra'" placeholder="Especifique cual" > 
                        </span>
                </div>  
            <div>


            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_dispositivo()"><img src="../../img/regresar.png" width="28$" /> Regresar</button></td> 
                <button @click="save_dispositivo()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/sp/c_dispositivo.js"></script>

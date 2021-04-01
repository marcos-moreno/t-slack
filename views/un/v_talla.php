<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_talla" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="gettallas()"> filtrar</button>
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
                        <li><h3> TALLA </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_talla()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_talla</th>
                                    
                        <th>valor</th>
                                    
                        <th>activo</th>
                                    
                        <th>tipo_producto</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="talla in paginaCollection" >
                        
                        <td>{{ talla.id_talla}}</td>
            
                        <td>{{ talla.valor}}</td>
            
                        <td>  
                            <div v-if="talla.activo">Si</div>
                            <div v-else >No</div>
                        </td> 
            
                        <td>{{ talla.tipo_producto[0].nombre_tipo_producto}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_talla(talla.id_talla)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_talla(talla.id_talla)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ talla.id_talla }}</label>  
            </div>
            <div class='form-group'>
                <label>valor</label>
                <input type='text' class='form-control' v-model='talla.valor' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='tallaactivo _id'   v-model='talla.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='tallaactivo _id'  >activo</label>
            </div>  
            <div class='form-group'>
                <label>tipo producto</label> 
                <select class='form-control'size='1'  v-model='talla.id_tipo_producto' >
                    <option value='0' >-</option>
                    <option v-for='rows in tipo_productoCollection' v-bind:value='rows.id_tipo_producto'>({{ rows.id_tipo_producto }}) {{ rows.nombre_tipo_producto }}</option>
                </select>
            </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_talla()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_talla()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/un/c_talla.js"></script>

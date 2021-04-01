<?php require "../header.php";?> 
<div class="container" style="width:90$">  
    <div id="app_marca_grupro_marca" style="margin-top:15px;"> 
        <div class="pre-scrollable" style="max-height: 75vh" >
            <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg}}</div>
            <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg}}</div>
            <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg}}</div>
        </div> 

        
        <div class="panel-body"  v-if="isFormCrud==false">
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  <li><h3><div style="width: 155px;"  >MARCA_GRUPRO_MARCA</div></h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_marca_grupro_marca()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_marca_grupro_marca</th>
                                    
                        <th>id_marca</th>
                                    
                        <th>id_grupo_marca</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="marca_grupro_marca in paginaCollection" >
                        
                        <td>{{ marca_grupro_marca.id_marca_grupro_marca}}</td>
            
                        <td>{{ marca_grupro_marca.id_marca}}</td>
            
                        <td>{{ marca_grupro_marca.id_grupo_marca}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_marca_grupro_marca(marca_grupro_marca.id_marca_grupro_marca)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_marca_grupro_marca(marca_grupro_marca.id_marca_grupro_marca)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ marca_grupro_marca.id_marca_grupro_marca }}</label>  
            </div> 
            <div class='form-group'>
                <label>marca</label> 
                <select class='form-control'size='1'  v-model='marca_grupro_marca.id_marca' >
                    <option value='0' >-</option>
                    <option v-for='rows in marcaCollection' v-bind:value='rows.id_marca'>{{ rows }}</option>
                </select>
            </div>  
            <div class='form-group'>
                <label>grupo marca</label> 
                <select class='form-control'size='1'  v-model='marca_grupro_marca.id_grupo_marca' >
                    <option value='0' >-</option>
                    <option v-for='rows in grupo_marcaCollection' v-bind:value='rows.id_grupo_marca'>{{ rows }}</option>
                </select>
            </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_marca_grupro_marca()"><img src="../../img/regresar.png" width="28$" /> Regresar</button></td> 
                <button @click="save_marca_grupro_marca()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/un/c_marca_grupro_marca.js"></script>

<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_acceso_rol" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getacceso_rols()"> filtrar</button>
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
                        <li><h3> ACCESO_ROL </h3></li>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_acceso_rol()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_acceso</th>
                                    
                        <th>id_rol</th>
                                    
                        <th>id_elemento</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="acceso_rol in paginaCollection" >
                        
                        <td>{{ acceso_rol.id_acceso}}</td>
            
                        <td>{{ acceso_rol.rol[0].rol}}</td>
            
                        <td>{{ acceso_rol.elemento[0].name }}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_acceso_rol(acceso_rol.id_acceso)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_acceso_rol(acceso_rol.id_acceso)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ acceso_rol.id_acceso }}</label>  
            </div> 
            <div class='form-group'>
                <label>rol</label> 
                <select class='form-control' size='1'  v-model='acceso_rol.id_rol' >
                    <option value='0' >-</option>
                    <option v-for='rows in rolCollection' v-bind:value='rows.id_rol'>({{ rows.id_rol }}) {{ rows.rol }}</option>
                </select>
            </div>  
            <div class='form-group'>
                <label>elemento</label> 
                <select class='form-control' size='1'  v-model='acceso_rol.id_elemento' >
                    <option value='0' >-</option>
                    <option v-for='rows in elementoCollection' v-bind:value='rows.id_elemento'>({{ rows.id_elemento }}) {{ rows.name }}</option>
                </select>
            </div>   
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_acceso_rol()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_acceso_rol()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controllers/generales/c_acceso_rol.js"></script>

<?php require "../header.php";?> 
<div class="container" style="width:90$">  
    <div id="app_rol" style="margin-top:15px;">
        <h3>ROL</h3> 
        <div style="height: 80px;" >
            <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg}}</div>
            <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg}}</div>
            <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg}}</div>
        </div> 

        
        <div class="panel-body"  v-if="isFormCrud==false">
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_rol()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id_rol</th>
                                    
                        <th>rol</th>
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="rol in rolCollection">
                        
                        <td>{{ rol.id_rol}}</td>
            
                        <td>{{ rol.rol}}</td>
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_rol(rol.id_rol)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_rol(rol.id_rol)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ rol.id_rol }}</label>  
            </div>
            <div class='form-group'>
                <label>rol</label>
                <input type='text' class='form-control' v-model='rol.rol' />
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_rol()"><img src="../../img/regresar.png" width="28$" /> Regresar</button></td> 
                <button @click="save_rol()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/generales/c_rol.js"></script>

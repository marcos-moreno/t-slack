
<?php   
    require "../header.php";
    if(isset($_GET['departamento_id'])){
        echo '<input id="departamento_id" value="'.$_GET['departamento_id'].'" style="display:none" >';
    }else{ 
?>  
<script> location.href="v_departamento.php";</script>  
<?php } ?> 

<div  class="container-fluid" style="width:90%;"> 
    <div id="app_lider_departamento" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr> 
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

        
        <br>
        <div class="panel-body"  v-if="isFormCrud==false">
            <h4><a href="v_departamento.php" >{{departamentoCollection[0].nombre}}</a> /
                ({{departamentoCollection[0].segmento[0].nombre}} -
                {{departamentoCollection[0].empresa[0].empresa_observaciones}})
            </h4>
            <br>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_lider_departamento()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>lider_departamento_id</th>
                                    
                        <th>id_empleado</th>
                                    
                        <th>departamento_id</th>
                                    
                        <th>tipo_lider</th> 
                                     
                        <th></th> 
                    </tr>
                    <tr v-for="lider_departamento in paginaCollection" >
                        
                        <td>{{ lider_departamento.lider_departamento_id}}</td>
            
                        <td> 
                        {{ lider_departamento.empleado[0].paterno }} 
                        {{ lider_departamento.empleado[0].materno }} 
                        {{ lider_departamento.empleado[0].nombre }}
                        </td>
            
                        <td>{{ lider_departamento.departamento[0].nombre }}</td>
            
                        <td>{{ lider_departamento.tipo_lider}}</td> 
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_lider_departamento(lider_departamento.lider_departamento_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_lider_departamento(lider_departamento.lider_departamento_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ lider_departamento.lider_departamento_id }}</label>  
            </div> 

                <div class='form-group'>
                    <div class="row">
                        <div class="col-5">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" >Líder</label>
                                </div>
                                <input class="form-control" type="search"  v-model="filtroLider" 
                                    v-on:keyup ="buscarValorLider"
                                    placeholder="Buscar Líder" aria-label="Search"> 
                            </div>
                        </div>
                        <div class="col-7">
                            <select class='form-control' size='1'  v-model='lider_departamento.id_empleado' >
                                <option value='' >-</option>
                                <option v-for='rows in empleadoCollectionfiltro' v-bind:value='rows.id_empleado'>
                                    {{ rows.paterno }} {{ rows.materno }} {{ rows.nombre }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div> 
                  
                <div class='form-group'>
                    <label>departamento</label> 
                    <select class='form-control' size='1'  v-model='lider_departamento.departamento_id' >
                        <option value='0' >-</option>
                        <option v-for='rows in departamentoCollection' v-bind:value='rows.departamento_id'>
                            {{ rows.nombre }} ({{ rows.segmento[0].nombre }} -> {{ rows.empresa[0].empresa_observaciones }})
                        </option>
                    </select>
                </div> 
            <div class='form-group'>
                <label>tipo lider</label>
                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="lider_departamento.tipo_lider" > 
                    <option value="Líder Departamento"  >Líder Departamento</option>
                    <option value="Alta Dirección" >Alta Dirección</option> 
                </select> 
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_lider_departamento()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_lider_departamento()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/admin/c_lider_departamento.js"></script>

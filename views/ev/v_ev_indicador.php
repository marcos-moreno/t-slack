<?php   
    require "../header.php";
    if(isset($_GET['ev_puesto_nivel_id'])){
        echo '<input id="ev_puesto_nivel_id" value="'.$_GET['ev_puesto_nivel_id'].'" style="display:none" >';
    }else{ 
?>  
<script> location.href="v_ev_puesto_nivel.php";</script>  
<?php } ?> 

<div  class="container-fluid" style="width:90%;">  
    <div id="app_ev_indicador" style="margin-top:15px;"> 
        <div >
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="width: 20%;word-wrap: break-word;"  v-if="isFormCrud==false" >
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" v-model="filter" />
                                </td> 
                                <td>
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getev_indicadors()"> filtrar</button>
                                </td> 
                            </tr>
                        </table>
                    </td>
                    <td style="weight: 50%" >
                        <div class="pre-scrollable" >
                            <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg}}</div>
                            <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg}}</div>
                            <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg}}</div>
                        </div> 
                        <a type="button" class="btn btn btn-xs" href="./v_ev_puesto_nivel.php"><img src="../../img/regresar.png" width="28px" /> Ir a Puestos</a>
                    </td> 
                </tr>
            </table> 
        </div> 
        <br>
        <div class="container"> 
            <div class="row">   
                <div class="col-md-auto">
                   <h4>INDICADORES -> PUESTO: </h4>
                </div>
                <div class="col-md-auto" style="color:#858C8A" v-for="item in puesto_nivel.ev_puesto">
                    <h4> {{ item.nombre_puesto }}</h4>
                </div>
                <div class="col-md-auto" style="color:#858C8A" v-for="item in puesto_nivel.ev_nivel_p">
                    <h4> {{ item.nombre_nivel_puesto }}</h4>
                </div>   
            </div> 
        </div>
        <br>
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
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_ev_indicador()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th>
                                    
                        <th>nombre</th>
                                    
                        <th>Tendencia</th>
                                    
                        <th>porcentaje</th>
                                    
                        <th>origen</th> 

                        <th></th> 

                        <th></th> 
                    </tr>
                    <tr v-for="ev_indicador in paginaCollection" >
                        
                        <td>{{ ev_indicador.ev_indicador_id}}</td>
            
                        <td>{{ ev_indicador.nombre}}</td>
            
                        <td>{{ ev_indicador.tendencia}}</td>
            
                        <td>{{ ev_indicador.porcentaje}}%</td>
            
                        <td>{{ ev_indicador.origen}}</td> 

                        <td><a :href="'./v_ev_punto_evaluar.php?ev_indicador_id=' + ev_indicador.ev_indicador_id" >Puntos a Evaluar</a></td> 
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_ev_indicador(ev_indicador.ev_indicador_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_ev_indicador(ev_indicador.ev_indicador_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ ev_indicador.ev_indicador_id }}</label>  
            </div> 

            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='ev_indicador.nombre' />
            </div>  
            <div class='form-group'>
                <label>descripcion</label>
                <input type='text' class='form-control' v-model='ev_indicador.descripcion' />
            </div>  
            <div class='form-group'>
                <label>porcentaje</label>
                <input type='number' class='form-control' v-model='ev_indicador.porcentaje' />
            </div>  
            <div class='form-group'>
                <label>Tendencia</label>
                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model='ev_indicador.tendencia' > 
                    <option value="CRECIENTE" >CRECIENTE</option> 
                    <option value="DECRECIENTE"  >DECRECIENTE</option> 
                </select> 
            </div>  
            <div class='form-group'>
                <label>origen</label>
                    <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model='ev_indicador.origen' > 
                        <option value="Adempiere" >Adempiere</option> 
                        <option value="Cerberus"  >Cerberus</option>
                        <option value="Basecamp"  >Basecamp</option>
                        <option value="Surver"  >Surver</option>
                    </select> 
            </div>    
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_ev_indicador()"><img src="../../img/regresar.png" width="28px" /> Cancelar</button></td> 
                <button @click="save_ev_indicador()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/ev/c_ev_indicador.js"></script>

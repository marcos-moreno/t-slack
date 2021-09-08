<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_jerarquizacion" style="margin-top:15px;"> 
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <td style="weight: 30%" v-if="isFormCrud==false">
                        <label>Filtrar</label>  
                        <table>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" v-model="filter" />
                                </td> 
                                <td>
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getjerarquizacions()"> filtrar</button>
                                </td> 
                            </tr>
                        </table>
                    </td>
                    <td>
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
            <h4>jerarquizacion</h4>
            <br>
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li>
                            <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="numByPag" @change="paginator(1)" >
                                <option value=5>5</option>
                                <option value=10>10</option>
                                <option value=15>15</option>
                                <option value=20>20</option>
                            </select>
                        </li>
                        <li v-for="li in paginas" class="page-item">
                            <a class="page-link" @click="paginator(li.element)" >{{ li.element }} <div v-if="li.element == paginaActual" >_</div></a> 
                        </li>
                    </ul>
                </nav>
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_jerarquizacion()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Id</th>
                        <th>empleado</th>
                        <th>Nivel</th>
                        <th>Activo</th>
                        <th>Superior</th>
                        <th></th>
                    </tr>
                    <tr v-for="jerarquizacion in paginaCollection" >
                        <td>{{ jerarquizacion.jerarquizacion_id}}</td>
                        <td>{{ jerarquizacion.nombre_empleado}}</td>
                        <td>({{ jerarquizacion.value }}) {{ jerarquizacion.descripcion }}</td>
                        <td><div v-if="jerarquizacion.activo">Si</div><div v-else>No</div></td>
                        <td>{{ jerarquizacion.superior}}</td> 
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_jerarquizacion(jerarquizacion.jerarquizacion_id)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_jerarquizacion(jerarquizacion.jerarquizacion_id)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ jerarquizacion.jerarquizacion_id }}</label>  
            </div> 
            <div class='form-group'>
                <div class="row">
                    <div class="col-5">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" >Empleado</label>
                            </div>
                            <input class="form-control" type="search"  v-model="filtroEmpleado" 
                                v-on:keyup ="buscarValorEmpleado(filtroEmpleado,false)"
                                placeholder="Buscar Empleado" aria-label="Search"> 
                        </div>
                    </div>
                    <div class="col-7">
                        <select class='form-control' size='1'  v-model='jerarquizacion.id_empleado'  >
                            <option v-for='rows in empleadoCollectionfiltro'
                                v-bind:value='rows.id_empleado'>
                                    {{ rows.paterno }} {{ rows.materno }} {{ rows.nombre }} ({{rows.departamento}})
                            </option>
                        </select>
                    </div>
                </div>
            </div> 
            <div class='form-group'>
                <div class="row">
                    <div class="col-5">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" >Superior</label>
                            </div>
                            <input class="form-control" type="search"  v-model="filtroSuperior" 
                                v-on:keyup ="buscarValorEmpleado(filtroSuperior,true)"
                                placeholder="Buscar Superior" aria-label="Search"> 
                        </div>
                    </div>
                    <div class="col-7">
                        <select class='form-control' size='1'  v-model='jerarquizacion.id_superior'  >
                            <option v-for='rows in superiorCollectionfiltro'
                                v-bind:value='rows.id_empleado'>
                                    {{ rows.paterno }} {{ rows.materno }} {{ rows.nombre }} ({{rows.departamento}})
                            </option>
                        </select>
                    </div>
                </div>
            </div> 

            <div class='row'>
                <div class='col-sm'>
                    <div class="input-group mb-3">
                        <label class="input-group-text">Departamento</label>
                        <select class='form-control' v-model='jerarquizacion.departamento_id'>
                               <option
                                        v-for='depa in departamentoCollection' 
                                        v-bind:value='depa.departamento_id'>
                                        {{depa.nombre}} -- {{depa.segmento[0].nombre}} ({{depa.empresa[0].empresa_observaciones}})
                                </option>  
                        </select> 
                    </div> 
                </div>   
                <div class='col-sm'>
                    <div class="input-group mb-3">
                        <label class="input-group-text">Nivel</label>
                        <select class='form-control' v-model='jerarquizacion.id_atributo_nivel'>
                               <option
                                        v-for='rows in ev_atributoCollection' 
                                        v-bind:value='rows.id_atributo'>
                                        ({{rows.value}}) {{ rows.descripcion }}
                                </option>  
                        </select> 
                    </div>  
                </div>   
            </div>  

            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='jerarquizacionactivo _id'   v-model='jerarquizacion.activo'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='jerarquizacionactivo _id'  >activo</label>
            </div>  
           
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_jerarquizacion()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_jerarquizacion()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>   
        </div>  








        <!-- v-html="myHtmlCode" -->
        <div class="organigrama">
 


                    <!--  <ul>
                        <li v-for="empleado0 in jerarquizacionCollection"  v-if="empleado0.value == 0">
                            <a>{{empleado0.nombre_empleado}}</a>
                            <div v-for="nivel in ev_atributoCollection" >
                                <ul v-for="empleado in jerarquizacionCollection" v-if="nivel.value == empleado.value">
                                    <li>
                                        <a>{{empleado.nombre_empleado}}</a>
                                    </li>
                                </ul>  
                            </div>
                        </li>
                    </ul>  -->
 
                <ul>
                    <li v-for="empleado0 in jerarquizacionCollection"  v-if="empleado0.value == 0">
                    <a>{{empleado0.nombre_empleado}}</a>
                        <ul >
                            <li v-for="empleado1 in jerarquizacionCollection"  v-if="empleado1.id_superior == empleado0.id_empleado">
                                <a>{{empleado1.nombre_empleado}}</a>

                                <ul v-for="empleado2 in jerarquizacionCollection"  v-if="empleado2.id_superior == empleado1.id_empleado">
                                    <li >
                                        <a>{{empleado2.nombre_empleado}}</a>  
                                        
                                        <ul v-for="empleado3 in jerarquizacionCollection"  v-if="empleado3.id_superior == empleado2.id_empleado">
                                            <li>
                                                <a>{{empleado3.nombre_empleado}}</a>  
                                            </li>
                                        </ul> 

                                    </li>
                                </ul> 

                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- <ul>
                    <li>
                    <a>Director</a>
                        <ul>
                            <li>
                                <a>Vicepresidente</a>
                                <ul>
                                    <li><a>Vicepresidente</a></li>
                                </ul>   
                            </li>
                        </ul>
                    </li>
                </ul> -->



            <!-- </div> -->
            <!-- <ul>
                <li>
                <a>Director</a>
                    <ul>
                        <li>
                            <a>Vicepresidente</a>
                            <ul>
                                <li><a>Vicepresidente</a></li>
                            </ul>   
                        </li>
                    </ul>
                </li>
            </ul> -->

      


        </div>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

</div>
<script type="text/javascript" src="../../controllers/admin/c_jerarquizacion.js"></script>

<style> 
body {
  background-color: #333;
}

.organigrama * {
  margin: 0px;
  padding: 0px;
}

.organigrama ul {
	padding-top: 20px;
  position: relative;
}

.organigrama li {
	float: left;
  text-align: center;
	list-style-type: none;
	padding: 20px 5px 0px 5px;
  position: relative;
}

.organigrama li::before, .organigrama li::after {
	content: '';
	position: absolute;
  top: 0px;
  right: 50%;
	border-top: 1px solid #f80;
	width: 50%;
  height: 20px;
}

.organigrama li::after{
	right: auto;
  left: 50%;
	border-left: 1px solid #f80;
}

.organigrama li:only-child::before, .organigrama li:only-child::after {
	display: none;
}

.organigrama li:only-child {
  padding-top: 0;
}

.organigrama li:first-child::before, .organigrama li:last-child::after{
	border: 0 none;
}

.organigrama li:last-child::before{
	border-right: 1px solid #f80;
	-webkit-border-radius: 0 5px 0 0;
	-moz-border-radius: 0 5px 0 0;
	border-radius: 0 5px 0 0;
}

.organigrama li:first-child::after{
	border-radius: 5px 0 0 0;
	-webkit-border-radius: 5px 0 0 0;
	-moz-border-radius: 5px 0 0 0;
}

.organigrama ul ul::before {
	content: '';
	position: absolute;
  top: 0;
  left: 50%;
	border-left: 1px solid #f80;
	width: 0;
  height: 20px;
}

.organigrama li a {
	border: 1px solid #f80;
	padding: 1em 0.75em;
	text-decoration: none;
	color: #333;
  background-color: rgba(255,255,255,0.5);
	font-family: arial, verdana, tahoma;
	font-size: 0.85em;
	display: inline-block;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
  -webkit-transition: all 500ms;
  -moz-transition: all 500ms;
  transition: all 500ms;
}

.organigrama li a:hover {
	border: 1px solid #fff;
	color: #ddd;
  background-color: rgba(255,128,0,0.7);
	display: inline-block;
}

.organigrama > ul > li > a {
  font-size: 1em;
  font-weight: bold;
}

.organigrama > ul > li > ul > li > a {
  width: 8em;
}

</style>

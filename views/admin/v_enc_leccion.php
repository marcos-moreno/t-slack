<?php require "../header.php";?>
<?php   
    if(isset($_GET['id_encuesta'])){
        echo '<input id="id_encuesta" value="'.$_GET['id_encuesta'].'" style="display:none" >';
    }else{ 
?>    
<script> location.href="p_poll.php";</script>  
<?php } ?> 

<div class="container-fluid" style="width:80%;" >  
    <div id="app_enc_leccion" style="margin-top:15px;"> 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getenc_leccions()"> filtrar</button>
                                </td> 
                            </tr>
                        </table>
                    </td>
                    <td >
                        <div class="pre-scrollable" >
                            <h4>{{encuesta.nombre}}</h4>
                            <h4>Lecciones</h4>
                            <a href="../admin/p_poll.php"><button type="button" class="btn btn btn-xs"><img src="../../img/regresar.png" width="28px" />
                                Regresar
                            </button></a>
                            <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg}}</div>
                            <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg}}</div>
                            <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg}}</div>
                        </div> 
                    </td> 
                </tr>
            </table> 
        </div> 
         
        <div class="panel-body"  v-if="isFormCrud==false">
            <div class="table-responsive">
                <!-- <nav aria-label="Page navigation example"> -->
                   
                    <ul class="pagination">  
                        <li> <button type="button" class="btn btn-info btn-xs edit" @click="add_enc_leccion()">Agregar</button></li>
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
                <!-- </nav> --> 
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>id</th>
                                    
                        <th>nombre</th>
                                    
                        <th>descipcion</th> 
                                    
                        <th>tipo</th>
                                    
                        <th>link</th>
                                    
                        <th>Texto</th>
                                    
                        <th>Mostrar</th>
                                    
                        <th>Orden</th> 

                        <th></th> 
                    </tr>
                    <tr v-for="enc_leccion in paginaCollection" >
                        
                        <td>{{ enc_leccion.id_enc_leccion}}</td>
            
                        <td>{{ enc_leccion.nombre}}</td>
            
                        <td>{{ enc_leccion.descipcion}}</td> 
            
                        <td>{{ enc_leccion.tipo}}</td> 

                        <td ><a v-if="enc_leccion.link.length > 0" target="_blank" v-bind:href="enc_leccion.link" >{{ enc_leccion.link.substring(0, 25) }}...</a></td>
                         
                        <td v-html="enc_leccion.valor.substring(0, 30) + (enc_leccion.valor.length > 0 ? '...':'')"> </td>
            
                        <td> 
                            <div v-if="enc_leccion.inicio == true">Al Inicio</div>
                            <div v-if="enc_leccion.final  == true">Al Final</div>
                            <div v-if="enc_leccion.leccion == true">Lección</div> 
                            <div v-if="enc_leccion.leccion == false && enc_leccion.inicio == false && enc_leccion.final == false"><img src="../../img/cancelar.png" width="15"> No se Mostrará</div> 
                        </td>
            
                        <td>{{ enc_leccion.orden}}</td> 
               
                        <td style="width:150px" >
                            <button type="button" class="btn btn" @click="update_enc_leccion(enc_leccion.id_enc_leccion)"><img src="../../img/lapiz.svg" width="25px" /></button>
                            <button type="button" class="btn btn" @click="delete_enc_leccion(enc_leccion.id_enc_leccion)"><img src="../../img/borrar.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class="form-group">
                <label>ID: {{ enc_leccion.id_enc_leccion }}</label>  
            </div>
            <div class='form-group'>
                <label>nombre</label>
                <input type='text' class='form-control' v-model='enc_leccion.nombre' />
            </div>  
            <div class='form-group'>
                <label>descipcion</label>
                <input type='text' class='form-control' v-model='enc_leccion.descipcion' />
            </div> 

            <div class='form-group'>
                <label>Orden</label>
                <input type='number' class='form-control' v-model='enc_leccion.orden' />
            </div> 

            <div class='form-group'>
                <label>tipo</label>
                <select class='form-control' v-model='enc_leccion.tipo' @change="movementetype()" >
                    <option value="link" >Link</option>
                    <option value="video" >Video</option>
                    <option value="image" >Imagen</option>
                    <option value="text" >Texto</option>
                </select> 
            </div>  
            <div class='form-group'>
                <label>link</label>
                <input type='text' class='form-control' v-model='enc_leccion.link' />
            </div>   
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='enc_leccioninicio _id'   v-model='enc_leccion.inicio'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='enc_leccioninicio _id'  >inicio</label>
            </div>  
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='enc_leccionfinal _id'   v-model='enc_leccion.final'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='enc_leccionfinal _id'  >final</label>
            </div>  
            <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='enc_leccionleccion _id'   v-model='enc_leccion.leccion'  false-value='false' true-value='true' >
                <label class='custom-control-label' for='enc_leccionleccion _id'  >Lección</label>
            </div>  
            <br>
            <!-- <label  v-if="enc_leccion.tipo=='text'">Valor</label>   -->
            <!-- <textarea  v-if="enc_leccion.tipo=='text'"  cols="50" rows="40" class='form-group' class='form-control' v-model='enc_leccion.valor'>
            </textarea >  -->
          
            <td><button type="button" class="btn btn btn-xs" @click="cancel_enc_leccion()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
            <button @click="save_enc_leccion()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>

            </div>   
        <br>
            <div  id="textEditor" style="display:none" >
                <label >Valor</label> 
                <center>    
                    <textarea id="ckeditor" class="ckeditor"></textarea>
                </center>  
            </div>
          
           <br> <br> <br>
        </div>  
    </div>
</div>

  


<script type="text/javascript" src="../../controller/admin/c_enc_lecc.js"></script>
<script type="text/javascript" src="../../lib/ckeditor/ckeditor.js"></script>

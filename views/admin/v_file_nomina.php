<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_file_nomina" style="margin-top:15px;"> 
        <div style="background:none;" id="mymodal" class="modal fade bd-example-modal-lg" tabindex="-1"  aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="background:none;">
            <div class="modal-content" style="background:none;">
            <div  align="center" style="width: 100%; height: 100%;background:none;
                                    padding-bottom: 120%; position: relative; margin: 0px; 
                                    padding-top: 0px; padding-left: 0px; padding-right: 0 px;">
            <iframe v-if="preview_file_load==false" style="position: absolute;
                                   top: 0; left: 0;
                                   width: 100%; height: 100%; 
                                   margin: 0px; padding: 0px;" 
             type="application/xml" :src="'data:' + file_nomina.type_file +';base64,' + src" >
            </iframe>   
            <br><br><br><br><br>
            <img width="400"  src="../../img/progress.gif"  v-if="preview_file_load">
            </div>
            </div>
        </div>
        </div>



        <div style="background:none;border:none" id="modalLoading" 
            class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="background:none;border:none">
                <div class="modal-header" style="background:none;">
                    <h5 class="modal-title" style="color:#fff;" >
                    Cargando Archivos, por favor se paciente...
                    </h5> 
                </div>
                <div class="modal-body">
                    <img width="400"  src="../../img/progress.gif">
                </div> 
                </div>
            </div>
        </div>

 
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
                                    <button type="button" name="filter" class="btn btn-info btn-xs" @click="getfile_nominas()"> filtrar</button>
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
                        <li><h3> Recibos de Nómina </h3></li>
                        <li>
                            <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="numByPag" @change="paginator(1)" > 
                                <option value=100  >100</option>
                                <option value=200 >200</option>
                                <option value=300 >500</option>
                                <option value=100000 >100000</option>
                            </select>
                        </li>
                        <li v-for="li in paginas" class="page-item">
                            <a class="page-link" @click="paginator(li.element)" >{{ li.element }} <div v-if="li.element == paginaActual" >_</div></a> 
                        </li>
                    </ul>  
                </nav>
                <td><button type="button" class="btn btn-info btn-xs edit" @click="add_file_nomina()">Agregar</button></td>
                <table class="table table-bordered table-striped">
                    <tr> 
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Empresa</th> 
                        <th>Código</th>
                        <th>Tipo</th>
                        <th></th> 
                    </tr>
                    <tr v-for="file_nomina in paginaCollection" >
                        <td>{{ file_nomina.id_file_nomina}}</td> 
                        <td>{{ file_nomina.nombre}}</td> 
                        <td>{{ file_nomina.empresa[0].empresa_observaciones}}</td>
                        <td>{{ file_nomina.code}}</td>
                        <td>{{ file_nomina.type_file.replace("application/","").replace("text/","")}}</td> 
                        <td style="width:150px" >
                            <!-- <button type="button" class="btn btn" @click="update_file_nomina(file_nomina.id_file_nomina)"><img src="../../img/lapiz.svg" width="25px" /></button> -->
                            <button type="button" class="btn btn" @click="delete_file_nomina(file_nomina.id_file_nomina)"><img src="../../img/borrar.png" width="25px" /></button>
                            <button type="button" class="btn" @click="get_file(file_nomina)" ><img src="../../img/ojo.png" width="25px" /></button>
                        </td> 
                    </tr>
                </table>
                <br>
                <br>
            </div>
        </div>  
            
        <div v-if="isFormCrud" >   
            <div class='form-group'>
                <label>empresa</label> 
                <select class='form-control' size='1'  v-model='file_nomina.id_empresa' >
                    <option value=0 >-</option>
                    <option v-for='rows in empresaCollection' v-bind:value='rows.id_empresa'>{{ rows.empresa_nombre }}</option>
                </select>
            </div> 
            <div class='form-group'>
                <label>file</label>
                <input type='file' class='form-control' id="file" multiple="multiple"  accept="application/pdf,application/xml"  />
            </div>   
 
            <br>
            <br>
            <div class="form-group">
                <td><button type="button" class="btn btn btn-xs" @click="cancel_file_nomina()"><img src="../../img/regresar.png" width="28px" /> Regresar</button></td> 
                <button @click="save_file_nomina()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
            </div>  
        </div>  
    </div>
</div>
<script type="text/javascript" src="../../controller/admin/c_file_nomina.js"></script>

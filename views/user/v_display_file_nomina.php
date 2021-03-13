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
        <div class="panel-body">
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  
                        <li><h3> Recibos de Nómina </h3></li>
                        <li>
                            <select class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="numByPag" @change="paginator(1)" > 
                                <option value=15 >15</option>
                                <option value=20 >20</option>
                                <option value=25 >25</option>
                                <option value=30 >30</option>
                            </select>
                        </li>
                        <li v-for="li in paginas" class="page-item">
                            <a class="page-link" @click="paginator(li.element)" >{{ li.element }} <div v-if="li.element == paginaActual" >_</div></a> 
                        </li>
                    </ul>  
                </nav> 
            </div>
        </div>  

        <div >
            <div class="row" >
                <div v-for="file_nomina in paginaCollection">  
                    <div class="card border-warning d-flex p-2"  class="col" style="margin:5px"  >
                        <div class="card-body"> 
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <img v-if="file_nomina.type_file=='application/pdf'" width="50px" src="../../img/pdf.png" alt="PDF">
                                    <img v-else src="../../img/xml.png" alt="Xml" width="50px" >
                                </li> 
                                <li class="list-group-item">Semana: {{ file_nomina.semana}}</li>
                                <li class="list-group-item">Ejercicio: {{ file_nomina.ejercicio}}</li> 
                            </ul> 
                            <button  type="button" class="btn btn-link" @click="get_file(file_nomina)">Abrir</button> 
                        </div>
                    </div>
                </div>
            </div>
        </div> 
  
        <br><br>
    </div>
</div>
<script type="text/javascript" src="../../controller/user/c_display_file_nomina.js"></script>

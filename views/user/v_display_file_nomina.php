<?php require "../header.php";?> 
<div class="container" style="width:90%">  
    <div id="app_file_nomina" style="margin-top:15px;"> 






        <div v-if="view_modal" style="background:none;" >  
            <transition name="model" style="background:none;" >
              <div class="modal-mask" style="background:none;"> 
                      <div class="modal-dialog modal-dialog-scrollable" style="background:#000;">
                        <div class="modal-content" style="background:none;">
                          <div class="modal-header" style="background:#000;"> 
                            <button type="button" style="background:#fff;" class="close" @click="view_modal=false"><span aria-hidden="true">&times;</span></button>
                          </div>     
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


       
            </transition>
        </div>








<!-- 
        <div style="background:none;" v-if="view_modal==true" id="mymodal" class="modal fade bd-example-modal-lg" tabindex="-1"  aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
        </div> -->
        
        <h3> Recibos de Nómina </h3>
        <div class="panel-body">
            <div class="table-responsive">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">  
                        <li></li>
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

        <div class="card" v-for="file_nomina in iterator">
        <div class="card-header"> 
        </div>
        <div class="card-block">
            <p class="card-title">
                Recibo de Nomina Ejercicio: {{file_nomina.ejercicio}} Semana: {{file_nomina.semana}}
            </p>
            <p class="card-text"></p>
            <table style="width:100%">
                <tr style="width:100%">
                    <td style="width:50%" v-for="iterator in file_nomina.files" >
                        <center>
                            <button @click="get_file(iterator)" class="btn btn-info" v-if="iterator.type_file=='application/pdf'">
                                <img  src="../../img/pdf.png" width="50px" />
                            </button>
                            <button @click="get_file(iterator)" class="btn btn-info" v-else>
                                <img src="../../img/xml.png" width="50px" />
                            </button>
                        </center> 
                    </td> 
                </tr>
            </table> 
        </div>
        </div>
 
        <br><br>
    </div>
</div>
<script type="text/javascript" src="../../controller/user/c_display_file_nomina.js"></script>

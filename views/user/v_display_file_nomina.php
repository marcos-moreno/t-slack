<?php require "../header.php";?> 
<div class="container" style="width:100%">  
    <div id="app_file_nomina" style="margin-top:15px;"> 

     
        <div v-if="view_modal"  class="modal-mask" style="height:100%" > 
            <div class="modal-dialog modal-dialog-centered"  >
                <div class="modal-content"  >  
                    <div class="modal-body"  >      
                        <img width="100%"  src="../../img/progress.gif">
                        Espera por favor...
                    </div>
                </div> 
            </div>
        </div>  
        
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
                        <br><div style="width:100%" :id="'pdf_' + iterator.id_file_nomina"></div>
                    </td> 
                </tr> 
            </table> 
        </div>
        </div> 
        <br><br>
    </div>
    <canvas id="the-canvas"></canvas>
</div>
<script src="http://mozilla.github.io/pdf.js/build/pdf.js"></script>
<script type="text/javascript" src="../../controller/user/c_display_files_nomina.js"></script>

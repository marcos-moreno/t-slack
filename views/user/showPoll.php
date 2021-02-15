<?php require '../header.php'; ?>
<div class="container" style="width:90%">  
    <div id="showPoll" style="margin-top:15px;"> 
    <h3>Encuestas Pendientes</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>
                        <!-- <th>id</th> -->
                        <th>Nombre</th>
                        <th>Observaciones</th>
                        <th>Válido Desde</th>
                        <th>Válido Hasta</th> 
                        <th></th> 
                    </tr>     
                    <tr v-for="row in poll">
                    <!-- <td>{{ row.id_encuesta }}</td> -->
                        <td>{{ row.nombre }}</td>
                        <td>{{ row.observaciones }}</td>
                        <td>{{ row.validodesde }}</td>
                        <td>{{ row.validohasta }}</td> 
                        <td><button type="button" class="btn btn-success  btn-xs" @click="openPoll(row.id_encuesta)">Responder</button></td>
                    </tr>
                </table> 
            </div> 

            <h3>Encuestas Terminadas</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>
                         <th>No.</th>
                        <th>nombre</th>
                        <th>Hora Respuesta</th>
                        <th>Válido Desde</th>
                        <th>Válido Hasta</th> 
                        <th>Estado Respuesta</th> 
                    </tr>     
                    <tr v-for="row in pollComplete">
                        <td>{{ row.no_enc }}</td>
                        <td>{{ row.nombre }}</td>
                        <td>{{ row.respuesta }}</td>
                        <td>{{ row.validodesde }}</td>
                        <td>{{ row.validohasta }}</td>  
                        <td>{{ row.estado }}</td>  
                    </tr>
                </table> 
            </div> 
    </div> 
</div>
<script type="text/javascript" src="../../controller/user/ctl_user_poll.js"></script>

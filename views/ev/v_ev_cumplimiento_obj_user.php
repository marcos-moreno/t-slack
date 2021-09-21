<?php require "../header.php";?> 
<div  class="container-fluid" style="width:90%;"> 
    <div id="app_ev_cumplimiento_obj" style="margin-top:15px;"> 
        <h4>Cumplimiento de Objetivos</h4>
      
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>   
                        <td>
                            <div class="pre-scrollable" >
                                <div class="alert alert-primary" v-if="typeMessage == 'info'" role="alert">{{msg2}}</div>
                                <div class="alert alert-danger"  v-if="typeMessage == 'error'" role="alert">{{msg2}}</div>
                                <div class="alert alert-success" v-if="typeMessage == 'success'" role="alert">{{msg2}}</div>
                            </div> 
                        </td> 
                    </tr>
                </table>                   
                
            
                        
                
               
            </div> 

            <div class="panel-body" v-if="tabla">
                
            <div class="container" >
                <!-- <button type="submit" @click="get_empleadoFilter2" class="btn-info">Verificar Indicadores</button><br><br> -->
                        
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" >Indicador</label>
                                    </div>
                                    <select class='form-control'  style="width:150px" @change="get_estado()" v-model='ev_cumplimiento.id_indicador'  > 
                                        <option > ------</option>
                                        <option v-for='rows in indicador' v-bind:value='rows.ev_indicador_general_id'>{{ rows.nombre }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div><br><br><br>

                    <div class="container" >
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" >Fecha Inicio</label>
                                        </div>
                                        <input class="form-control" type="datetime-local"  v-model='ev_cumplimiento.fechainicio'> 
                                    </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" >Fecha Termino</label>
                                    </div>
                                    <input class="form-control" type="datetime-local" v-model='ev_cumplimiento.fechatermino'> 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container" >
                        <div class="form-group">
                            <div class="row">
                                <div class="col-5">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" >Objetivo</label>
                                        </div>
                                        <input class="form-control" type="text" v-model='ev_cumplimiento.nombre_objetivo'> 
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" >Descripci&oacute;n</label>
                                        </div>
                                        <input class="form-control" type="text" v-model='ev_cumplimiento.descripcion'> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" >Estado</label>
                                        </div>
                                        <select class='form-control'  style="width:150px"   v-model='ev_cumplimiento.estado'> 
                                            <option v-for='rows in estados' v-bind:value='rows.value'> ({{ rows.value }}) {{ rows.descripcion }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div v-show="select2">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" >Estado</label>
                                        </div>
                                        <select class='form-control' style="width:150px"   v-model='ev_cumplimiento.estado'> 
                                            <option value='NS-NO seleccionado '>--------</option>
                                            <option>(NE) Negociaci&oacute;n &Eacute;xitosa</option>
                                            <option>(NNE) Negociaci&oacute;n no &Eacute;xitosa</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                
                    <div class="form-group" >
                        <td>
                            <button type="button" class="btn btn btn-xs btn-danger" @click="cancel_ev_cumpli()"> Cancelar</button>
                        </td> 
                            <button @click="save()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="18px" /> *Guardar</button>
                    </div>
                
                  
            </div>
            
            <div v-if="tabla==false">
                    
                 
                
            </div>
        


        
        </div>        
    </div>
</div>
<script type="text/javascript" src="../../controllers/ev/c_ev_cumplimiento_obj.js"></script>

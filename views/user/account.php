<?php require '../header.php';?> 
<div class="container" style="width:90%">  
    <div id="account" style="margin-top:15px;"> 
    <h3>Perfil</h3> 

      <!-- Modal -->
      <div class="modal fade" id="ModalMsg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Refividrio</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
             <div v-if="isHello" >Hola <font style='font-weight: bold;' v-if="isError==false" > <?php  echo($_SESSION["nombre"]) ?></font></div>  
             <div v-if="isError" ><font style='font-weight: bold;color:red;'>Error</font> </div> 
             
             {{ msg }}
            </div>
            <div class="modal-footer">
              <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Aceptar</button> -->
                 <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
              <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
          </div>
        </div>
      </div>      <!-- Modal -->

      <div class="form-group">
        <label>ID: {{account.id_empleado}}</label>  
      </div>  

      <div class="form-group">
        <label>Empresa</label>
        <input type="text" class="form-control" v-model="account.empresa_nombre"    :disabled="true" />
      </div>

      <div class="form-group">
        <label>Sucursal</label>
        <input type="text" class="form-control" v-model="account.segmento"    :disabled="true" />
      </div>

      <div class="form-group">
        <label>Nombre</label>
        <input type="text" class="form-control" v-model="account.nombre"    :disabled="true" />
      </div>

      <div class="form-group">
        <label>Apellido Paterno</label>
        <input type="text" class="form-control" v-model="account.paterno"    :disabled="true" />
      </div>

      <div class="form-group">
        <label>Apellido Materno</label>
        <input type="text" class="form-control" v-model="account.materno"    :disabled="true" />
      </div>

      <div class="form-group">
        <label>ID Cerberus</label>
        <input type="text" class="form-control" v-model="account.id_cerberus_empleado"    :disabled="true" />
      </div>
 
      <div class="form-group">
        <label>Fecha de Nacimiento</label>
        <input type="date" class="form-control" v-model="account.fecha_nacimiento" />
        <div style="color:red;" id="error_fecha_naci" ></div>
      </div>  

      <div class="form-group">
        <label>*celular</label>
        <input type="text" class="form-control" v-model="account.celular"   />
        <div style="color:red;" id="error_celular" ></div>
      </div> 

      <div class="form-group">
        <label>*Correo</label>
        <input type="mail" class="form-control" v-model="account.correo" />
        <div style="color:red;" id="error_correo" ></div> 
      </div> 
         
      <div class="form-group">
        <label>*Usuario</label>
        <input type="text" class="form-control" v-model="account.usuario" :disabled="true"  />
        <div style="color:red;" id="error_usuario" ></div> 
      </div> 

      <div class="form-group">
        <label>*Genero</label>
        <input type="radio" id="H" value="H" v-model="account.genero"  >
        <label for="H">Hombre</label> 
        <input type="radio" id="M" value="M" v-model="account.genero"  >
        <label for="M">Mujer</label>
        <div style="color:red;" id="error_genero" ></div> 
      </div>  

      <div class='form-group'>
          <div class="row">
              <div class="col-sm">
                  <label>Playera</label> 
                  <select :disabled="account.id_talla_playera != null" class='form-control' size='1'  v-model='account.id_talla_playera' >
                      <option value='null' >-</option>
                      <option v-for='rows in tallaCollection' v-bind:value='rows.id_talla'>{{ rows.valor }}</option>
                  </select>
                  <div style="color:red;" id="error_playera" ></div>

              </div>  
              <div class="col-sm">
                  <label>Número Zapato</label> 
                  <select :disabled="account.id_numero_zapato != null"  class='form-control' size='1'  v-model='account.id_numero_zapato' >
                      <option value='null' >-</option>
                      <option v-for='rows in numsZapatoCollection' v-bind:value='rows.id_talla'> {{ rows.valor }}</option>
                  </select>
                  <div style="color:red;" id="error_zapato" ></div>
              </div>  
          </div>  
      </div> 

       <div v-if="modalRegistros">
        <transition name="model">
        <div class="modal-mask"> 
          <div  class="modal-dialog modal-dialog-scrollable" >
            <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Registros</h4> 
              <button type="button" class="close" @click="modalRegistros=false"><span aria-hidden="true">&times;</span></button>
            </div>

            <div class="form-group">
                <div class="row"> 
                  <div class="col-sm"> 
                    <input type="date" class="form-control" v-model="FechaI"/>
                  </div>
                  <div class="col-sm"> 
                    <input type="date" class="form-control" v-model="FechaF"/>
                  </div>
                </div>
                <button @click="findRegister()" class="btn btn-info btn-xs" >Buscar</button>
            </div>   

            <div class="modal-body"> 
              <br />
              <div >
                <ul class="list-group">
                  <li v-for="item in registros" class="list-group-item d-flex justify-content-between align-items-center">
                    <h4>{{ item.fecha }}</h4>  
                    <ul > 
                      <li v-for="hora in item.hora">
                        {{ hora }}   
                      </li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>

            </div>
          </div>
        </div>
        </transition>
      </div>
      <br>
      <br> 
      <div class="form-group">
          <div class="row">
            <div class="col-sm">
              <button @click="save()" class="btn btn-info btn-xs" ><img src="../../img/send.png" width="15%" />Guardar</button>
            </div>
            <div class="col-sm"> 
              <button @click="modalRegistros=true" class="btn btn" ><img src="../../img/check-list.svg" width="45px" /></button> 
            </div>
          </div>
      </div>  
    </div> 
</div>
<script type="text/javascript" src="../../controller/user/user_account2.js"></script>

<?php  
	require_once 'models/auth/check.php'; 
	if (check_session()) {   
		header('Location: '.$_SESSION['pagina_inicio']);
	}
?>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Refividrio</title> 
	<link rel="stylesheet" href="css/boostrap4.5.css"> 
	<script src="lib/js/vue.js"></script>
	<script src="lib/js/axios.min.js"></script> 
</head>

<body class="bg-gradient-primary" style="background-image: url('img/walper.jpg');">
	<div id="login">
		<div v-if="msgErro!=''" class="alert alert-danger" role="alert">
			{{msgErro}}
		</div>
		<div v-if="msg!=''" class="alert alert-success" role="alert">
			{{msg}}
		</div>
		<center>
			<div class="modal-dialog" style="color: #636363; width: 350px;">
				<div class="modal-content" style="opacity: 0.85;"> 
					<div class="modal-header"> 
						<div >
							<img src="img/logo.png" alt="refividrio">
						</div>  
					</div> 
					<div class="modal-body"> 
							<div class="form-group">
								<input :disabled="!isRols" type="name" class="form-control" name="user" 
								placeholder="user - cel - mail" v-model="user" required="required">		
							</div>
							<div class="form-group">
								<input :disabled="!isRols" type="password" v-model="password"  class="form-control" v-on:keyup.enter="getRoles()" name="password" id="password" placeholder="Password"  required="required">	
							</div>     
							<div class="form-group" >
								<select :disabled="isRols" v-model="id_rol" class="form-control" id="rol">
									<option v-for='rol in roles' v-bind:value='rol.id_rol'>
										{{rol.rol}}
									</option>
								</select>
								<br/> 
							</div>
					</div>
					<div class="modal-footer">
						<button v-if="isRols" type="submit" id="button" class="btn btn-primary btn-block" 
								style="color: #fff; border-radius: 4px; background: #60c7c1 !important; line-height: normal; border: none;"  
								@click="getRoles()">Comprobar</button>
						<button v-if="!isRols"  type="submit" id="button" class="btn btn-primary btn-block" 
								style="color: #fff; border-radius: 4px; background: #60c7c1 !important; line-height: normal; border: none;"  
								@click="login()">Ingresar</button>
						<button v-if="!isRols" type="submit"
								class="btn btn-danger btn-block"
								style="color: #fff; border-radius: 4px; background: #D75A5A !important; line-height: normal; border: none;"  
								@click="reset()">Cancelar</button>
					</div> 
				</div>
			</div>
		</center>
	</div>     
</body>

<script src="controllers/generales/c_login_2.js"></script> 
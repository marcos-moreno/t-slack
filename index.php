<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Refividrio</title> 
	<link rel="stylesheet" href="css/boostrap4.5.css">
	<!-- <link href="css/sb-admin-2.min.css" rel="stylesheet"> -->
	<script src="lib/js/vue.js"></script>
	<script src="lib/js/axios.min.js"></script> 
</head>

<body class="bg-gradient-primary"
style="background-image: url('img/walper.jpg');"
>
	<div id="login">
		<div class="alert alert-danger" role="alert" id="msgErro" style="display:none"></div>
		<div class="alert alert-success" role="alert" id="msg" style="display:none" ></div>
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
								<input type="name" class="form-control" name="user" 
								placeholder="user - cel - mail" id="user" required="required">		
							</div>
							<div class="form-group">
								<input type="password" class="form-control" v-on:keyup.enter="seachUser()" name="password" id="password" placeholder="Password"  required="required">	
							</div>        
							<div class="form-group" >
								<select style="display:none" class="form-control" id="rol"></select><br/> 
							</div>  
					</div>
					<div class="modal-footer">
						<button type="submit" id="button" class="btn btn-primary btn-block" style="color: #fff; border-radius: 4px; background: #60c7c1 !important; line-height: normal; border: none;"  @click="seachUser()">Comprobar</button>
						<button type="submit" id="buttonCancel" class="btn btn-danger btn-block" style="color: #fff; border-radius: 4px; background: #D75A5A !important; line-height: normal; border: none;display: none;"  @click="reset()">Cancelar</button>
					</div> 
				</div>
			</div>
			</center>
	</div>     
</body>

<script src="controllers/generales/c_login.js"></script> 
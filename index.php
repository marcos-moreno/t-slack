<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Refividrio</title> 
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"> 
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>  
<link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

<div id="login">
<div class="alert alert-danger" role="alert" id="msgErro" style="display:none"> 
</div>
<div class="alert alert-success" role="alert" id="msg" style="display:none" > 
</div>
	<center>
	<div class="modal-dialog" style="color: #636363; width: 350px;">
		<div class="modal-content"> 
			<div class="modal-header"> 
				<div >
					<img src="img/logo.png" alt="refividrio">
				</div>  
			</div> 
			<div class="modal-body"> 
					<div class="form-group">
						<input type="text" class="form-control" name="user" placeholder="user" id="user" required="required">		
					</div>
					<div class="form-group">
						<input type="password" class="form-control" name="password" id="password" placeholder="Password" required="required">	
					</div>        
                    <div class="form-group">
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

<script src="controller/generales/c_login_.js"></script> 
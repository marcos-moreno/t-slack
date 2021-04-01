<?php 
/**  @author Marcos Moreno   */  
class msg 
{
	 	
	public function get_msg($mensaje){  
        return  '<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
				<meta charset="utf-8">  
			<style> 
			html,
			body {
				margin: 0 auto !important;
				padding: 0 !important;
				height: 100% !important;
				width: 100% !important;
				background: #f1f1f1;
			} 
			.primary{
				background: #f3a333;
			} 
			.bg_white{
				background: #ffffff;
			}
			.bg_light{
				background: #fafafa;
			}
			.bg_black{
				background: #000000;
			}
			.bg_dark{
				background: rgba(0,0,0,.8);
			}
			.email-section{
				padding:2.5em;
			} 
			.btn{
				padding: 10px 15px;
			}
			.btn.btn-primary{
				border-radius: 30px;
				background: #f3a333;
				color: #ffffff;
			} 
			h1,h2,h3,h4,h5,h6{
				font-family: "Playfair Display", serif;
				color: #000000;
				margin-top: 0;
			} 
			body{
				font-family: "Montserrat", sans-serif;
				font-weight: 400;
				font-size: 15px;
				line-height: 1.8;
				color: rgba(0,0,0,.4);
			} 
			a{
				color: #f3a333;
			}  
			.logo h1{
				margin: 0;
			}
			.logo h1 a{
				color: #000;
				font-size: 20px;
				font-weight: 700;
				text-transform: uppercase;
				font-family: "Montserrat", sans-serif;
			} 
			.hero{
				position: relative;
			} 
			.hero .text{
				color: rgba(255,255,255,.8);
			}
			.hero .text h2{
				color: #ffffff;
				font-size: 30px;
				margin-bottom: 0;
			} 
			.heading-section h2{
				color: #000000;
				font-size: 28px;
				margin-top: 0;
				line-height: 1.4;
			}
			.heading-section .subheading{
				margin-bottom: 20px !important;
				display: inline-block;
				font-size: 13px;
				text-transform: uppercase;
				letter-spacing: 2px;
				color: rgba(0,0,0,.4);
				position: relative;
			}
			.heading-section .subheading::after{
				position: absolute;
				left: 0;
				right: 0;
				bottom: -10px;
				content: "";
				width: 100%;
				height: 2px;
				background: #f3a333;
				margin: 0 auto;
			} 
			.heading-section-white{
				color: rgba(255,255,255,.8);
			}
			.heading-section-white h2{
				font-size: 28px;
				font-family: 
				line-height: 1;
				padding-bottom: 0;
			}
			.heading-section-white h2{
				color: #ffffff;
			}
			.heading-section-white .subheading{
				margin-bottom: 0;
				display: inline-block;
				font-size: 13px;
				text-transform: uppercase;
				letter-spacing: 2px;
				color: rgba(255,255,255,.4);
			} 
			.icon{
				text-align: center;
			}  
			.text-services{
				padding: 10px 10px 0; 
				text-align: center;
			}
			.text-services h3{
				font-size: 20px;
			} 
			.text-services .meta{
				text-transform: uppercase;
				font-size: 14px;
			} 
			.text-testimony .name{
				margin: 0;
			}
			.text-testimony .position{
				color: rgba(0,0,0,.3);

			}   
			.counter-text{
				text-align: center;
			}
			.counter-text .num{
				display: block;
				color: #ffffff;
				font-size: 34px;
				font-weight: 700;
			}
			.counter-text .name{
				display: block;
				color: rgba(255,255,255,.9);
				font-size: 13px;
			}  
			</style> 
			</head>
			<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #222222;"> 
			<center style="width: 100%; background-color: #f1f1f1;">
				<div style="display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
				&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
				</div>
				<div style="max-width: 600px; margin: 0 auto;" class="email-container"> 
				<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
					<tr>
					<td class="bg_white logo" style="padding: 1em 2.5em; text-align: center">
						<h1><a href="https://dev.refividrio.com.mx/encuesta_refividrio/">NOTIFICACIONES REFIVIDRIO</a></h1>
					</td>
					</tr>  
					<tr>
						<td class="bg_white">
							<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td class="bg_dark email-section" style="text-align:center;">
									<div class="heading-section heading-section-white"><br/>
									<!--<span class="subheading">Notificaciones refividrio</span>
										<h2>Titulo</h2> --> 
										<img src="https://dev.refividrio.com.mx/encuesta_refividrio/img/logo.png" width="200px" /> 
									<p>Este Correo es una Notificación de refividrio no es necesaria una respuesta.</p>
									</div>
								</td>
							</tr> 
							<tr>
								<td class="bg_light email-section">
									<div class="heading-section" style="text-align: center; padding: 0 30px;">
										 
									<h2>'. $mensaje .'</h2>
									<p>Información de Encuesta.</p>
									</div> 
								</td>
							</tr><!-- end: tr --> 
							</table> 
						</td>
						</tr><!-- end:tr -->
				<!-- 1 Column Text + Button : END -->
				</table> 
				</div>
			</center>
			</body>
			</html>' ;
		}
}
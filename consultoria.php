<?php
require_once "includes/funciones.inc.php";

// Comprobar si se guarda
if (@$_POST ['submit']) {
	$tipo_solicitud = @$_POST ['tipo_solicitud'];
	$nombre = @$_POST ['nombre'];
	$apellidos = @$_POST ['apellidos'];
	$email = @$_POST ['email'];
	$nif = @$_POST ['nif'];
	$tfno = @$_POST ['tfno'];
	$observaciones = @$_POST ['observaciones'];
	$fecha_solicitud = new DateTime ();
	
	try {
		$res = consulta ( "insert INTO SOLICITUDES (TIPO_SOLICITUD, NOMBRE, APELLIDOS, EMAIL, NIF, TFNO_CONTACTO,
				FECHA_SOLICITUD, OBSERVACIONES, LEIDO)
				VALUES ('$tipo_solicitud', '$nombre', '$apellidos', '$email', '$nif',
				'$tfno', '" . ($fecha_solicitud->format ( 'Y-m-d' )) . "', '$observaciones', '0')" );
		
		global $produccion;
		$cabeceras2 = 'From: gestion@cerroviejo.org' . "\r\n" .
				'Reply-To: gestion@cerroviejo.org' . "\r\n" .
				"Bcc: " . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
		$correo = 'Se ha recibido un nuevo mensaje de '.$nombre.' '.$apellidos.':
		Email: '.$email.'
		Teléfono: '.$tfno.'
		'.$observaciones;
		
		if ($res) {
			if ($produccion==0) {
				mail ( 'jmtalavera@gmail.com', '[DESARROLLO] Nuevo mensaje recibido: '.$tipo_solicitud , $correo, $cabeceras2);
			} else {
				mail ( 'gestion@cerroviejo.org', 'Nuevo mensaje recibido: '.$tipo_solicitud , $correo, $cabeceras2);
			}
			$_SESSION ['mensaje_generico'] = 'Solicitud registrada correctamente. En breve nos pondremos en contacto con usted.';
			Header ( "Location: index.php" );
		} else {
			$mensaje = 'No se ha podido registrar la solicitud. Int&eacute;ntelo de nuevo m&aacute;s tarde.';
		}
	} catch ( Exception $ex ) {
		// Devuelve el mensaje de error
		$mensaje = $ex->qetMessage ();
	}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="DC.Language" scheme="RFC1766" content="Spanish" />
<meta name="title" content="CerroViejo">
<meta name="viewport"
	content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="description"
	content="Grupo para dar a conocer de lo importante que es alimentarse de forma sana, mediante alimentación ecológica." />
<meta name="keywords"
	content="ecologico,agricultura,alimentos locales,sin animo de lucro, sano, sana, cerroviejo, sevilla, cazalla de la sierra, ecologica,asociacion, grupo, consultoria ecologica, consultoria agricola, comercio justo, garantia ecologica" />
<meta name="robots" content="index, noarchive" />
<meta http-equiv="Cache-Control"
	content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />

<title>CerroViejo</title>
<link rel="stylesheet" href="css/inuit.css" />
<link rel="stylesheet" href="css/fluid-grid16-1100px.css" />
<link rel="stylesheet" href="css/eve-styles.css" />
<link rel="stylesheet" href="css/formalize.css" />
<link rel="shortcut icon" href="icon.png" />
<link rel="apple-touch-icon-precomposed" href="img/icon.png" />

<script src="js/respond-min.js" type="text/javascript"></script>
<script
	src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"
	type="text/javascript"></script>
<script>window.jQuery || document.write('<script src="scripts/jquery164min.js">\x3C/script>')</script>
<!--local fallback for JQuery-->
<script src="js/jquery.flexslider-min.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/flexslider.css" />

<script type="text/javascript">
			  $(window).load(function() {
				$('.flexslider').flexslider({
					  animation: "slide",<!--you can also choose fade here-->
					  directionNav: false,<!--Attention: if you choose true here, the nav-buttons will also appear in the ticker! -->
					  keyboardNav: true,
					  mousewheel: true
				});
			  });
			</script>

<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			
			  ga('create', 'UA-51824827-1', 'cerroviejo.org');
			  ga('send', 'pageview');
			</script>

<!--Hide the hr img because of ugly borders in IE7. You can change the color of border-top to display a line -->
<!--[if lte IE 7]>

                        <style>
                    		hr { display:block; height:1px; border:0; border-top:1px solid #fff; margin:1em 0; padding:0; }
                            .grid-4{ width:22% }
                        </style>
                    <![endif]-->

</head>
<!--===============================================================  Logo, social and menu =====================================================================================-->

<body>
	<div class="wrapper">
		<a href="index.php" id="logo"><img src="img/cerroViejo.png"
			alt="something" />
			<h1 class="accessibility">GRUPO CERRO VIEJO</h1></a>

		<!--These are just samples, use your own icons. If you use larger ones, make sure too change the css-file to fit them in.
                       Dont´t forget to place your links -->
		<div class="social">
			<form action="index.php" method="post" enctype="multipart/form-data"
				autocomplete="off">
				<input type="text" class="input_medium" id="usuario" name="usuario"
					required="required" placeholder="Usuario" title="Usuario" /> <input
					type="password" class="input_medium" id="clave" name="clave"
					required="required" placeholder="Contraseña" title="Contraseña" />
				<input type="submit" id="submit" name="submit" value="Acceder"
					class="float_left" />
			</form>

		</div>


		 <ul id="nav" class="main">
			<li><a href="proveedores.html"><img src="img/proveedor.png" alt="" /><br>NUESTROS<br> PROVEEDORES</a></li>
			<li><a href="trabajamos.html"><img src="img/trabajamos.png" alt="" /><br>¿CÓMO<br> TRABAJAMOS?</a></li>
			<li><a href="solicitud.php"><img src="img/nuevo_socio.png" alt="" /><br>QUIERO SER<br> SOCIO</a></li>
			<li><a href="http://comunidad.cerroviejo.org"><img src="img/consultor.png" alt="" /><br>COMUNIDAD<br>CERROVIEJO</a></li>
			<li><a href="consultoria.php"><img src="img/calendario.png" alt="" /><br>SOLICITUD DE<br> CONSULTOR&Iacute;A</a></li>
			
		</ul>


	</div>
	<!--end of wrapper div-->
	<div class="clear"></div>

	<!--===============================================================  Left content, address =====================================================================================-->
	<div class="wrapper">

		<div class="grids top">

			<div class="grid-6 grid">

				<div class="green bottom">
					<h3>Consultor&iacute;a</h3>
					<p>
						En Cerro Viejo ofrecemos un servicio de Consultoría-Asesoría en producción y 
						comercialización de alimentos ecológicos, por un equipo de personas con dilatada 
						experiencia en el sector. <br><br>
						En la actualidad nos encontramos desarrollando un programa denominado Red de 
						Comercialización de Alimentos Ecológicos, que consiste en establecer los pilares 
						principales de un sistema de distribución de alimentos ecológicos desde el productor al consumidor. 
						Los actores de dicho programa son proveedores-comercializadores-compradores.
					</p>
					<p>
						<b>Las líneas de trabajo en las que realizamos la consultoría y asesoría, por sectores</b>, son:<br>
						<ul>
							<li>Administración y gestión de la pequeña empresa agroalimentaria.</li>
							<li>Ganadería: asesoramiento técnico veterinario especializado. Puesta en marcha de línea de transformación y comercialización de alimentos cárnicos (Servicios Veteco).</li>
							<li>Agricultura: asesoramiento técnico agrícola especializado. Puesta en marcha de línea de transformación y comercialización de alimentos vegetales (Servicios Arieco).</li>
							<li>Sistemas de Autocontrol APPCC para Empresas Agroalimentarias: cárnica, láctica y conservera. Comercios al por Mayor y el por Menor, Restauración y Hostelería. (Servicios Veteco)</li>
							<li>Estudio, diseño y legalización de proyectos para la Industria Agroalimentaria (Servicios Intragrec).</li>
							<li>Comercialización y Marketing de alimentos ecológicos (Servicios Alimentación Cerro Viejo)</li>
							<li>Certificaciones de calidad: Certificados de producción ecológica, Certificados de Calidad Agroalimentaria, Certificados de Artesanía Alimentaria (Servicios Veteco).</li>
						</ul>	
						Si estás interesado en alguna de las líneas de trabajo que tenemos y/o necesitas de éstos u otros servicios, por favor,
					 ponte en contacto con nosotros y solicita un presupuesto personalizado a las características de tu proyecto.				
					</p>
				</div>

			</div>

			<!--===============================================================  Contact form =====================================================================================-->
			<div class="grid-10 grid">
				<h2>D&eacute;janos tus datos</h2>

				<!--An example for a contact form from formalize.me, table in use.</h6>-->
			            <?php
															if (isset ( $mensaje )) {
																echo "<h5>$mensaje</h5>";
															}
															?>
               <form action="#" method="post"
					action="consultoria.php" enctype="multipart/form-data"
					autocomplete="off">
					<table class="form">
						<tr>
							<th><label for="nombre"> Nombre </label></th>
							<td><input type="text" class="input_full" id="nombre"
								name="nombre" value="<?=@$nombre?>" maxlength="100" required />

							</td>
						</tr>
						<tr>
							<th><label for="apellidos"> Apellidos </label></th>
							<td><input type="text" class="input_full" id="apellidos"
								name="apellidos" value="<?=@$apellidos?>" maxlength="120"
								required /></td>
						</tr>
						<tr>
							<th><label for="nif"> DNI </label></th>
							<td><input type="text" class="input_full" id="nif" name="nif"
								value="<?=@$nif?>" maxlength="10" /></td>
						</tr>
						<tr>
							<th><label for="email"> Email </label></th>
							<td><input type="email" class="input_full" id="email"
								name="email" value="<?=@$email?>" maxlength="120" required /></td>
						</tr>

						<tr>

							<th><label for="tfno"> Tel&eacute;fono </label></th>
							<td><input type="tel" class="input_full" id="tfno" name="tfno"
								value="<?=@$tfno?>" maxlength="9" required /></td>
						</tr>
						<tr style="display: none; visibility: hidden;">
							<th><label for="tipo_solicitud"> Solicitud </label></th>
							<td><select id="tipo_solicitud" class="input_full" name="tipo_solicitud">
									<option value="CONSULTORÍA" selected="selected">de Consultoría</option>
							</select></td>
						</tr>
						<tr>
							<th><label for="observaciones"> Observaciones </label></th>
							<td><textarea class="input_full" id="observaciones"
									name="observaciones" maxlength="1000" rows="8" required
									placeholder="Por favor, introduzca sus observaciones"><?=@$observaciones?></textarea>
							</td>

						</tr>
						<tr>
							<th><label for="description"> ¿Has terminado? </label></th>
							<td><input type="submit" id="submit" name="submit"
								value="Enviar Solicitud" class="float_left" /></td>

						</tr>

					</table>


				</form>

			</div>
			<!--end of grid-10-->
		</div>
		<!--end of grids-->

	</div>
	<!--end of wrapper-->

	<!--========================================================================== Content Part 2 =====================================================================================-->
	<div class="wrapper">
		<!--end of grids-->
	</div>
	<!--end of wrapper-->
	<hr />
	<!--========================================================================== Ticker =====================================================================================-->

	<!--If you don´t want to use the ticker just delete or comment it and uncomment this to use static text instead-->
	<!--<div class="intro">
                       <p class="text-center">
                       Hey, let your creativity flow and create something great!
                       </p>
                       </div>-->

	<!--This is FlexSlider and uses the same settings like the one at the top. If you change them, be aware that it is for both sliders!-->

	<div class=" grids flexslider intro ticker top">
		<!--http://flex.madebymufffin.com/-->
		<noscript>Para verlo necesitarías activar Javascript en las opciones
			de tu navegador</noscript>

		<ul class="slides">

			<li>
				<p class="text-center">Comercio Justo</p>
			</li>
			<li>
				<p class="text-center">Alimentación Sana</p>
			</li>
			<li>
				<p class="text-center">Apoyo a Pequeños Agricultores</p>
			</li>

			<li>
				<p class="text-center">Garantía Ecológica</p>
			</li>

			<li>
				<p class="text-center">Sin Ánimo de Lucro</p>
			</li>


		</ul>

	</div>
	<!--end of div flexslider-->
	</div>
	<!--end of wrapper-->
	<!--========================================================================== Footer =====================================================================================-->
	<div class="wrapper">
		<div id="footer">


			<div class="grids">


				<div class="grid-15 grid text-center">
					<p align="center" class="grid-16">
						Para contactar con el Grupo Cerro Viejo mediante correo
						electrónico:&nbsp;<a href="mailto:info@cerroviejo.org"
							title="Contacto">&nbsp;pulse aquí</a>
							<a href="https://play.google.com/store/apps/details?id=org.cerroviejo.android" target="_blank">
								<img style="float:right;" src="img/android-app-on-google-play.png" alt="App.Android" width="120" />
							</a>
					</p>
				</div>

			</div>
			<!--end of grids-->
		</div>
		<!--end of footer-->
	</div>
	<!--end of wrapper-->


	<script type="text/javascript"> <!--Outdated browsers warning/message and link to Browser-Update. Comment or delete it if you don´t want to use it-->
						var $buoop = {} 
						$buoop.ol = window.onload; 
						window.onload=function(){ 
						 try {if ($buoop.ol) $buoop.ol();}catch (e) {} 
						 var e = document.createElement("script"); 
						 e.setAttribute("type", "text/javascript"); 
						 e.setAttribute("src", "http://browser-update.org/update.js"); 
						 document.body.appendChild(e); 
						} 
						</script>

</body>
</html>
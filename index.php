<?php
require_once "includes/funciones.inc.php";

$mensaje = NULL;

$usuario = "";
global $produccion;

if (@$_POST['submit']) {
	$usuario = @$_POST['usuario'];
	$clave = @$_POST['clave'];
	
	try {
		$res = consulta ("select * from USUARIOS where ID_USUARIO='$usuario' and CLAVE='$clave' and ACTIVO=1");
		
		if ($fila = extraer_registro ($res)) {
			$idUsuario = $fila['ID_USUARIO'];
			$tipoUsuario = $fila['TIPO_USUARIO'];
			$nombreCompleto = $fila['NOMBRE']." ".$fila['APELLIDOS'];
			
			$_SESSION['ID_USUARIO'] = $idUsuario;
			$_SESSION['TIPO_USUARIO'] = $tipoUsuario;
			$_SESSION['NOMBRE_COMPLETO'] = $nombreCompleto;
			$_SESSION['CORREO_USUARIO'] = $fila['EMAIL'];
			$_SESSION['SUBGRUPO'] = $fila['ID_SUBGRUPO'];
			consultarFechaDesdeCalculoSaldoUsuarios();
			
			if ($tipoUsuario=='USUARIO' && strtolower($usuario)==strtolower($clave)) {
				$_SESSION['mensaje_generico'] = 'Por favor, por seguridad, cambie la contrase&ntilde;a en la pesta&ntilde;a MI CLAVE.';
			}
			
			if ($_SESSION['TIPO_USUARIO']=='USUARIO') {
				header ("Location: ./usuarios/index.php");
			} else if ($_SESSION['TIPO_USUARIO']=='ADMINISTRADOR') {
				header ("Location: ./administrador/index.php");
			} else if ($_SESSION['TIPO_USUARIO']=='REPARTIDOR') {
				header ("Location: ./administrador/productos.php?idCategoria=-1");
			} else {
				$mensaje = 'Tipo de usuario incorrecto.';
			}
			exit;
		} else {
			$mensaje = 'Usuario / clave incorrecto.';
		}
	} catch (Exception $ex) {
		//Devuelve el mensaje de error
		$mensaje = $ex->qetMessage();
	}
}

$clave = "";
?><!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="DC.Language" scheme="RFC1766" content="Spanish" />
    <meta name="title" content="CerroViejo"> 
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
	<meta name="description" content="Grupo para dar a conocer de lo importante que es alimentarse de forma sana, mediante alimentación ecológica." /> 
	<meta name="keywords" content="alimentos ecológicos, agricultura ecológica, consumo responsable, comercio justo, ecologico, agricultura, alimentos locales, sin animo de lucro, sano, sana, cerroviejo, alimentos ecológicos sevilla, cazalla de la sierra, ecologica,asociacion, grupo, consultoria ecologica, consultoria agricola, comercio justo, garantia ecologica" />
	<meta name="robots" content="index, noarchive" /> 
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	
            <link rel="stylesheet" href="css/inuit.css" />
            <link rel="stylesheet" href="css/fluid-grid16-1100px.css" />
            <link rel="stylesheet" href="css/eve-styles.css" />
             <link rel="stylesheet" href="css/formalize.css" />
            <link rel="shortcut icon" href="icon.png" />
            <link rel="apple-touch-icon-precomposed" href="img/icon.png" />
            
            <script src="js/respond-min.js" type="text/javascript"></script>
            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
            <script>window.jQuery || document.write('<script src="scripts/jquery164min.js">\x3C/script>')</script><!--local fallback for JQuery-->
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
      <a href="index.php" id="logo"><img src="img/cerroViejo.png" alt="something" />
                     <h1 class="accessibility">GRUPO CERRO VIEJO</h1></a>
	   <div class="social">
           <form action="index.php" method="post" enctype="multipart/form-data" autocomplete="off"> 
				<input type="text" class="input_medium" id="usuario" name="usuario" required="required" placeholder="Usuario" title="Usuario" />
				<input type="password" class="input_medium" id="clave" name="clave" required="required" placeholder="Contraseña" title="Contraseña"/>
				<input type="submit" id="submit" name="submit" value="Acceder" class="float_left" />
            </form>
        <?php 
			if ($mensaje) {
				echo "<h5>$mensaje</h5>";
			}
			if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5>".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			} 
			if ($produccion==0) {
				echo "<h5>ENTORNO DE DESARROLLO</h5>";
			}
		?>
        </div>
        
        <ul id="nav" class="main">
			<li><a href="proveedores.html"><img src="img/proveedor.png" alt="" /><br>NUESTROS<br> PROVEEDORES</a></li>
			<li><a href="trabajamos.html"><img src="img/trabajamos.png" alt="" /><br>¿CÓMO<br> TRABAJAMOS?</a></li>
			<li><a href="solicitud.php"><img src="img/nuevo_socio.png" alt="" /><br>QUIERO SER<br> SOCIO</a></li>
			<li><a href="http://comunidad.cerroviejo.org"><img src="img/consultor.png" alt="" /><br>COMUNIDAD<br>CERROVIEJO</a></li>
			<li><a href="consultoria.php"><img src="img/calendario.png" alt="" /><br>SOLICITUD DE<br> CONSULTOR&Iacute;A</a></li>
			
		</ul>
                    
            
        </div><!--end of wrapper div-->    
	<div class="clear"></div> 
<!--========================================================================== Intro and FlexSlider =====================================================================================-->    

	<div class="wrapper">
 		<div class="grids top">
                <div class="grid-6 grid intro">
                 <h2>MISIÓN</h2>
                       <p class="textintro">Este Grupo, nació por la simpática perseverancia de dos personas cabezotas de dar a conocer y que tomaran conciencia amigos y conocidos de lo relevante que es para la salud alimentarse de forma sana, mediante alimentación ecológica. </p>

<p class="textintro">Esta modélica iniciativa, tras cuatro años de evolución, cuenta con un centenar de socios de diferentes ámbitos y vocaciones: familias de un variado estrato social o ámbito cultural, servicios de hospedaje, Catering, Restaurantes y Centros Educativos (Residencias Escolares y Granjas Escuelas) son algunos de los ejemplos de los agentes participantes de Cerro Viejo. </p>

<p class="textintro"></p>
                                        
                 </div><!--end of slogan div-->
 
                 <div class="grid-10 grid"> 
                  <div class="flexslider">                  
                      <noscript>Para verlo necesitarías activar Javascript en las opciones de tu navegador</noscript> 

  						<ul class="slides">
                             
                            	<li>
                        				<a href="#"><img src="img/IMG-20131102-WA0000.jpg" alt="demo-image" />
                                                                               </a>
                                </li>
                                <li>
                        				<a href="#."><img src="img/IMG-20131102-WA0002.jpg" alt="demo-image" />
                                        <!--<p class="flex-caption">Texto sobre imagen</p>-->
                                        </a>
                                </li> 
                              
                                <li>                                  
                        				<a href="#"><img src="img/IMG-20131222-WA0006.jpg" alt="demo-image" />
                                       
                                        </a>
                                </li>
                               
                                <li>                                
                        				<a href="#"><img src="img/IMG-20131223-WA0065.jpg" alt="demo-image" />
                                                                               </a>
                                </li>
								<li>                                
                        				<a href="#"><img src="img/IMG-ROTACION-CARNES01.jpg" alt="demo-image" />
                                                                               </a>
                                </li>
								<li>                                
                        				<a href="#"><img src="img/IMG-ROTACION-CARNES02.jpg" alt="demo-image" />
                                                                               </a>
                                </li>
								<li>                                
                        				<a href="#"><img src="img/IMG-ROTACION-CARNES03.jpg" alt="demo-image" />
                                                                               </a>
                                </li>
								<li>                                
                        				<a href="#"><img src="img/IMG-ROTACION-CARNES04.jpg" alt="demo-image" />
                                                                               </a>
                                </li>
								<li>                                
                        				<a href="#"><img src="img/IMG-ROTACION-CARNES05.jpg" alt="demo-image" />
                                                                               </a>
                                </li>
								<li>                                
                        				<a href="#"><img src="img/IMG-ROTACION-FRUTA01.jpg" alt="demo-image" />
                                                                               </a>
                                </li>
                                <li>                                
                        				<a href="#"><img src="img/IMG-ROTACION-VARIOS01.jpg" alt="demo-image" />
                                                                               </a>
                                </li>
								<li>                                
                        				<a href="#"><img src="img/IMG-ROTACION-VARIOS02.jpg" alt="demo-image" />
                                                                               </a>
                                </li>
                                                             							
                            </ul>
                            
						</div><!--end of div flexslider-->
					</div><!--end of div grid-10-->
            	</div><!--end of div grids-->
                <!--<span class="slidershadow"></span>-->
						 <div class="grid grid intro"> 
				 <p class="textintro">Sí quieres más información puedes acceder al apartado <a href="solicitud.php">"Quiero ser socio"</a> y realizar cualquier pregunta, o directamente a través del <a href="mailto:info@cerroviejo.org">correo electrónico</a>.  Anímate, te esperamos, estaremos encantados de ayudarte a alimentarte de forma sana y segura. </p>
<p class="textintro">En el Grupo ofrecemos además Asesoramiento y Consultoria en 
cualquier ámbito posible relacionado con las técnicas de producción 
ecológica,la biodiversidad y la prevención de incendios, marketing y 
comercialización, reportados por varios especialistas anejos a la 
agrupación, solicite información en <a href="mailto:asesoria@cerroviejo.org">asesoria@cerroviejo.org</a></p> 
<p class="textintro">Queremos destacar de esta iniciativa los pilares en los que se sustenta, que son:</p>
</div>
    		</div><!--end of div wrapper-->
            


<!--========================================================================== Content Part 2 =====================================================================================-->         
         <div class="wrapper">   
         
                    <div class="grids">
                        <div class="grid-6 grid"> 
                            <h4>Sin ánimo de lucro</h4>
                            <p><b>El Grupo no tiene ánimo de lucro, aunque si aspiraba a generar una actividad económica dignificada que actualmente ha permitido generar varios puestos de trabajo. Los recargos sobre el precio en origen se consideran una Aportación de mínimos, necesaria para mantener en marcha esta iniciativa y dar cobertura a costes fijos como transporte, elementos de refrigeración, personal para la preparación de las cestas, etc. Otros gastos, son cubiertos con fórmulas ingeniosas para evitar repercutirlo en los precios de los alimentos de forma directa, como es el caso de la Web Cerro Viejo 
desde la cual se realiza la compra-venta Online.</p>
							
							
                        </div>
                        
                          <div class="grid-5 grid">
                       	 <h4>Comercio justo</h4>
                            <p>Este volumen de "adeptos" ha permitido al grupo fomentar nuevas iniciativas de abastecimiento de alimentos ecológicos en canal corto de comercialización fomentando un comercio justo, adaptado a las necesidades de agricultores, ganaderos e industrias artesanas; siempre alejados de las políticas de suministro de las grandes corporaciones agroalimentarias. También se han ido eliminando todo tipo de intermediarios que se hayan considerado innecesarios, 
consiguiendo la venta directa más directa al mejor precio para todas las partes que integran CerroViejo. </p>
                        </div>
                        
                        <div class="grid-5 grid">
                       	 <h4>Alimentación Ecológica</h4>
                            <p>El abanico de alimentos es cada día mayor (frutas, verduras, carnes, aves, conservas, cereales y legumbres...) y es gracias a los trabajos como Consultor-Asesor, antiguo Inspector, en Producción Ecológica de uno de sus activos fundadores que nos ha permitido acceder a un importante número de proveedores y productores con total garantía ecológica. </p>
                        </div>
                        
                     
                       

					</div><!--end of grids-->
		</div><!--end of wrapper-->
 <hr /> 
 

	
<!--========================================================================== Ticker =====================================================================================-->                    
                    
                    <!--If you don´t want to use the ticker just delete or comment it and uncomment this to use static text instead-->
					<!--<div class="intro">
                       <p class="text-center">
                       Hey, let your creativity flow and create something great!
                       </p>
                       </div>-->
                       
                   <!--This is FlexSlider and uses the same settings like the one at the top. If you change them, be aware that it is for both sliders!-->
                      
                      <div class=" grids flexslider intro ticker top"><!--http://flex.madebymufffin.com/-->
                      <noscript>Para verlo necesitarías activar Javascript en las opciones de tu navegador</noscript>

  						<ul class="slides">
                             
                            	<li>
                        				 <p class="text-center">
                                           Comercio Justo
                                         </p>		
                                </li>
                                <li>
                        				 <p class="text-center">
                                           Alimentación Sana 
                                         </p>
                                </li> 
                                <li>
                        				 <p class="text-center">
                                           Apoyo a Pequeños Agricultores
                                         </p>		
                                </li>
                              
                                <li>                                  
                        				 <p class="text-center">
                                           Garantía Ecológica
                                         </p>
                                </li>
                               
                                <li>                                
                        				 <p class="text-center">
                                           Sin Ánimo de Lucro
                                         </p>
                                </li>
                                
                                							
                            </ul>
                            
						</div><!--end of div flexslider-->
		</div><!--end of wrapper-->

<!--========================================================================== Footer =====================================================================================-->     
		<div class="wrapper">
					<div id="footer">
            	
                
                			<div class="grids">
                                                             
                                
                                <div class="grid-15 grid text-center">
                                  <p align="center" class="grid-16">Para contactar con el Grupo Cerro Viejo mediante correo electrónico:&nbsp;<a href="mailto:info@cerroviejo.org" title="Contacto">&nbsp;pulse aquí</a>
                                  	<a href="https://play.google.com/store/apps/details?id=org.cerroviejo.android" target="_blank">
										<img style="float:right;" src="img/android-app-on-google-play.png" alt="App.Android" width="120" />
									</a>
                                  </p>
                                </div>
                                
                           </div><!--end of grids-->
                   </div><!--end of footer-->
		   </div><!--end of wrapper-->
    
    
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
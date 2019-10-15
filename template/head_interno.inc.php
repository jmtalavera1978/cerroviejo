<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="DC.Language" scheme="RFC1766" content="Spanish" />
<meta http-equiv="Cache-Control"
	content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta name="title" content="CerroViejo">

<!-- <link href="../css/estilo.css" rel="stylesheet" media="all" /> -->
<link href="../css/custom-theme/jquery-ui-1.10.3.custom.css" rel="stylesheet" />
<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" src="../js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="../js/jquery.blockUI.js"></script>


<link rel="stylesheet" href="../css/inuit.css" />
<link rel="stylesheet" href="../css/fluid-grid16-1100px.css" />
<link rel="stylesheet" href="../css/eve-styles.css" />
<link rel="stylesheet" href="../css/formalize.css" />

<link rel="stylesheet" href="../css/productos.css" />

<link rel="shortcut icon" href="../img/icon.png" />
<link rel="apple-touch-icon-precomposed" href="../img/icon.png" />

<script src="../js/respond-min.js" type="text/javascript"></script>
<!--<script
	src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"
	type="text/javascript"></script>
<script>window.jQuery || document.write('<script src="../js/jquery164min.js">\x3C/script>')</script>-->

<!--Hide the hr img because of ugly borders in IE7. You can change the color of border-top to display a line -->
<!--[if lte IE 7]>
	<style>
		hr { display:block; height:1px; border:0; border-top:1px solid #fff; margin:1em 0; padding:0; }
		.grid-4{ width:22% }
	</style>
 <![endif]-->
<link rel="stylesheet" href="../css/html5-pagination.css" />
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-51824827-1', 'cerroviejo.org');
  ga('send', 'pageview');

  var num_productos_cesta  = <?=$_SESSION["ocarrito"]->num_productos()?>;

  function logout () {
	  if (num_productos_cesta > 0) {
		if (confirm('Tiene productos sin confirmar en su cesta, si continúa se perderán, ¿Desea continuar?')) {
	  		document.location = '../includes/logout.php';
		}
	  } else {
		document.location = '../includes/logout.php';
	  }
  }
</script>
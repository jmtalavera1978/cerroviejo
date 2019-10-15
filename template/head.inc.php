	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="DC.Language" scheme="RFC1766" content="Spanish" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<meta name="title" content="CerroViejo"> 
	
	<link href="../css/estilo.css" rel="stylesheet" media="all" />
	<link href="../css/custom-theme/jquery-ui-1.10.3.custom.css"
		rel="stylesheet" />
	<link rel="stylesheet" href="../css/html5-pagination.css" />
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	
	<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-1.10.3.custom.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript" src="../js/jquery.blockUI.js"></script>
	<script>
		$(document).ready(function()
		{
			 $( "input[type=submit]" )
		      .button();
			 $( "input[type=button]" )
		      .button();
			 $( "input[type=reset]" )
		      .button();
			// Muestra y oculta los menús
			   $('#menu-altern ul li:has(ul)').click( //función al hace click en un "li" que tiene una "ul"
			      function()
			      {
			         $(this).find('ul').toggle();
			      });
			   $('#menu-altern ul li:has(ul)').hover( //función al pasar el ratón por encima de un "li" que tiene una "ul"
			      function(e) //Cuando el ratón deja de estar encima.
			      {
			         $(this).find('ul').fadeOut();
			      }
			   );
			   try {
			   	$('.jqte-test').jqte();
			   } catch (e) {}
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
		
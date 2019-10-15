<?php
if (@$_GET['excel']=='true') {
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-Type: application/vnd.ms-excel");
	header ("Content-Disposition: attachment; filename=FacturaAlbaranPedido.xls" ); 
}
$idPedido = @$_GET['idPedido'];
$tipo = @$_GET['tipo'];
?>
<!DOCTYPE html>
<html lang="es">
<?php if (@$_GET['excel']!='true') { ?>
<head>
	<?php require_once "../template/head.inc.php"; ?>
</head>
<?php } else { ?>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php } ?>
<body>
	<?php 
	require_once "../includes/funciones.inc.php";
	compruebaSesionRepartidorOAdministrador();
	
	if (@$_GET['excel']!='true') {
	?>	
	<div align="left" style="margin-left: 10%">
	    <script>var pfHeaderImgUrl = '';var pfHeaderTagline = '';var pfdisableClickToDel = 0;var pfHideImages = 0;var pfImageDisplayStyle = 'right';var pfDisablePDF = 0;var pfDisableEmail = 0;var pfDisablePrint = 0;var pfCustomCSS = '';var pfBtVersion='1';(function(){var js, pf;pf = document.createElement('script');pf.type = 'text/javascript';if('https:' == document.location.protocol){js='https://pf-cdn.printfriendly.com/ssl/main.js'}else{js='http://cdn.printfriendly.com/printfriendly.js'}pf.src=js;document.getElementsByTagName('head')[0].appendChild(pf)})();</script>
		<a href="http://www.printfriendly.com" style="color:#6D9F00;text-decoration:none;" class="printfriendly" onclick="window.print();return false;" title="Printer Friendly and PDF"><img style="border:none;-webkit-box-shadow:none;box-shadow:none;" src="http://cdn.printfriendly.com/button-print-grnw20.png" alt="Print Friendly and PDF"/></a>
	    <input type="button" id="excel" value="Excel">
	    <input type="button" id="cerrar" value="Cerrar">
		<script>
			$(document).ready(function()
				{
					$("#excel").bind("click",function()
						{
							document.location='imprimirFacturaPedido.php?idPedido=<?=@$_GET['idPedido']?>&excel=true&tipo=<?=$tipo?>';
						});
					$("#cerrar").bind("click",function()
					{
						window.close();
					});
				});
		</script>
	</div>   
    <?php 
    }
	
	require_once "../includes/factura.inc.php";
	?>
</body>
</html>
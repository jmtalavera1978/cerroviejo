<?php
if (@$_GET['excel']=='true') {
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-Type: application/vnd.ms-excel");
	header ("Content-Disposition: attachment; filename=FacturaAlbaranPedido.xls" ); 
}
$anyo = @$_GET['anyo'];
$mes = @$_GET['mes'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="DC.Language" scheme="RFC1766" content="Spanish" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<meta name="title" content="CerroViejo"> 
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>FACTURAS</title>
	<link media="screen,print" type="text/css" rel="stylesheet" href="../css/print.css"/>
	<style type="text/css">
		#pf-content img.mediumImage {margin: 1em 0 1em 1.5em;clear: right;display: inline;float: right;}
	</style>
</head>
<body id="pf-app">
	<?php 
	require_once "../includes/funciones.inc.php";
	compruebaSesionRepartidorOAdministrador();
	
	if (@$_GET['excel']!='true') {
	?>	
	<div id="botones" align="left" style="margin-left: 10%">
	    <input type="button" id="cerrar" value="Imprimir" onclick="document.getElementById('botones').style.display = 'none'; window.print();document.getElementById('botones').style.display = 'block'; " />
	    <input type="button" id="cerrar" value="Cerrar" onclick="window.close()">
	</div>   
    <?php 
    }
    

    $idPedido = @$_GET['idPedido'];
    $lote = @$_GET['lote'];
    $usuario = @$_GET['usuario'];
    $trimestre = @$_GET['trimestre'];
    $tipo = 3;
    
    $consulta = "select p.* FROM PEDIDOS p
					where p.COBRADO=1 AND p.FECHA_FACTURA like '$anyo-$mes%' AND p.ID_USUARIO <> 'ADMIN'";
	if (isset($trimestre) && $trimestre!=NULL) {
		if ($trimestre=='1')
			$consulta.=" AND (p.FECHA_FACTURA like '$anyo-01%' OR p.FECHA_FACTURA like '$anyo-02%' OR p.FECHA_FACTURA like '$anyo-03%')";
		else if ($trimestre=='2')
			$consulta.=" AND (p.FECHA_FACTURA like '$anyo-04%' OR p.FECHA_FACTURA like '$anyo-05%' OR p.FECHA_FACTURA like '$anyo-06%')";
		else if ($trimestre=='3')
			$consulta.=" AND (p.FECHA_FACTURA like '$anyo-07%' OR p.FECHA_FACTURA like '$anyo-08%' OR p.FECHA_FACTURA like '$anyo-09%')";
		else if ($trimestre=='4')
			$consulta.=" AND (p.FECHA_FACTURA like '$anyo-10%' OR p.FECHA_FACTURA like '$anyo-11%' OR p.FECHA_FACTURA like '$anyo-12%')";
	}
	if (isset($lote) && $lote!=NULL) {
		$consulta.=" AND p.LOTE='$lote'";
	}
	if (isset($usuario) && $usuario!=NULL) {
		$consulta.=" AND p.ID_USUARIO='$usuario'";
	}
	$consulta .= " ORDER BY p.NUM_FACTURA_ANUAL";
    	
    $res = consulta($consulta);
	
    while ($filaCon = extraer_registro($res)) {
    	$idPedido = $filaCon['ID_PEDIDO'];
    	
		require "../includes/factura.inc.php";
    }
	?>
</body>
</html>
<?php
require_once "../includes/funciones.inc.php";
	$idPedidoActual = @$_GET['idPedidoActual'];
	$check = @$_GET['check'];
	
	if ($check=='true') {
		$fechaActual = new DateTime();
		$res = consulta("select MAX(NUM_FACTURA_ANUAL) as NUM_FACTURA from PEDIDOS where FECHA_FACTURA like '".$fechaActual->format("Y")."%' and COBRADO='1' and NUM_FACTURA_ANUAL is not null");
		$fila = extraer_registro($res);
		$numFactura = $fila['NUM_FACTURA'] + 1;
		consulta("update PEDIDOS set COBRADO='1', FECHA_FACTURA='".$fechaActual->format("Y-m-d H:i:s")."', NUM_FACTURA_ANUAL='$numFactura' where ID_PEDIDO='$idPedidoActual' AND NUM_FACTURA_ANUAL is null");
		$numAlbaran = obtenerNumeroFactura ($idPedidoActual);
	} else {
		//consulta("update PEDIDOS set COBRADO='0', FECHA_FACTURA=NULL, NUM_ALBARAN=NULL where ID_PEDIDO='$idPedidoActual'");
	}
	
	$resAjax = consulta("select COBRADO FROM PEDIDOS where ID_PEDIDO='$idPedidoActual'");
	$productoAj = extraer_registro($resAjax);
	if ($productoAj['COBRADO']!='1') {
?><input type="button" id="checkCobrado<?=$idPedidoActual?>" onclick="clickarCobrado('<?=$idPedidoActual?>', 'true');"/>
<?php } ?>
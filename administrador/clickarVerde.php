<?php
require_once "../includes/funciones.inc.php";
	$idPedidoActual = @$_GET['idPedidoActual'];
	$check = @$_GET['check'];
	
	if ($check=='true') {
		$fechaActual = new DateTime();
		consulta("update PEDIDOS set VERDE='1' where ID_PEDIDO='$idPedidoActual'");
		$numAlbaran = obtenerNumeroFactura ($idPedidoActual);
	} else {
		consulta("update PEDIDOS set VERDE='0' where ID_PEDIDO='$idPedidoActual'");
	}
	
	$resAjax = consulta("select VERDE FROM PEDIDOS where ID_PEDIDO='$idPedidoActual'");
	$productoAj = extraer_registro($resAjax);
?>
<input type="checkbox" id="checkVerde<?=$idPedidoActual?>" <?php if ($productoAj['VERDE']=='1') echo 'checked' ?> onmouseup="clickarVerde('<?=$idPedidoActual?>', !this.checked);"/>
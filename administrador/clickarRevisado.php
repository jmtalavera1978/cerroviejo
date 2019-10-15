<?php
require_once "../includes/funciones.inc.php";
	$idPedidoActual = @$_GET['idPedidoActual'];
	$idProducto = @$_GET['idProducto'];
	$cantidad = @$_GET['cantidad'];
	$check = @$_GET['check'];
	
	if ($check=='true') {
		consulta("update PEDIDOS_PRODUCTOS set CHECK_REVISADO='1' where ID_PEDIDO='$idPedidoActual' and ID_PRODUCTO='$idProducto'");
	} else {
		consulta("update PEDIDOS_PRODUCTOS set CHECK_REVISADO='0' where ID_PEDIDO='$idPedidoActual' and ID_PRODUCTO='$idProducto'");
	}
	
	$resAjax = consulta("select CHECK_REVISADO FROM PEDIDOS_PRODUCTOS where ID_PEDIDO='$idPedidoActual' and ID_PRODUCTO='$idProducto'");
	$productoAj = extraer_registro($resAjax);
?><input type="checkbox" id="checkB<?=$idProducto?>" <?php if ($productoAj['CHECK_REVISADO']=='1') echo 'checked' ?> onmouseup="clickarRevisado('check<?=$idProducto?>', '<?=$idPedidoActual?>', '<?=$idProducto?>', '<?=$cantidad?>', !this.checked);"/>
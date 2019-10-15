<?php
include("../includes/funciones.inc.php");
$res = consulta("delete from PEDIDOS_PRODUCTOS where ID_PEDIDO='".$_GET['idPedido']."' and ID_PRODUCTO='".$_GET['idProducto']."'");
if ($res == 1) {
	$_SESSION['mensaje_generico'] = 'Producto eliminado correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se puede eliminar el producto.';
}
Header ("Location: ".$_GET["url"]);
?>
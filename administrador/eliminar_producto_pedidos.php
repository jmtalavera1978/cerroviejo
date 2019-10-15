<?php
include("../includes/funciones.inc.php");
$res = consulta("delete from PEDIDOS_PROVEEDORES_PROD where ID_PEDIDO_PROVEEDOR IN (select ID_PEDIDO_PROVEEDOR FROM PEDIDOS_PROVEEDORES WHERE LOTE='".$_GET['lote']."') and ID_PRODUCTO='".$_GET['idProducto']."'");
if ($res == 1) {
	$_SESSION['mensaje_generico'] = 'Producto eliminado correctamente del pedido a proveedores.';
} else {
	$_SESSION['mensaje_generico'] = 'No se puede eliminar el producto del pedido a proveedores.';
}
Header ("Location: ".$_GET["url"]);
?>
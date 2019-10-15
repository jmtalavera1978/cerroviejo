<?php
include("../includes/funciones.inc.php");
compruebaSesionAdministracion();
$cantidad = $_GET['cantidad'];

if ($cantidad=='NULL') {
	$resProd = consulta("update PEDIDOS_PROVEEDORES SET TOTAL_REVISADO=NULL WHERE ID_PEDIDO_PROVEEDOR='".$_GET['idPedidoProv']."'");
} else {
	$resProd = consulta("update PEDIDOS_PROVEEDORES SET TOTAL_REVISADO='$cantidad' WHERE ID_PEDIDO_PROVEEDOR='".$_GET['idPedidoProv']."'");
}
if ($resProd) {
	$_SESSION['mensaje_generico'] .= 'Se ha actualizado el total revisado correctamente.';
} else {
	$_SESSION['mensaje_generico'] .= 'No se ha podido actualizar el total revisado.';
}
Header ("Location: ".$_GET["url"]);
?>
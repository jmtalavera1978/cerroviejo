<?php
include("../includes/funciones.inc.php");
$res = consulta("delete from PEDIDOS_PRODUCTOS where ID_PEDIDO='".$_GET['idPedido']."'");
$res = consulta("delete from PEDIDOS where ID_PEDIDO='".$_GET['idPedido']."'");
if ($res == 1) {
	$_SESSION['mensaje_generico'] = 'Pedido eliminado correctamente.';
	$_SESSION['idUsuarioSel'] = NULL;
} else {
	$_SESSION['mensaje_generico'] = 'No se puede eliminar el pedido del usuario.';
}
Header ("Location: pedidos.php");
?>
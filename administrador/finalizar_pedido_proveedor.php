<?php
include("../includes/funciones.inc.php");
compruebaSesionAdministracion();
$idPedidoProveedor = @$_GET['idPedidoProveedor'];
$nombre = @$_GET['nombre'];
$_SESSION['mensaje_generico'] = finalizarPedidoProveedor ($idPedidoProveedor, $nombre);
Header ("Location: ".$_GET["url"]);
?>
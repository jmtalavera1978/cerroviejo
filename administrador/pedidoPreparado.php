<?php
include("../includes/funciones.inc.php");
compruebaSesionAdministracion();
$idPedido = @$_GET['idPedido'];
$mensajeG = finalizaPedidoYActualizaSaldo ($idPedido);
$_SESSION['idUsuarioSelPedUsu'] = null;
$_SESSION['mensaje_generico'] = $mensajeG;
Header ("Location: ".$_GET["url"]);
?>
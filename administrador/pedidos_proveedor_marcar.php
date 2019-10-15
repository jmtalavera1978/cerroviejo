<?php
require_once "../includes/funciones.inc.php";
$idPedidoProv = @$_GET['idPedidoProv'];
$consulta = @$_GET['consulta'];

consulta("update PEDIDOS_PROVEEDORES set ENVIADO='1' where ID_PEDIDO_PROVEEDOR='$idPedidoProv'");
Header ("Location: detallePedidosProv.php?idPedidoProv=$idPedidoProv&consulta=$consulta");
?>
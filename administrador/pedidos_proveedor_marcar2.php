<?php
require_once "../includes/funciones.inc.php";
$idPedidoProv = @$_GET['idPedidoProv'];

consulta("update PEDIDOS_PROVEEDORES set ENVIADO='1' where ID_PEDIDO_PROVEEDOR='$idPedidoProv'");
Header ("Location: pedidos.php#columna$idPedidoProv");
?>
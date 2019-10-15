<?php
include("../includes/funciones.inc.php");
compruebaSesionAdministracion();
$cantidad = $_GET['cantidad'];
$idProducto = $_GET['idProducto'];
$idPedido = $_GET['idPedido'];
consulta("update PEDIDOS_PROVEEDORES set ENVIADO='0' where ID_PEDIDO_PROVEEDOR = '$idPedido'");
$resProd = consulta("update PEDIDOS_PROVEEDORES_PROD SET CANTIDAD_REV='$cantidad' WHERE ID_PEDIDO_PROVEEDOR = '$idPedido' and ID_PRODUCTO='$idProducto'");
Header ("Location: ".$_GET["url"]);
?>
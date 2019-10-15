<?php
include("../includes/funciones.inc.php");
compruebaSesionAdministracion();
$idProducto = $_GET['idProducto'];
$iva = $_GET['iva'];

$res3 = consulta("select IMPORTE_SIN_IVA, PRECIO from PRODUCTOS WHERE ID_PRODUCTO='$idProducto' ");
$fila3 = @extraer_registro($res3);
$nuevoPrecioSinIva = $fila3['IMPORTE_SIN_IVA'];
//$nuevoPrecio = calculaPrecioConRecargo($idProducto, $nuevoPrecio);
$nuevoPrecio = calculaPVP ($idProducto, $nuevoPrecioSinIva, $iva);
?><?=$nuevoPrecio?> &euro;
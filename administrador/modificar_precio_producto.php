<?php
include("../includes/funciones.inc.php");
compruebaSesionAdministracion();
$idProducto = $_GET['idProducto'];
$precio = $_GET['precio'];
$iva = $_GET['iva'];

//$importeSinIVA = round( (($precio*100)/($iva+100)) , 2);
$precioConIVA = $precio + round( (($precio*$iva)/100), 2);

if ($precio>0.0) {
	$resProd = consulta("update PRODUCTOS SET PRECIO='$precioConIVA', IMPORTE_SIN_IVA='$precio' WHERE ID_PRODUCTO='$idProducto'");
}

$res3 = consulta("select IMPORTE_SIN_IVA, PRECIO from PRODUCTOS PP WHERE ID_PRODUCTO='$idProducto' ");
$fila3 = @extraer_registro($res3);
$nuevoPrecioSinIva = $fila3['IMPORTE_SIN_IVA'];
?><input type="text" style="width: 70%; text-align:right;"
	onfocus="this.value=''"
	onkeypress="return NumCheck(event, this)"
	onblur="modificarPrecio ('<?=$idProducto?>', '<?=$iva?>', this.value)" 
	value="<?=$nuevoPrecioSinIva?>"/> &euro;
<?php
include("../includes/funciones.inc.php");
$res = consulta("update PRODUCTOS set OFERTA='0'");
if ($res == 1) {
	$_SESSION['mensaje_generico'] = 'Ofertas se ha vaciado correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se puede vaciar la categoría de ofertas.';
}
Header ("Location: productos.php");
?>
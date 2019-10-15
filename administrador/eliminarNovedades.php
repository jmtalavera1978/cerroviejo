<?php
include("../includes/funciones.inc.php");
$res = consulta("update PRODUCTOS set NOVEDAD='0'");
if ($res == 1) {
	$_SESSION['mensaje_generico'] = 'Novedades se ha vaciado correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se puede vaciar la categoría de novedades.';
}
Header ("Location: productos.php");
?>
<?php
include("../includes/funciones.inc.php");
$res = consulta("delete from SUBCATEGORIAS where ID_SUBCATEGORIA='".$_GET['idSubcategoria']."'");
if ($res == 1) {
	$_SESSION['mensaje_generico'] = 'Subcategoría eliminada correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se puede eliminar la subcategoría.';
	$res = consulta("update SUBCATEGORIAS set ACTIVO='0' where ID_SUBCATEGORIA='".$_GET['idSubcategoria']."'");
	
	if ($res == 1) {
		$_SESSION['mensaje_generico'] .= ' Se ha desactivado correctamente.';
	} else {
		$_SESSION['mensaje_generico'] .= ' No se ha podido desactivar.';
	}
}
Header ("Location: ".$_GET["url"]);
?>
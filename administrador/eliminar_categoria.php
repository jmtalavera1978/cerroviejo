<?php
include("../includes/funciones.inc.php");
$res = consulta("delete from CATEGORIAS where ID_CATEGORIA='".$_GET['idCategoria']."'");
if ($res == 1) {
	$_SESSION['mensaje_generico'] = 'Categoría eliminada correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se puede eliminar la categoría.';
	$res = consulta("update CATEGORIAS set ACTIVO='0' where ID_CATEGORIA='".$_GET['idCategoria']."'");
	
	if ($res == 1) {
		$_SESSION['mensaje_generico'] .= ' Se ha desactivado correctamente.';
	} else {
		$_SESSION['mensaje_generico'] .= ' No se ha podido desactivar.';
	}
}
Header ("Location: ".$_GET["url"]);
?>
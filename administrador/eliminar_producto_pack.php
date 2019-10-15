<?php
include("../includes/funciones.inc.php");
$res = consulta("delete from PRODUCTOS_PACK where ID_PRODUCTO_PACK='".$_GET['idProductoPack']."' and ID_PRODUCTO='".$_GET['idProducto']."'");
if ($res == 1) {
	$_SESSION['mensaje_generico'] = 'Producto del Pack eliminado correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se puede eliminar el producto del Pack.';
}
Header ("Location: ".$_GET["url"]);
?>
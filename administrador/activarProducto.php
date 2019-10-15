<?php
include("../includes/funciones.inc.php");
compruebaSesionRepartidorOAdministrador();
$resProd = consulta("select * from PRODUCTOS WHERE ID_PRODUCTO=".$_GET["idProducto"]);
$filaProd = extraer_registro($resProd);
if ($filaProd['ACTIVO']==1) {
	$res = consulta("UPDATE PRODUCTOS SET ACTIVO=0 WHERE ID_PRODUCTO=".$_GET["idProducto"]);
} else {
	$res = consulta("UPDATE PRODUCTOS SET ACTIVO=1 WHERE ID_PRODUCTO=".$_GET["idProducto"]);
}
if ($res==1) {
	$_SESSION['mensaje_generico'] = 'Se ha modificado el estado activo correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se ha podido modificar el estado.';
}
Header ("Location: ".$_GET["url"]);
?>
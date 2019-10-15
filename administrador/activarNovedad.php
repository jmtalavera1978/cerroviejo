<?php
include("../includes/funciones.inc.php");
compruebaSesionRepartidorOAdministrador();
$resProd = consulta("select * from PRODUCTOS WHERE ID_PRODUCTO=".$_GET["idProducto"]);
$filaProd = extraer_registro($resProd);
if ($filaProd['NOVEDAD']==1) {
	$res = consulta("UPDATE PRODUCTOS SET NOVEDAD=0 WHERE ID_PRODUCTO=".$_GET["idProducto"]);
} else {
	$res = consulta("UPDATE PRODUCTOS SET NOVEDAD=1 WHERE ID_PRODUCTO=".$_GET["idProducto"]);
}
if ($res==1) {
	$_SESSION['mensaje_generico'] = 'Se ha modificado el estado de novedad correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se ha podido modificar el estado de novedad.';
}
Header ("Location: ".$_GET["url"]);
?>
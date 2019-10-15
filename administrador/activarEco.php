<?php
include("../includes/funciones.inc.php");
compruebaSesionRepartidorOAdministrador();
$resProd = consulta("select * from PRODUCTOS WHERE ID_PRODUCTO=".$_GET["idProducto"]);
$filaProd = extraer_registro($resProd);
if ($filaProd['ECOLOGICO']==1) {
	$res = consulta("UPDATE PRODUCTOS SET ECOLOGICO=0 WHERE ID_PRODUCTO=".$_GET["idProducto"]);
} else {
	$res = consulta("UPDATE PRODUCTOS SET ECOLOGICO=1 WHERE ID_PRODUCTO=".$_GET["idProducto"]);
}
if ($res==1) {
	$_SESSION['mensaje_generico'] = 'Se ha modificado el estado de ecológico correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se ha podido modificar el estado de ecológico.';
}
Header ("Location: ".$_GET["url"]);
?>
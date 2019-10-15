<?php
include("../includes/funciones.inc.php");
compruebaSesionRepartidorOAdministrador();
$resCat = consulta("select * from CATEGORIAS WHERE ID_CATEGORIA=".$_GET["idCategoria"]);
$filaCat = extraer_registro($resCat);
if ($filaCat['ACTIVO']=='1') {
	$res = consulta("UPDATE CATEGORIAS SET ACTIVO=0 WHERE ID_CATEGORIA=".$_GET["idCategoria"]);
} else {
	$res = consulta("UPDATE CATEGORIAS SET ACTIVO=1 WHERE ID_CATEGORIA=".$_GET["idCategoria"]);
}
if ($res==1) {
	$_SESSION['mensaje_generico'] = 'Se ha modificado el estado activo correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se ha podido modificar el estado.';
}
Header ("Location: ".$_GET["url"]);
?>
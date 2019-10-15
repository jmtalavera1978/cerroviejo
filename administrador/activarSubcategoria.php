<?php
include("../includes/funciones.inc.php");
compruebaSesionRepartidorOAdministrador();
$resCat = consulta("select ACTIVO from SUBCATEGORIAS WHERE ID_SUBCATEGORIA=".$_GET["idSubcategoria"]);
$filaCat = extraer_registro($resCat);
if ($filaCat['ACTIVO']=='1') {
	$res = consulta("UPDATE SUBCATEGORIAS SET ACTIVO='0' WHERE ID_SUBCATEGORIA=".$_GET["idSubcategoria"]);
} else {
	$res = consulta("UPDATE SUBCATEGORIAS SET ACTIVO='1' WHERE ID_SUBCATEGORIA=".$_GET["idSubcategoria"]);
}
if ($res==1) {
	$_SESSION['mensaje_generico'] = 'Se ha modificado el estado activo correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se ha podido modificar el estado.';
}
Header ("Location: ".$_GET["url"]);
?>
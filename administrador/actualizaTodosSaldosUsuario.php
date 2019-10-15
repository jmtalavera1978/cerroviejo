<?php
include("../includes/funciones.inc.php");
$resUsers = consulta ("select ID_USUARIO from USUARIOS where TIPO_USUARIO='USUARIO'");
while ($filaUser = extraer_registro($resUsers)) {
	$mensaje = actualizaContabilidadRealUsuario ($filaUser['ID_USUARIO']);
	$_SESSION['mensaje_generico'] = $mensaje ;
}
Header ("Location: usuarios.php");
?>
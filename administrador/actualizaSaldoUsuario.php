<?php
include("../includes/funciones.inc.php");
$mensaje = actualizaContabilidadUsuario ($_GET['idUsuario']);
$_SESSION['mensaje_generico'] = $mensaje;
Header ("Location: historicoSaldoUsuario.php");
?>
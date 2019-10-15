<?php
include("../includes/funciones.inc.php");
compruebaSesionRepartidorOAdministrador();
$mensaje = generarDatosPedidosProveedores($_GET["lote"], $_GET["subgrupos"]);
$_SESSION['mensaje_generico'] = $mensaje;
Header ("Location: ".$_GET["url"]);
?>
<?php
include("lib_carrito.php");
$_SESSION["ocarrito"]->elimina_producto($_GET["linea"]);
Header ("Location: ".$_GET["url"]);
?>

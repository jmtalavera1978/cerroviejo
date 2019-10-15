<?php
include("lib_carrito.php");
$_SESSION["ocarrito"]->actualiza_producto($_GET["indice"], $_GET["cantidad"]);
Header ("Location: ".$_GET["url"]);
?>
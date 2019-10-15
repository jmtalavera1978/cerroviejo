<?php
include("../includes/lib_carrito.php");
$_SESSION["ocarrito"]->repetir_ult_compra();
Header ("Location: cesta.php");
?>

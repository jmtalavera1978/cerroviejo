<?php
include("../includes/lib_carrito.php");
$_SESSION["ocarrito"]->repetir_ult_compra_no_servidos();
Header ("Location: cesta.php");
?>

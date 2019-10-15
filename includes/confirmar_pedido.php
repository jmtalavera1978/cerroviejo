<?php
include_once("lib_carrito.php");
$comentarios = @$_GET['comentarios'].@$_POST['comentarios'];
$horaIni = @$_GET['horaIni'].@$_POST['horaIni'];
$horaFin = @$_GET['horaFin'].@$_POST['horaFin'];
$mensajeG = $_SESSION["ocarrito"]->confirmar_pedido($comentarios, $horaIni, $horaFin);
$_SESSION['mensaje_generico'] = $mensajeG;
Header ("Location: ../usuarios/cesta.php?pestanya=0");
?>
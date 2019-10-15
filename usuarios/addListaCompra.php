<?php
include_once("../includes/lib_carrito.php");
$idLista = @$_GET['idLista'];
$error = $_SESSION["ocarrito"]->repetir_lista($idLista);
if ($error == 0) {
	$_SESSION['mensaje_generico'] = 'Productos a&ntilde;adidos a la cesta correctamente.';
} else {
	$_SESSION['mensaje_generico'] = 'No se han podido a&ntilde;adir los productos a la cesta.';
}
Header ("Location: mislistas.php");
?>
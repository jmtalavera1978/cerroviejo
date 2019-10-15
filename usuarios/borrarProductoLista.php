<?php
include("../includes/lib_carrito.php");
$idLista = @$_GET['idLista'];
$idProducto = @$_GET['idProducto'];
$usuario = $_SESSION['ID_USUARIO'];

$res = consulta("delete from LISTAS_PRODUCTOS where ID_PRODUCTO = '$idProducto' and ID_LISTA in (select ID_LISTA from LISTAS where ID_LISTA='$idLista' and ID_USUARIO='$usuario')");

if (!$res) {
	$_SESSION['mensaje_generico'] = 'No se han podido eliminar el producto de la lista';
}
Header ("Location: verLista.php?idLista=$idLista");
?>
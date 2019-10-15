<?php
include("../includes/lib_carrito.php");
$idLista = @$_GET['idLista'];
$usuario = $_SESSION['ID_USUARIO'];

$res = consulta("delete from LISTAS_PRODUCTOS where ID_LISTA in (select ID_LISTA from LISTAS where ID_LISTA='$idLista' and ID_USUARIO='$usuario')");

if ($res) {
	$res = consulta("delete from LISTAS where ID_LISTA='$idLista' and ID_USUARIO='$usuario'");
	
	if (!$res) {
		$_SESSION['mensaje_generico'] = 'No se ha podido eliminar la lista';
	}
} else {
	$_SESSION['mensaje_generico'] = 'No se han podido eliminar los productos de la lista';
}
Header ("Location: mislistas.php");
?>
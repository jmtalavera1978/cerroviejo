<?php
include_once("funciones.inc.php");

try {
	$idLista = $_GET["idLista"];
	$idProd = $_GET["idProd"];
	$cantidad = $_GET["cantidad"];
	$usuario = @$_SESSION['ID_USUARIO'];
	
	$resC = consulta("select * from LISTAS_PRODUCTOS where ID_LISTA = '$idLista' and ID_PRODUCTO= '$idProd'");
	
	if (numero_filas($resC)>0) {
		$resI = consulta("UPDATE LISTAS_PRODUCTOS set CANTIDAD = '$cantidad' where ID_LISTA = '$idLista' and ID_PRODUCTO= '$idProd'");
		if ($resI) {
			$mensaje = "existe";
		} else {
			$mensaje = "error";
		}
	} else {
		$resI = consulta("INSERT INTO LISTAS_PRODUCTOS (ID_LISTA, ID_PRODUCTO, CANTIDAD) VALUES ('$idLista', '$idProd', '$cantidad')");
		if ($resI) {
			$mensaje = "correcto";
		} else {
			$mensaje = "error";
		}
	}
} catch (Exception $ex) {
   	$mensaje = "error";
}
?><?=$mensaje?>
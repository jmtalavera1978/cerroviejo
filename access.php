<?php
require_once "includes/funciones.inc.php";
$usuario = @$_POST['usuario'];
$clave = @$_POST['clave'];
try {
	$res = consulta ("select * from USUARIOS where ID_USUARIO='$usuario' and CLAVE='$clave' and ACTIVO=1");
	
	if ($fila = extraer_registro ($res)) {
		$resultado[]=array("logstatus"=>"1");
	} else {
		$resultado[]=array("logstatus"=>"0");
	}
} catch (Exception $ex) {
	//Devuelve el mensaje de error
	$resultado[]=array("logstatus"=>"0");
}

echo json_encode($resultado);
?>
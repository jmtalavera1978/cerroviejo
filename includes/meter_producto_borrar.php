<?php
include_once("lib_carrito.php");
include_once("funciones.inc.php");

try {
	$resultado = consulta("select * from PRODUCTOS WHERE ID_PRODUCTO=".$_GET["id"]);
	$producto = extraer_registro($resultado);
	
	$cantidad = $_GET["cantidad"];
	
	// Extraer datos y validar los enviados
	$lote = consultarLoteActual();
	$cantidad_vendida = cantidadVendidaProducto($lote, $_GET["id"]);
	$max = ($producto['CANTIDAD_1'] + $producto['CANTIDAD_2'] - $cantidad_vendida);
	
	$ilimitada = $producto['CANTIDAD_ILIMITADA'];
	
	$precioProd = $producto['IMPORTE_SIN_IVA'];
	$iva = $producto['TIPO_IVA'];
	$precioProd = calculaPVP ($_GET["id"], $precioProd, $iva);
	
	if ($ilimitada=='0' || $ilimitada==NULL) {
		if ($cantidad > $max) {
			$cantidad = $max;
		}
	} else {
		$max = 0;
	}
	
	$_SESSION["ocarrito"]->introduce_producto($_GET["id"], $_GET["nombre"], $precioProd, $cantidad, $max, $ilimitada);

} catch (Exception $ex) {
   	
}
Header ("Location: ../usuarios/cesta.php");
?>
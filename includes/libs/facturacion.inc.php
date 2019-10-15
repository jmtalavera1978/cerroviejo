<?php
/**
 * Devuelve el albarán de pedido de año
 * 
 * @param unknown $idPedido
 * @param unknown $lote
 * @return Number
 */
function obtenerNumAlbaranPedidoAnual ($idPedido, $anyoPedido) {
	$res = consulta("select NUM_ALBARAN from (
			select @rownum:=@rownum+1 AS NUM_ALBARAN, p.ID_PEDIDO, p.FECHA_PEDIDO FROM (SELECT @rownum:=0) r, PEDIDOS p
			where p.FECHA_PEDIDO like '$anyoPedido-%' order by p.FECHA_PEDIDO
		) as t  where ID_PEDIDO = '$idPedido'");
	$numAlbaran = extraer_registro($res);
	return $numAlbaran['NUM_ALBARAN'];
}

/**
 * Devuelve el albarán de pedido de un lote
 *
 * @param unknown $idPedido
 * @param unknown $lote
 * @return Number
 */
function obtenerNumAlbaranPedido ($idPedido, $lote) {
	$res = consulta("select NUM_ALBARAN from (
			select @rownum:=@rownum+1 AS NUM_ALBARAN, p.ID_PEDIDO, p.FECHA_PEDIDO FROM (SELECT @rownum:=0) r, PEDIDOS p
			where p.LOTE='$lote' order by p.FECHA_PEDIDO
	) as t  where ID_PEDIDO = '$idPedido'");
	$numAlbaran = extraer_registro($res);
	return $numAlbaran['NUM_ALBARAN'];
}

/**
 * Permite obtener el numero de albaran de entrega de un pedido
 * @param unknown $lote
 * @param unknown $idPedido
 */
function obtenerNumeroAlbaran ($idPedido, $conexion) {
	$numero = 1;
	
	$resC = mysql_query ("select NUM_ALBARAN, LOTE from PEDIDOS where ID_PEDIDO='$idPedido'", $conexion);
	$resC = extraer_registro($resC);
	$numero = $resC['NUM_ALBARAN'];
	$lote = $resC['LOTE'];
	
	if (!isset($numero) || $numero==NULL || $numero==0) {
		//Consultar el siguiente numero de albaran para el lote
		$resC2 = mysql_query ("select MAX(NUM_ALBARAN) AS NUM_ALBARAN from PEDIDOS where LOTE='$lote'", $conexion);
		$resC2 = extraer_registro($resC2);
		$numero = $resC2['NUM_ALBARAN'];
		
		if (!isset($numero) || $numero==NULL || $numero==0) {
			$numero = 1;
		} else {
			$numero = $numero + 1;
		}
		
		// Guardar el numero generado
		mysql_query ("update PEDIDOS set NUM_ALBARAN='$numero' where LOTE='$lote' and ID_PEDIDO='$idPedido'", $conexion);
	}
	
	return $numero;
}

/**
 * Permite obtener el numero de albaran de un pedido para un lote ya finalizado
 * 
 * @param unknown $lote
 * @param unknown $idPedido
 */
function obtenerNumeroAlbaranAnterior ($idPedido) {
	$numero = 1;

	$conexion = conectar();
			
	if ($conexion==FALSE) {
		$numero = 0;
	} else {
		$numero = obtenerNumeroAlbaran ($idPedido, $conexion);
	}
	
	mysql_query("COMMIT", $conexion);
	@mysql_close($conexion);

	return $numero;
}

/**
 * Permite obtener el numero de factura de un pedido para un lote
 * @param unknown $lote
 * @param unknown $idPedido
 */
function obtenerNumeroFactura ($idPedido) {
	$numero = 1;
	
	$resC = consulta("select NUM_FACTURA, LOTE from PEDIDOS where ID_PEDIDO='$idPedido'");
	$resC = extraer_registro($resC);
	$numero = $resC['NUM_FACTURA'];
	$lote = $resC['LOTE'];
	
	if (!isset($numero) || $numero==NULL || $numero==0) {
		//Consultar el siguiente numero de albaran para el lote
		$resC2 = consulta ("select MAX(NUM_FACTURA) AS NUM_FACTURA from PEDIDOS where LOTE='$lote'");
		$resC2 = extraer_registro($resC2);
		$numero = $resC2['NUM_FACTURA'];
		
		if (!isset($numero) || $numero==NULL || $numero==0) {
			$numero = 1;
		} else {
			$numero = $numero + 1;
		}
		
		// Guardar el numero generado
		//$fechaActual = new DateTime();
		consulta ("update PEDIDOS set NUM_FACTURA='$numero' where LOTE='$lote' and ID_PEDIDO='$idPedido'");
	}
	
	return $numero;
}

/**
 * Devuelve el numero de factura según facturados del año
 *
 * @param unknown $idPedido
 * @param unknown $lote
 * @return Number
 */
function obtenerNumFacturaAnual ($idPedido, $anyoFactura) {
	$res = consulta("select * from (
			select @rownum:=@rownum+1 AS NUM_FACTURA, p.ID_PEDIDO, p.FECHA_FACTURA FROM (SELECT @rownum:=0) r, PEDIDOS p
			where p.COBRADO=1 AND p.FECHA_FACTURA like '$anyoFactura-%' ORDER BY p.FECHA_FACTURA
	) as t where ID_PEDIDO = '$idPedido'");
	$numAlbaran = extraer_registro($res);
	return $numAlbaran['NUM_FACTURA'];
}

/**
 * Calcula el porcentaje aplicado de recargo
 * 
 * @param unknown $importeConRecargo
 * @param unknown $importeSinRecargo
 */
function calculaRecargoAplicado ($importeConRecargo, $importeSinRecargo) {
	if ($importeSinRecargo>0) {
		return round(($importeConRecargo - $importeSinRecargo) * 100 / $importeSinRecargo, 0);
	} else {
		return 0;
	}
}

/**
 * Permite calcular el PVC de un producto por unidad
 * 
 * @param unknown $precioConIVA
 * @param unknown $precioConRecargoSinIVA
 * @param unknown $tipoIVA
 * @param unknown $peso_por_unidad
 * @param unknown $recargoEq
 * @return number
 */
function calcularPVC ($precioConIVA, $precioConRecargoSinIVA, $tipoIVA, $peso_por_unidad, $recargoEq) {
	$PVC = $precioConIVA;
	$precio_sin_iva = $precioConRecargoSinIVA;
	
	if (isset($peso_por_unidad) && $peso_por_unidad>0) {
		$PVC = $precioConIVA / $peso_por_unidad;
		$precio_sin_iva = $precioConRecargoSinIVA / $peso_por_unidad;
	}
	
	$rePorUnidad = 0;
	
	if ($recargoEq) { //SOBRE EL PRECIO SIN IVA
		$re = 0;
		
		if ($tipoIVA == 4) {
			$re = 0.5; // el recargo de equivaencia para el tipo de iva 4% es un 0,5% adicional
		} else {
			$re = 1.4; // el recargo para el resto de tipos es 1,4% adicional
		}
		$rePorUnidad = $precio_sin_iva * $re / 100;
	} else {
		$rePorUnidad = 0.0;
	}
	
	$PVC = round($PVC + $rePorUnidad, 2);
	
	return $PVC;
}

/**
 * Calcula el valor de R.E. de un pedido
 * 
 * @param unknown $idPedido
 */
function calculaREPedido ($idPedido) {
	$reFinal = 0.0;
	
	$consulta2 = "SELECT PP.*, PR.DESCRIPCION, U.DESCRIPCION AS DESC_UNIDAD, PR.INC_CUARTOS, P.ESTADO
								FROM PEDIDOS_PRODUCTOS PP, PEDIDOS P, PRODUCTOS PR, UNIDADES U
								WHERE PP.ID_PRODUCTO = PR.ID_PRODUCTO
								and P.ID_PEDIDO = PP.ID_PEDIDO
								and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
								AND PP.ID_PEDIDO=$idPedido";
	$resProductos = consulta($consulta2);
	
	while ($producto = extraer_registro($resProductos)) {
		$cantidad = $producto['CANTIDAD'];
		$cantidadRevisada = $producto['CANTIDAD_REVISADA'];		
		$peso_por_unidad = $producto['PESO_POR_UNIDAD'];

		
		// IMPORTES GUARDADOS BASE Y FINAL
		$tipoIVA = $producto['TIPO_IVA'];
		$precioConIVA = $producto['PRECIO'];
		
		//CALCULAR IMPORTES SIN IVA
		$precioConRecargoSinIVA = ((100 * $precioConIVA) / (100 + $tipoIVA));
		
		$precioConRecargoSinIVATotal = $precioConRecargoSinIVA;
		
		// Cantidad NO revisada si no finalizado el pedido y si el tipo es 2 o 3
		if (!(isset($cantidadRevisada) && $cantidadRevisada>=0 && $producto['ESTADO']=='FINALIZADO')) {
			$cantidadRevisada = $cantidad;
				
			if (isset($peso_por_unidad) && $peso_por_unidad>0) {
				$cantidadRevisada = $cantidadRevisada * $peso_por_unidad; //Cantidad en KGs
			}
		}
		
		if (isset($peso_por_unidad) && $peso_por_unidad>0) {
			$precio_por_kg_sin_iva = $precioConRecargoSinIVA / $peso_por_unidad;
		
			$precioConRecargoSinIVA =round($precio_por_kg_sin_iva, 2);
			$precioConRecargoSinIVATotal = round(($precioConRecargoSinIVA * $cantidadRevisada), 2);
		} else {
			$precioConRecargoSinIVATotal = round(($precioConRecargoSinIVA * $cantidadRevisada), 2);
		}
		
		// Calculo totales factura por IVA
		if ($tipoIVA == 4) {
			$re = 0.5; // el recargo de equivaencia para el tipo de iva 4% es un 0,5% adicional
		} else {
			$re = 1.4; // el recargo para el resto de tipos es 1,4% adicional
		}
		$reRev =  round(($precioConRecargoSinIVATotal * $re / 100), 2);

		$reFinal = $reFinal + $reRev;
	}
	
	return $reFinal;
}

/**
 * Calcula el valor de R.E. de un pedido
 *
 * @param unknown $idPedido
 * @param unknown $conexion
 */
function calculaREPedidoConexion ($idPedido, $conexion) {
	$reFinal = 0.0;

	$consulta2 = "SELECT PP.*, PR.DESCRIPCION, U.DESCRIPCION AS DESC_UNIDAD, PR.INC_CUARTOS
	FROM PEDIDOS_PRODUCTOS PP, PRODUCTOS PR, UNIDADES U
	WHERE PP.ID_PRODUCTO = PR.ID_PRODUCTO
	and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
	AND PP.ID_PEDIDO=$idPedido";
	$resProductos = mysql_query($consulta2, $conexion);

	while ($producto = extraer_registro($resProductos)) {
		$cantidad = $producto['CANTIDAD'];
		$cantidadRevisada = $producto['CANTIDAD_REVISADA'];
		$peso_por_unidad = $producto['PESO_POR_UNIDAD'];


		// IMPORTES GUARDADOS BASE Y FINAL
		$tipoIVA = $producto['TIPO_IVA'];
		$precioConIVA = $producto['PRECIO'];

		//CALCULAR IMPORTES SIN IVA
		$precioConRecargoSinIVA = ((100 * $precioConIVA) / (100 + $tipoIVA));

		$precioConRecargoSinIVATotal = $precioConRecargoSinIVA;

		// Cantidad NO revisada si no finalizado el pedido y si el tipo es 2 o 3
		if (!(isset($cantidadRevisada) && $cantidadRevisada>=0 && $pedido['ESTADO']=='FINALIZADO')) {
			$cantidadRevisada = $cantidad;

			if (isset($peso_por_unidad) && $peso_por_unidad>0) {
				$cantidadRevisada = $cantidadRevisada * $peso_por_unidad; //Cantidad en KGs
			}
		}

		if (isset($peso_por_unidad) && $peso_por_unidad>0) {
			$precio_por_kg_sin_iva = $precioConRecargoSinIVA / $peso_por_unidad;

			$precioConRecargoSinIVA =round($precio_por_kg_sin_iva, 2);
			$precioConRecargoSinIVATotal = round(($precioConRecargoSinIVA * $cantidadRevisada), 2);
		} else {
			$precioConRecargoSinIVATotal = round(($precioConRecargoSinIVA * $cantidadRevisada), 2);
		}

		// Calculo totales factura por IVA
		if ($tipoIVA == 4) {
			$re = 0.5; // el recargo de equivaencia para el tipo de iva 4% es un 0,5% adicional
		} else {
			$re = 1.4; // el recargo para el resto de tipos es 1,4% adicional
		}
		$reRev =  round(($precioConRecargoSinIVATotal * $re / 100), 2);

		$reFinal = $reFinal + $reRev;
	}

	return $reFinal;
}
?>

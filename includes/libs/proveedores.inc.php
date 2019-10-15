<?php
/**
 * Permite Clasificar los pedidos de los usuarios del lote indicado en grupos
 * según el proveedor que se haga cargo
 *
 * @param unknown $lote
 * @param $subgruposList Lista de subgrupos separadas por coma
 * @return string[][]
 */
function clasificarPedidosPorProveedorYLote($lote, $subgruposList) {
	$totalesPorProveedor = array ();
	
	try {
		if (!isset($subgruposList) || $subgruposList=='-1000') {
			return $totalesPorProveedor;
		}
		$consultaProductosPedidos = "select U.ID_SUBGRUPO, PP.*
		from PEDIDOS_PRODUCTOS PP, PEDIDOS P, PRODUCTOS PR, USUARIOS U
		where P.LOTE='$lote'
		and PP.ID_PEDIDO=P.ID_PEDIDO
		and PR.ID_PRODUCTO = PP.ID_PRODUCTO
		and PR.ID_SUBCATEGORIA<>'-10'
		and P.ID_USUARIO = U.ID_USUARIO
		and PP.PROVEEDOR_1 <> '0'
		and U.ID_SUBGRUPO in ($subgruposList)
		ORDER BY ID_SUBGRUPO, PP.ID_PRODUCTO";
		
		$productosPedidos = consulta ( $consultaProductosPedidos );
		
		// Variables para cálculo
		$id_producto_actual = -1000;
		$id_subgrupo_actual = -1000;
		$id_proveedor_1 = 0;
		$id_proveedor_2 = 0;
		$cantidad_max_1 = 0;
		$cantidad_max_2 = 0;
		$cantidad_ilimitada = 0;
		$peso_por_unidad = 0;
		$precio_actual = 0;
		$cantidad_pedida_total = 0;
		
		// Recorrer todos los productos pedidos
		while ( $producto = extraer_registro ( $productosPedidos ) ) {
			$id_subgrupo = $producto ['ID_SUBGRUPO'];
			$id_producto = $producto ['ID_PRODUCTO'];
			$cantidad = $producto ['CANTIDAD'];
			$cantidadRevisada = $producto ['CANTIDAD_REVISADA'];
			
			// Cambia el producto, recalculamos el anterior y añadimos los totales al array
			if ($id_subgrupo != $id_subgrupo_actual || $id_producto != $id_producto_actual) {
				if ($cantidad_pedida_total > 0) {
					$cantidad_a_solicitar = $cantidad_pedida_total;
					
					// AÑADIR CANTIDAD SOLICITADA A PEDIDOS PROVEEDORES
					if (isset ( $peso_por_unidad ) && $peso_por_unidad > 0) {
						$cantidad_a_solicitar = ceil ( $cantidad_pedida_total );
					}
					
					// Solo se pide al primer proveedor
					if ($id_proveedor_2 == NULL || $id_proveedor_2 == 0 || $cantidad_ilimitada == 1 || $cantidad_a_solicitar <= $cantidad_max_1) {
						
						array_push ( $totalesPorProveedor, array (
								$id_proveedor_1,
								$id_producto_actual,
								$precio_actual,
								$cantidad_pedida_total,
								$cantidad_a_solicitar,
								$id_subgrupo_actual 
						) );
						
						// Se pide a ambos proveedores
					} else {
						$resto_cantidad = $cantidad_pedida_total - $cantidad_max_1;
						$resto_cantidad_a_solicitar = $cantidad_a_solicitar - $cantidad_max_1;
						
						array_push ( $totalesPorProveedor, array (
								$id_proveedor_1,
								$id_producto_actual,
								$precio_actual,
								$cantidad_max_1,
								$cantidad_max_1,
								$id_subgrupo_actual 
						) );
						
						array_push ( $totalesPorProveedor, array (
								$id_proveedor_2,
								$id_producto_actual,
								$precio_actual,
								$resto_cantidad,
								$resto_cantidad_a_solicitar,
								$id_subgrupo_actual 
						) );
					}
				}
				
				$id_subgrupo_actual = $id_subgrupo;
				$id_producto_actual = $id_producto;
				$id_proveedor_1 = $producto ['PROVEEDOR_1'];
				$id_proveedor_2 = $producto ['PROVEEDOR_2'];
				$cantidad_max_1 = $producto ['CANTIDAD_1'];
				$cantidad_max_2 = $producto ['CANTIDAD_2'];
				$cantidad_ilimitada = $producto ['CANTIDAD_ILIMITADA'];
				$precio_actual = $producto ['PRECIO'];
				$peso_por_unidad = $producto ['PESO_POR_UNIDAD'];
				$cantidad_pedida_total = 0;
			}
			
			if (isset ( $cantidadRevisada ) && $cantidadRevisada >= 0) {
				if (isset ( $peso_por_unidad ) && $peso_por_unidad > 0) {
					$cantidad_pedida_total += round ( ($cantidadRevisada / $peso_por_unidad), 3 );
				} else {
					$cantidad_pedida_total += $cantidadRevisada;
				}
			} else {
				$cantidad_pedida_total += $cantidad;
			}
		}
		
		// Último
		if ($cantidad_pedida_total > 0) {
			$cantidad_a_solicitar = $cantidad_pedida_total;
			
			// AÑADIR CANTIDAD SOLICITADA A PEDIDOS PROVEEDORES
			if (isset ( $peso_por_unidad ) && $peso_por_unidad > 0) {
				$cantidad_a_solicitar = ceil ( $cantidad_pedida_total );
			}
			
			// Solo se pide al primer proveedor
			if ($id_proveedor_2 == NULL || $id_proveedor_2 == 0 || $cantidad_ilimitada == 1 || $cantidad_a_solicitar <= $cantidad_max_1) {
				
				array_push ( $totalesPorProveedor, array (
						$id_proveedor_1,
						$id_producto_actual,
						$precio_actual,
						$cantidad_pedida_total,
						$cantidad_a_solicitar,
						$id_subgrupo_actual 
				) );
				
				// Se pide a ambos proveedores
			} else {
				$resto_cantidad = $cantidad_pedida_total - $cantidad_max_1;
				$resto_cantidad_a_solicitar = $cantidad_a_solicitar - $cantidad_max_1;
				
				array_push ( $totalesPorProveedor, array (
						$id_proveedor_1,
						$id_producto_actual,
						$precio_actual,
						$cantidad_max_1,
						$cantidad_max_1,
						$id_subgrupo_actual 
				) );
				
				array_push ( $totalesPorProveedor, array (
						$id_proveedor_2,
						$id_producto_actual,
						$precio_actual,
						$resto_cantidad,
						$resto_cantidad_a_solicitar,
						$id_subgrupo_actual 
				) );
			}
		}
	} catch ( Exception $ex ) {
		$_SESSION ['mensaje_generico'] = "No Se han podido clasificar los pedidos a proveedores correctamente.";
		$totalesPorProveedor = array ();
	}
	
	return $totalesPorProveedor;
}

/**
 * Permite Clasificar los pedidos de los usuarios del lote indicando el resto
 * de grupos no seleccionado
 *
 * @param unknown $lote        	
 * @param $subgruposList Lista
 *        	de subgrupos separadas por coma
 * @return string[][]
 */
function clasificarPedidosPorProveedorYLoteResto($totalesPorProveedor, $lote, $subgruposList) {
	// $totalesPorProveedor = array();
	try {
		$consultaProductosPedidos = "select PP.*
		from PEDIDOS_PRODUCTOS PP, PEDIDOS P, PRODUCTOS PR, USUARIOS U
		where P.LOTE='$lote'
		and PP.ID_PEDIDO=P.ID_PEDIDO
		and PR.ID_PRODUCTO = PP.ID_PRODUCTO
		and PR.ID_SUBCATEGORIA<>'-10'
		and P.ID_USUARIO = U.ID_USUARIO
		and PP.PROVEEDOR_1 <> '0' ";
		if (isset($subgruposList) && $subgruposList!='') {
			$consultaProductosPedidos .= " and U.ID_SUBGRUPO not in ($subgruposList)";
		}
		$consultaProductosPedidos .= " ORDER BY PP.ID_PRODUCTO";
		
		$productosPedidos = consulta ( $consultaProductosPedidos );
		
		// Variables para cálculo
		$id_producto_actual = - 1000;
		$id_proveedor_1 = 0;
		$id_proveedor_2 = 0;
		$cantidad_max_1 = 0;
		$cantidad_max_2 = 0;
		$cantidad_ilimitada = 0;
		$peso_por_unidad = 0;
		$precio_actual = 0;
		$cantidad_pedida_total = 0;
		
		// Recorrer todos los productos pedidos
		while ( $producto = extraer_registro ( $productosPedidos ) ) {
			$id_producto = $producto ['ID_PRODUCTO'];
			$cantidad = $producto ['CANTIDAD'];
			$cantidadRevisada = $producto ['CANTIDAD_REVISADA'];
			
			// Cambia el producto, recalculamos el anterior y añadimos los totales al array
			if ($id_producto != $id_producto_actual) {
				if ($cantidad_pedida_total > 0) {
					$cantidad_a_solicitar = $cantidad_pedida_total;
					
					// AÑADIR CANTIDAD SOLICITADA A PEDIDOS PROVEEDORES
					if (isset ( $peso_por_unidad ) && $peso_por_unidad > 0) {
						$cantidad_a_solicitar = ceil ( $cantidad_pedida_total );
					}
					
					// Solo se pide al primer proveedor
					if ($id_proveedor_2 == NULL || $id_proveedor_2 == 0 || $cantidad_ilimitada == 1 || $cantidad_a_solicitar <= $cantidad_max_1) {
						
						array_push ( $totalesPorProveedor, array (
								$id_proveedor_1,
								$id_producto_actual,
								$precio_actual,
								$cantidad_pedida_total,
								$cantidad_a_solicitar,
								NULL 
						) );
						
						// Se pide a ambos proveedores
					} else {
						$resto_cantidad = $cantidad_pedida_total - $cantidad_max_1;
						$resto_cantidad_a_solicitar = $cantidad_a_solicitar - $cantidad_max_1;
						
						array_push ( $totalesPorProveedor, array (
								$id_proveedor_1,
								$id_producto_actual,
								$precio_actual,
								$cantidad_max_1,
								$cantidad_max_1,
								NULL 
						) );
						
						array_push ( $totalesPorProveedor, array (
								$id_proveedor_2,
								$id_producto_actual,
								$precio_actual,
								$resto_cantidad,
								$resto_cantidad_a_solicitar,
								NULL 
						) );
					}
				}
				
				$id_producto_actual = $id_producto;
				$cantidad_pedida_total = 0;
				$id_proveedor_1 = $producto ['PROVEEDOR_1'];
				$id_proveedor_2 = $producto ['PROVEEDOR_2'];
				$cantidad_max_1 = $producto ['CANTIDAD_1'];
				$cantidad_max_2 = $producto ['CANTIDAD_2'];
				$cantidad_ilimitada = $producto ['CANTIDAD_ILIMITADA'];
				$precio_actual = $producto ['PRECIO'];
				$peso_por_unidad = $producto ['PESO_POR_UNIDAD'];
			}
			
			if (isset ( $cantidadRevisada ) && $cantidadRevisada >= 0) {
				if (isset ( $peso_por_unidad ) && $peso_por_unidad > 0) {
					$cantidad_pedida_total += round ( ($cantidadRevisada / $peso_por_unidad), 3 );
				} else {
					$cantidad_pedida_total += $cantidadRevisada;
				}
			} else {
				$cantidad_pedida_total += $cantidad;
			}
		}
		
		// Último
		if ($cantidad_pedida_total > 0) {
			$cantidad_a_solicitar = $cantidad_pedida_total;
			
			// AÑADIR CANTIDAD SOLICITADA A PEDIDOS PROVEEDORES
			if (isset ( $peso_por_unidad ) && $peso_por_unidad > 0) {
				$cantidad_a_solicitar = ceil ( $cantidad_pedida_total );
			}
			
			// Solo se pide al primer proveedor
			if ($id_proveedor_2 == NULL || $id_proveedor_2 == 0 || $cantidad_ilimitada == 1 || $cantidad_a_solicitar <= $cantidad_max_1) {
				
				array_push ( $totalesPorProveedor, array (
						$id_proveedor_1,
						$id_producto_actual,
						$precio_actual,
						$cantidad_pedida_total,
						$cantidad_a_solicitar,
						NULL 
				) );
				
				// Se pide a ambos proveedores
			} else {
				$resto_cantidad = $cantidad_pedida_total - $cantidad_max_1;
				$resto_cantidad_a_solicitar = $cantidad_a_solicitar - $cantidad_max_1;
				
				array_push ( $totalesPorProveedor, array (
						$id_proveedor_1,
						$id_producto_actual,
						$precio_actual,
						$cantidad_max_1,
						$cantidad_max_1,
						NULL 
				) );
				
				array_push ( $totalesPorProveedor, array (
						$id_proveedor_2,
						$id_producto_actual,
						$precio_actual,
						$resto_cantidad,
						$resto_cantidad_a_solicitar,
						NULL 
				) );
			}
		}
	} catch ( Exception $ex ) {
		$_SESSION ['mensaje_generico'] = "No Se han podido clasificar los pedidos a proveedores correctamente.";
		$totalesPorProveedor = array ();
	}
	
	return $totalesPorProveedor;
}

/**
 * Finaliza el pedido de un proveedor y pasa a contabilidad
 * 
 * @param unknown $idPedidoProveedor        	
 * @param unknown $nombre        	
 */
function finalizarPedidoProveedor($idPedidoProveedor, $nombre) {
	$error = 0; // variable para detectar error
	
	try {
		$cajaActual = consultarCajaActual ();
		
		$conexion = conectar ();
		
		if ($conexion == FALSE) {
			$error = 1;
		}
		
		if (! $error) {
			$res = mysql_query ( "UPDATE PEDIDOS_PROVEEDORES SET PEDIDO_PROCESADO=1 WHERE ID_PEDIDO_PROVEEDOR='$idPedidoProveedor'", $conexion );
			if (! $res) {
				$error = 1;
			}
			
			// SUMAR TOTAL
			$total = 0.00;
			
			$res = mysql_query ( "select TOTAL_REVISADO, LOTE from PEDIDOS_PROVEEDORES where ID_PEDIDO_PROVEEDOR='$idPedidoProveedor'", $conexion );
			if (! $res) {
				$error = 1;
			} else {
				$fila = mysql_fetch_array ( $res, MYSQL_ASSOC );
				$valor = $fila ['TOTAL_REVISADO'];
				$lote = $fila ['LOTE'];
				if ($valor && $valor != NULL) {
					$total += $valor;
				}
			}
			
			if (! $total || $total == 0 || $total == NULL) {
				$total = 0.00;
				
				$consulta2 = "SELECT PP.*, PR.DESCRIPCION, U.DESCRIPCION AS DESC_UNIDAD, PR.INC_CUARTOS, PR.IMPORTE_SIN_IVA, PR.TIPO_IVA
				FROM PEDIDOS_PROVEEDORES_PROD PP, PRODUCTOS PR, UNIDADES U
				WHERE PP.ID_PRODUCTO = PR.ID_PRODUCTO
				and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
				AND PP.ID_PEDIDO_PROVEEDOR ='$idPedidoProveedor'";
				$resProductos = mysql_query ( $consulta2, $conexion );
				
				if (! $resProductos) {
					$error = 1;
				}
				
				while ( $producto = extraer_registro ( $resProductos ) ) {
					$cantidad = $producto ['CANTIDAD'];
					$idProductoActual = $producto ['ID_PRODUCTO'];
					$cantidad_rev = $producto ['CANTIDAD_REV'];
					if ($cantidad_rev == NULL) {
						$cantidad_rev = $cantidad;
					}
					
					$IMP_SIN_IVA = $producto['IMPORTE_SIN_IVA'];
					$TIPO_IVA = $producto['TIPO_IVA'];
					$PRECIO_CON_IVA = round(($IMP_SIN_IVA + ($IMP_SIN_IVA * $TIPO_IVA / 100)), 2);
					
					$subtotal = round ( ($PRECIO_CON_IVA * $cantidad_rev), 2 );
					$total += $subtotal;
				}
			}
			
			// INSERTAR ASIENTO CONTABLE CON EL TOTAL DEL PEDIDO
			$fechaActual = new DateTime ();
			$nuevosaldo = number_format ( ($cajaActual - $total), 2, '.', '' );
			$res = mysql_query ( "insert into CONTABILIDAD (FECHA, CONCEPTO, AUTOMATICA, IMPORTE, ESDEBE, TOTAL_CAJA) VALUES ('" . $fechaActual->format ( "Y-m-d" ) . "', 'Pedido LOTE$lote a fecha " . $fechaActual->format ( "d/m/Y" ) . " al proveedor $nombre', '1', '$total', '1', '" . $nuevosaldo . "')", $conexion );
			if (! $res) {
				$error = 1;
			}
			
			$res = mysql_query ( "update CONFIGURACION SET VALOR='" . ($nuevosaldo) . "' WHERE PARAMETRO='CAJA'", $conexion );
			if (! $res) {
				$error = 1;
			}
			
			if ($error) {
				mysql_query ( "ROLLBACK", $conexion );
				$mensaje = "No se ha podido finalizar el pedido del proveedor " . $nombre;
			} else {
				mysql_query ( "COMMIT", $conexion );
				$mensaje = "Se ha finalizado correctamente el pedido al proveedor " . $nombre;
			}
		} else {
			$mensaje = 'Error de conexión';
		}
	} catch ( Exception $ex ) {
		mysql_query ( "ROLLBACK", $conexion );
		// Devuelve el mensaje de error
		$mensaje = $ex->qetMessage ();
	}
	
	@mysql_close ( $conexion );
	
	return $mensaje;
}

/**
 * Genera datos de pedidos a proveedores del lote seleccionado
 * 
 * @param unknown $loteSel        	
 * @param $subgruposList Lista
 *        	de subgrupos separadas por coma
 */
function generarDatosPedidosProveedores($loteSel, $subgruposList) {
	$error = 0; // variable para detectar error
	$mensaje = '';
	
	try {
		// Calcular y clasificar los pedidos de usuarios del lote indicado, por proveedor
		$totalesPorProveedor = array ();
		if (! isset ( $subgruposList ) || $subgruposList == '') {
			$subgruposList = '-1000';
		}
		$totalesPorProveedor = clasificarPedidosPorProveedorYLote ( $loteSel, $subgruposList );
		// Resto
		$totalesPorProveedor = clasificarPedidosPorProveedorYLoteResto ( $totalesPorProveedor, $loteSel, $subgruposList );
		//echo '<pre>';
		//print_r ($totalesPorProveedor);
		//echo '</pre>';
		
		$conexion = conectar ();
		
		if (! $conexion) {
			$error = 1;
			$mensaje = "Error de conexión";
		}
		
		if ($error == 0) {
			$borrar = mysql_query ( "DELETE FROM PEDIDOS_PROVEEDORES_PROD WHERE ID_PEDIDO_PROVEEDOR IN (
					SELECT ID_PEDIDO_PROVEEDOR
					FROM PEDIDOS_PROVEEDORES
					WHERE LOTE = '$loteSel'
			)", $conexion );
			
			// $mensaje .= "DELETE FROM PEDIDOS_PROVEEDORES_PROD WHERE ID_PEDIDO_PROVEEDOR IN (
			// SELECT ID_PEDIDO_PROVEEDOR
			// FROM PEDIDOS_PROVEEDORES
			// WHERE LOTE = '$loteSel'
			// );<br/>";
			
			if (! $borrar) {
				$error = 1;
				$mensaje .= "Error al eliminar los productos de pedidos anteriores del lote";
			} else {
				$borrar = mysql_query ( "DELETE FROM PEDIDOS_PROVEEDORES WHERE LOTE='$loteSel'", $conexion );
				// $mensaje .= "DELETE FROM PEDIDOS_PROVEEDORES WHERE LOTE='$loteSel';<br/>";
				if (! $borrar) {
					$error = 1;
					$mensaje = "Error al eliminar los pedidos anteriores del lote";
				} else {
					// Recorrer el array y sumar el total de precio * cantidad_pedida_proveedor, si corresponde al proveedor
					foreach ( $totalesPorProveedor as $array_total_producto ) {
						$id_proveedor_actual = $array_total_producto [0];
						$id_producto = $array_total_producto [1];
						$precio = $array_total_producto [2];
						$cantidad_pedida_usuario = $array_total_producto [3];
						$cantidad_pedida_proveedor = $array_total_producto [4];
						$id_subgrupo = $array_total_producto [5];
						
						$id = 0;
						
						// Insertar pedido a proveedor si no existe
						$existePedidoProveedor = mysql_query ( "SELECT ID_PEDIDO_PROVEEDOR FROM PEDIDOS_PROVEEDORES WHERE ID_PROVEEDOR='$id_proveedor_actual' and LOTE='$loteSel' and ID_SUBGRUPO='$id_subgrupo'", $conexion );
						
						if (numero_filas ( $existePedidoProveedor ) == 0) {
							$insertar = mysql_query ( "INSERT INTO PEDIDOS_PROVEEDORES (ID_PROVEEDOR, LOTE, PEDIDO_PROCESADO, TOTAL_REVISADO, ID_SUBGRUPO)
							values ('$id_proveedor_actual', '$loteSel', '0', NULL, '$id_subgrupo')", $conexion );
							
							$id = mysql_insert_id ( $conexion );
							// $mensaje .= "INSERT INTO PEDIDOS_PROVEEDORES (ID_PEDIDO_PROVEEDOR, ID_PROVEEDOR, LOTE, PEDIDO_PROCESADO, TOTAL_REVISADO)
							// values ('$id ', '$id_proveedor_actual', '$loteSel', '0', NULL);<br/>";
							if (! $insertar) {
								$error = 1;
							}
						} else {
							$fila = extraer_registro ( $existePedidoProveedor );
							$id = $fila ['ID_PEDIDO_PROVEEDOR'];
						}
						mysql_freeresult ( $existePedidoProveedor );
						
						// Insertar producto en el pedido a proveedor
						$insertar2 = mysql_query ( "insert into PEDIDOS_PROVEEDORES_PROD (ID_PEDIDO_PROVEEDOR, ID_PRODUCTO, PRECIO, CANTIDAD, CANTIDAD_REV)
					values ('$id', '$id_producto', '$precio', '$cantidad_pedida_proveedor', NULL)", $conexion );
						// $mensaje .= "insert into PEDIDOS_PROVEEDORES_PROD (ID_PEDIDO_PROVEEDOR, ID_PRODUCTO, PRECIO, CANTIDAD, CANTIDAD_REV)
						// values ('$id', '$id_producto', '$precio', '$cantidad_pedida_proveedor', NULL);<br/>";
						if (! $insertar2) {
							$error = 1;
							$mensaje .= "Error al insertar los productos de pedidos a proveedores.";
						}
					}
				}
			}
			
			if ($error == 1) {
				mysql_query ( "ROLLBACK", $conexion );
				$mensaje .= " NO se han generado los datos de pedidos a proveedores.";
			} else {
				mysql_query ( "COMMIT", $conexion );
				$mensaje .= "Se han generado los datos de pedidos a proveedores correctamente.";
			}
		} else {
			$mensaje .= 'Error de conexión';
		}
	} catch ( Exception $ex ) {
		mysql_query ( "ROLLBACK", $conexion );
		// Devuelve el mensaje de error
		$mensaje .= $ex->qetMessage ();
	}
	
	@mysql_close ( $conexion );
	
	return $mensaje;
}

/**
 * Devuelve si hay pedidos a proveedores finalizados
 *
 * @param unknown $lote        	
 * @return boolean
 */
function hayPedidosProveedorFinalizados($lote) {
	$hay = true;
	
	$resH = consulta ( "select PEDIDO_PROCESADO from PEDIDOS_PROVEEDORES where LOTE='$lote' and PEDIDO_PROCESADO='1'" );
	if (numero_filas ( $resH ) == 0) {
		$hay = false;
	}
	
	return $hay;
}
?>
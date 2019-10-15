<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

@session_start();

date_default_timezone_set('Europe/Madrid');

 /****************************************************
        MODULO GENERAL DE FUNCIONES EN PHP
        ----------------------------------

 Este modulo contiene funciones utiles que se usan en
 la mayoria de las paginas de nuestra aplicacion.
 
 A continuacion describiremos cada funcion de forma
 detallada:
 ****************************************************/

require_once "datos_conexion.inc.php";
require_once "libs/sql.inc.php";
require_once "libs/config.inc.php";
require_once "libs/sesiones.inc.php";
require_once "libs/options.inc.php";
require_once "libs/contabilidad.inc.php";
require_once "libs/transporte.inc.php";
require_once "libs/logs.inc.php";
require_once "libs/facturacion.inc.php";
require_once "libs/proveedores.inc.php";

/**
 * Calcular la cantidad vendida para otros usuarios de un producto para el lote actual
 * @return number
 */
function cantidadVendidaProducto ($lote, $idProducto) {
	$cantidad_vendida = 0;
	$usuario = $_SESSION['ID_USUARIO'];
	
	$cantidad_vendidaC = consulta("SELECT SUM( CANTIDAD ) CANTIDAD_VENDIDA
		FROM PEDIDOS PE, PEDIDOS_PRODUCTOS PP
		WHERE PE.ID_PEDIDO = PP.ID_PEDIDO
		AND PE.LOTE = '$lote'
		AND PP.ID_PRODUCTO = '$idProducto'
		AND PE.ID_USUARIO <> '$usuario'
		AND PE.ESTADO='PREPARACION'
		GROUP BY PP.ID_PRODUCTO ");
	$cantidad_vendidaF = extraer_registro($cantidad_vendidaC);
	if ($cantidad_vendidaF) {
		$cantidad_vendida = $cantidad_vendidaF['CANTIDAD_VENDIDA'];
	}
	return $cantidad_vendida;
}

/**
 * Calcular la cantidad vendida para otros usuarios de un producto para el lote actual
 * @return number
 */
function cantidadVendidaProductoConexion ($lote, $idProducto, $conexion) {
	$cantidad_vendida = 0;
	$usuario = $_SESSION['ID_USUARIO'];

	$cantidad_vendidaC = mysql_query("SELECT SUM( CANTIDAD ) CANTIDAD_VENDIDA
			FROM PEDIDOS PE, PEDIDOS_PRODUCTOS PP
			WHERE PE.ID_PEDIDO = PP.ID_PEDIDO
			AND PE.LOTE = '$lote'
			AND PP.ID_PRODUCTO = '$idProducto'
			AND PE.ID_USUARIO <> '$usuario'
			AND PE.ESTADO='PREPARACION'
			GROUP BY PP.ID_PRODUCTO ", $conexion);
	$cantidad_vendidaF = extraer_registro($cantidad_vendidaC);
	if ($cantidad_vendidaF) {
		$cantidad_vendida = $cantidad_vendidaF['CANTIDAD_VENDIDA'];
}
return $cantidad_vendida;
}

/**
 * Permite generar un nuevo lote y:
 * - Inicializa los valores de cantidades revisadas de productos
 * - Inicializa el estado de los pedidos a proveedores en PROVEEDORES
 * 
 * @return String message
 */
function generarNuevoLote () {
	$error = 0; //variable para detectar error

	try {
		$conexion = conectar();
			
		if ($conexion==FALSE) {
			$error = 1;
		}
	
		if (!$error) {
			$res = mysql_query ("update CONFIGURACION set VALOR=VALOR+1 WHERE PARAMETRO='LOTE'", $conexion);
			if (!$res) {
				$error = 1;
			}
			$loteRes = mysql_query ("select * from CONFIGURACION WHERE PARAMETRO='LOTE'", $conexion);
			$fila = extraer_registro($loteRes);
			
			if (!$loteRes) {
				$error = 1;
			}

			if (!$res) {
				$error = 1;
			}

			if (!$res) {
				$error = 1;
			}
				
			if($error) {
				mysql_query("ROLLBACK", $conexion);
				$mensaje = 'No se ha podido generar un nuevo lote';
			} else {
				mysql_query("COMMIT", $conexion);
				$mensaje = 'Fechas modificadas correctamente: LOTE'.$fila['VALOR'];
			}
		} else {
			$mensaje = 'Error de conexión';
		}
	} catch (Exception $ex) {
		mysql_query("ROLLBACK", $conexion);
		//Devuelve el mensaje de error
		$mensaje = $ex->qetMessage();
	} 
	
	@mysql_close($conexion);
	
	return $mensaje;
}

/**
 * Finaliza un pedido y actualiza el saldo si es posible
 * 
 * * Tambien actualiza la cantidad máxima de producto disponible, en su caso *
 * 
 * @param unknown $idPedido
 */
function finalizaPedidoYActualizaSaldo ($idPedido) {
	$error = 0; //variable para detectar error
	
	try {
		$fechaApertura = consultarFechaApertura();
		$fechaCierre = consultarFechaCierre();
			
		$conexion = conectar();
			
		if ($conexion==FALSE) {
			$error = 1;
		}
		
		if (!$error) {
			$resProductos = mysql_query("select P.ID_USUARIO, P.RE, PP.* from PEDIDOS P, PEDIDOS_PRODUCTOS PP where P.ID_PEDIDO=PP.ID_PEDIDO and P.ESTADO='PREPARACION' and P.ID_PEDIDO='$idPedido'", $conexion);
			
			//$total = 0.00;
			$totalRevisado = 0.00;
			$idUsuario = '';
			$tieneRE = FALSE;
			
			if (numero_filas($resProductos) > 0) {
			
				while ($producto = extraer_registro($resProductos)) {
					$idUsuario = $producto['ID_USUARIO'];
					$idPedidoActual = $producto['ID_PEDIDO'];
					$idProducto = $producto['ID_PRODUCTO'];
					$cantidad = $producto['CANTIDAD'];
					$cantidadRevisada = $producto['CANTIDAD_REVISADA'];
					$subtotal = round(($producto['PRECIO'] * $cantidad), 2);
					
					$peso_por_unidad = $producto['PESO_POR_UNIDAD'];
					
					$tieneRE = $producto['RE'] == '1';
					
					if (isset($cantidadRevisada) && $cantidadRevisada>=0) {
						if (isset($peso_por_unidad) && $peso_por_unidad>0) {
							$precio_por_kg = $producto['PRECIO'] / $peso_por_unidad;
							$subtotalRevisado = round(($precio_por_kg * $cantidadRevisada), 2);
						} else {
							$subtotalRevisado = round(($producto['PRECIO'] * $cantidadRevisada), 2);
						}
					} else {
						if (isset($peso_por_unidad) && $peso_por_unidad>0) {
							$precio_por_kg = $producto['PRECIO'] / $peso_por_unidad;
							$cantidadRevisada = round(($cantidad * $peso_por_unidad), 2);
							$subtotalRevisado = round(($precio_por_kg * $cantidadRevisada), 2);
						} else {
							$cantidadRevisada = $cantidad;
							$subtotalRevisado = round(($producto['PRECIO'] * $cantidadRevisada), 2);
						}
					}
					
					// Restará la cantidad
					if ($cantidadRevisada>0) {
						
						if (isset($peso_por_unidad) && $peso_por_unidad>0) { //Por si es cantidad revisada por kilo
							$cantidadRevisada = $cantidad;
						}
						
						$res = restaCantidadDisponibleProducto ($idProducto, $cantidadRevisada, $conexion);
						
						if (!$res) {
							$error = 1;
							break;
						}
					}
	
					$totalRevisado += $subtotalRevisado;
				}
				
				//Añadimos al total el RE
				if ($tieneRE) {
					$totalRevisado += calculaREPedidoConexion ($idPedido, $conexion);
				}
				
				//Calcular y actualizar saldo
				$saldo = number_format(($totalRevisado), 2, '.', ''); /** Se le resta al saldo del usuario el total del pedido revisado, en vez de la diferencia */
				
				write_log("FINALIZAR PEDIDO - ".$idUsuario.": -".$saldo, "Info");
				$res = mysql_query("update USUARIOS set SALDO=(SALDO - ".$saldo.") where id_usuario='$idUsuario'", $conexion);
				
				if (!$res) {
					$error = 1;
				}
				
				//Actualizar estado pedido
				$fechaActual = new DateTime();
				$numFactura = obtenerNumeroAlbaran ($idPedido, $conexion);
				$res = mysql_query("update PEDIDOS SET ESTADO='FINALIZADO', FECHA_ENTREGA='".$fechaActual->format("Y-m-d H:i:s")."' WHERE ID_PEDIDO='$idPedido'", $conexion);
				
				if (!$res) {
					$error = 1;
				} else {
					$error = eliminarApuntesContablesFechaMayorPedido($idUsuario, $idPedido, $conexion);
				}
				
				if($error) {
					mysql_query("ROLLBACK", $conexion);
					$mensaje = 'Se ha producido un ERROR al finalizar el pedido';
				} else {
					$error = actualizaContabilidadUsuarioConexion ($idUsuario, $conexion);
					if ($error == 1) {
						mysql_query("ROLLBACK", $conexion);
						$mensaje = 'Se ha producido un ERROR al finalizar el pedido';
					} else {
						mysql_query("COMMIT", $conexion);
						$mensaje = 'Pedido FINALIZADO correctamente.';
					}
				}
			} else {
				$mensaje = 'Nada que finalizar.';
			}
		} else {
			$mensaje = 'Error de conexión';
		}
	} catch (Exception $ex) {
		mysql_query("ROLLBACK", $conexion);
		//Devuelve el mensaje de error
		$mensaje = $ex->qetMessage();
	}
	
	@mysql_close($conexion);
	 
	return $mensaje;
}

/**
 * Permite restar una cantidad de producto disponible cuando se finaliza un pedido, en su caso
 * 
 * @param unknown $idProducto
 * @param unknown $cantidadRevisada
 * @param unknown $conexion
 */
function restaCantidadDisponibleProducto ($idProducto, $cantidad, $conexion) {
	$res = mysql_query ("select CANTIDAD_1, CANTIDAD_2
			from PRODUCTOS WHERE CANTIDAD_ILIMITADA=0 
			and (CANTIDAD_1 + CANTIDAD_2)>0 
			and ID_PRODUCTO='$idProducto'", $conexion);
	
	if (!$res) {
		return $res;
	}
	
	if (numero_filas($res)>0) {
		$fila = mysql_fetch_array($res);
		$cantidad1 = $fila['CANTIDAD_1'];
		$cantidad2 = $fila['CANTIDAD_2'];
		
		if ($cantidad2>=$cantidad) {
			$cantidad2 = $cantidad2 - $cantidad;
		} else {
			$cantidad1 = $cantidad1 - ($cantidad - $cantidad2);
			$cantidad2 = 0;
		}
		
		$res = mysql_query ("update PRODUCTOS SET CANTIDAD_1='$cantidad1',  CANTIDAD_2='$cantidad2'
				where ID_PRODUCTO='$idProducto'", $conexion);
	}
	
	return $res;
}




/**
 * Calcula el total revisado pedido por usuarios a un proveedor lote actual
 * 
 * @param unknown $idProveedor
 * @param unknown $totalesPorProveedor
 */
function calcularTotalRevisadoProveedorCompradoPorUsuarios ($idProveedor, $totalesPorProveedor) {
	$total = 0.00;
	
	try {
		// Recorrer el array y sumar el total de precio * cantidad_pedida_usuario, si corresponde al proveedor
		foreach ($totalesPorProveedor as $array_total_producto) {
			$id_proveedor_actual = $array_total_producto[0];
			$id_producto = $array_total_producto[1];
			$precio = $array_total_producto[2];
			$cantidad_pedida_usuario = $array_total_producto[3];
			$cantidad_pedida_proveedor = $array_total_producto[4];
			
		    if ($idProveedor == '-1' || $id_proveedor_actual == $idProveedor) {
		    	$total += round(($precio * $cantidad_pedida_usuario), 2);
		    }
		}
	} catch ( Exception $ex ) {
		$total = -1;
	}
	
	@mysql_close($conexion);
	
	return $total;
}

/**
 * Calcula el total revisado pedido por usuarios SIN RECARGO a un proveedor
 * 
 * @param unknown $idProveedor
 * @param unknown $totalesPorProveedor
 */
function calcularTotalRevisadoProveedorCompradoPorUsuariosSinRecargo ($idProveedor, $totalesPorProveedor) {
	$total = 0.00;
	
	try {
		// Recorrer el array y sumar el total de precio * cantidad_pedida_usuario, si corresponde al proveedor
		foreach ($totalesPorProveedor as $array_total_producto) {
			$id_proveedor_actual = $array_total_producto[0];
			$id_producto = $array_total_producto[1];
			$precio = $array_total_producto[2];
			$cantidad_pedida_usuario = $array_total_producto[3];
			$cantidad_pedida_proveedor = $array_total_producto[4];
				
			if ($id_proveedor_actual == $idProveedor) {
				$recargo = calculaRecargoProducto($id_producto);
				if ($recargo && $recargo>0.01) {
					//Precio sin recargo
					//$precio = round(($precio - ($precio * $recargo / 100)), 2);
					$precio = round( (($precio*100)/($recargo+100)), 2);
				}
				
				$total += round(($precio * $cantidad_pedida_usuario), 2);
			}
		}
	} catch ( Exception $ex ) {
		$total = -1;
	}
	
	@mysql_close($conexion);
	
	return $total;
	
	$error = 0; //variable para detectar error
	$total = 0.00;
	
	try {
		$cajaActual = consultarCajaActual ();
	
		$conexion = conectar();
			
		if ($conexion==FALSE) {
			$error = 1;
		}
	
		if (!$error) {
			$res = mysql_query("select distinct PP.*
						from PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR
						where P.ID_PEDIDO=PP.ID_PEDIDO and PP.ID_PRODUCTO=PR.ID_PRODUCTO
						and (PR.PROVEEDOR_1='$idProveedor')
						and PR.ID_SUBCATEGORIA<>'-10' and P.LOTE='$lote'", $conexion); // || (PR.CANTIDAD_ILIMITADA='0' && PR.PROVEEDOR_2='$idProveedor')
			if (!$res) {
				$error = "Error";
			} else {
				$total = calculaTotalRevisadoListaProductosSinRecargo ($res);
			}
			
		} else {
			$total = "Error";	
		}
	} catch ( Exception $ex ) {
		mysql_query ( "ROLLBACK", $conexion );
		$total = "Error";
	}
	
	@mysql_close($conexion);
	
	return $total;
}

/**
 * Calcula el recargo para un producto
 * 
 * @param unknown $idProducto
 */
function calculaRecargoProducto($idProducto) {
	$recargo = 0.00;
	
	try {
		$res1 = consulta("SELECT P.RECARGO AS RECARGOP, S.RECARGO AS RECARGOS, C.RECARGO as RECARGOC
							FROM PRODUCTOS P, SUBCATEGORIAS S, CATEGORIAS C
							WHERE P.ID_SUBCATEGORIA = S.ID_SUBCATEGORIA
							AND P.ID_CATEGORIA = C.ID_CATEGORIA
							AND ID_PRODUCTO = '$idProducto'");
		$datos = extraer_registro($res1);
		$rp = $datos['RECARGOP'];
		$rs = $datos['RECARGOS'];
		$rc = $datos['RECARGOC'];
		
		if ($rp && $rp>0.00) {
			$recargo = $rp;
		} else if ($rs && $rs>0.00) {
			$recargo = $rs;
		} else {
			$recargo = $rc;
		}

	} catch ( Exception $ex ) {
		$_SESSION['mensaje_generico'] = "No se ha podido calcular el recargo del producto";
		$recargo = 15;
	}
	
	return $recargo;
}

/**
 * Calcula el recargo para un producto solo por categoria y subcategoria
 *
 * @param unknown $idProducto
 */
function calculaRecargoProductoCatYSubCat($idProducto) {
	$recargo = 0.00;

	try {
		$res1 = consulta("SELECT P.RECARGO AS RECARGOP, S.RECARGO AS RECARGOS, C.RECARGO as RECARGOC
				FROM PRODUCTOS P, SUBCATEGORIAS S, CATEGORIAS C
				WHERE P.ID_SUBCATEGORIA = S.ID_SUBCATEGORIA
				AND P.ID_CATEGORIA = C.ID_CATEGORIA
				AND ID_PRODUCTO = '$idProducto'");
		$datos = extraer_registro($res1);
		$rs = $datos['RECARGOS'];
		$rc = $datos['RECARGOC'];

		if ($rs && $rs>0.00) {
			$recargo = $rs;
		} else {
			$recargo = $rc;
		}

	} catch ( Exception $ex ) {
		$_SESSION['mensaje_generico'] = "No se ha podido calcular el recargo del producto por categoria-subcategoria";
		$recargo = 15;
	}

	return $recargo;
}

/**
 * Calcula el recargo para un producto
 *
 * @param unknown $idProducto
 * @param unknown $conexion
 */
function calculaRecargoProductoConexion ($idProducto, $conexion) {
	$recargo = 0.00;

	try {
		$res1 = mysql_query("SELECT P.RECARGO AS RECARGOP, S.RECARGO AS RECARGOS, C.RECARGO as RECARGOC
				FROM PRODUCTOS P, SUBCATEGORIAS S, CATEGORIAS C
				WHERE P.ID_SUBCATEGORIA = S.ID_SUBCATEGORIA
				AND P.ID_CATEGORIA = C.ID_CATEGORIA
				AND ID_PRODUCTO = '$idProducto'", $conexion);
		$datos = extraer_registro($res1);
		$rp = $datos['RECARGOP'];
		$rs = $datos['RECARGOS'];
		$rc = $datos['RECARGOC'];

		if ($rp && $rp>0.00) {
			$recargo = $rp;
		} else if ($rs && $rs>0.00) {
			$recargo = $rs;
		} else {
			$recargo = $rc;
		}

	} catch ( Exception $ex ) {
		$_SESSION['mensaje_generico'] = "No se ha podido calcular el recargo del producto";
		$recargo = 15;
	}

	return $recargo;
}

/**
 * Calcula la descripción de recargo para un producto
 *
 * @param $idProducto
 */
function calculaRecargoProductoDesc($idProducto) {
	$recargo = '';

	try {
		$lote = consultarLoteActual();
		$cajaActual = consultarCajaActual ();

		$res1 = consulta("SELECT P.RECARGO AS RECARGOP, S.RECARGO AS RECARGOS, C.RECARGO as RECARGOC
				FROM PRODUCTOS P, SUBCATEGORIAS S, CATEGORIAS C
				WHERE P.ID_SUBCATEGORIA = S.ID_SUBCATEGORIA
				AND P.ID_CATEGORIA = C.ID_CATEGORIA
				AND ID_PRODUCTO = '$idProducto'");
		$datos = extraer_registro($res1);
		$rp = $datos['RECARGOP'];
		$rs = $datos['RECARGOS'];
		$rc = $datos['RECARGOC'];

		if ($rp && $rp>0.00) {
			$recargo = $rp.' % PROD.';
		} else if ($rs && $rs>0.00) {
			$recargo = $rs.' % SUB.';
		} else if ($rc>0.00) {
			$recargo = $rc.' % CAT.';
		} else {
			$recargo = 'NO';
		}

	} catch ( Exception $ex ) {
		$_SESSION['mensaje_generico'] = "No se ha podido obtener el detalle del recargo del producto";
	}

	return $recargo;
}

/**
 * calcula el precio con recargo, con descuento posible
 * @param unknown $idProducto
 * @param unknown $precioProd
 * @return number|unknown
 */
function calculaPrecioConRecargo ($idProducto, $precioProd) {
	$recargoProd = calculaRecargoProducto($idProducto);
	$descRecargo = calculaDescuentoRecargoUsuario ();
	
	if ($recargoProd && $recargoProd>0.01) {
		if ($descRecargo > 0) {
			$recargoProd = round(($recargoProd - ($recargoProd*$descRecargo/100)), 2);
		}
		return round(($precioProd + ($precioProd * $recargoProd / 100)), 2);
	} else {
		return $precioProd;
	}
}

/**
 * CALCULA PVP: Aplica el iva al precio de un producto sin recargo (aplicando el recargo con descuento antes si aplica)
 * @param unknown $idProducto
 * @param unknown $precio_sin_recargo
 * @param unknown $iva
 * @return $PVP
 */
function calculaPVP ($idProducto, $precio_sin_recargo, $iva) {
	//$precioProd = calculaPrecioConRecargo($idProducto, $precio_sin_recargo);
	
	$recargoProd = calculaRecargoProducto($idProducto);
	$descRecargo = calculaDescuentoRecargoUsuario ();
	$precioProd = $precio_sin_recargo;
	
	if ($recargoProd && $recargoProd>0.01) {
		if ($descRecargo > 0) {
			$recargoProd = round(($recargoProd - ($recargoProd*$descRecargo/100)), 2);
		}
		$precioProd =  $precioProd + ($precioProd * $recargoProd / 100);
	} 
	
	return round(($precioProd + ($precioProd * $iva / 100)), 2);
}

/**
 * calcula el precio con recargo, sin descuento
 * @param unknown $idProducto
 * @param unknown $precioSinRecargo
 * @param unknown $iva
 * @return number|unknown
 */
function calculaPVPSinDescuento ($idProducto, $precio_sin_recargo, $iva) {
	$recargoProd = calculaRecargoProducto($idProducto);
	$precioProd = $precio_sin_recargo;
	
	if ($recargoProd && $recargoProd>0.01) {
		$precioProd =  $precioProd + ($precioProd * $recargoProd / 100);
	} 
	
	return round(($precioProd + ($precioProd * $iva / 100)), 2);
}

/**
 * calcula el precio con recargo, con descuento posible, de un usuario concreto
 * @param unknown $idProducto
 * @param unknown $precioProd
 * @return number|unknown
 */
function calculaPrecioConRecargoUsuario ($idProducto, $precioProd, $usuario) {
	$recargoProd = calculaRecargoProducto($idProducto);
	$descRecargo = calculaDescuentoRecargoUsuarioConcreto ($usuario);

	if ($recargoProd && $recargoProd>0.01) {
		if ($descRecargo > 0) {
			$recargoProd = round(($recargoProd - ($recargoProd*$descRecargo/100)), 2);
		}
		return round(($precioProd + ($precioProd * $recargoProd / 100)), 2);
	} else {
		return $precioProd;
	}
}

/**
 * calcula el pvp con recargo, con descuento posible, de un usuario concreto
 * @param unknown $idProducto
 * @param unknown $precioProd
 * @return number|unknown
 */
function calculaPVPUsuario ($idProducto, $precio_sin_recargo, $iva, $usuario) {
	$recargoProd = calculaRecargoProducto($idProducto);
	$descRecargo = calculaDescuentoRecargoUsuarioConcreto ($usuario);
	$precioProd = $precio_sin_recargo;
	
	if ($recargoProd && $recargoProd>0.01) {
		if ($descRecargo > 0) {
			$recargoProd = round(($recargoProd - ($recargoProd*$descRecargo/100)), 2);
		}
		$precioProd =  $precioProd + ($precioProd * $recargoProd / 100);
	} 
	
	return round(($precioProd + ($precioProd * $iva / 100)), 2);
}

/**
 * Permite calcular si el usuario tiene descuento sobre el recargo
 */
function calculaDescuentoRecargoUsuario () {
	if (!isset($_SESSION['DESCUENTO_USUARIO'])) {
		$usuarioLogado = $_SESSION['ID_USUARIO'];
		$resDesc = consulta("select DESCUENTO_RECARGO, TIPO_USUARIO from USUARIOS where ID_USUARIO='$usuarioLogado'");
		if ($filaDesc = extraer_registro($resDesc)) {
			$desc = $filaDesc['DESCUENTO_RECARGO'];
			$tipoUser = $filaDesc['TIPO_USUARIO'];
			
			if ($tipoUser=='USUARIO' && $desc>0) {
				$_SESSION['DESCUENTO_USUARIO'] = $desc;
			} else {
				$_SESSION['DESCUENTO_USUARIO'] = 0;
			}
		} else {
			$_SESSION['DESCUENTO_USUARIO'] = 0;
		}
	}
	return $_SESSION['DESCUENTO_USUARIO'];
}

/**
 * Permite calcular si el usuario indicado tiene descuento sobre el recargo
 */
function calculaDescuentoRecargoUsuarioConcreto ($usuario) {
	$desc = 0;
	if (isset($usuario)) {
		$resDesc = consulta("select DESCUENTO_RECARGO, TIPO_USUARIO from USUARIOS where ID_USUARIO='$usuario'");
		if ($filaDesc = extraer_registro($resDesc)) {
			$desc = $filaDesc['DESCUENTO_RECARGO'];
			$tipoUser = $filaDesc['TIPO_USUARIO'];
				
			if ($tipoUser!='USUARIO') {
				$desc = 0;
			}
		}
	}
	return $desc;
}

/**
 * Calcula el total revisado a pedir a un proveedor
 * @param unknown $idProveedor
 */
function calcularTotalRevisadoProveedor ($idProveedor, $lote) {
	$error = 0; //variable para detectar error
	$total = 0.00;

	try {
		$cajaActual = consultarCajaActual ();

		$conexion = conectar();
			
		if ($conexion==FALSE) {
			$error = 1;
		}

		if (!$error) {
			// SUMAR TOTAL
			$res = mysql_query("select TOTAL_REVISADO from PEDIDOS_PROVEEDORES where ID_PROVEEDOR='$idProveedor' and LOTE='$lote'", $conexion);
			if (!$res) {
				$error = 1;
			} else {
				while ($fila = mysql_fetch_array($res, MYSQL_ASSOC)) {
					$valor = $fila['TOTAL_REVISADO'];
					if ($valor && $valor!=NULL) {
						$total += $valor;
					}
					
					if (!$total || $total==0|| $total==NULL) {
						$total = 0.00;
						$consulta2 = "SELECT * FROM PEDIDOS_PROVEEDORES_PROD
						WHERE ID_PEDIDO_PROVEEDOR IN (select ID_PEDIDO_PROVEEDOR from PEDIDOS_PROVEEDORES where LOTE='$lote' and ID_PROVEEDOR='$idProveedor')";
						$resProductos = mysql_query($consulta2, $conexion);
					
						if (!$resProductos) {
							$error = 1;
						}
					
						while ($producto= extraer_registro ( $resProductos ) ) {
							$cantidad = $producto ['CANTIDAD'];
							$cantidad_rev = $producto ['CANTIDAD_REV'];
							if ($cantidad_rev == NULL) {
								$cantidad_rev = $cantidad;
							}
							$subtotal = round ( ($producto ['PRECIO'] * $cantidad_rev), 2 );
							$total += $subtotal;
						}
					}
				}
			}
		
			if ($error) {
				mysql_query ( "ROLLBACK", $conexion );
				$total = "No se ha podido finalizar el pedido del proveedor.";
			} else {
				mysql_query ( "COMMIT", $conexion );
			}
			
		} else {
			$total = "Error";	
		}
	} catch ( Exception $ex ) {
		mysql_query ( "ROLLBACK", $conexion );
		$total = "Error";
	}
	
	@mysql_close($conexion);
	
	return $total;
}



/**
 * Devuelve el ultimo día de un mes
 * @param unknown $elAnio
 * @param unknown $elMes
 * @return string
 */
function getUltimoDiaMes($elAnio,$elMes) {
	$dateUlt = date("Y-m-d 23:59:59", mktime(23,59,59,$elMes+1,1,$elAnio));
	$dateUlt = DateTime::createFromFormat("Y-m-d H:i:s", $dateUlt);
	return $dateUlt->modify("-1 day");
}

/**
 * Devuelve si esta abierto el periodo de compra
 * @return boolean
 */
function estaAbiertoPeriodoCompra () {
	$fechaApertura = consultarFechaApertura();
	$fechaCierre = consultarFechaCierre();
	$abierto = FALSE;
	
	if (isset($fechaApertura) && isset($fechaCierre)) {
		$fechaApertura = date_create_from_format("d/m/Y H:i", $fechaApertura);
		$fechaCierre = date_create_from_format("d/m/Y H:i", $fechaCierre);
		$fechaActual = new DateTime();
	
		if ($fechaActual>=$fechaApertura && $fechaActual<=$fechaCierre) {
			$abierto = TRUE;
		}
	}
	
	return $abierto;
}


/**
 * Permite calcular el saldo pendiente del usuario (pedidos no finalizados)
 * @param unknown $usuario
 */
function calculaSaldoPendienteUsuario ($usuario) {
	$consulta2 = "select * from (
		select ID_PEDIDO, FECHA_PEDIDO AS FECHA, CONCAT('COMPRA LOTE ', LOTE) as CONCEPTO, '0' AS IMPORTE, '1' AS ESDEBE, RE
		FROM PEDIDOS WHERE ID_USUARIO = '".$usuario."' AND ESTADO = 'PREPARACION'
	) tabla order by FECHA";
	
	$resUsuarios = consulta($consulta2);
	$total = 0;
	
	while ($filaUsuarios = extraer_registro($resUsuarios)) {
		// Calcular importe real de pedido de usuario
		$totalRevisado = 0;
		$tieneRE = $filaUsuarios['RE'] == '1';
		
		$resProductos = consulta("select PP.* from PEDIDOS_PRODUCTOS PP where ID_PEDIDO='".$filaUsuarios['ID_PEDIDO']."'");
		while ($filaProd = extraer_registro($resProductos)) {
			$cantidad = $filaProd['CANTIDAD'];
			
			$cantidadRevisada = $filaProd['CANTIDAD_REVISADA'];
			$peso_por_unidad = $filaProd['PESO_POR_UNIDAD'];
			
			if (isset($cantidadRevisada) && $cantidadRevisada>=0) {
				if (isset($peso_por_unidad) && $peso_por_unidad>0) {
					$precio_por_kg = $filaProd['PRECIO'] / $peso_por_unidad;
					$subtotalRevisado = round(($precio_por_kg * $cantidadRevisada), 2);
				} else {
					$subtotalRevisado = round(($filaProd['PRECIO'] * $cantidadRevisada), 2);
				}
			} else {
				if (isset($peso_por_unidad) && $peso_por_unidad>0) {
					$precio_por_kg = $filaProd['PRECIO'] / $peso_por_unidad;
					$cantidadRevisada = round(($cantidad * $peso_por_unidad), 2);
					$subtotalRevisado = round(($precio_por_kg * $cantidadRevisada), 2);
				} else {
					$cantidadRevisada = $cantidad;
					$subtotalRevisado = round(($filaProd['PRECIO'] * $cantidadRevisada), 2);
				}
			}
			$totalRevisado += $subtotalRevisado;
		}
		$total += $totalRevisado;
		
		if ($tieneRE) { //SUMAMOS AL DEBE EL R.E.
			$total += calculaREPedido($filaUsuarios['ID_PEDIDO']);
		}
	} 
	
	return $total;
}


/**
 * Permite mostrar un acceso directo si hay nuevos registros web
 */
function consultaNuevosRegistros() {
	$resNR = consulta("select count(*) as num from SOLICITUDES where LEIDO='0'");
	$resNR = extraer_registro($resNR);
	$resNR = $resNR['num'];
	
	if ($resNR>0) {
		echo "(".$resNR."+)";
	}
}


function sanear_string($string)
{

	$string = trim($string);

	$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
	);

	$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
	);

	$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
	);

	$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
	);

	$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
	);

	$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
	);

	//Esta parte se encarga de eliminar cualquier caracter extraño
	$string = str_replace(
			array("\\", "¨", "º", "-", "~",
					"#", "@", "|", "!", "\"",
					"·", "$", "%", "&", "/",
					"(", ")", "?", "'", "¡",
					"¿", "[", "^", "`", "]",
					"+", "}", "{", "¨", "´",
					">", "< ", ";", ",", ":",
					".", " ", "\"", "*"),
			'',
			$string
	);


	return $string;
}


/**
 * Consultar proveedores de un producto en texto
 */
function consultaProveedoresTexto ($idProducto) {
	$proveedores = '';
	
	$proveedor1 = consulta ("select P2.NOMBRE from PRODUCTOS P1, PROVEEDORES P2 WHERE P1.PROVEEDOR_1 = P2.ID_PROVEEDOR and P1.ID_PRODUCTO='$idProducto'");
	$proveedor1 = extraer_registro($proveedor1);
	$proveedor1 = $proveedor1['NOMBRE'];
	
	$proveedores = $proveedor1;
	
	$proveedor2 = consulta ("select P2.NOMBRE from PRODUCTOS P1, PROVEEDORES P2 WHERE P1.PROVEEDOR_2 = P2.ID_PROVEEDOR and P1.ID_PRODUCTO='$idProducto'");
	if (numero_filas($proveedor2)>0) {
		$proveedor2 = extraer_registro($proveedor2);
		$proveedor2 = ', '.$proveedor2['NOMBRE'];
	} else {
		$proveedor2 = '';
	}
	
	$proveedores .= $proveedor2;
	
	return $proveedores;
}



/**
 * Calcula el importe total NO revisado de una lista de productos resultado de una consulta o resulset
 */
function calculaTotalPedidoListaProductos ($resultadoConsulta) {
	$total = 0;

	while ($producto = mysql_fetch_array($resultadoConsulta, MYSQL_ASSOC)) {
		$cantidad = $producto['CANTIDAD'];
		$cantidadRevisada = $producto['CANTIDAD_REVISADA'];
		$peso_por_unidad = $producto['PESO_POR_UNIDAD'];

		$subtotalRevisado = 0;
		
		if (!isset($cantidad) || $cantidad==0) {
			if (isset($cantidadRevisada) && $cantidadRevisada>=0) {
				if (isset($peso_por_unidad) && $peso_por_unidad>0) {
					$precio_por_kg = $producto['PRECIO'] / $peso_por_unidad;
					$subtotalRevisado = round(($precio_por_kg * $cantidadRevisada), 2);
				} else {
					$subtotalRevisado = round(($producto['PRECIO'] * $cantidadRevisada), 2);
				}
			}
		} else {
			$subtotalRevisado = round(($producto['PRECIO'] * $cantidad), 2);
		}

		$total += $subtotalRevisado;
	}

	return $total;
}


/**
 * Permite Clasificar los pedidos de los usuarios del lote indicado en grupos
 * según el proveedor y el producto que se haga cargo
 *
 * @param unknown $lote
 * @param unknown $idProveedor
 * @param unknown $idProducto
 * @return string[][]
 */
function calculaPedidosPorProveedorProductoYLote ($lote, $idProveedor, $idProducto) {
	$totalesPorProveedor = array();
	$cantidad_usuario = 0;
	$precio_actual = 0;

	try {
		$consultaProductosPedidos = "select PP.*
		from PEDIDOS_PRODUCTOS PP, PEDIDOS P, PRODUCTOS PR
		where P.LOTE='$lote'
		and PP.ID_PEDIDO=P.ID_PEDIDO
		and PR.ID_PRODUCTO = PP.ID_PRODUCTO
		and PR.ID_SUBCATEGORIA<>'-10'
		and PP.ID_PRODUCTO = '$idProducto'
		and (PP.PROVEEDOR_1 = '$idProveedor' || (PP.CANTIDAD_ILIMITADA='0' && PP.PROVEEDOR_2='$idProveedor'))
		ORDER BY PP.ID_PRODUCTO";

		$productosPedidos = consulta ($consultaProductosPedidos);

		// Variables para cálculo
		$id_producto_actual = -1000;
		$id_proveedor_1 = 0;
		$id_proveedor_2 = 0;
		$cantidad_max_1 = 0;
		$cantidad_max_2 = 0;
		$cantidad_ilimitada = 0;
		$peso_por_unidad = 0;
		$cantidad_pedida_total = 0;

		// Recorrer todos los productos pedidos
		while ($producto = extraer_registro($productosPedidos)) {
			$id_producto = $producto ['ID_PRODUCTO'];
			$cantidad = $producto ['CANTIDAD'];
			$cantidadRevisada = $producto ['CANTIDAD_REVISADA'];
				
			//Cambia el producto, recalculamos el anterior y añadimos los totales al array
			if ($id_producto<>$id_producto_actual) {
				if ($cantidad_pedida_total>0) {
					$cantidad_a_solicitar = $cantidad_pedida_total;
						
					// AÑADIR CANTIDAD SOLICITADA A PEDIDOS PROVEEDORES
					if (isset($peso_por_unidad) && $peso_por_unidad>0) {
						$cantidad_a_solicitar = ceil($cantidad_pedida_total);
					}

					// Solo se pide al primer proveedor
					if ($id_proveedor_2==NULL || $id_proveedor_2==0 || $cantidad_ilimitada==1 || $cantidad_a_solicitar<=$cantidad_max_1) {
							
						if ($id_proveedor_1 == $idProveedor) {
							array_push($totalesPorProveedor,
								array ($id_proveedor_1, $id_producto_actual, $precio_actual, $cantidad_pedida_total, $cantidad_a_solicitar));
						}
							
						// Se pide a ambos proveedores
					} else {
						$resto_cantidad = $cantidad_pedida_total - $cantidad_max_1;
						$resto_cantidad_a_solicitar = $cantidad_a_solicitar - $cantidad_max_1;

						if ($id_proveedor_1 == $idProveedor) {
							array_push($totalesPorProveedor,
								array ($id_proveedor_1, $id_producto_actual, $precio_actual, $cantidad_max_1, $cantidad_max_1));
						}
							
						if ($id_proveedor_2 == $idProveedor) {
							array_push($totalesPorProveedor,
								array ($id_proveedor_2, $id_producto_actual, $precio_actual, $resto_cantidad, $resto_cantidad_a_solicitar));
						}
					}
				}

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
				
			if (isset($cantidadRevisada) && $cantidadRevisada>=0) {
				if (isset($peso_por_unidad) && $peso_por_unidad>0) {
					$cantidad_pedida_total += round(($cantidadRevisada / $peso_por_unidad), 3);
				} else {
					$cantidad_pedida_total += $cantidadRevisada;
				}
			} else {
				$cantidad_pedida_total += $cantidad;
			}
		}

		// Último
		if ($cantidad_pedida_total>0) {
			$cantidad_a_solicitar = $cantidad_pedida_total;

			// AÑADIR CANTIDAD SOLICITADA A PEDIDOS PROVEEDORES
			if (isset($peso_por_unidad) && $peso_por_unidad>0) {
				$cantidad_a_solicitar = ceil($cantidad_pedida_total);
			}

			// Solo se pide al primer proveedor
			if ($id_proveedor_2==NULL || $id_proveedor_2==0 || $cantidad_ilimitada==1 || $cantidad_a_solicitar<=$cantidad_max_1) {
					
				if ($id_proveedor_1 == $idProveedor) {
					array_push($totalesPorProveedor,
						array ($id_proveedor_1, $id_producto_actual, $precio_actual, $cantidad_pedida_total, $cantidad_a_solicitar));
				}
					
				// Se pide a ambos proveedores
			} else {
				$resto_cantidad = $cantidad_pedida_total - $cantidad_max_1;
				$resto_cantidad_a_solicitar = $cantidad_a_solicitar - $cantidad_max_1;

				if ($id_proveedor_1 == $idProveedor) {
					array_push($totalesPorProveedor,
						array ($id_proveedor_1, $id_producto_actual, $precio_actual, $cantidad_max_1, $cantidad_max_1));
				}

				if ($id_proveedor_2 == $idProveedor) {
					array_push($totalesPorProveedor,
						array ($id_proveedor_2, $id_producto_actual, $precio_actual, $resto_cantidad, $resto_cantidad_a_solicitar));
				}
			}
		}
		
		// SUMAR CANTIDADES
		foreach ($totalesPorProveedor as $array_total_producto) {
			$id_proveedor_actual = $array_total_producto[0];
			$id_producto = $array_total_producto[1];
			$precio = $array_total_producto[2];
			$cantidad_pedida_usuario = $array_total_producto[3];
			$cantidad_pedida_proveedor = $array_total_producto[4];
			
			$cantidad_usuario += $cantidad_pedida_usuario;
		}

	} catch ( Exception $ex ) {
		$_SESSION['mensaje_generico'] = "No Se han podido clasificar los pedidos a proveedores correctamente.";
		$totalesPorProveedor = array();
	}

	return array($cantidad_usuario, $precio_actual);
}
?>

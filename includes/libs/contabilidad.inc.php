<?php
/**
 * Añade un apunte contable y realiza la suma o resta en el total
 *
 * @param unknown $concepto
 * @param unknown $importe
 * @param unknown $esdebe
 * @param unknown $usuario
 */
function addApunteContable ($concepto, $importe, $esdebe, $usuario) {
	$error = 0; //variable para detectar error

	try {
		$importe = number_format(($importe), 2, '.', '');
		$total = consultarCajaActual();
		$total = number_format(($total), 2, '.', '');
		$conexion = conectar();
			
		if ($conexion==FALSE) {
			$error = 1;
		}

		if (!$error) {
			$fechaActual = new DateTime();
			if ($esdebe=='1') {
				$total = $total - $importe;
			} else {
				$total = $total + $importe;
			}
				
			$res = @mysql_query ("insert INTO CONTABILIDAD (FECHA, CONCEPTO, AUTOMATICA, IMPORTE, ESDEBE, USUARIO, TOTAL_CAJA)
							VALUES ('".($fechaActual->format("Y-m-d H:i:s"))."', '$concepto', '0', '$importe', '".(isset($esdebe) ? '1' : '0')."', '$usuario', '$total')", $conexion);

			if (!$res) {
				$error = 1;
			}

			$res = @mysql_query ("update CONFIGURACION SET VALOR='$total' WHERE PARAMETRO='CAJA'", $conexion);
				
			if (!$res) {
				$error = 1;
			}
				
			//ACTUALIZAR SALDO DE USUARIO, en su caso
			if ($usuario!='-SISTEMA-') {
				if ($esdebe=='1') {
					write_log("APUNTE CONTABLE - ".$usuario.": -".$importe, "Info");
					$resActSaldo = @mysql_query("UPDATE USUARIOS SET SALDO = SALDO - $importe WHERE ID_USUARIO='$usuario'", $conexion);
				} else {
					write_log("APUNTE CONTABLE - ".$usuario.": +".$importe, "Info");
					$resActSaldo = @mysql_query("UPDATE USUARIOS SET SALDO = SALDO + $importe WHERE ID_USUARIO='$usuario'", $conexion);
				}
					
				if (!$resActSaldo) {
					$error = 1;
				} else {
					$error = actualizaContabilidadUsuarioConexion ($usuario, $conexion);
				}
			}

			if($error) {
				@mysql_query("ROLLBACK", $conexion);
				$mensaje = "No se ha podido guardar el apunte.";
			} else {
				@mysql_query("COMMIT", $conexion);
				$mensaje = "OK";
			}
		} else {
			$mensaje = 'Error de conexión';
		}
	} catch (Exception $ex) {
		@mysql_query("ROLLBACK", $conexion);
		//Devuelve el mensaje de error
		$mensaje = $ex->qetMessage();
	}

	@mysql_close($conexion);

	return $mensaje;
}

/**
 * Actualiza la contabilidad de un usuario
 *
 * @param unknown $usuario
 */
function actualizaContabilidadUsuario ($usuario) {
	$error = 0; //variable para detectar error

	try {
		$conexion = conectar();
			
		if ($conexion==FALSE) {
			$error = 1;
		}

		if (!$error) {
			//TODO ELIMINAR
			mysql_query ("DELETE FROM CONTABILIDAD_USUARIO WHERE USUARIO='$usuario'", $conexion);
			//Obtener la última fecha de actualización de la contabilidad del usuario
			$resUltFecha = mysql_query ("SELECT MAX(FECHA) FECHA FROM CONTABILIDAD_USUARIO WHERE USUARIO='$usuario'", $conexion);
			if (!$resUltFecha) {
				$error = 1;
			}
			$resUltFecha = extraer_registro ($resUltFecha);
			$resUltFecha = $resUltFecha['FECHA'];

			$saldoActual = 0;
			if($resUltFecha==NULL) {
				$consulta = "select * from (
							select '-1' AS ID_PEDIDO, FECHA, CONCEPTO, IMPORTE, ESDEBE FROM CONTABILIDAD WHERE USUARIO='".$usuario."' AND FECHA>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
							union all
							select ID_PEDIDO, FECHA_PEDIDO AS FECHA, CONCAT('COMPRA LOTE ', LOTE) as CONCEPTO, '0' AS IMPORTE, '1' AS ESDEBE FROM PEDIDOS WHERE ID_USUARIO = '".$usuario."' AND ESTADO <> 'PREPARACION' AND FECHA_PEDIDO>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
							) tabla order by FECHA";
			} else {
				$consulta = "select * from (
							select '-1' AS ID_PEDIDO, FECHA, CONCEPTO, IMPORTE, ESDEBE FROM CONTABILIDAD WHERE USUARIO='".$usuario."' AND FECHA>'".$resUltFecha."' AND FECHA>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
							union all
							select ID_PEDIDO, FECHA_PEDIDO AS FECHA, CONCAT('COMPRA LOTE ', LOTE) as CONCEPTO, '0' AS IMPORTE, '1' AS ESDEBE FROM PEDIDOS WHERE ID_USUARIO = '".$usuario."' AND ESTADO <> 'PREPARACION' AND FECHA_PEDIDO>'".$resUltFecha."' AND FECHA_PEDIDO>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
							) tabla order by FECHA";

				//Obtener último saldo
				$resUltSaldo = mysql_query ("SELECT TOTAL_SALDO FROM CONTABILIDAD_USUARIO WHERE FECHA='".$resUltFecha."' AND USUARIO='$usuario'", $conexion);
				$saldoActual = 0.0;
				if (numero_filas($resUltSaldo)>0) {
					$resUltSaldo = extraer_registro ($resUltSaldo);
					$saldoActual = $resUltSaldo['TOTAL_SALDO'];
				}
			}
				
			//Hacemos la consulta desde la fecha última
			$resUsuarios = mysql_query($consulta, $conexion);
				
			if (!$resUsuarios) {
				$error = 1;
			}

			if (numero_filas($resUsuarios)>0) {
				while ($filaUsuarios = extraer_registro($resUsuarios)) {
					$importe = $filaUsuarios['IMPORTE'];
						
					if ($filaUsuarios['ID_PEDIDO']!='-1') {
						// Calcular importe real de pedido de usuario
						$pedidos = mysql_query("select * from PEDIDOS where ID_USUARIO='$usuario' AND ID_PEDIDO='".$filaUsuarios['ID_PEDIDO']."'", $conexion);
						if (!$pedidos) {
							$error = 1;
						} else  {
							if ($filaPed = extraer_registro($pedidos)) {
								$totalRevisado = 0;
								$tieneRE = $filaPed['RE'] == '1';
								
								$resProductos = mysql_query("select PP.* from PEDIDOS_PRODUCTOS PP where ID_PEDIDO='".$filaPed['ID_PEDIDO']."'", $conexion);
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
								
								if ($tieneRE) {
									$totalRevisado += calculaREPedidoConexion($filaPed['ID_PEDIDO'], $conexion);
								}
								
								$importe = $totalRevisado;
							}
						}
					}
						
					if ( $filaUsuarios['ESDEBE']=='0') {
						$saldoActual = round(($saldoActual + $importe), 2);
					} else {
						$saldoActual = round(($saldoActual - $importe), 2);
					}
						
					$sqlInsert = "INSERT INTO CONTABILIDAD_USUARIO (FECHA, USUARIO, CONCEPTO, IMPORTE, ESDEBE, TOTAL_SALDO)
									VALUES ('".$filaUsuarios['FECHA']."', '$usuario', '".$filaUsuarios['CONCEPTO']."', '$importe', '".$filaUsuarios['ESDEBE']."', '$saldoActual')";
					$resInsertar = mysql_query($sqlInsert, $conexion);
						
					if (!$resInsertar) {
						$error = 1;
						break;
					}
				}
			}
				
			//DESARROLLO
			//$error = !mysql_query("UPDATE USUARIOS SET SALDO='".$saldoActual."' WHERE ID_USUARIO='$usuario'", $conexion);

			if($error) {
				mysql_query("ROLLBACK", $conexion);
				$mensaje = "No se ha podido actualizar el saldo contable del usuario.";
			} else {
				mysql_query("COMMIT", $conexion);
				$mensaje = "Se ha actualizado el saldo del usuario correctamente";
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
 * Actualiza la contabilidad de un usuario, desde una conexión abierta
 *
 * @param unknown $usuario
 */
function actualizaContabilidadUsuarioConexion ($usuario, $conexion) {
	$error = 0; //variable para detectar error

	try {
		//Obtener la última fecha de actualización de la contabilidad del usuario
		$resUltFecha = @mysql_query ("SELECT MAX(FECHA) FECHA FROM CONTABILIDAD_USUARIO WHERE USUARIO='$usuario'", $conexion);
		if (!$resUltFecha) {
			$error = 1;
		}
		$resUltFecha = extraer_registro ($resUltFecha);
		$resUltFecha = $resUltFecha['FECHA'];

		$saldoActual = 0;
		if($resUltFecha==NULL) {
			$consulta = "select * from (
						select '-1' AS ID_PEDIDO, FECHA, CONCEPTO, IMPORTE, ESDEBE FROM CONTABILIDAD WHERE USUARIO='".$usuario."' AND FECHA>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
						union all
						select ID_PEDIDO, FECHA_PEDIDO AS FECHA, CONCAT('COMPRA LOTE ', LOTE) as CONCEPTO, '0' AS IMPORTE, '1' AS ESDEBE FROM PEDIDOS WHERE ID_USUARIO = '".$usuario."' AND ESTADO <> 'PREPARACION' AND FECHA_PEDIDO>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
						) tabla order by FECHA";
		} else {
			$consulta = "select * from (
						select '-1' AS ID_PEDIDO, FECHA, CONCEPTO, IMPORTE, ESDEBE FROM CONTABILIDAD WHERE USUARIO='".$usuario."' AND FECHA>'".$resUltFecha."' AND FECHA>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
						union all
						select ID_PEDIDO, FECHA_PEDIDO AS FECHA, CONCAT('COMPRA LOTE ', LOTE) as CONCEPTO, '0' AS IMPORTE, '1' AS ESDEBE FROM PEDIDOS WHERE ID_USUARIO = '".$usuario."' AND ESTADO <> 'PREPARACION' AND FECHA_PEDIDO>'".$resUltFecha."' AND FECHA_PEDIDO>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
						) tabla order by FECHA";

			//Obtener último saldo
			$resUltSaldo = @mysql_query ("SELECT TOTAL_SALDO FROM CONTABILIDAD_USUARIO WHERE FECHA='".$resUltFecha."' AND USUARIO='$usuario'", $conexion);
			$saldoActual = 0.0;
			if (numero_filas($resUltSaldo)>0) {
				$resUltSaldo = extraer_registro ($resUltSaldo);
				$saldoActual = $resUltSaldo['TOTAL_SALDO'];
			}
		}
			
		//Hacemos la consulta desde la fecha última
		$resUsuarios = @mysql_query($consulta, $conexion);
			
		if (!$resUsuarios) {
			$error = 1;
		}

		if (numero_filas($resUsuarios)>0) {
			while ($filaUsuarios = extraer_registro($resUsuarios)) {
				$importe = $filaUsuarios['IMPORTE'];
					
				if ($filaUsuarios['ID_PEDIDO']!='-1') {
					// Calcular importe real de pedido de usuario
					$pedidos = mysql_query("select * from PEDIDOS where ID_USUARIO='$usuario' AND ID_PEDIDO='".$filaUsuarios['ID_PEDIDO']."'", $conexion);
					if (!$pedidos) {
						$error = 1;
					} else  {
						if ($filaPed = extraer_registro($pedidos)) {
							$totalRevisado = 0;
							$tieneRE = $filaPed['RE'] == '1';
							
							$resProductos = mysql_query("select PP.* from PEDIDOS_PRODUCTOS PP where ID_PEDIDO='".$filaPed['ID_PEDIDO']."'", $conexion);
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
							
							if ($tieneRE) {
								$totalRevisado += calculaREPedidoConexion($filaPed['ID_PEDIDO'], $conexion);
							}
							
							$importe = $totalRevisado;
						}
					}
				}
					
				if ( $filaUsuarios['ESDEBE']=='0') {
					$saldoActual = round(($saldoActual + $importe), 2);
				} else {
					$saldoActual = round(($saldoActual - $importe), 2);
				}
					
				$sqlInsert = "INSERT INTO CONTABILIDAD_USUARIO (FECHA, USUARIO, CONCEPTO, IMPORTE, ESDEBE, TOTAL_SALDO)
								VALUES ('".$filaUsuarios['FECHA']."', '$usuario', '".$filaUsuarios['CONCEPTO']."', '$importe', '".$filaUsuarios['ESDEBE']."', '$saldoActual')";
				$resInsertar = @mysql_query($sqlInsert, $conexion);
					
				if (!$resInsertar) {
					$error = 1;
				}
			}
		}
	} catch (Exception $ex) {
		$error = 1;
	}

	return $error;
}

/**
 * Actualiza la contabilidad de un usuario y su saldo
 *
 * @param unknown $usuario
 */
function actualizaContabilidadRealUsuario ($usuario) {
	$error = 0; //variable para detectar error
	$totalSaldoActual = 0;

	try {
		$conexion = conectar();
			
		if ($conexion==FALSE) {
			$error = 1;
		}

		if (!$error) {
			mysql_query ("DELETE FROM CONTABILIDAD_USUARIO WHERE USUARIO='$usuario'", $conexion);
			//Obtener la última fecha de actualización de la contabilidad del usuario
			$resUltFecha = mysql_query ("SELECT MAX(FECHA) FECHA FROM CONTABILIDAD_USUARIO WHERE USUARIO='$usuario'", $conexion);
			if (!$resUltFecha) {
				$error = 1;
			}
			$resUltFecha = extraer_registro ($resUltFecha);
			$resUltFecha = $resUltFecha['FECHA'];

				
			if($resUltFecha==NULL) {
				$consulta = "select * from (
							select '-1' AS ID_PEDIDO, FECHA, CONCEPTO, IMPORTE, ESDEBE FROM CONTABILIDAD WHERE USUARIO='".$usuario."' AND FECHA>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
							union all
							select ID_PEDIDO, FECHA_PEDIDO AS FECHA, CONCAT('COMPRA LOTE ', LOTE) as CONCEPTO, '0' AS IMPORTE, '1' AS ESDEBE FROM PEDIDOS WHERE ID_USUARIO = '".$usuario."' AND ESTADO <> 'PREPARACION' AND FECHA_PEDIDO>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
							) tabla order by FECHA";
			} else {
				$consulta = "select * from (
							select '-1' AS ID_PEDIDO, FECHA, CONCEPTO, IMPORTE, ESDEBE FROM CONTABILIDAD WHERE USUARIO='".$usuario."' AND FECHA>'".$resUltFecha."' AND FECHA>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
							union all
							select ID_PEDIDO, FECHA_PEDIDO AS FECHA, CONCAT('COMPRA LOTE ', LOTE) as CONCEPTO, '0' AS IMPORTE, '1' AS ESDEBE FROM PEDIDOS WHERE ID_USUARIO = '".$usuario."' AND ESTADO <> 'PREPARACION' AND FECHA_PEDIDO>'".$resUltFecha."' AND FECHA_PEDIDO>'".consultarFechaDesdeCalculoSaldoUsuarios()."'
							) tabla order by FECHA";

				//Obtener último saldo
				$resUltSaldo = mysql_query ("SELECT TOTAL_SALDO FROM CONTABILIDAD_USUARIO WHERE FECHA='".$resUltFecha."' AND USUARIO='$usuario'", $conexion);
				if (numero_filas($resUltSaldo)>0) {
					$resUltSaldo = extraer_registro ($resUltSaldo);
					$totalSaldoActual = $resUltSaldo['TOTAL_SALDO'];
				}
			}

			//Hacemos la consulta desde la fecha última
			$resUsuarios = mysql_query($consulta, $conexion);

			if (!$resUsuarios) {
				$error = 1;
			}

			if (numero_filas($resUsuarios)>0) {
				while ($filaUsuarios = extraer_registro($resUsuarios)) {
					$importe = $filaUsuarios['IMPORTE'];

					if ($filaUsuarios['ID_PEDIDO']!='-1') {
						// Calcular importe real de pedido de usuario
						$pedidos = mysql_query("select * from PEDIDOS where ID_USUARIO='$usuario' AND ID_PEDIDO='".$filaUsuarios['ID_PEDIDO']."'", $conexion);
						if (!$pedidos) {
							$error = 1;
						} else  {
							if ($filaPed = extraer_registro($pedidos)) {
								$totalRevisado = 0;
								$tieneRE = $filaPed['RE'] == '1';
								
								$resProductos = mysql_query("select PP.* from PEDIDOS_PRODUCTOS PP where ID_PEDIDO='".$filaPed['ID_PEDIDO']."'", $conexion);
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
								
								if ($tieneRE) {
									$totalRevisado += calculaREPedidoConexion($filaPed['ID_PEDIDO'], $conexion);
								}
								$importe = $totalRevisado;
							}
						}
					}

					if ( $filaUsuarios['ESDEBE']=='0') {
						$totalSaldoActual = round(($totalSaldoActual + $importe), 2);
					} else {
						$totalSaldoActual = round(($totalSaldoActual - $importe), 2);
					}

					$sqlInsert = "INSERT INTO CONTABILIDAD_USUARIO (FECHA, USUARIO, CONCEPTO, IMPORTE, ESDEBE, TOTAL_SALDO)
									VALUES ('".$filaUsuarios['FECHA']."', '$usuario', '".$filaUsuarios['CONCEPTO']."', '$importe', '".$filaUsuarios['ESDEBE']."', '$totalSaldoActual')";
					$resInsertar = mysql_query($sqlInsert, $conexion);


					if (!$resInsertar) {
						$error = 1;
						break;
					}
				}
			}
				
			//DESARROLLO
			//$error = !mysql_query("UPDATE USUARIOS SET SALDO='".$totalSaldoActual."' WHERE ID_USUARIO='$usuario'", $conexion);
				
			if($error) {
				mysql_query("ROLLBACK", $conexion);
				$mensaje = "No se ha podido actualizar el saldo contable del usuario.";
			} else {
				mysql_query("COMMIT", $conexion);
				$mensaje = "Se ha actualizado el saldo del usuario correctamente";
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
 * Eliminar los apuntes contables de usuario mayores a un pedido, para regenerarlos
 * @param unknown $idUsuario
 * @param unknown $idPedido
 * @param unknown $conexion
 */
function eliminarApuntesContablesFechaMayorPedido($idUsuario, $idPedido, $conexion) {
	$error = 0; //variable para detectar error

	try {
		//Obtener la fecha del pedido
		$resFechaPedido = mysql_query ("SELECT FECHA_PEDIDO FROM PEDIDOS WHERE ID_USUARIO='$idUsuario' and ID_PEDIDO='$idPedido'", $conexion);
		if (!$resFechaPedido) {
			$error = 1;
		} else {
			$resFechaPedido = extraer_registro ($resFechaPedido);
			$resFechaPedido = $resFechaPedido['FECHA_PEDIDO'];

			$resDelete = mysql_query("DELETE FROM CONTABILIDAD_USUARIO WHERE FECHA>='$resFechaPedido' and USUARIO='$idUsuario'");
				
			if (!$resDelete) {
				$error = 1;
			}
		}

	} catch (Exception $ex) {
		$error = 1;
	}

	return $error;
}
?>
<?php
include_once ("funciones.inc.php");
class carrito {
	//atributos de la clase
   	var $num_productos;
   	var $array_id_prod;
   	var $array_nombre_prod;
   	var $array_precio_prod;
   	var $array_cantidad_prod;
   	var $array_transporte;

	//constructor. Realiza las tareas de inicializar los objetos cuando se instancian
	//inicializa el numero de productos a 0
	function carrito () {
   		$this->num_productos=0;
	}
	
	//Introduce un producto en el carrito. Recibe los datos del producto
	//Se encarga de introducir los datos en los arrays del objeto carrito
	//luego aumenta en 1 el numero de productos
	function introduce_producto($id_prod,$nombre_prod,$precio_prod,$cantidad_prod, $max, $ilimitada) {
		if ($ilimitada==1 || $cantidad_prod<=$max) {
			$encontrado = FALSE;
			
			for ($i=0; $i< ($this->num_productos); $i++) {
				if(!$encontrado && $this->array_id_prod[$i]!=-1 && $this->array_id_prod[$i]==$id_prod) {
					$this->array_cantidad_prod[$i] = $cantidad_prod;
					$encontrado = TRUE;
				}
			}
			
			if (!$encontrado) {
				$this->array_id_prod[$this->num_productos]=$id_prod;
				$this->array_nombre_prod[$this->num_productos]=$nombre_prod;
				$this->array_precio_prod[$this->num_productos]=$precio_prod;
				$this->array_cantidad_prod[$this->num_productos]=$cantidad_prod;
				$this->array_transporte[$this->num_productos] = 0;
				$this->num_productos = $this->num_productos + 1;
			}
		}
	}
	
	//Introduce un producto en el carrito de tipo transporte
	function introduce_producto_transporte ($id_prod, $total_cesta) {
		$nombre_prod = consulta("select DESCRIPCION from PRODUCTOS where ID_PRODUCTO='$id_prod'");
		$nombre_prod = extraer_registro($nombre_prod);
		$nombre_prod = $nombre_prod['DESCRIPCION'];
		
		$precio_prod = calcula_precio_transporte ($id_prod, $total_cesta);
		
		$this->array_id_prod[$this->num_productos]=$id_prod;
		$this->array_nombre_prod[$this->num_productos]=$nombre_prod;
		$this->array_precio_prod[$this->num_productos]=$precio_prod;
		$this->array_cantidad_prod[$this->num_productos]=1;
		$this->array_transporte[$this->num_productos] = 1;
		$this->num_productos = $this->num_productos + 1;
		
		return $precio_prod;
	}
	
	//Eliminar producto transporte
	function elimina_producto_transporte () {
		for ($i=0; $i< ($this->num_productos); $i++) {
			if($this->array_id_prod[$i]!=-1 && $this->array_transporte[$i] == 1) {
				$this->array_id_prod[$i]=-1;
			}
		}
	}
	
	//Obtiene producto transporte
	function obtiene_producto_transporte () {
		for ($i=0; $i< ($this->num_productos); $i++) {
			if($this->array_id_prod[$i]!=-1 && $this->array_transporte[$i] == 1) {
				return $this->array_id_prod[$i];
			}
		}
		return NULL;
	}
	
	function actualiza_producto($indice, $cantidad) {

		$resultado = consulta("select * from PRODUCTOS WHERE ID_PRODUCTO=".$this->array_id_prod[$indice]);
		$producto = extraer_registro($resultado);
		$lote = consultarLoteActual();
		$cantidadVendida = cantidadVendidaProducto($lote, $producto['ID_PRODUCTO']);
		$max = ($producto['CANTIDAD_1']+$producto['CANTIDAD_2']-$cantidadVendida);
		$ilimitada = $producto['CANTIDAD_ILIMITADA'];
		
		if ($ilimitada==0 && $cantidad>$max) {
			$this->array_cantidad_prod[$indice]=$max;
		} else {
			$this->array_cantidad_prod[$indice]=$cantidad;
		}
	}

	//Muestra el contenido del carrito de la compra
	//ademas pone los enlaces para eliminar un producto del carrito
	function imprime_carrito() {
		$suma = 0;
		$precio_transporte = 0;
		$ahorroTotal = 0.0;
		$sumaSinIVA = 0.0;
		
		//Comprobación RE
		$tieneRE = FALSE; 
		$usuario = $_SESSION['ID_USUARIO'];
		$resRE = consulta("select RECARGO_EQ from USUARIOS where ID_USUARIO='$usuario'");
		$resRE = extraer_registro($resRE);
		$tieneRE = ($resRE['RECARGO_EQ'] == '1');
		$reFinal = 0.0;
		
		$lote = consultarLoteActual();
		$resComprados = consulta("select PP.*, PR.DESCRIPCION_MEDIDA as DESCRIPCION_MEDIDA, U.DESCRIPCION as MEDIDA, PR.PEDIDO_MINIMO, PR.INC_CUARTOS, PR.DESCRIPCION 
				from PEDIDOS_PRODUCTOS PP, PEDIDOS P, PRODUCTOS PR, UNIDADES U
				where PP.ID_PEDIDO=P.ID_PEDIDO AND PP.ID_PRODUCTO=PR.ID_PRODUCTO AND PR.UNIDAD_MEDIDA=U.ID_UNIDAD	
				AND P.ID_USUARIO='$usuario' and P.LOTE='$lote'");
		if (numero_filas($resComprados)>0) {
			echo '<tr><td colspan="6" class="subtabla">Productos confirmados</td></tr>';
		}
		while ($filaComprados = extraer_registro($resComprados)) {
			$idProducto =  $filaComprados['ID_PRODUCTO'];
			$encontrado = FALSE;
			
			for ($i=0; $i< ($this->num_productos); $i++) {
				if(!$encontrado && $this->array_id_prod[$i]!=-1 && $this->array_id_prod[$i]==$idProducto) {
					$encontrado = TRUE;
				}
			}
			
			//if (!$encontrado) {
				$unidad = $filaComprados['MEDIDA'];
				$descMedida = $filaComprados['DESCRIPCION_MEDIDA'];
				$ilimitada = $filaComprados['CANTIDAD_ILIMITADA'];	
				$minimo = $filaComprados['PEDIDO_MINIMO'];
				$precioSinRecargo = $filaComprados['IMPORTE_SIN_IVA'];
				
				$iva=$filaComprados['TIPO_IVA'];
				
				if ($ilimitada=='0' || $ilimitada==NULL) {
					$cantidad_vendida = cantidadVendidaProducto($lote, $idProducto);
					$max = ($filaComprados['CANTIDAD_1'] + $filaComprados['CANTIDAD_2'] - $cantidad_vendida);
				} else {
					$max = 0;
				}
				$cuartos = $filaComprados['INC_CUARTOS'];
				
				$consultaTransporte = consulta("select ID_SUBCATEGORIA from PRODUCTOS WHERE ID_PRODUCTO='$idProducto' AND ID_SUBCATEGORIA=-10");
				
				echo '<tr '.(($encontrado || (numero_filas($consultaTransporte)>0 && $_SESSION["ocarrito"]->num_productos()>0)) ? 'style="text-decoration:line-through"' : "").'>';
				echo '<td>' . $filaComprados['DESCRIPCION'] . '</td>';
				echo "<td align=\"center\">" . $filaComprados['PRECIO'] . "  &euro;</td>";
				echo "<td align=\"center\">" . $filaComprados['CANTIDAD'];
				echo "</td>";
				echo "<td align=\"right\">" . round(($filaComprados['PRECIO'] * $filaComprados['CANTIDAD']), 2) . " &euro;&nbsp;</td>";
				
				if (strpos($_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'])) {
					$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				} else {
					$url="http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
				}
				
				if (numero_filas($consultaTransporte)>0) { // Es transporte
					echo "<td align=\"right\"></td>";
					echo "<td align=\"right\"></td>";
					$precio_transporte = round(($filaComprados['PRECIO'] * $filaComprados['CANTIDAD']), 2);
				} else {
					if ($ilimitada || $max>0) {
						echo "<td align=\"right\">";
						if (!$encontrado) echo "<a href=\"#\"><img alt=\"Modificar\" title=\"Modificar\" onclick=\"addCesta('".$idProducto."', '".htmlspecialchars($filaComprados['DESCRIPCION'], ENT_COMPAT, 'UTF-8')."', '".$filaComprados['PRECIO']."', '$unidad', '".htmlspecialchars($descMedida, ENT_COMPAT, 'UTF-8')."', '$max', '$ilimitada',  '$cuartos', '$minimo');\" src=\"../img/EDITAR.png\" height=\"25\" width=\"25\" style=\"float: left;\" /></a>";
						echo "</td>";
					}
					echo "<td align=\"right\">";
					if (!$encontrado) echo "<a class=\"imgcomprar\" href=\"../includes/meter_producto_borrar.php?id=".$idProducto."&precio=".$filaComprados['PRECIO']."&cantidad=0&nombre=+".$filaComprados['DESCRIPCION']."\"><img alt=\"Eliminar\" title=\"Eliminar\" src=\"../img/SACAR.png\" height=\"25\" width=\"25\" style=\"float: left;\" /></a>";
					echo "</td>";
				}
				echo '</tr>';
				
				$PVP = round(($filaComprados['PRECIO'] * $filaComprados['CANTIDAD']), 2);
				if (!$encontrado) $suma += $PVP;
				
				//$precioSinIVA = round(($PVP - ($PVP * $iva / 100)), 2);
				$precioConRecargoSinIVA = round(((100 *$PVP) / (100 + $iva) ), 4);
				//$precioConRecargoSinIVA = calculaPrecioConRecargo ($this->array_id_prod[$i], $precioSinRecargo);
				//$precioConRecargoSinIVA = round($precioConRecargoSinIVA * $this->array_cantidad_prod[$i], 2);
				if (!$encontrado) $sumaSinIVA += $precioConRecargoSinIVA;
				
				if ($tieneRE) {
					if ($iva == 4) {
						$re = 0.5; // el recargo de equivaencia para el tipo de iva 4% es un 0,5% adicional
					} else {
						$re = 1.4; // el recargo para el resto de tipos es 1,4% adicional
					}
					$reRev =  round(($precioConRecargoSinIVA * $re / 100), 2);
				
					if (!$encontrado) $reFinal = $reFinal + $reRev;
					if (!$encontrado) $suma += $reRev;
				}
				
				// Mostrar descuento aplicado
				if (!$encontrado) {
				if (calculaDescuentoRecargoUsuario() > 0 && numero_filas($consultaTransporte)==0) {
					$precioSinDesc = calculaPVPSinDescuento($idProducto, $precioSinRecargo, $iva);
					$ahorro = round(($precioSinDesc * $filaComprados['CANTIDAD']), 2) - round(($filaComprados['PRECIO'] * $filaComprados['CANTIDAD']), 2);
					$ahorroTotal += $ahorro;
					//echo '<tr><td align="right" style="text-align:right" colspan="3"><b>Descuento Aplicado:</b></td><td align="right">'.$ahorro,' &euro;</td><td colspan="2">&nbsp;</td></tr>';
				}
				}
			}
		//}
		
		if (numero_filas($resComprados)>0 && $this->num_productos ()>0) {
			echo '<tr><td colspan="6" class="subtabla">Productos en la cesta</td></tr>';
		}
		
		for ($i=0; $i< ($this->num_productos); $i++) {
			if($this->array_id_prod[$i]!=-1) {
				//Para modificar cantidad
				$consulta = "select P.*, U.DESCRIPCION AS UNIDAD from PRODUCTOS P, UNIDADES U
						WHERE P.UNIDAD_MEDIDA=U.ID_UNIDAD and ID_PRODUCTO='".$this->array_id_prod[$i]."'
						and P.ACTIVO=1 ";
				$productosRes = consulta($consulta);
				$producto = extraer_registro($productosRes);
				$unidad = $producto['UNIDAD'];
				$descMedida = $producto['DESCRIPCION_MEDIDA'];
				$ilimitada = $producto['CANTIDAD_ILIMITADA'];
				$minimo = $producto['PEDIDO_MINIMO'];
				$precioSinRecargo = $producto['IMPORTE_SIN_IVA'];
		
				$iva=$producto['TIPO_IVA'];
		
				if ($ilimitada=='0' || $ilimitada==NULL) {
					$cantidad_vendida = cantidadVendidaProducto($lote, $this->array_id_prod[$i]);
					$max = ($producto['CANTIDAD_1'] + $producto['CANTIDAD_2'] - $cantidad_vendida);
				} else {
					$max = 0;
				}
				$cuartos = $producto['INC_CUARTOS'];
		
				echo '<tr>';
				echo "<td>" . $this->array_nombre_prod[$i] . "</td>";
				echo "<td align=\"center\">" . $this->array_precio_prod[$i] . "  &euro;</td>";
				//echo "<td align=\"center\"><input type=\"text\" style=\"width: 25%; text-align:right;\" readonly=\"readonly\" onkeypress=\"return NumCheck(event, this)\"  value=\"" . $this->array_cantidad_prod[$i] . "\">";
				echo "<td align=\"center\">" . $this->array_cantidad_prod[$i];
				echo "</td>";
				echo "<td align=\"right\">" . round(($this->array_precio_prod[$i] * $this->array_cantidad_prod[$i]), 2) . " &euro;&nbsp;</td>";
		
				if (strpos($_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'])) {
					$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				} else {
					$url="http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
				}
		
				if ($this->array_transporte[$i] == 1) {
					echo "<td align=\"right\"></td>";
					echo "<td align=\"right\"></td>";
					$precio_transporte = round(($this->array_precio_prod[$i] * $this->array_cantidad_prod[$i]), 2);
				} else {
					if ($ilimitada || $max>0) {
						echo "<td align=\"right\">";
						echo "<a href=\"#\"><img alt=\"Modificar\" title=\"Modificar\" onclick=\"addCesta('".$this->array_id_prod[$i]."', '".htmlspecialchars($this->array_nombre_prod[$i], ENT_COMPAT, 'UTF-8')."', '".$this->array_precio_prod[$i]."', '$unidad', '".htmlspecialchars($descMedida, ENT_COMPAT, 'UTF-8')."', '$max', '$ilimitada',  '$cuartos', '$minimo');\" src=\"../img/EDITAR.png\" height=\"25\" width=\"25\" style=\"float: left;\" /></a>";
						echo "</td>";
					}
					echo "<td align=\"right\"><a class=\"imgcomprar\" href='../includes/eliminar_producto.php?linea=$i&url=$url'><img alt=\"Eliminar\" title=\"Eliminar\" src=\"../img/SACAR.png\" height=\"25\" width=\"25\" style=\"float: left;\" /></a></td>";
				}
				echo '</tr>';
		
				$PVP = round(($this->array_precio_prod[$i] * $this->array_cantidad_prod[$i]), 2);
				$suma += $PVP;
		
				//$precioSinIVA = round(($PVP - ($PVP * $iva / 100)), 2);
				$precioConRecargoSinIVA = round(((100 *$PVP) / (100 + $iva) ), 4);
				//$precioConRecargoSinIVA = calculaPrecioConRecargo ($this->array_id_prod[$i], $precioSinRecargo);
				//$precioConRecargoSinIVA = round($precioConRecargoSinIVA * $this->array_cantidad_prod[$i], 2);
				$sumaSinIVA += $precioConRecargoSinIVA;
		
				if ($tieneRE) {
					if ($iva == 4) {
						$re = 0.5; // el recargo de equivaencia para el tipo de iva 4% es un 0,5% adicional
					} else {
						$re = 1.4; // el recargo para el resto de tipos es 1,4% adicional
					}
					$reRev =  round(($precioConRecargoSinIVA * $re / 100), 2);
		
					$reFinal = $reFinal + $reRev;
					$suma += $reRev;
				}
		
				// Mostrar descuento aplicado
				if (calculaDescuentoRecargoUsuario() > 0 && $this->array_transporte[$i] != 1) {
					$precioSinDesc = calculaPVPSinDescuento($this->array_id_prod[$i], $precioSinRecargo, $iva);
					$ahorro = round(($precioSinDesc * $this->array_cantidad_prod[$i]), 2) - round(($this->array_precio_prod[$i] * $this->array_cantidad_prod[$i]), 2);
					$ahorroTotal += $ahorro;
					//echo '<tr><td align="right" style="text-align:right" colspan="3"><b>Descuento Aplicado:</b></td><td align="right">'.$ahorro,' &euro;</td><td colspan="2">&nbsp;</td></tr>';
				}
			}
		}
		//muestro el total
		if ($this->num_productos ()>0) {
			echo "<tr><td colspan=\"6\">&nbsp;</td></tr><tr>";
			echo "<td align=\"right\" colspan=\"3\" style=\"text-align:right\"><b>Subtotal:</b></td><td align=\"right\"><b><span id=\"total\">&nbsp;&nbsp;&nbsp;&nbsp;".round(($sumaSinIVA+$ahorroTotal),2)."&nbsp;</span> &euro;</b></td><td colspan=\"2\">&nbsp;</td></tr>";
			if (calculaDescuentoRecargoUsuario() > 0) {
				echo "<tr><td align=\"right\" colspan=\"3\" style=\"text-align:right\"><b>En esta cantidad total CerroViejo le ha aplicado un descuento de:</b></td><td align=\"right\"><b><span id=\"total\">&nbsp;&nbsp;&nbsp;&nbsp;-$ahorroTotal&nbsp;</span> &euro;</b></td><td colspan=\"2\">&nbsp;</td></tr>";
			}
			echo "<td align=\"right\" colspan=\"3\" style=\"text-align:right\"><b>De esta cantidad total se abona de IVA:</b></td><td align=\"right\"><b><span id=\"total\">&nbsp;&nbsp;&nbsp;&nbsp;+".round(($suma-$sumaSinIVA-$reFinal),2)."&nbsp;</span> &euro;</b></td><td colspan=\"2\">&nbsp;</td></tr>";
			if ($tieneRE) {
				echo "<td align=\"right\" colspan=\"3\" style=\"text-align:right\"><b>R.E.:</b></td><td align=\"right\"><b><span id=\"total\">&nbsp;&nbsp;&nbsp;&nbsp;+$reFinal&nbsp;</span> &euro;</b></td><td colspan=\"2\">&nbsp;</td></tr>";
			}
			echo "<td align=\"right\" colspan=\"3\" style=\"text-align:right\"><b>Total:</b></td><td align=\"right\"><b><span id=\"total\">&nbsp;&nbsp;&nbsp;&nbsp;$suma&nbsp;</span> &euro;</b></td><td colspan=\"2\">&nbsp;</td></tr>";
		} else {
			if (numero_filas($resComprados)==0) {
				echo "<tr><td align=\"right\" colspan=\"6\"><b>No hay productos en su cesta</b></td></tr>";
			}
		}
		$_SESSION['SUMA_CESTA'] = $suma;
		return ($suma-$precio_transporte);
	}
	
	/** Elimina un producto del carrito. recibe la linea del carrito que debe eliminar
	  * no lo elimina realmente, simplemente pone a cero el id, para saber que esta en estado retirado*/
	function elimina_producto($linea){
		$this->array_id_prod[$linea]=-1;
		
		if ($this->num_productos()==1 && $this->obtiene_producto_transporte()!=NULL) {
			$this->elimina_producto_transporte();
		}
	}
	
	/**
	 * Devuelve un indicador de numero de productos en la cesta
	 */
	function num_productos () {
		$num = 0;
			
		for ($i=0; $i< ($this->num_productos); $i++) {
			if($this->array_id_prod[$i]!=-1) {
				$num ++;
			}
		}
		
		return $num;
	}
	
	/**
	 * Confirma el pedido realizado y devuelve un mensaje 
	 */
	function confirmar_pedido ($comentarios, $horaIni, $horaFin) {
		$error = 0; //variable para detectar error
		global $cabeceras, $produccion;
		
		try {
			$fechaApertura = consultarFechaApertura();
			$fechaCierre = consultarFechaCierre();
			$lote = consultarLoteActual();
			
			$transporte = FALSE;
			for ($i=0; $i< ($this->num_productos); $i++) {
				if($this->array_id_prod[$i]!=-1 && $this->array_transporte[$i] == 1) {
					$transporte = TRUE;
				}
			}
			
			if ($transporte == FALSE) {
				return 'Es obligatorio introducir algún tipo de envío';
			}
			
			$conexion = conectar();
			
			if ($conexion==FALSE) {
				$error = 1;
			}
			
			//Variables necesarias
			$usuario = $_SESSION['ID_USUARIO'];
			$nombreUsuario = $_SESSION['NOMBRE_COMPLETO'];
			$fechaActual = new DateTime();
			$correo = 'Hola '.$nombreUsuario.', 
					
Su pedido ha sido confirmado correctamente:
					
';
			
			//Comprobar si está abierto el plazo de compra			
			$abierto = FALSE;
			$nuevo = TRUE;
			
			if (isset($fechaApertura) && isset($fechaCierre)) {
				$fechaApertura = date_create_from_format("d/m/Y H:i", $fechaApertura);
				$fechaCierre = date_create_from_format("d/m/Y H:i", $fechaCierre);
			
				if ($fechaActual>=$fechaApertura && $fechaActual<=$fechaCierre) {
					$abierto = TRUE;
				}
			}
			
			if (!$error) {
				if (!$abierto) {
					$this->num_productos=0;
					$array_id_prod = NULL;
					$array_nombre_prod = NULL;
					$array_precio_prod = NULL;
					$array_cantidad_prod = NULL;
					$mensaje = 'El plazo de compra ha terminado';
				} else {
					$idPedido = NULL;
					//Obtener o insertar el nuevo pedido
					$filaPedido = mysql_query("select * from PEDIDOS WHERE LOTE='$lote' and ID_USUARIO='$usuario'", $conexion); 

					if (numero_filas($filaPedido)==0) {
						$descuento = calculaDescuentoRecargoUsuario ();
						
						//Consulta si tiene RE
						$resultRE = mysql_query("SELECT RECARGO_EQ from USUARIOS WHERE ID_USUARIO='$usuario'", $conexion);
						$filaRE = mysql_fetch_array($resultRE);
						$re = $filaRE['RECARGO_EQ'];
						mysql_free_result($resultRE);
						
						$resn = mysql_query("select MAX(NUM_ALBARAN_ANUAL) as NUM_ALBARAN from PEDIDOS where FECHA_PEDIDO like '".$fechaActual->format("Y")."-%' AND NUM_ALBARAN_ANUAL is not null", $conexion);
						$filan = extraer_registro($resn);
						$numAlbaran = $filan['NUM_ALBARAN'] + 1;
						
						$resPedido = mysql_query("INSERT INTO PEDIDOS(ID_USUARIO, LOTE, FECHA_PEDIDO, COMENTARIOS, HORA_INI, HORA_FIN, DESCUENTO_RECARGO, RE, NUM_ALBARAN_ANUAL) VALUES ('$usuario', '$lote', '".$fechaActual->format("Y-m-d H:i:s")."', '$comentarios', '$horaIni', '$horaFin', '$descuento', '$re', '$numAlbaran')", $conexion);
						
						if (!$resPedido) {
							$error = 1;
						} else {
							$idPedido = mysql_insert_id($conexion);
						}
					} else {
						$nuevo = false;
						$filaPedido = mysql_fetch_array($filaPedido);
						$idPedido = $filaPedido['ID_PEDIDO'];
						$estado = $filaPedido['ESTADO'];
						$comentarios = $filaPedido['COMENTARIOS'].'<br/>'.$comentarios;
						
						if ($estado == 'FINALIZADO') {
							mysql_query("ROLLBACK", $conexion);
							return 'No se puede modificar un pedido finalizado.';
						}
						
						$resPedido = mysql_query("UPDATE PEDIDOS SET COMENTARIOS='$comentarios', HORA_INI='$horaIni', HORA_FIN='$horaFin' WHERE ID_PEDIDO='$idPedido'", $conexion);
						
						if (!$resPedido) {
							$error = 1;
							return 'No se puede ha podido modificar el pedido.';
						} 
						
						// Borrar transporte anterior
						$resPedido = mysql_query("DELETE PP.* FROM PEDIDOS_PRODUCTOS PP INNER JOIN PRODUCTOS P ON PP.ID_PRODUCTO = P.ID_PRODUCTO AND PP.ID_PEDIDO='$idPedido' AND P.ID_SUBCATEGORIA = '-10'", $conexion);
						
						if (!$resPedido) {
							$error = 1;
							return 'No se puede ha podido eliminar el transporte del pedido.';
						}
					}
					
					if (!$error) {
						$total = 0;
						
						for ($i=0; ($i< ($this->num_productos) && !$error); $i++) {
							if($this->array_id_prod[$i]!=-1) {
								$id_prod= $this->array_id_prod[$i];
								$cantidad_prod = $this->array_cantidad_prod[$i];
								$precio_prod = $this->array_precio_prod[$i];
								$subtotal = round(($cantidad_prod * $precio_prod), 2);
								$total = $total + $subtotal;
								$nombreProd = $this->array_nombre_prod[$i];

								$correo = $correo.'- '.$nombreProd.' ('.$cantidad_prod.' a '.$precio_prod.' €) = '.$subtotal.' €
';
								
								//Obtiene el peso por unidad y la subcategoria, en su caso
								$resDeProd = mysql_query("select PRECIO, PESO_POR_UNIDAD, ID_SUBCATEGORIA, PROVEEDOR_1, PROVEEDOR_2, CANTIDAD_1, CANTIDAD_2, CANTIDAD_ILIMITADA, TIPO_IVA, IMPORTE_SIN_IVA from PRODUCTOS where ID_PRODUCTO='$id_prod'", $conexion);
								$resDeProd = mysql_fetch_array($resDeProd);
								$subcat = $resDeProd['ID_SUBCATEGORIA'];
								$prov1 = $resDeProd['PROVEEDOR_1'];
								$prov2 = $resDeProd['PROVEEDOR_2'];
								$cant1 = $resDeProd['CANTIDAD_1'];
								$cant2 = $resDeProd['CANTIDAD_2'];
								$cantIlimitada = $resDeProd['CANTIDAD_ILIMITADA'];
								$iva = $resDeProd['TIPO_IVA'];
								$importeSinIVA = $resDeProd['IMPORTE_SIN_IVA'];
								$resCantKg = $resDeProd['PESO_POR_UNIDAD'];
								
								$precio_sin_recargo = $resDeProd['PRECIO']; 
								
								// Comprobación de que no se sobrepase las existencias
								if ($cantIlimitada=='0' || $cantIlimitada==NULL) {
									$cantidad_vendida = cantidadVendidaProductoConexion($lote, $id_prod, $conexion);
									$max = ($cant1 + $cant2 - $cantidad_vendida);
									
									if ($cantidad_prod>$max) { //Se ha sobrepasado el limite maximo de unidades
										mysql_query("ROLLBACK", $conexion);
										return 'No hay suficiente existencias para '.$nombreProd.', ajuste su cesta, solo quedan: '.$max.' unidades';
									}
								}						
								
								
								if ($subcat=='-10') { //Transporte el coste sin recargo es 0
									$precio_sin_recargo = 0;
								} /*else {
									$recargo_actual = calculaRecargoProductoConexion($id_prod, $conexion);
									$precio_sin_recargo = $precio_prod;
									if ($recargo_actual && $recargo_actual>0.01) {
										//$precio_sin_recargo = round(($precio_prod - ($precio_prod * $recargo_actual / 100)), 2);
										$precio_sin_recargo = round(($precio_prod * 100 / (100 + $recargo_actual)), 2); // ES ASI
									} 
								}*/
								
								
								//Consultar si existe el producto en el pedido
								$resPedidoProd = mysql_query("SELECT * FROM PEDIDOS_PRODUCTOS WHERE ID_PEDIDO='$idPedido' and ID_PRODUCTO='$id_prod'", $conexion);
								
								if (numero_filas($resPedidoProd)>0) { //Actualizar
									
									//ACTUALIZAR VALORES, NO SE ACTUALIZA EL SALDO
									$nuevo = FALSE;
									$resPedidoProd = mysql_query("UPDATE PEDIDOS_PRODUCTOS set CANTIDAD = '$cantidad_prod' WHERE ID_PEDIDO='$idPedido' and ID_PRODUCTO='$id_prod'", $conexion);
									
								} else {// Insertar Producto al Pedido
									$resPedidoProd = mysql_query("INSERT INTO PEDIDOS_PRODUCTOS
											(ID_PEDIDO, ID_PRODUCTO, CANTIDAD, PRECIO, PESO_POR_UNIDAD, PRECIO_SIN_RECARGO, PROVEEDOR_1, PROVEEDOR_2, CANTIDAD_1, CANTIDAD_2, CANTIDAD_ILIMITADA, TIPO_IVA, IMPORTE_SIN_IVA) 
											VALUES ('$idPedido', '$id_prod', '$cantidad_prod', '$precio_prod', '$resCantKg', '$precio_sin_recargo', '$prov1', '$prov2', '$cant1', '$cant2', '$cantIlimitada', '$iva', '$importeSinIVA')", $conexion);
								}

								if (!$resPedidoProd) {
									$error = 1;
									break;
								}
							}
						}
						
						$correo = $correo.'
Total = '.$total.' €';
								
						//ACTUALIZAR SALDO TOTAL - NO SE ACTUALIZA HASTA QUE NO SE CONFIRME -
						/*$resActSaldo = mysql_query("UPDATE USUARIOS SET SALDO = SALDO - $total WHERE ID_USUARIO='$usuario'", $conexion);
							
						if (!$resActSaldo) {
							$error = 1;
						}*/
					}
					
					if($error) {
						mysql_query("ROLLBACK", $conexion);
						$mensaje = 'Se ha producido un ERROR al confirmar el pedido';
					} else {
						$this->num_productos=0;
						$array_id_prod = NULL;
						$array_nombre_prod = NULL;
						$array_precio_prod = NULL;
						$array_cantidad_prod = NULL;
						
						mysql_query("COMMIT", $conexion);
						if ($nuevo) {
							$mensaje = 'Pedido CONFIRMADO correctamente. Revíselo en la opción: Mis Pedidos';
							if ($produccion==0) {
								mail ( 'jmtalavera@gmail.com', '[DESARROLLO] Pedido Web CERROVIEJO Lote '.$lote , $correo, $cabeceras);
							} else {
								mail ( $_SESSION['CORREO_USUARIO'], 'Pedido Web CERROVIEJO Lote '.$lote , $correo, $cabeceras);
							}
						} else {
							$mensaje = 'Pedido ACTUALIZADO correctamente. Revíselo en la opción: Mis Pedidos';
							if ($produccion==0) {
								mail ( 'jmtalavera@gmail.com', '[DESARROLLO] Pedido Web CERROVIEJO Lote '.$lote , $correo, $cabeceras);
							} else {
								mail ( $_SESSION['CORREO_USUARIO'], 'Pedido Web CERROVIEJO Lote '.$lote , $correo, $cabeceras);
							}
						}
						
					}
				}
			} else {
				$mensaje = 'Error de conexión';
			}
   		} catch (Exception $ex) {
   			mysql_query("ROLLBACK", $conexion);
   			//Devuelve el mensaje de error
   			$mensaje = $ex->qetMessage();
   		}
   		
   		return $mensaje;
	}
	
	// Repite el ultimo pedido realizado
	function repetir_ult_compra () {
   		$error = 0; //variable para detectar error
		
		try {
			$lote = consultarLoteActual();
			$usuario = $_SESSION['ID_USUARIO'];
			$conexion = conectar();
			if ($conexion==FALSE) {
				$error = 1;
			}
			
			$filaPedido = mysql_query("select ID_PEDIDO, FECHA_PEDIDO from PEDIDOS WHERE ID_USUARIO='$usuario' order by FECHA_PEDIDO DESC", $conexion); 
			if (!$filaPedido) {
				$error = 1;
			}
			
			if (numero_filas($filaPedido)>0) {
				$filaPedido = mysql_fetch_array($filaPedido);
				$idPedido = $filaPedido['ID_PEDIDO'];
				
				$filaPedido = mysql_query("select P.*, PP.CANTIDAD AS C1, PP.CANTIDAD_REVISADA AS C2 from PEDIDOS_PRODUCTOS PP, PRODUCTOS P WHERE PP.ID_PEDIDO='$idPedido' and P.ID_SUBCATEGORIA<>'-10' and PP.ID_PRODUCTO=P.ID_PRODUCTO and P.ACTIVO='1'", $conexion); 
				if (!$filaPedido) {
					$error = 1;
				}
				while ($fila = mysql_fetch_array($filaPedido)) {
					$id_prod = $fila['ID_PRODUCTO'];
					$nombre_prod = $fila['DESCRIPCION'];
					
					//$precio_prod = $fila['PRECIO'];
					//$precio_prod = calculaPrecioConRecargo($id_prod, $precio_prod);
					$precio_prod = $fila['IMPORTE_SIN_IVA'];
					$iva = $fila['TIPO_IVA'];
					$precio_prod = calculaPVP ($id_prod, $precio_prod, $iva);
					
					$cantidad_prod = $fila['C1'];
					if ($cantidad_prod==NULL) {
						$cantidad_prod = $fila['C2'];
					}
					
					$ilimitada = $fila['CANTIDAD_ILIMITADA'];
					if ($ilimitada=='0' || $ilimitada==NULL) {
						$cantidad_vendida = cantidadVendidaProducto($lote, $id_prod);
						$max = ($fila['CANTIDAD_1'] + $fila['CANTIDAD_2'] - $cantidad_vendida);
					} else {
						$max = 0;
					}
					
					$_SESSION["ocarrito"]->introduce_producto($id_prod,$nombre_prod,$precio_prod,$cantidad_prod, $max, $ilimitada);
				}
			}
			
		} catch (Exception $ex) {
   			@mysql_query("ROLLBACK", $conexion);
   			//Devuelve el mensaje de error
   			$mensaje = $ex->qetMessage();
   		}
	}
	
	// Repite el ultimo pedido realizado, pero solo los productos no servidos
	function repetir_ult_compra_no_servidos () {
		$error = 0; //variable para detectar error
	
		try {
			$lote = consultarLoteActual();
			$usuario = $_SESSION['ID_USUARIO'];
			$conexion = conectar();
			if ($conexion==FALSE) {
				$error = 1;
			}
				
			$filaPedido = mysql_query("select ID_PEDIDO, FECHA_PEDIDO from PEDIDOS WHERE ID_USUARIO='$usuario' order by FECHA_PEDIDO DESC", $conexion);
			if (!$filaPedido) {
				$error = 1;
			}
				
			if (numero_filas($filaPedido)>0) {
				$filaPedido = mysql_fetch_array($filaPedido);
				$idPedido = $filaPedido['ID_PEDIDO'];
	
				$filaPedido = mysql_query("select P.*, PP.CANTIDAD AS C1, PP.CANTIDAD_REVISADA AS C2 from PEDIDOS_PRODUCTOS PP, PRODUCTOS P WHERE PP.ID_PEDIDO='$idPedido' and P.ID_SUBCATEGORIA<>'-10' and PP.ID_PRODUCTO=P.ID_PRODUCTO and PP.CANTIDAD_REVISADA=0 and P.ACTIVO='1'", $conexion);
				if (!$filaPedido) {
					$error = 1;
				}
				while ($fila = mysql_fetch_array($filaPedido)) {
					$id_prod = $fila['ID_PRODUCTO'];
					$nombre_prod = $fila['DESCRIPCION'];
						
					//$precio_prod = $fila['PRECIO'];
					//$precio_prod = calculaPrecioConRecargo($id_prod, $precio_prod);
					$precio_prod = $fila['IMPORTE_SIN_IVA'];
					$iva = $fila['TIPO_IVA'];
					$precio_prod = calculaPVP ($id_prod, $precio_prod, $iva);
						
					$cantidad_prod = $fila['C1'];
					if ($cantidad_prod==NULL) {
						$cantidad_prod = $fila['C2'];
					}
						
					$ilimitada = $fila['CANTIDAD_ILIMITADA'];
					if ($ilimitada=='0' || $ilimitada==NULL) {
						$cantidad_vendida = cantidadVendidaProducto($lote, $id_prod);
						$max = ($fila['CANTIDAD_1'] + $fila['CANTIDAD_2'] - $cantidad_vendida);
					} else {
						$max = 0;
					}
						
					$_SESSION["ocarrito"]->introduce_producto($id_prod,$nombre_prod,$precio_prod,$cantidad_prod, $max, $ilimitada);
				}
			}
				
		} catch (Exception $ex) {
			@mysql_query("ROLLBACK", $conexion);
			//Devuelve el mensaje de error
			$mensaje = $ex->qetMessage();
		}
	}
	
	// Añade los productos de una lista a la cesta del usuario
	function repetir_lista($idLista) {
		$error = 0; //variable para detectar error
	
		try {
			$lote = consultarLoteActual();
			$usuario = $_SESSION['ID_USUARIO'];
			$conexion = conectar();
			if ($conexion==FALSE) {
				$error = 1;
			}
				
			$filaPedido = mysql_query("select ID_LISTA from LISTAS WHERE ID_USUARIO='$usuario' and ID_LISTA='$idLista'", $conexion);
			if (!$filaPedido) {
				$error = 1;
			}
				
			if (numero_filas($filaPedido)>0) {
				$filaPedido = mysql_fetch_array($filaPedido);
				$idLista = $filaPedido['ID_LISTA'];
	
				$filaPedido = mysql_query("select P.*, PP.CANTIDAD AS C1 from LISTAS_PRODUCTOS PP, PRODUCTOS P WHERE PP.ID_LISTA='$idLista' and P.ID_SUBCATEGORIA<>'-10' and PP.ID_PRODUCTO=P.ID_PRODUCTO and P.ACTIVO='1'", $conexion);
				if (!$filaPedido) {
					$error = 1;
				}
				while ($fila = mysql_fetch_array($filaPedido)) {
					$id_prod = $fila['ID_PRODUCTO'];
					$nombre_prod = $fila['DESCRIPCION'];

					$precio_prod = $fila['IMPORTE_SIN_IVA'];
					$iva = $fila['TIPO_IVA'];
					$precio_prod = calculaPVP ($id_prod, $precio_prod, $iva);
						
					$cantidad_prod = $fila['C1'];
						
					$ilimitada = $fila['CANTIDAD_ILIMITADA'];
					if ($ilimitada=='0' || $ilimitada==NULL) {
						$cantidad_vendida = cantidadVendidaProducto($lote, $id_prod);
						$max = ($fila['CANTIDAD_1'] + $fila['CANTIDAD_2'] - $cantidad_vendida);
					} else {
						$max = 0;
					}
						
					$_SESSION["ocarrito"]->introduce_producto($id_prod,$nombre_prod,$precio_prod,$cantidad_prod, $max, $ilimitada);
				}
			}
				
		} catch (Exception $ex) {
			@mysql_query("ROLLBACK", $conexion);
			//Devuelve el mensaje de error
			$mensaje = $ex->qetMessage();
			$error = 1;
		}
		
		return $error;
	}
	
	// Permite obtener el envío preseleccionado
	function envio_pedido_realizado($total_cesta) {
		$penvio = NULL;
		
		$usuario = $_SESSION['ID_USUARIO'];
		$lote = consultarLoteActual();
		$resComprados = consulta("select PP.ID_PRODUCTO, P.HORA_INI, P.HORA_FIN
				from PEDIDOS_PRODUCTOS PP, PEDIDOS P, PRODUCTOS PR
				where PP.ID_PEDIDO=P.ID_PEDIDO AND PP.ID_PRODUCTO=PR.ID_PRODUCTO
				AND P.ID_USUARIO='$usuario' and P.LOTE='$lote' AND PR.ID_SUBCATEGORIA='-10' ");
		
		if (numero_filas($resComprados)>0) {
			$fila = mysql_fetch_array($resComprados);
			$transporte = $_SESSION["ocarrito"]->obtiene_producto_transporte ();
			if ($transporte==NULL) {
				$penvio[0] = $fila['ID_PRODUCTO'];
				if ($_SESSION["ocarrito"]->num_productos ()>0) {
					$_SESSION["ocarrito"]->elimina_producto_transporte ();
					$_SESSION["ocarrito"]->introduce_producto_transporte ($penvio[0], $total_cesta);
				}
			} else {
				$penvio[0] = $transporte;
			}
			$penvio[1] = $fila['HORA_INI'];
			$penvio[2] = $fila['HORA_FIN'];
		}
		
		return $penvio;
	}
} 
//inicio la sesión
@session_start();
//si no esta creado el objeto carrito en la sesion, lo creo
if (!isset($_SESSION["ocarrito"])){
	$_SESSION["ocarrito"] = new carrito();
}

?>
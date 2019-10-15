<?php require_once "../includes/lib_carrito.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head_interno.inc.php"; ?>
	<script type="text/javascript" src="../js/print.js"></script>
	<script type="text/javascript">
		function generarAlbaranFactura(idPedido, tipo) {
			window.open('imprimirFacturaPedido.php?idPedido='+idPedido+'&tipo='+tipo, 'impresionFactura', '');
		}
	</script>
</head>
<body>
	 <?php require_once "../template/cabeceraUsuarios.inc.php"; ?>
	 <div class="wrapper">
		<div class="grids top">
		<?php require_once "../template/menuLateralUsuarios.inc.php"; 
		
		if (isset($_GET['idPedido'])) {
			$idPedido = $_GET['idPedido'];
			$usuario = $_SESSION['ID_USUARIO'];
			$pedidoProductos = consulta("select distinct PE.*, P.DESCUENTO_RECARGO, P.ESTADO, P.COBRADO, P.RE, PR.DESCRIPCION, U.DESCRIPCION AS DESC_MEDIDA from PEDIDOS P, PEDIDOS_PRODUCTOS PE, PRODUCTOS PR, UNIDADES U 
					where P.id_PEDIDO=PE.ID_PEDIDO and PE.id_producto= PR.id_producto and PR.UNIDAD_MEDIDA=U.ID_UNIDAD and P.id_usuario='$usuario' and PE.id_pedido='$idPedido'");
			$lotePed = consulta("select P.* from PEDIDOS P where P.id_pedido='$idPedido'");
			$lotePed = @extraer_registro($lotePed);
			$lotePed = @$lotePed['LOTE'];
		} else {
			$_SESSION['mensaje_generico'] = 'Pedido no encontrado';
			header("Location: mispedidos.php");
		}
	?>
			<h1 class="cal">Productos del Pedido</h1>
			<h5><?=@$_SESSION['NOMBRE_COMPLETO']?> (LOTE <?=$lotePed?>)</h5>
				<table class="tablaResultados">
					<thead>
						<tr>
							<th>Producto</th>
							<th align="center">Precio</th>
							<th align="center">Cantidad</th>
							<th align="center">Subtotal</th>
							<th align="center">Cantidad Revisada</th>
							<th align="center">Subtotal Revisado</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($pedidoProductos)==0) {
						
?>
					<tr>
						<td colspan="6">No hay productos en el pedido</td>
					</tr>
<?php 
					} else {
						$total = 0;
						$totalRevisado = 0;
						$ahorroTotal = 0;
						$ahorro = 0;
						$tieneRE = FALSE; 
						$subtotalRevisadoSinRecargoRevisado = 0;
						$totalSinRecargo = 0;
						$ivaTotal = 0;
						$recargoTotal = 0;
						
						while ($filaP = extraer_registro($pedidoProductos)) {
							$idPedidoActual = $filaP['ID_PEDIDO'];
							$estado = $filaP['ESTADO'];
							$cobrado = $filaP['COBRADO'];
							$idProducto = $filaP['ID_PRODUCTO'];
							
							$cantidad = $filaP['CANTIDAD'];
							$cantidadRevisada = $filaP['CANTIDAD_REVISADA'];
							$subtotal = round(($filaP['PRECIO'] * $cantidad), 2);
							$tieneRE = $filaP['RE'] == '1';
							
							$precioBase = $filaP['IMPORTE_SIN_IVA'];
							$tipoIVA = $filaP['TIPO_IVA'];
							$subtotalRevisadoSinRecargo = round(($precioBase * $cantidad), 2);
							
								
							$productoMedida = $filaP['DESC_MEDIDA'];
							$productoMedidaRevisado = $filaP['DESC_MEDIDA'];
							$peso_por_unidad = $filaP['PESO_POR_UNIDAD'];
							if (isset($peso_por_unidad) && $peso_por_unidad>0) {
								$productoMedida = $productoMedida." ($peso_por_unidad Kg)";
								$productoMedidaRevisado = 'Kg';
							}
								
							if (isset($cantidadRevisada) && $cantidadRevisada>=0) {
								if (isset($peso_por_unidad) && $peso_por_unidad>0) {
									$precio_por_kg = $filaP['PRECIO'] / $peso_por_unidad;
									$subtotalRevisado = round(($precio_por_kg * $cantidadRevisada), 2);
									
									$precio_por_kg_sin_recargo = $precioBase / $peso_por_unidad;
									$subtotalRevisadoSinRecargoRevisado = round(($precio_por_kg_sin_recargo * $cantidadRevisada), 2);
								} else {
									$subtotalRevisado = round(($filaP['PRECIO'] * $cantidadRevisada), 2);
									
									$subtotalRevisadoSinRecargoRevisado = round(($precioBase * $cantidadRevisada), 2); 
								}
							} else {
								if (isset($peso_por_unidad) && $peso_por_unidad>0) {
									$precio_por_kg = $filaP['PRECIO'] / $peso_por_unidad;
									$cantidadRevisada = round(($cantidad * $peso_por_unidad), 2);
									$subtotalRevisado = round(($precio_por_kg * $cantidadRevisada), 2);
									
									$precio_por_kg_sin_recargo = $precioBase / $peso_por_unidad;
									$subtotalRevisadoSinRecargoRevisado = round(($precio_por_kg_sin_recargo * $cantidadRevisada), 2);
								} else {
									$cantidadRevisada = $cantidad;
									$subtotalRevisado = round(($filaP['PRECIO'] * $cantidadRevisada), 2);
									
									$subtotalRevisadoSinRecargoRevisado = round(($precioBase * $cantidadRevisada), 2);
								}
							}
							
							// Calcular el ahorro según descuento en el recargo del pedido
							$descuento = $filaP['DESCUENTO_RECARGO'];
							if ($descuento > 0) {
								if ($filaP['PRECIO_SIN_RECARGO'] > 0) {
									$precioConRecargo = $filaP['PRECIO']; 
									$porcentajeRecargoPagado = round((($precioConRecargo - $filaP['PRECIO_SIN_RECARGO']) * 100 / $filaP['PRECIO_SIN_RECARGO']), 2);
									// Calculamos el porcentaje no pagado
									$porcentajeAhorro = round(($porcentajeRecargoPagado * 100 / (100-$descuento)), 2) - $porcentajeRecargoPagado;
									
									// Calculamos el ahorro por unidad, según el porcentaje ahorrado
									$ahorroUnidad = round (($filaP['PRECIO_SIN_RECARGO'] * $porcentajeAhorro / 100), 2);
									$ahorro = round(($ahorroUnidad * $cantidadRevisada), 2);
									$ahorroTotal += $ahorro;
								}
							}
								
							$total += $subtotal;
							$totalRevisado += $subtotalRevisado;
							$totalSinRecargo += $subtotalRevisadoSinRecargoRevisado;
							$ivaAplicado = round(($subtotalRevisadoSinRecargoRevisado * $tipoIVA /100), 2);
							$ivaTotal += $ivaAplicado;
							$recargoTotal += $subtotalRevisado - ($subtotalRevisadoSinRecargoRevisado + $ivaAplicado);
?>
					<tr>
						<td><?=$filaP['DESCRIPCION']?></td>
						<td align="center" nowrap><?=$filaP['PRECIO']?> &euro; IVA <?=number_format($tipoIVA, 0)?>%</td>
						<td align="center"><?=round($cantidad, 2)?> <?=$productoMedida?></td>
						<td align="center" nowrap>
							<?=round(($filaP['PRECIO'] * $cantidad), 2)?> &euro;
						</td>
						<td align="center"><?=round($cantidadRevisada, 2)?> <?=$productoMedidaRevisado?></td>
						<td align="center"  style="white-space: nowrap"><?=number_format($subtotalRevisado, 2, '.', '')?> &euro;</td>
					</tr>
						<?php
						}
						?>
					<tr>
						<td align="right" style="text-align: right;" colspan="5"><b>Total:</b></td>
						<td style="white-space: nowrap"><b><span id="total">&nbsp;&nbsp;&nbsp;&nbsp;<?=number_format($totalRevisado, 2)?></span> &euro;</b></td>
					</tr>	
						<?php 
						if ($descuento > 0) {
							echo "<tr><td align=\"right\" colspan=\"5\" style=\"text-align:right\"><b>En esta cantidad total CerroViejo le ha aplicado un descuento de:</b></td><td align=\"left\"><b><span id=\"total\">&nbsp;&nbsp;&nbsp;&nbsp;$ahorroTotal&nbsp;</span> &euro;</b></td></tr>";
						}
						
						if ($recargoTotal>0) {
						?>
					<tr>
						<td align="right" style="text-align: right;"  colspan="5"><b>De esta cantidad total CerroViejo recibe como Aportación al Reparto:</b></td>
						<td style="white-space: nowrap"><b><span id="totalRE">&nbsp;&nbsp;&nbsp;&nbsp;<?=number_format($recargoTotal, 2)?></span> &euro;</b></td>
					</tr>
						<?php 
						}
						
						if ($tieneRE) {
							$reTotal = calculaREPedido ($idPedido);
							$totalRevisado += $reTotal;
						?>
					<tr>
						<td align="right" style="text-align: right;"  colspan="5"><b>De esta cantidad total se abona de R.E.:</b></td>
						<td style="white-space: nowrap"><b><span id="totalRE">&nbsp;&nbsp;&nbsp;&nbsp;<?=number_format($reTotal, 2)?></span> &euro;</b></td>
					</tr>
						<?php 
						}
						
						if ($ivaTotal>0) {
						?>
					<tr>
						<td align="right" style="text-align: right;"  colspan="5"><b>De esta cantidad total se abona de IVA:</b></td>
						<td style="white-space: nowrap"><b><span id="totalIVA">&nbsp;&nbsp;&nbsp;&nbsp;<?=number_format($ivaTotal, 2)?></span> &euro;</b></td>
					</tr>
						<?php 
						}
						?>
					<tr>
						<td colspan="6">&nbsp;</td>
					</tr>
					<?php 
					}
					?>
				</tbody>
			</table>
			
			<div id="botonera" style="margin-right: 11%">
 <!--
				<input id="albaranp" name="albaranp" type="button" value="Albarán de pedido" onclick="generarAlbaranFactura('<?=$idPedido?>', 1);" />
				<?php 
				$consulta = "select ESTADO, COBRADO from PEDIDOS where ID_USUARIO='".@$_SESSION['ID_USUARIO']."' and ID_PEDIDO='$idPedido' order by fecha_pedido desc";
					
				$pedidos = consulta($consulta);
				$filaPed = extraer_registro($pedidos);
				$estado = $filaPed['ESTADO'];
				$cobrado = $filaPed['COBRADO'] == '1';
				
				if ($estado=='FINALIZADO') { ?>
				<input id="albarane" name="albarane" type="button" value="Albarán de entrega" onclick="generarAlbaranFactura('<?=$idPedido?>', 2);" />
				<?php 
				} 
					$saldo = consulta("select VER_FACTURA from USUARIOS where ID_USUARIO='".@$_SESSION['ID_USUARIO']."'");
					$saldo = extraer_registro($saldo);
					$verFactura = $saldo['VER_FACTURA'];
					
					if ($cobrado=='1' && $estado=='FINALIZADO' && $verFactura) { ?>
				<input id="factura" name="factura" type="button" value="Factura" onclick="generarAlbaranFactura('<?=$idPedido?>', 3);" />
				<?php } ?>
-->
				<input id="Imprimir" name="Imprimir" type="button" value="Imprimir" onclick="$('#contenido').printArea();" />
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='mispedidos.php'" />
			</div>	
	
        	</div>
		</div>
	</div>
</div>

<?php require_once "../template/pie.inc.php";  ?>
</body>
</html>
	<section id="pf-content" class="page-break-after">
	<div id="pf-body" style="text-align: center; width: 90%; margin-left: 10%">
	<?php		
		$consultaPedido = consulta ("select P.*, U.* from PEDIDOS P, USUARIOS U where P.ID_USUARIO=U.ID_USUARIO and P.ID_PEDIDO='$idPedido'");
		
		if ($consultaPedido && numero_filas($consultaPedido)==1) {
			$pedido = extraer_registro($consultaPedido);
			
			$cobrado = $pedido['COBRADO']=='1' && ( ($pedido['ESTADO']=='FINALIZADO' && !isset($tipo)) || ($tipo=='3') );
			
			if ($pedido['ESTADO']!='FINALIZADO' || $tipo=='1') {
				$descAlb = ' DE PEDIDO';
				$fechaPedido = new DateTime($pedido['FECHA_PEDIDO']);
			} else {
				$descAlb = ' DE ENTREGA';
				$fechaPedido = new DateTime($pedido['FECHA_ENTREGA']);
				if (!isset($fechaPedido) || $fechaPedido==NULL) {
					$fechaPedido = new DateTime($pedido['FECHA_PEDIDO']);
				}
			}
			
			$recargoEq = $pedido['RECARGO_EQ']=='1';
			
			$lote = $pedido['LOTE'];
			$nombreCompleto = @$pedido['NOMBRE']." ".@$pedido['APELLIDOS'];
			$usuario = @$pedido['ID_USUARIO'];
			$nif = $pedido['NIF'];
			$numAlbaranAnual = $pedido['NUM_ALBARAN_ANUAL'];
			$numFacturaAnual = $pedido['NUM_FACTURA_ANUAL'];
			
			$direccion = '';
			$direccion = @$pedido['DIRECCION'].', '.@$pedido['CODIGO_POSTAL'].' '.@$pedido['POBLACION'].', '.@$pedido['PROVINCIA'];
			
			// Obtener y/o calcular número albaran y factura en su caso
			//$numAlbaran = obtenerNumeroAlbaran ($idPedido);
			$numAlbaran = $numAlbaranAnual; //obtenerNumAlbaranPedidoAnual ($idPedido, $fechaPedido->format("Y"));
			$numAlbaranLote = obtenerNumAlbaranPedido($idPedido, $lote);
			$numAlbaranPedido = obtenerNumAlbaranPedido($idPedido, $lote);
			/*if ($pedido['ESTADO']!='FINALIZADO' || $tipo=='1') {
				$numAlbaranLote = obtenerNumAlbaranPedido($idPedido, $lote);
			} else if ($numAlbaranLote<1) {
				$numAlbaranLote = obtenerNumeroAlbaranAnterior($idPedido);
			}*/
			
			$tipoFactura = 'A';
			if (@$pedido['ID_USUARIO']=='ADMIN') {
			    $tipoFactura = 'V';
			}
			
			if ($cobrado) {
				//$numFactura = obtenerNumeroFactura ($lote, $idPedido);
				$numFactura = @$pedido['NUM_FACTURA'];
				if ($numAlbaran<1) {
					$numFactura = obtenerNumeroFactura ($idPedido);
				}
				$fechaFactura = new DateTime($pedido['FECHA_FACTURA']);
				/*if (!isset($fechaFactura) || $fechaFactura==NULL) {
					$fechaActual = new DateTime();
					consulta("update PEDIDOS FECHA_FACTURA='".$fechaActual->format("Y-m-d H:i:s")."' where ID_PEDIDO='$idPedido'");
				}*/
			}
	    	
	    	$telefonos = (@$pedido['TFNO_CONTACTO']==0?'':@$pedido['TFNO_CONTACTO']).(@$pedido['TFNO_MOVIL']==0?'':(@$pedido['TFNO_CONTACTO']==0?'':' / ').$pedido['TFNO_MOVIL']);
	    	$esRE = $pedido['RECARGO_EQ'] == '1' ? 'Sí' : 'No';
	    	$descuento_recargo = $pedido['DESCUENTO_RECARGO'];
	    	$ahorro = 0;
	?>
	<table style="width:100%">
		<tbody>
		<tr>
			<td width="35%" rowspan="<?=(($cobrado) ? '5' : (($tipo=='2') ? '4' : '3'))?>"><img id="logoInterno" alt="logo" src="http://www.cerroviejo.org/img/cerroViejo.png"/></td>
			<td width="5%" rowspan="<?=(($cobrado) ? '5' : (($tipo=='2') ? '4' : '3'))?>" nowrap="nowrap">&nbsp;&nbsp;&nbsp;</td>
			<?php if ($cobrado) { ?>
			<td width="25%" align="right" class="text-node"><b>Nº FACTURA:&nbsp;</b></td>
			<td width="35%" align="left" style="font-weight: normal;"><?=@$tipoFactura?><?=str_pad($numFacturaAnual/*obtenerNumFacturaAnual($idPedido, @$fechaFactura->format('Y'))*/, 4, "0", STR_PAD_LEFT)?>/<?=@$fechaFactura->format('Y')?></td>
			<?php } else { ?>
				<td width="25%" align="right" class="text-node"><b>Nº ALBARÁN:&nbsp;</b></td>
				<td width="35%" align="left" style="font-weight: normal;"><?=str_pad($numAlbaran, 5, "0", STR_PAD_LEFT)?>/<?=@$fechaPedido->format('Y')?></td>
			<?php } ?>
		</tr>
		<?php if ($cobrado) { ?>
		<tr>
			<td align="right" class="text-node"><b>FECHA FACTURA:&nbsp;</b></td>
			<td align="left" style="font-weight: normal;"><?=$fechaFactura->format('d/m/Y')?></td>
		</tr>
		<tr>
			<td align="right" class="text-node"><b>Nº ALBARÁN:&nbsp;</b></td>
			<td align="left" style="font-weight: normal;"><?=str_pad($numAlbaran, 5, "0", STR_PAD_LEFT)?>/<?=@$fechaPedido->format('Y')?></td>
		</tr>
		<?php } ?>
		<tr>
			<td align="right" class="text-node"><b>FECHA <?=($cobrado ? 'ALBARÁN' : $descAlb)?>:&nbsp;</b></td>
			<td align="left" style="font-weight: normal;"><?=@$fechaPedido->format('d/m/Y')?></td>
		</tr>
		<?php if ($tipo<>'1' && $tipo<>'3' && ($pedido['ESTADO']=='FINALIZADO' && !$cobrado)) { ?>
		<!-- 
		<tr>
			<td align="right" class="text-node"><b>Nº ALBARÁN DE PEDIDO:&nbsp;</b></td>
			<td align="left" style="font-weight: normal;">P-<?=@$fechaPedido->format('Y')?>-L<?=str_pad($lote, 3, "0", STR_PAD_LEFT)?>-<?=str_pad($numAlbaranPedido, 4, "0", STR_PAD_LEFT)?>-<?=$usuario?></td>
		</tr>
		 -->
		<tr>
			<td align="right" class="text-node"><b>FECHA DE PEDIDO:&nbsp;</b></td>
			<td align="left" style="font-weight: normal;"><?=(new DateTime($pedido['FECHA_PEDIDO']))->format('d/m/Y')?></td>
		</tr>
		<?php } ?>
		<tr>
			<td align="right" class="text-node"><b>LOTEADO:&nbsp;</b></td>
			<td align="left" style="font-weight: normal;"><?=($cobrado ? 'F' : ( ($pedido['ESTADO']!='FINALIZADO' || $tipo=='1') ? 'P' : 'A'))?>-<?=@$fechaPedido->format('Y')?>-L<?=str_pad($lote, 3, "0", STR_PAD_LEFT)?>-<?=str_pad(($cobrado ? $numFactura : $numAlbaranLote), 4, "0", STR_PAD_LEFT)?>-<?=$usuario?></td>
		</tr>
		<tr>
			<td align="left" style="font-weight: normal;" rowspan="5" class="text-node">
				ALIMENTACIÓN CERRO VIEJO<br/>
				ALVARO MIGUEL FERNÁNDEZ-BLANCO BARRETO<br/>
				C/ANTONIO DELGADO Nº 4, 1º DERECHA<br/>
				41005 SEVILLA<br/>
				NIF: 48963416-G
			</td>
			<td rowspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td align="right" class="text-node"><b>NOMBRE:&nbsp;</b></td>
			<td align="left" style="font-weight: normal;"><?=$nombreCompleto?></td>
		</tr>
		<tr>
			<td align="right" style="vertical-align: top;" class="text-node"><b>DIRECCIÓN:&nbsp;</b></td>
			<td align="left" style="font-weight: normal;"><?=$direccion?></td>
		</tr>
		<tr>
			<td align="right" style="vertical-align: top;" class="text-node"><b>CIF/NIF:&nbsp;</b></td>
			<td align="left" style="font-weight: normal;"><?=$nif?></td>
		</tr>
		<tr>
			<td align="right" style="vertical-align: top;" class="text-node"><b>TELÉFONO/S:&nbsp;</b></td>
			<td align="left" style="font-weight: normal;"><?=$telefonos?></td>
		</tr>
		<tr>
			<td colspan="3" align="right" style="vertical-align: top;" class="text-node"><b>R.E. (Recargo de Equivalencia):&nbsp;</b></td>
			<td align="left" style="font-weight: normal;"><?=$esRE?></td>
		</tr>
		</tbody>
    </table>
	</div>
    <br/>
	<div class="listadoProductos">
		<?php 
			$consulta2 = "SELECT PP.*, PR.DESCRIPCION, U.DESCRIPCION AS DESC_UNIDAD, PR.INC_CUARTOS
								FROM PEDIDOS_PRODUCTOS PP, PRODUCTOS PR, UNIDADES U
								WHERE PP.ID_PRODUCTO = PR.ID_PRODUCTO
								and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
								AND PP.ID_PEDIDO=$idPedido";
			$resProductos = consulta($consulta2);
		?>
		<table class="tablaResultados" style="width: 100%">
			<thead>
				<tr>
					<th align="left" class="text-node">ID.</th>
					<th align="left" class="text-node">PRODUCTO</th>
					<th align="center" class="text-node"><?=($recargoEq ? 'P.V.C.' : 'P.V.S')?></th>
					<th align="center" class="text-node">TIPO IVA</th>  
					<?php if ($recargoEq) { ?>
					<th align="center" class="text-node">R.E.</th>
					<?php } ?>
					<th align="center" class="text-node">CANTIDAD</th>
					<th align="center" class="text-node">IMPORTE</th>
					<th align="center" class="text-node">SUBTOTAL</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$indice=1;
					$total = 0.00;
					$totalRevisado = 0.00;
					$totalesFacturaPorIVA = array();
					$totalRevisadoSinIVA = 0.00;
					
					$numProductos = numero_filas($resProductos);
					
					if ($numProductos==0) {
					?>
						<tr><td colspan="3">No hay productos en <?=($cobrado ? 'la factura' : 'el albarán')?></td></tr>
						<?php 
					}
					 
					while ($producto = extraer_registro($resProductos)) {
						$idPedidoActual = $producto['ID_PEDIDO'];
						$idProducto = $producto['ID_PRODUCTO'];
						$cantidad = $producto['CANTIDAD'];
						$cantidadRevisada = $producto['CANTIDAD_REVISADA'];
						
						if ($cantidad==0.0) {
							$cantidad = $cantidadRevisada;
						}
						
						$productoMedida = $producto['DESC_UNIDAD'];
						$productoMedidaRevisado = $producto['DESC_UNIDAD'];
						$peso_por_unidad = $producto['PESO_POR_UNIDAD'];
						if (isset($peso_por_unidad) && $peso_por_unidad>0) {
							//$productoMedida = $productoMedida." ($peso_por_unidad Kg)";
							$productoMedida = "Kg (en $productoMedida $peso_por_unidad Kg)";
							$productoMedidaRevisado = 'Kg';
						}
						
						// IMPORTES GUARDADOS BASE Y FINAL
						$precioSinRecargoNiIVA = $producto['IMPORTE_SIN_IVA'];
						$tipoIVA = $producto['TIPO_IVA'];
						$precioConIVA = $producto['PRECIO'];

						//CALCULAR IMPORTES SIN IVA
						$precioConRecargoSinIVA = ((100 * $precioConIVA) / (100 + $tipoIVA));
						$ivaProductoFinal = $precioConIVA - $precioConRecargoSinIVA;
						//$recargoPorUnidad = $precioConRecargoSinIVA - $precioSinRecargoNiIVA; //NO SE USA

						$precioConRecargoSinIVATotal = $precioConRecargoSinIVA;

						//Calcular el PVC por unidad con el descuento 
						$PVC = calcularPVC ($precioConIVA, $precioConRecargoSinIVA, $tipoIVA, $peso_por_unidad, $recargoEq);
						
						// Cantidad NO revisada si no finalizado el pedido y si el tipo es 2 o 3
						if (!(isset($cantidadRevisada) && $cantidadRevisada>=0 && (($pedido['ESTADO']=='FINALIZADO' && $tipo!='1') || $tipo=='3'))) { 
							$cantidadRevisada = $cantidad;
							
							if (isset($peso_por_unidad) && $peso_por_unidad>0) {
								$cantidadRevisada = $cantidadRevisada * $peso_por_unidad; //Cantidad en KGs
							}
						}
						
						if (isset($peso_por_unidad) && $peso_por_unidad>0) {
							$precio_por_kg = $precioConIVA / $peso_por_unidad;
							$precio_por_kg_sin_iva = $precioConRecargoSinIVA / $peso_por_unidad;
						
							$subtotalRevisado = round(($precio_por_kg * $cantidadRevisada), 2);
							$precioConRecargoSinIVA =round($precio_por_kg_sin_iva, 2);
							$precioConRecargoSinIVATotal = round(($precioConRecargoSinIVA * $cantidadRevisada), 2);
						} else {
							$subtotalRevisado = round(($precioConIVA * $cantidadRevisada), 2);
							$precioConRecargoSinIVATotal = round(($precioConRecargoSinIVA * $cantidadRevisada), 2);
						}
						
						//Calculo el subtotal con recargo y sin iva
						//$precioConRecargoSinIVATotal = round(((100 * $subtotalRevisado) / (100 + $tipoIVA)), 2);
						
						//Calculo iva producto como el resto entre el total y el total sin iva
						$ivaProductoFinal = $subtotalRevisado - $precioConRecargoSinIVATotal;
						
						// Añado al total acumulado y revisado, sin iva
						if (!isset($totalRevisadoSinIVA)) {
							$totalRevisadoSinIVA = 0;
						}
						$totalRevisadoSinIVA += $precioConRecargoSinIVATotal;
						
						// Calculo totales factura por IVA
						if ($recargoEq) { //Se calcula sobre el importe sin RECARGO
							if ($tipoIVA == 4) {
								$re = 0.5; // el recargo de equivaencia para el tipo de iva 4% es un 0,5% adicional
							} else {
								$re = 1.4; // el recargo para el resto de tipos es 1,4% adicional
							}
							$reRev =  round(($precioConRecargoSinIVATotal * $re / 100), 2);
						} else {
							$reRev = 0.0;
							$re = 0.0;
						}
						$importeIVAE = $subtotalRevisado + $reRev;
						
						if (isset($totalesFacturaPorIVA[strval($tipoIVA)]) && $totalesFacturaPorIVA[strval($tipoIVA)]!=NULL) {
							$valor1 = $totalesFacturaPorIVA[strval($tipoIVA)][0];
							$valor2 = $totalesFacturaPorIVA[strval($tipoIVA)][1];
							$valor3 = $totalesFacturaPorIVA[strval($tipoIVA)][2];
							$valor4 = $totalesFacturaPorIVA[strval($tipoIVA)][3];
							$totalesFacturaPorIVA[strval($tipoIVA)] = array ($valor1+$precioConRecargoSinIVATotal, $valor2+$ivaProductoFinal, $valor3+$reRev, $valor4+$importeIVAE, $re);
						} else {
							$totalesFacturaPorIVA[strval($tipoIVA)] = array ($precioConRecargoSinIVATotal, $ivaProductoFinal, $reRev, $importeIVAE, $re);
						}
						
						if ($descuento_recargo > 0) {
							if ($producto['PRECIO_SIN_RECARGO'] > 0) {
								$precioConRecargo = $producto['PRECIO'];
								$porcentajeRecargoPagado = round((($precioConRecargo - $producto['PRECIO_SIN_RECARGO']) * 100 / $producto['PRECIO_SIN_RECARGO']), 2);
								// Calculamos el porcentaje no pagado
								$porcentajeAhorro = round(($porcentajeRecargoPagado * 100 / (100-$descuento_recargo)), 2) - $porcentajeRecargoPagado;
								// Calculamos el ahorro por unidad, según el porcentaje ahorrado
								$ahorroUnidad = round (($producto['PRECIO_SIN_RECARGO'] * $porcentajeAhorro / 100), 2);
								$ahorro += round(($ahorroUnidad * $cantidadRevisada), 2);	
							}
						}
					?>
					<tr>
						<td align="center"><?=$idProducto?></td>
						<td align="left"><?=$producto['DESCRIPCION']?> / <?=$productoMedida?></td>
						<td align="right" nowrap="nowrap"><?=number_format(($PVC), 2, '.', '')?> &euro;</td>
						<td align="right"><?=round($tipoIVA)?>%</td>
						<?php if ($recargoEq) { ?>
							<td align="right"><?=$re?>%</td>
						<?php } ?>
						<td align="left"><?=round($cantidadRevisada, 2)?> <?=$productoMedidaRevisado?></td>
						<td align="right"><?=number_format($precioConRecargoSinIVA, 2, '.', '')?> &euro;</td>  
						<td align="right"><?=number_format($precioConRecargoSinIVATotal, 2, '.', '')?> &euro;</td>
					</tr>
				<?php } ?>
					<tr>
						<td align="right">&nbsp;</td>
						<td align="left"><span class="text-node"><b>&nbsp;SUBTOTAL:</b></span></td>
						<td align="right">&nbsp;</td>
						<?php if ($recargoEq) { ?>
							<td align="right">&nbsp;</td>
						<?php } ?>
						<td align="right">&nbsp;</td>  
						<td align="right">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="right"><span class="text-node"><b>&nbsp;<?=number_format($totalRevisadoSinIVA, 2, '.', '')?> &euro;</b></span></td>
					</tr>
				<?php /*if ($recargoEq) {*/ ?>
					<tr>
						<td colspan="6" style="border: 0px">&nbsp;</td>
					</tr>
					<tr>
						<th style="border: 0px" align="right">&nbsp;</th>
						<?php if ($recargoEq) { ?>
						<th style="border: 0px">&nbsp;</th>
						<?php } ?>
						<th class="text-node" align="right">B.I.&nbsp;&nbsp;</th>
						<th class="text-node" align="right">TIPO IVA</th>
						<th class="text-node" align="right">IVA</th>
						<th class="text-node" align="right">TIPO R.E.</th>
						<th class="text-node" align="right">R.E.</th>
						<th class="text-node" align="right">IMPORTE</th>
					</tr>
				<?php 
					ksort($totalesFacturaPorIVA);
					$totalesRE = 0.0;
					foreach ($totalesFacturaPorIVA as $key => $val) { 
						$totalesRE = $totalesRE + $val[3];
				?>
					<tr>
						<td style="border: 0px" align="right">&nbsp;</td>
						<?php if ($recargoEq) { ?>
						<td style="border: 0px">&nbsp;</td>
						<?php } ?>
						<td align="right" nowrap="nowrap"><?=number_format($val[0], 2, '.', '')?> &euro;</td>
						<td align="right" nowrap="nowrap"><?=number_format($key, 0)?>%</td>
						<td align="right" nowrap="nowrap"><?=number_format($val[1], 2, '.', '')?> &euro;</td>
						<td align="right" nowrap="nowrap"><?=$val[4]?>%</td>
						<td align="right" nowrap="nowrap"><?=number_format($val[2], 2, '.', '')?> &euro;</td>
						<td align="right" nowrap="nowrap"><?=number_format($val[3], 2, '.', '')?> &euro;</td>
					</tr>
				<?php } ?>
					<tr>
						<td>&nbsp;</td>
					<?php if ($recargoEq) { ?>
						<td>&nbsp;</td>
					<?php } ?>
						<td align="left" colspan="4"><span class="text-node"><b>&nbsp;IMPORTE TOTAL <?=($cobrado ? 'FACTURA' : 'ALBARÁN'.$descAlb)?>:</b></span></td> 
						<td align="right">&nbsp;</td>
						<td align="right"><span class="text-node"><b><?=number_format($totalesRE, 2, '.', '')?> &euro;</b></span></td>
					</tr>
					<tr>
						<td style="border: 0px" align="right">&nbsp;</td>
						<?php if ($recargoEq) { ?>
						<td style="border: 0px">&nbsp;</td>
						<?php } ?>
						<td colspan="6" align="left"><span class="text-node">Nº CCC: 3187-0604-29-2840023119 Caja Rural del Sur</span>
						<?php if ($descuento_recargo>0)  {?><br/><span class="text-node">Descuento especial para usted del <?=$descuento_recargo?>% sobre la Aportación al Reparto.<br/>En esta cantidad total CerroViejo le ha aplicado un descuento de: <?=$ahorro?> €.</span><?php } ?>
						</td>
					</tr>
				<?php /* } */?>
			</tbody>
		</table>
		
		<br/>
	</div>
<?php } ?>
	</section>
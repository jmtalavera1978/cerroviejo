<?php
		$lote = consultarLoteActual();
		if (isset($_GET['lote'])) {
			$lote = $_GET['lote'];
			$_SESSION['loteSel'] = $lote;
		} else if (isset($_SESSION['loteSel'])) {
			$lote = $_SESSION['loteSel'];
		}

		$consulta = "select P.*, PP.ID_PEDIDO_PROVEEDOR, PP.TOTAL_REVISADO, S.NOMBRE AS SUBGRUPO, PP.ENVIADO, PP.PEDIDO_PROCESADO from PROVEEDORES P, PEDIDOS_PROVEEDORES PP, SUBGRUPOS S
					WHERE P.ID_PROVEEDOR = PP.ID_PROVEEDOR
					AND PP.ID_SUBGRUPO = S.ID_SUBGRUPO
					AND PP.ID_PEDIDO_PROVEEDOR = '$idPedidoProv'
		UNION ALL 
			select P.*, PP.ID_PEDIDO_PROVEEDOR, PP.TOTAL_REVISADO, 'RESTO' AS SUBGRUPO, PP.ENVIADO, PP.PEDIDO_PROCESADO from PROVEEDORES P, PEDIDOS_PROVEEDORES PP
					WHERE P.ID_PROVEEDOR = PP.ID_PROVEEDOR
					AND (PP.ID_SUBGRUPO IS NULL OR PP.ID_SUBGRUPO=0)
					AND PP.ID_PEDIDO_PROVEEDOR = '$idPedidoProv'";

		$proveedores = consulta($consulta);
	
		while ($proveedor = extraer_registro($proveedores)) {
			$idProveedor = $proveedor['ID_PROVEEDOR'];
			$idPedidoProveedor = $proveedor['ID_PEDIDO_PROVEEDOR'];
			$subgrupo = $proveedor['SUBGRUPO'];
			$nombreProveedor = $proveedor['NOMBRE'];
			$descProveedor = $proveedor['DESCRIPCION'];
			$totalRevisado = $proveedor['TOTAL_REVISADO'];
			$enviado = ($proveedor['ENVIADO'] == 1);
			
			if ($proveedor['PEDIDO_PROCESADO'] == 1) {
				$esConsulta = 'true';
			}
			?>
			
			<div id="tituloProveedores">
			<span>&nbsp;PROVEEDOR <?=$nombreProveedor?> (<?=$descProveedor?>)</span>
			</div>
			<div id="listadoProductos">
				<?php 
					$consulta2 = "SELECT PP.*, PR.DESCRIPCION, U.DESCRIPCION AS DESC_UNIDAD, PR.INC_CUARTOS, PR.IMPORTE_SIN_IVA, PR.TIPO_IVA
									FROM PEDIDOS_PROVEEDORES_PROD PP, PRODUCTOS PR, UNIDADES U
									WHERE PP.ID_PRODUCTO = PR.ID_PRODUCTO
									and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
									AND PP.ID_PEDIDO_PROVEEDOR=$idPedidoProveedor";
					$resProductos = consulta($consulta2);
				?>
				<div id="dialogConfirmElim" title="">
					¿Desea eliminar el producto indicado? <br/>
					Se eliminará el producto en el pedido a proveedores.
				</div>
					
				<script>
					var idProductoActual;	
					
					function openConfirmacion(idProducto) {
						idProductoActual = idProducto;
						$("#dialogConfirmElim").dialog("open");
					}
					
					$("#dialogConfirmElim").dialog({
				      autoOpen: false,
					  height: 250,
					  width: 400,
					  modal: true,
				      buttons : {
				          "Sí" : function() {
				        	  document.location = 'eliminar_producto_pedidos.php?idProducto='+idProductoActual+'&lote=<?=$lote?>&url=detallePedidosProv.php';
					          $(this).dialog("close");
				          },
				          "No" : function() {
					          $(this).dialog("close");
				          }
				        }
				    });
				</script>
				<table style="width: 100%">
				<tr><td width="80%">
				<table class="tablaResultados" style="width: 100%">
					<thead>
						<tr>
							<th>PRODUCTO</th>
							<th align="center">PSI (PVS)</th>
							<th align="center">CANTIDAD</th>
							<th align="center">A PEDIR</th>
							<th align="center">SUBTOTAL</th>
							<?php if ($esConsulta!='true') { ?>
							<th align="center">&nbsp;</th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
					<?php
						$total = 0.00;
						$totalRev = 0.00;
						$totalSinIva = 0.00;
						$totalSinIvaRev = 0.00;
						
						$numProductos = numero_filas($resProductos);
						
						if ($numProductos==0) {
						?>
							<tr><td colspan="4">No ha pedidos para este proveedor</td></tr>
							<?php 
						}
						 
						while ($producto = extraer_registro($resProductos)) {
							$cantidad = $producto['CANTIDAD'];
							$idProductoActual = $producto['ID_PRODUCTO'];
							$cantidad_rev = $producto['CANTIDAD_REV'];
							if ($cantidad_rev == NULL) {
								$cantidad_rev = $cantidad;
							}
							$IMP_SIN_IVA = $producto['IMPORTE_SIN_IVA'];
							$TIPO_IVA = $producto['TIPO_IVA'];
							//$PRECIO_CON_IVA = round(($IMP_SIN_IVA + ($IMP_SIN_IVA * $TIPO_IVA / 100)), 2);
							
							$PRECIO_CON_IVA = calculaPVP($idProductoActual, $IMP_SIN_IVA, $TIPO_IVA);
							$PRECIO_CON_IVA_SINREC = round(($IMP_SIN_IVA + ($IMP_SIN_IVA * $TIPO_IVA / 100)), 2);
							
							$subtotalSinIva = round(($IMP_SIN_IVA * $cantidad), 2);
							$totalSinIva += $subtotalSinIva;
							$subtotalSinIvaRev = round(($IMP_SIN_IVA * $cantidad_rev), 2);
							$totalSinIvaRev += $subtotalSinIvaRev;
							$subtotal = round(($PRECIO_CON_IVA_SINREC * $cantidad), 2);
							$total += $subtotal;
							$subtotalRev = round(($PRECIO_CON_IVA_SINREC * $cantidad_rev), 2);
							$totalRev +=$subtotalRev;
					?>
						<tr>
							<td><?=$producto['DESCRIPCION']?> (<?=$TIPO_IVA?>%)</td>
							<td align="center"><?=$IMP_SIN_IVA?>&euro; (<?=$PRECIO_CON_IVA?>&euro;)</td>
							<td align="center"><?=$cantidad?> <?=$producto['DESC_UNIDAD']?></td>
							<td align="right" nowrap id="indice<?=$idProductoActual?>">
							<?php if ($esConsulta=='true') { ?>
								<?=round($cantidad_rev, 2)?> <?=$producto['DESC_UNIDAD']?>
							<?php } else { ?>
								<input id="aPedir<?=$idProductoActual?>" type="text" style="width: 50px; text-align:right;" onkeypress="return NumCheck(event, this)" 
									onblur="document.location='modificar_cantidad_revisada_proveedores.php?idPedido=<?=$idPedidoProveedor?>&idProducto=<?=$idProductoActual?>&lote=<?=$lote?>&cantidad='+this.value+'&url=detallePedidosProv.php?idPedidoProv=<?=$idPedidoProv?>&consulta=<?=$esConsulta?>#indice<?=$idProductoActual?>'" 
									value="<?=round($cantidad_rev, 2)?>"/> <?=$producto['DESC_UNIDAD']?>
								<input type="button" style="font-size: 0.6rem; font-size: 0.6em; color: black" onclick="disminuir('aPedir<?=$idProductoActual?>', '<?=$idPedidoProveedor?>', '<?=$idProductoActual?>', '<?=$producto['DESC_UNIDAD'] == 'Kg' ? ($producto['INC_CUARTOS'] ? '0.25' : '0.5') : '1'?>', '<?=$lote?>')" class="miniboton" value="-" />
								<input type="button" style="font-size: 0.6rem; font-size: 0.6em; color: black" onclick="aumentar('aPedir<?=$idProductoActual?>', '<?=$idPedidoProveedor?>', '<?=$idProductoActual?>', '<?=$producto['DESC_UNIDAD'] == 'Kg' ? ($producto['INC_CUARTOS'] ? '0.25' : '0.5') : '1'?>', '<?=$lote?>')" class="miniboton" value="+" />
							<?php } ?>
							</td>
							<td align="center"><?=$subtotalSinIvaRev?> &euro;</td>
							<?php if ($esConsulta!='true') { ?>
								<td align="center"><a href="#" onclick="openConfirmacion('<?=$idProductoActual?>')"><img src="../img/BORRAR.png" alt="eliminar" width="32"/></a></td>
							<?php } ?>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				</td><td width="80%" valign="top" style="vertical-align: top;">
				<?php if ($numProductos>0) { ?>
				<table style="width: 100%" style="vertical-align: top;">
					<tr>
						<td>&nbsp;</td>
						<td valign="middle" align="right">
							<a href="imprimir_preveedor.php?idPedidoProv=<?=$idPedidoProv?>&consulta=true" target="_blank">
								<img alt="Imprimir" title="Imprimir" src="../img/impresora.png" />
							</a>
						</td>
					</tr>
					<tr>
						<td valign="middle" align="right" nowrap><b>Total:</b></td>
						<td valign="middle" align="right" nowrap><input id="total<?=$idProveedor?>" class="total" style="background-color: #459e00"  type="text" readonly="readonly" contenteditable="false" value="<?=number_format($totalSinIva, 2, '.', '')?>"/> &euro;</td>
					</tr>
					<tr>
						<td valign="middle" align="right"><b>Solicitado:<br/>(iva incl.)</b></td>
						<td valign="middle" align="right" nowrap><input id="totalRev" type="text" class="total" style="background-color: #459e00" size="8" contenteditable="true" value="<?=number_format($total, 2, '.', '')?>"/>  &euro;</td>
					</tr>
					<tr>
						<td valign="middle" align="right" nowrap><b>Total:<br/>(iva incl.)</b>
						<!-- 
							<?php if ($esConsulta!='true') { ?>
							<a href="#" onclick="document.location='modificar_cantidad_total_proveedor.php?idPedidoProv=<?=$idPedidoProv?>&consulta=<?=$esConsulta?>&cantidad=NULL&url='+document.location"><img src="../img/clear.gif" alt="actualizar" width="32"/></a>
							<?php } ?>
						 -->
						</td>
						<td valign="middle" align="right"><input id="total2<?=$idProveedor?>" class="total" style="background-color: white; color: black"  type="text" readonly="readonly" contenteditable="false" value="<?=number_format($totalRev, 2, '.', '')?>"/> &euro;</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
						<?php if ($esConsulta!='true') { ?>
							<input style="font-size: small; padding-left: 5px; padding-right: 5px; margin-bottom: 5px; width: 200px" id="imprimirAlbaran<?=$idProveedor?>" name="imprimirAlbaran<?=$idProveedor?>" type="button" onclick="window.open('imprimirAlbaran.php?idPedidoProv=<?=$idPedidoProv?>', 'impresionAlbaran', '');" value="Generar Albarán PDF" />
							<?php if (!$enviado) { ?>
								<input style="font-size: small; padding-left: 5px; padding-right: 5px; margin-bottom: 5px; width: 200px" id="enviado<?=$idProveedor?>" name="enviado<?=$idProveedor?>" type="button" onclick="if (confirm ('¿Desea Marcar como Enviado?')) document.location='pedidos_proveedor_marcar.php?idPedidoProv=<?=$idPedidoProv?>&consulta=<?=$esConsulta?>';" value="Marcar como Enviado" />
							<?php } ?>
							<input style="font-size: small; padding-left: 5px; padding-right: 5px; margin-bottom: 5px; width: 200px" type="button" onclick="openConfirmacionPedidoProveedor('<?=$idPedidoProv?>', '<?=$nombreProveedor?>')" value="Cerrar Pedido con Apunte"/>
						<?php } ?>
						</td>
					</tr>
				</table>
				<?php } ?>
				</td>
				</tr>
				</table>
				&nbsp;
				
				
				<div id="dialogConfirmPedidoProveedor" title="">
					¿Desea finalizar el pedido del proveedor? <br/>Se generará un apunte contable con la cantidad indicada en total revisado.</div>
				<script>
					var idPedidoProvActual;
					var nombreActual;
					
					$(function() {
					    $( "input[type=button]" )
					      .click(function( event ) {
					        event.preventDefault();
					      });
					  });
					  
					function openConfirmacionPedidoProveedor (idPedidoProv, nombre) {
						idPedidoProvActual = idPedidoProv;
						nombreActual = nombre;
						$("#dialogConfirmPedidoProveedor").dialog("open");
					}
					
					$("#dialogConfirmPedidoProveedor").dialog({
				      autoOpen: false,
					  height: 250,
					  width: 600,
					  modal: true,
				      buttons : {
				          "Sí" : function() {
				        	  finalizarPedidoProveedor (idPedidoProvActual, nombreActual);
					          $(this).dialog("close");
				          },
				          "No" : function() {
					          $(this).dialog("close");
				          }
				        }
				    });
				</script>
			</div>
			
			<?php 
		}
		echo "</div>";


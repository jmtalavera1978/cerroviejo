<?php
	$lote = consultarLoteActual();
	$idUsuario = NULL;
	if (isset($_GET['idUsuario'])) {
		$idUsuario = $_GET['idUsuario'];
		$_SESSION['idUsuarioSelPedUsu'] = $idUsuario;
	} else if (isset($_SESSION['idUsuarioSelPedUsu'])) {
		$idUsuario = $_SESSION['idUsuarioSelPedUsu'];
	}
	
	if (isset($_GET['lote'])) {
		$lote = $_GET['lote'];
		$_SESSION['loteSel'] = $lote;
	} else if (isset($_SESSION['loteSel'])) {
		$lote = $_SESSION['loteSel'];
	}
	
	if (isset($_GET['envio'])) {
		$envio = $_GET['envio'];
		$_SESSION['envioSel'] = $envio;
	} else if (isset($_SESSION['envioSel'])) {
		$envio = $_SESSION['envioSel'];
	}
	
	if ($lote && $idUsuario) {
		$resProductos = consulta("select P.ID_PEDIDO, P.RE, PP.*, PR.DESCRIPCION, U.DESCRIPCION AS MEDIDA, COD_CLASE_PRODUCTO, CP.DESCRIPCION as CLASE
				from PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR, UNIDADES U, CLASE_PRODUCTO CP
				where P.ID_PEDIDO=PP.ID_PEDIDO and PP.ID_PRODUCTO=PR.ID_PRODUCTO and PR.UNIDAD_MEDIDA=U.ID_UNIDAD AND PR.ID_CLASE_PRODUCTO=CP.ID_CLASE_PRODUCTO
				and P.ESTADO='PREPARACION' and P.LOTE='$lote' and P.ID_USUARIO='$idUsuario' order by CP.SECUENCIA, PR.ID_PROVEEDOR_1, PR.DESCRIPCION ");
	}
	
	$ordenHoras = @$_GET['ordenHoras'];
	if (isset($ordenHoras)) {
		if (strlen($ordenHoras)==0) {
			$_SESSION['ordenHoras'] = NULL;
			$ordenHoras = false;
		} else {
			$_SESSION['ordenHoras'] = $ordenHoras;
		}
	} else {
		$ordenHoras = @$_SESSION['ordenHoras'];
	}
	
	echo "<div style=\"position:relative; top: -20px\">";
	?>
	<div id="tituloProveedores">
	<span>&nbsp;Usuario:&nbsp;
		<select id="idUsuario" name="idUsuario" onchange="document.location='pedidos.php?idUsuario='+this.value">
			<option value="">Seleccione un usuario...</option>
			<?=optionsUsuariosConPedidos($idUsuario, $lote)?>
		</select>
		&nbsp;&nbsp;&nbsp;LOTE:&nbsp;
		<select id="lote" name="lote" onchange="document.location='pedidos.php?lote='+this.value">
			<option value="">Seleccione un lote...</option>
			<?=optionsLotes($lote)?>
		</select>
		<br/>
		&nbsp;<b>TIPO de ENV&Iacute;O:</b>&nbsp;
		<select id="transporte" name="transporte" onchange="document.location='pedidos.php?envio='+this.value">
			<option value="">Seleccione un tipo de env&iacute;o...</option>
			<?php 
				optionsTransporte ($envio);
			?>
		</select>
		&nbsp;<b>ORDENAR POR HORAS REPARTO:</b>&nbsp;
		<input type="checkbox" name="ordenHoras" id="ordenHoras" <?php if (@$ordenHoras=='true') { echo "checked=\"true\""; } ?> onchange="document.location='pedidos.php?ordenHoras='+this.checked" />
	</span>
	</div>
	<?php 
	
	if (isset($resProductos)) {
	
	if (numero_filas($resProductos)==0) {
		?>
			<br/>No hay pedidos con los criterios seleccionados.
		<?php 
	} else {
		$comentarios = @consulta("select P.COMENTARIOS from PEDIDOS P
								  where P.ESTADO='PREPARACION' and P.LOTE='$lote' 
								  and P.ID_USUARIO='$idUsuario' ");
		$comentarios = @extraer_registro($comentarios);
		$comentarios = @$comentarios['COMENTARIOS'];
		?>
				
		<div id="listadoProductos">
			<h1 style=" margin-top: 50px; margin-bottom: -40px;"><?=$idUsuario?> &nbsp;&nbsp;(LOTE <?=$lote?>)</h1>
				<br/>
				
			<table class="tablaResultados" style="width: 100%">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>PRODUCTO</th>
						<th>CLASE</th>
						<th align="center">PRECIO</th>
						<th align="center">CANTIDAD</th>
						<th align="center">SUBTOTAL</th>
						<th align="left">CANTIDAD FINAL</th>
						<th align="center">SUBTOTAL REVISADO</th>
						<th align="center">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$indice=1;
					$total = 0.00;
					$totalRevisado = 0.00;
					$saldoUsuarioRes = consulta("select SALDO from USUARIOS WHERE ID_USUARIO='$idUsuario'");
					$filaRes = extraer_registro($saldoUsuarioRes);
					$saldoUsuario = $filaRes['SALDO'];
					
					$tieneRE = FALSE;
					 
					while ($producto = extraer_registro($resProductos)) {
						$idPedidoActual = $producto['ID_PEDIDO'];
						$idProducto = $producto['ID_PRODUCTO'];
						$cantidad = $producto['CANTIDAD'];
						$cantidadRevisada = $producto['CANTIDAD_REVISADA'];
						$subtotal = round(($producto['PRECIO'] * $cantidad), 2);
						$tieneRE = $producto['RE'] == '1';
						
						$productoMedida = $producto['MEDIDA'];
						$productoMedidaRevisado = $producto['MEDIDA'];
						$peso_por_unidad = $producto['PESO_POR_UNIDAD'];
						if (isset($peso_por_unidad) && $peso_por_unidad>0) {
							$productoMedida = $productoMedida." ($peso_por_unidad Kg)";
							$productoMedidaRevisado = 'Kg';
						}
						
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
						
						$total += $subtotal;
						$totalRevisado += $subtotalRevisado;
				?>
					<tr id="fila<?=$idProducto?>" <?=($cantidadRevisada==0) ? 'style="background-color:#FFAAAA"' : (($producto['CHECK_FINALIZADO']=='1') ? 'style="background-color:#A2F461"' : ($producto['CHECK_REVISADO']=='1' ? 'style="background-color:#FFFF66"' : ''))?>>
						<td id="check2<?=$idProducto?>">
							<input type="checkbox" id="checkA<?=$idProducto?>" <?php if ($producto['CHECK_FINALIZADO']=='1') echo 'checked' ?> onmouseup="clickarFinalizado('<?=$idProducto?>', '<?=$idPedidoActual?>', '<?=$idProducto?>', '<?=$cantidadRevisada?>', !this.checked);"/>
						</td>
						<td id="check<?=$idProducto?>">
							<input type="checkbox" id="checkB<?=$idProducto?>" <?php if ($producto['CHECK_REVISADO']=='1') echo 'checked' ?> onmouseup="clickarRevisado('check<?=$idProducto?>', '<?=$idPedidoActual?>', '<?=$idProducto?>', '<?=$cantidadRevisada?>', !this.checked);"/>
						</td>
						<td><?=$producto['DESCRIPCION']?></td>
						<td title="<?=$producto['CLASE']?>"><?=$producto['COD_CLASE_PRODUCTO']?></td>
						<td align="center"><?=$producto['PRECIO']?> &euro;</td>
						<td align="center"><?=round($cantidad, 2)?> <?=$productoMedida?></td>
						<td align="center"><?=number_format($subtotal, 2, '.', '')?> &euro;</td>
						<td align="left" id="indice<?=$indice?>">
							<input type="text" style="width: 50%; text-align:right;"
								onfocus="this.value=''"
								onkeypress="return NumCheck(event, this)"
								onblur="modificarRevisado('indice<?=$indice?>', '<?=$idPedidoActual?>', '<?=$idProducto?>', '<?=(isset($peso_por_unidad) && $peso_por_unidad>0) ? $precio_por_kg : $producto['PRECIO']?>', '<?=$idUsuario?>', '<?=$productoMedidaRevisado?>',  this)" 
								value="<?=round($cantidadRevisada, 2)?>"/>
							<?=$productoMedidaRevisado?>
						</td>
						<td align="center" id="indice<?=$indice?>_total"><?=number_format($subtotalRevisado, 2, '.', '')?> &euro;</td>
						<td align="center"><a href="#" onclick="openConfirmacionElim1('<?=$idPedidoActual?>', '<?=$idProducto?>')"><img src="../img/BORRAR.png" alt="factura" width="32"/></a></td>
					</tr>
				<?php 
						$indice = $indice + 1;
					} 
					
					//SUMAMOS AL TOTAL EL R.E.
					if ($tieneRE) {
						$totalRE = calculaREPedido($idPedidoActual);
						$totalRevisado += $totalRE;
				?>
					<tr id="filaRE" >
						<td colspan="8" align="right" style="font-weight: bold;">R.E.:&nbsp;&nbsp;&nbsp;</td>
						<td align="center"><?=number_format($totalRE, 2, '.', '')?> &euro;</td>
						<td align="center">&nbsp;</td>
					</tr>
				<?php 
					}
				?>
				</tbody>
			</table>		
			
			<?php 
			if (isset($comentarios) && $comentarios!=NULL && $comentarios!='') {
				?>
				<br/>&nbsp;&nbsp;<span style="font-size:x-large; font-weight: bolder; text-decoration: underline;">Comentarios del usuario:</label></span>  <?=$comentarios?>
				<br/><br/>
				<?php 
			}
			?>	
			
			<div id="dialogConfirmElim" title="Eliminaci&oacute;n" style="display: none">
				¿Desea eliminar el producto indicado?</div>
				
			<script>
				var idPedidoActual;	 
				var idProductoActual;	
				
				function openConfirmacionElim1(idPedido, idProducto) {
					idPedidoActual = idPedido;
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
			        	  document.location = 'eliminar_pedido_producto.php?idPedido='+idPedidoActual+'&idProducto='+idProductoActual+'&url=pedidos.php';
				          $(this).dialog("close");
			          },
			          "No" : function() {
				          $(this).dialog("close");
			          }
			        }
			    });
			</script>
			
			<div style="clear: both;"></div>
			
			<div id="tablaTotales">
			<table style="width: 100%; float: right;">
				<tr>
					<td valign="middle" align="right" rowspan="6">
						<a href="#" onclick="$('#listadoProductos').printArea();">
							<img alt="Imprimir" title="Imprimir" src="../img/impresora.png" />
						</a>
					</td>
				</tr>
				<tr>
					<td valign="middle" align="right"><b>Total Comprado:</b></td>
					<td valign="middle" align="right">
						<input id="total" type="text" readonly="readonly" contenteditable="false" value="<?=number_format($total, 2, '.', '')?> &euro;" 
							style="width: 80%; background-color: silver; color: gray; border-color: silver; "/>
					</td>
					<td valign="middle" align="right"><b>Total Revisado:</b></td>
					<td valign="middle" align="right">
						<input id="total" type="text" readonly="readonly" contenteditable="false" value="<?=number_format($totalRevisado, 2, '.', '')?> &euro;"
							style="background-color: #c1d0a9; color: gray; border-color: #c1d0a9;" />
					</td>
					<td valign="middle" align="right"><b>Genera Saldo:</b></td>
					<td valign="middle" align="right">
						<input id="total" type="text" readonly="readonly" contenteditable="false" value="<?=number_format((0 - $totalRevisado), 2, '.', '')?> &euro;" 
							style="width: 80%; background-color: #52a411; color: white"/>
					</td>
				</tr>
				<tr>
					<td valign="middle" align="right" colspan="5"><b>Saldo del Usuario:</b></td>
					<td valign="middle" align="right">
						<input id="total" type="text" readonly="readonly" contenteditable="false" value="<?=number_format($saldoUsuario, 2, '.', '')?> &euro;"
							style="background-color: #c1d0a9; color: gray; border-color: #c1d0a9;" />
					</td>
				</tr>
				<tr>
					<td valign="middle" align="right" colspan="5"><b>Saldo final:</b></td>
					<td valign="middle" align="right">
						<input id="total" type="text" readonly="readonly" contenteditable="false" value="<?=number_format(($saldoUsuario - $totalRevisado), 2, '.', '')?> &euro;" 
							style="width: 80%; background-color: #52a411; color: white"/>
					</td>
				</tr>
			</table>
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<?php 
	echo "</div>";
	} 
} else {

$sqlu1 = "select distinct U.ID_USUARIO, U.NOMBRE, U.APELLIDOS, P.ID_PEDIDO, P.LOTE, P.FECHA_PEDIDO, P.ESTADO, P.COMENTARIOS, P.HORA_INI, P.HORA_FIN, P.RE, SU.NOMBRE AS SUBGRUPO, P.COBRADO, P.VERDE
	from USUARIOS U, PEDIDOS P, PEDIDOS_PRODUCTOS PP, SUBGRUPOS SU 
	WHERE U.ID_USUARIO=P.ID_USUARIO and TIPO_USUARIO='USUARIO' and U.ID_SUBGRUPO=SU.ID_SUBGRUPO and P.LOTE='$lote' and P.ID_PEDIDO=PP.ID_PEDIDO";
if (isset($envio) && $envio!='') {
	$sqlu1 .= " and PP.ID_PRODUCTO='$envio'";
}
if ($ordenHoras=='true') {
	$sqlu1 .= " order by P.HORA_INI";
} else {
	$sqlu1 .= " order by P.FECHA_PEDIDO";
}
$usuarios = consulta($sqlu1);

if (!isset($resProductos) && numero_filas($usuarios)>0) {
?>

<div id="listadoProductos">
	<table class="tablaResultados" style="width: 100%">
		<thead>
			<tr>
				<th title="Verde">Sel.</th>
				<th>PEDIDO</th>
				<th>NOMBRE COMPLETO</th>
				<th align="center">GRUPO</th>
				<th align="center">FECHA PEDIDO <?=((@$_SESSION['ordenHoras']==NULL || @$_SESSION['ordenHoras']=='false') ? '(asc)' : '')?></th>
				<th align="center">HORA REPARTO <?=((@$_SESSION['ordenHoras']!=NULL && @$_SESSION['ordenHoras']=='true') ? '(asc)' : '')?></th>
				<th align="center">ESTADO</th>
				<th align="right">IMPORTE</th>
				<th title="Facturar">&nbsp;</th>
				<th align="center">&nbsp;</th>
				<th align="center">&nbsp;</th>
				<th align="center">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$tieneRE = FALSE;
			
			while ($user = extraer_registro($usuarios)) {
				$tieneRE = $user['RE'] == '1';
				$idPedido = $user['ID_PEDIDO'];
				
				$date = new DateTime($user['FECHA_PEDIDO']);
				$comentarios = $user['COMENTARIOS'];
				
				//Calcular el importe del pedido del usuario actual
				$resProductos3 = consulta("select P.ID_PEDIDO, PP.*, PR.DESCRIPCION, U.DESCRIPCION AS MEDIDA from PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR, UNIDADES U
						where P.ID_PEDIDO=PP.ID_PEDIDO and PP.ID_PRODUCTO=PR.ID_PRODUCTO and PR.UNIDAD_MEDIDA=U.ID_UNIDAD
						and P.LOTE='$lote' and P.ID_USUARIO='".$user['ID_USUARIO']."'");
				$totalRevisado = 0;
				
				while ($producto = extraer_registro($resProductos3)) {
					$cantidad = $producto['CANTIDAD'];
					$cantidadRevisada = $producto['CANTIDAD_REVISADA'];
				
					$productoMedida = $producto['MEDIDA'];
					$productoMedidaRevisado = $producto['MEDIDA'];
					$peso_por_unidad = $producto['PESO_POR_UNIDAD'];
					if (isset($peso_por_unidad) && $peso_por_unidad>0) {
						$productoMedida = $productoMedida." ($peso_por_unidad Kg)";
						$productoMedidaRevisado = 'Kg';
					}
				
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
				
					$totalRevisado += $subtotalRevisado;
				}
				
				if ($tieneRE) {
					$totalRevisado += calculaREPedido($idPedido);
				}
				
		?>
			<tr title="<?=@$comentarios?>" id="filaCobrada<?=$user['ID_PEDIDO']?>" <?=($user['VERDE']=='1') ? 'style="background-color:#A2F461"' : (($user['COBRADO']=='1') ? 'style="background-color:#ddffff"' : '')?>>
				<td align="center" id="verde<?=$user['ID_PEDIDO']?>">
					<input type="checkbox" id="checkVerde<?=$user['ID_PEDIDO']?>" <?php if ($user['VERDE']=='1') echo 'checked' ?> onmouseup="clickarVerde('<?=$user['ID_PEDIDO']?>', !this.checked);"/>
				</td>
				<td align="center"><?=$user['ID_USUARIO']?>_LOTE<?=$user['LOTE']?></td>
				<td align="center"><?=$user['NOMBRE']?> <?=$user['APELLIDOS']?></td>
				<td align="center"><?=$user['SUBGRUPO']?></td>
				<td align="center"><?=$date->format('d/m/Y H:i:s')?></td>
				<td align="center"><?=$user['HORA_INI']?> - <?=$user['HORA_FIN']?></td>
				<td align="center"><?=$user['ESTADO']?></td>
				<td align="right"><?=$totalRevisado?> &euro;</td>
				<td align="center" id="cobrado<?=$user['ID_PEDIDO']?>">
					<?php if ($user['COBRADO']!='1') { ?> 
					<input type="button" id="checkCobrado<?=$user['ID_PEDIDO']?>" value="Fac." title="FACTURAR" onclick="openConfirmacionFacturar('<?=$user['ID_PEDIDO']?>')"/>
					<?php } ?>
				</td>
				<td align="center">
					<a title="Albarán de pedido" href="#" onclick="generarAlbaranFactura('<?=$user['ID_PEDIDO']?>', 1);"><img src="../img/ALBARAN.png" alt="informe" width="32"/></a>
					<?php if ($user['ESTADO']=='FINALIZADO') { ?>
					<a title="Albarán de envío" href="#" onclick="generarAlbaranFactura('<?=$user['ID_PEDIDO']?>', 2);"><img src="../img/INFORME.png" alt="informe" width="32"/></a>
					<?php } ?>
					<a id="factura<?=$user['ID_PEDIDO']?>" title="Factura" <?=(($user['COBRADO']=='1') ? 'style="display:block"' :  'style="display:none"' )?> 
						 href="#" onclick="generarAlbaranFactura('<?=$user['ID_PEDIDO']?>', 3);"><img src="../img/FACTURA.png" alt="factura" width="32"/></a>
				</td>
				<td align="center">
					<?php if ($user['ESTADO']=='PREPARACION') { ?>
					<a title="Detalle del Pedido" href='pedidos.php?idUsuario=<?=$user['ID_USUARIO']?>'><img src="../img/EDITAR.png" alt="informe" width="32"/></a>
					<?php } else { ?>
					<a title="Ver Pedido" href="verPedido.php?idPedido=<?=$user['ID_PEDIDO']?>"><img src="../img/INFO.png" alt="factura" width="32"/></a>
					<?php } ?>
				</td>
				<td align="center">
				<?php if ($user['ESTADO']=='PREPARACION') { ?>
					<a title="Eliminar Pedido Completo" href="#" onclick="openConfirmacion('<?=$user['ID_PEDIDO']?>');"><img src="../img/BORRAR.png" alt="informe" width="32"/></a>
				<?php } else { ?>
					&nbsp;
				<?php } ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	
	<div id="dialogConfirmFacturar" title="Facturación" style="display: none;">
		¿Desea generar factura para el pedido seleccionado?
	</div>
	<script>		
		var idPedidoSel;  
		function openConfirmacionFacturar(idPedido) {
			idPedidoSel = idPedido;
			$("#dialogConfirmFacturar").dialog("open");
		}
		
		$("#dialogConfirmFacturar").dialog({
	      autoOpen: false,
		  height: 250,
		  width: 400,
		  modal: true,
	      buttons : {
	          "Sí" : function() {
	        	  $(this).dialog("close");
	        	  clickarCobrado(idPedidoSel, 'true');
	          },
	          "Cancelar" : function() {
		          $(this).dialog("close");
	          }
	        }
	    });
	</script>
	
	<div id="dialogConfirmDelete" title="Eliminaci&oacute;n" style="display: none;">
		¿Desea eliminar el pedido seleccionado?</div>
		
	<script>		
		var idPedidoSel;  
		function openConfirmacion(idPedido) {
			idPedidoSel = idPedido;
			$("#dialogConfirmDelete").dialog("open");
		}
		
		$("#dialogConfirmDelete").dialog({
	      autoOpen: false,
		  height: 250,
		  width: 400,
		  modal: true,
	      buttons : {
	          "Sí" : function() {
	        	  $(this).dialog("close");
	        	  document.location='eliminarPedidoUsuario.php?idPedido='+idPedidoSel+'&url='+document.location;
	          },
	          "Cancelar" : function() {
		          $(this).dialog("close");
	          }
	        }
	    });
	</script>
	
	<div style="clear: both;"></div>
</div>
<?php }
} ?>
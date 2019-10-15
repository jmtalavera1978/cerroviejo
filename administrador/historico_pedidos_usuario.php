<?php
	$lote = consultarLoteActual();
	$idUsuario = NULL;
	if (isset($_GET['idUsuario'])) {
		$idUsuario = $_GET['idUsuario'];
		$_SESSION['idUsuarioSel'] = $idUsuario;
	} else if (isset($_SESSION['idUsuarioSel'])) {
		$idUsuario = $_SESSION['idUsuarioSel'];
	}
	if (isset($_GET['lote'])) {
		$lote = $_GET['lote'];
		$_SESSION['loteSel'] = $lote;
	} else if (isset($_SESSION['loteSel'])) {
		$lote = $_SESSION['loteSel'];
	}
	
	if ($lote && $idUsuario) {
		$resProductos = consulta("select P.ID_PEDIDO, P.RE, PP.*, PR.DESCRIPCION, U.DESCRIPCION AS MEDIDA from PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR, UNIDADES U 
				where P.ID_PEDIDO=PP.ID_PEDIDO and PP.ID_PRODUCTO=PR.ID_PRODUCTO and PR.UNIDAD_MEDIDA=U.ID_UNIDAD
				and P.LOTE='$lote' and P.ID_USUARIO='$idUsuario' order by PR.DESCRIPCION ");
	}
	
	echo "<div style=\"position:relative; top: -20px\">";
	?>
	<div id="tituloProveedores">
	<span>&nbsp;Usuario:&nbsp;
		<select id="idUsuario" name="idUsuario" onchange="document.location='pedidos.php?idUsuario='+this.value">
			<option value="">Seleccione un usuario...</option>
			<?=optionsUsuariosConPedidosHist($idUsuario, $lote)?>
		</select>
		&nbsp;&nbsp;&nbsp;LOTE:&nbsp;
		<select id="lote" name="lote" onchange="document.location='pedidos.php?lote='+this.value">
			<option value="">Seleccione un lote...</option>
			<?=optionsLotes($lote)?>
		</select>
	</span>
	</div>
	<?php 
	
	if (isset($resProductos)) {
	
	if (numero_filas($resProductos)==0) {
		?>
			<br/>No hay pedidos con los criterios seleccionados.
		<?php 
	} else {
		?>
		
		<div id="listadoProductos">
			<span><?=$idUsuario?>&nbsp;&nbsp;(LOTE <?=$lote?>)</span>
			<table class="tablaResultados" style="width: 100%">
				<thead>
					<tr>
						<th>PRODUCTO</th>
						<th align="center">PRECIO</th>
						<th align="center">CANTIDAD</th>
						<th align="center">SUBTOTAL</th>
						<th align="center">CANTIDAD FINAL</th>
						<th align="center">SUBTOTAL REVISADO<th>
					</tr>
				</thead>
				<tbody>
				<?php
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
						
						/*if (isset($cantidadRevisada) && $cantidadRevisada>=0) {
							$subtotalRevisado = round(($producto['PRECIO'] * $cantidadRevisada), 2);
						} else {
							$cantidadRevisada = $cantidad;
							$subtotalRevisado = round(($producto['PRECIO'] * $cantidad), 2);
						}*/
						$total += $subtotal;
						$totalRevisado += $subtotalRevisado;
				?>
					<tr>
						<td><?=$producto['DESCRIPCION']?></td>
						<td align="center"><?=$producto['PRECIO']?> &euro;</td>
						<td align="center"><?=round($cantidad, 2)?> <?=$productoMedida?></td>
						<td align="center"><?=number_format($subtotal, 2, '.', '')?> &euro;</td>
						<td align="center">
							<?=round($cantidadRevisada, 2)?>
							<?=$productoMedidaRevisado?>
						</td>
						<td align="center"><?=number_format($subtotalRevisado, 2, '.', '')?> &euro;</td>
					</tr>
				<?php } //SUMAMOS AL TOTAL EL R.E.
					if ($tieneRE) {
						$totalRE = calculaREPedido($idPedidoActual);
						$totalRevisado += $totalRE;
				?>
					<tr id="filaRE" >
						<td colspan="5" align="right" style="font-weight: bold;">R.E.:&nbsp;&nbsp;&nbsp;</td>
						<td align="center"><?=number_format($totalRE, 2, '.', '')?> &euro;</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			
			<div style="clear: both;"></div>
			
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
				</tr>
			</table>
			
			<div style="clear: both;"></div>
		</div>
		
		<?php 
	echo "</div>";
	} 
} 

$usuarios = consulta("select distinct U.ID_USUARIO, U.NOMBRE, U.APELLIDOS, P.ID_PEDIDO, P.LOTE, P.FECHA_PEDIDO, P.ESTADO, P.COBRADO, P.VERDE
	from USUARIOS U, PEDIDOS P WHERE U.ID_USUARIO=P.ID_USUARIO and TIPO_USUARIO='USUARIO' and P.LOTE='$lote' order by ID_USUARIO");

if (!isset($resProductos) && numero_filas($usuarios)>0) {
?>

<div id="listadoProductos">
	<table class="tablaResultados" style="width: 100%">
		<thead>
			<tr>
				<th title="Verde">Sel.</th>
				<th>PEDIDO</th>
				<th>NOMBRE</th>
				<th align="center">APELLIDOS</th>
				<th align="center">FECHA</th>
				<th align="center">ESTADO</th>
				<th title="Facturar">&nbsp;</th>
				<th align="center">&nbsp;</th>
				<th align="center">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
			while ($user = extraer_registro($usuarios)) {
				$date = new DateTime($user['FECHA_PEDIDO']);
		?>
			<tr title="<?=@$comentarios?>" id="filaCobrada<?=$user['ID_PEDIDO']?>" <?=($user['VERDE']=='1') ? 'style="background-color:#A2F461"' : (($user['COBRADO']=='1') ? 'style="background-color:#ddffff"' : '')?>>
				<td align="center" id="verde<?=$user['ID_PEDIDO']?>">
					<input type="checkbox" id="checkVerde<?=$user['ID_PEDIDO']?>" <?php if ($user['VERDE']=='1') echo 'checked' ?> onmouseup="clickarVerde('<?=$user['ID_PEDIDO']?>', !this.checked);"/>
				</td>
				<td align="center"><?=$user['ID_USUARIO']?>_LOTE<?=$user['LOTE']?></td>
				<td align="center"><?=$user['NOMBRE']?></td>
				<td align="center"><?=$user['APELLIDOS']?></td>
				<td align="center"><?=$date->format('d/m/Y')?></td>
				<td align="center"><?=$user['ESTADO']?></td>
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
					<a id="factura<?=$user['ID_PEDIDO']?>" title="Factura" <?=(($user['ESTADO']=='FINALIZADO' && $user['COBRADO']=='1') ? 'style="display:block"' :  'style="display:none"' )?> 
						href="#" onclick="generarAlbaranFactura('<?=$user['ID_PEDIDO']?>', 3);"><img src="../img/FACTURA.png" alt="factura" width="32"/></a>
				</td>
				<td align="center"><a href='pedidos.php?idUsuario=<?=$user['ID_USUARIO']?>'><img src="../img/INFO.png" alt="ver" width="32"/></a></td>
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
	
	<div style="clear: both;"></div>
</div>
<?php } ?>
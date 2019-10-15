<?php
	require_once "../includes/funciones.inc.php";
	
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-Type: application/vnd.ms-excel");
	//header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=UsuariosLote".@$_GET['lote'].".xls" );
		
	$lote = @$_GET['lote'];
		
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php		
	$envio = @$_GET['envio'];
	$lote = $_GET['lote'];
	
	if ($lote) {
		$sqlu2 = "select distinct U.ID_USUARIO, U.NOMBRE, U.APELLIDOS, P.ID_PEDIDO, P.COMENTARIOS, P.LOTE, P.FECHA_PEDIDO, P.ESTADO, P.HORA_INI, P.HORA_FIN
		from USUARIOS U, PEDIDOS P, PEDIDOS_PRODUCTOS PP WHERE U.ID_USUARIO=P.ID_USUARIO and TIPO_USUARIO='USUARIO' and P.LOTE='$lote' and P.ID_PEDIDO=PP.ID_PEDIDO";
		if (isset($envio) && $envio!='') {
			$sqlu2 .= " and PP.ID_PRODUCTO='$envio'";
		}
		if (isset($_SESSION['ordenHoras']) && @$_SESSION['ordenHoras']!=NULL && @$_SESSION['ordenHoras']=='true') {
			$sqlu2 .= " order by P.HORA_INI";
		} else {
			$sqlu2 .= " order by P.FECHA_PEDIDO";
		}
		$usuarios = consulta($sqlu2);
	}
	
		
	
	while ($usuario = extraer_registro($usuarios)) {
		$fechaPedido = $usuario['FECHA_PEDIDO'];
		$comentarios = $usuario['COMENTARIOS'];
		$horaIni = $usuario['HORA_INI'];
		$horaFin = $usuario['HORA_FIN'];
		
		if (isset($fechaPedido)) {
			$fechaPedido = date_create_from_format('Y-m-d H:i:s', $fechaPedido);
		}
		
		$direccion = consultaDireccionCompletaUsuario ($usuario['ID_USUARIO']);
?>
	<table>
	<tr><td>
	<table class="tablaResultados" border="1">
			<?php
			if (isset($envio) && $envio!='') {
				$descripcion_trans = consultaTipoTransporte($envio);
				?>
				<tr bgcolor="brown" style="color: white">
					<td colspan="10"><b><u><?=$descripcion_trans?></u></b></td>
				</tr>
				<?php 
			}
			?>
			<tr bgcolor="blue" style="color: white">
				<td colspan="10"><b><?=$usuario['NOMBRE']?> <?=$usuario['APELLIDOS']?>&nbsp;&nbsp;(<?=$usuario['ID_USUARIO']?>, LOTE <?=$lote?>, Fecha: <?=@$fechaPedido->format('d/m/Y')?>)</b></td>
			</tr>
			<tr bgcolor="green" style="color: white">
				<td colspan="10"><?=$direccion?></td>
			</tr>
			<?php 
			if (isset($horaIni) && $horaIni!=NULL && $horaIni!='') {
				?>
				<tr style="color: blue">
					<td colspan="10"><b>Horario de entrega:</b> <?=$horaIni.' - '.$horaFin?></td>
				</tr>
				<?php 
			}
			if (isset($comentarios) && $comentarios!=NULL && $comentarios!='') {
				?>
				<tr style="color: blue">
					<td colspan="10"><b>Comentarios del usuario:</b> <?=$comentarios?></td>
				</tr>
				<?php 
			}
			?>
			<tr>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>Id.</th>
				<th align="left">CANTIDAD</th>
				<th>PROVEEDOR</th>
				<th>SUBCATEGORIA</th>
				<th>PRODUCTO</th>
				<th align="center">PRECIO</th>
				<th align="center">SUBTOTAL</th>
				<th align="center">REVISI&Oacute;N CANTIDAD</th>
			</tr>
			<?php
				$idUsuario = $usuario['ID_USUARIO'];
				$resProductos = consulta("select PP.*, PR.DESCRIPCION, U.DESCRIPCION AS MEDIDA
					, PV.NOMBRE AS PROVEEDOR, C.DESCRIPCION AS CATEGORIA, SC.DESCRIPCION AS SUBCATEGORIA
					from PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR, UNIDADES U, PROVEEDORES PV, CATEGORIAS C, SUBCATEGORIAS SC
					where P.ID_PEDIDO=PP.ID_PEDIDO and PP.ID_PRODUCTO=PR.ID_PRODUCTO 
					and PR.UNIDAD_MEDIDA=U.ID_UNIDAD
					and PR.PROVEEDOR_1=PV.ID_PROVEEDOR
					and PR.ID_CATEGORIA=C.ID_CATEGORIA and PR.ID_SUBCATEGORIA=SC.ID_SUBCATEGORIA
					and P.ID_USUARIO='$idUsuario' and P.LOTE='$lote'
					order by PV.NOMBRE, C.DESCRIPCION, SC.DESCRIPCION, PR.DESCRIPCION");
			
				$total = 0.00;
				$totalRevisado = 0.00;
				$saldoUsuarioRes = consulta("select SALDO from USUARIOS WHERE ID_USUARIO='$idUsuario'");
				$filaRes = extraer_registro($saldoUsuarioRes);
				$saldoUsuario = $filaRes['SALDO'];
				 
				while ($producto = extraer_registro($resProductos)) {
					$idPedidoActual = $producto['ID_PEDIDO'];
					$idProducto = $producto['ID_PRODUCTO'];
					$cantidad = $producto['CANTIDAD'];
					$cantidadRevisada = $producto['CANTIDAD_REVISADA'];
					$subtotal = round(($producto['PRECIO'] * $cantidad), 2);
					if (isset($cantidadRevisada) && $cantidadRevisada>=0) {
						$subtotalRevisado = round(($producto['PRECIO'] * $cantidadRevisada), 2);
					} else {
						$cantidadRevisada = $cantidad;
						$subtotalRevisado = round(($producto['PRECIO'] * $cantidad), 2);
					}
					$total += $subtotal;
					$totalRevisado += $subtotalRevisado;
			?>
				<tr style="border-bottom: 1px solid #cccccc;">
					<td><input type="checkbox" /></td>
					<td><input type="checkbox" <?php if ($producto['CHECK_REVISADO']=='1') echo 'checked' ?>/></td>
					<td align="left"><?=$producto['ID_PRODUCTO']?></td>
					<td align="left"><?=round($cantidad, 2)?> <?=$producto['MEDIDA']?></td>
					<td align="left"><?=$producto['PROVEEDOR']?></td>
					<td align="left"><?=$producto['SUBCATEGORIA']?></td>
					<td><?=$producto['DESCRIPCION']?></td>
					<td align="center"><?=$producto['PRECIO']?> &euro;</td>
					<td align="center"><?=number_format($subtotal, 2, '.', '')?> &euro;</td>
					<td align="center">&nbsp;</td>
				</tr>
			<?php } ?>
			</table>
	</td>
	</tr>
	<tr>
	<td>	
		<table style="width: 100%; float: right;">
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td valign="middle" align="right"><b>Total Comprado:</b></td>
				<td valign="middle" align="right">
					<?=number_format($total, 2, '.', '')?> &euro;
				</td>
				<!-- 
				<td valign="middle" align="right"><b>Total Revisado:</b></td>
				<td valign="middle" align="right">
					<?=number_format($totalRevisado, 2, '.', '')?> &euro;
				</td>
				<td valign="middle" align="right"><b>Genera Saldo:</b></td>
				<td valign="middle" align="right">
					<?=number_format((0 - $totalRevisado), 2, '.', '')?> &euro;
				</td>
				 -->
				<td></td>
			</tr>
			<tr>
				<td colspan="10">&nbsp;</td>
			</tr>
		</table>
	</td>
	</tr>
	</table>	
	<?php 
		} 
	?>
</body>
</html>
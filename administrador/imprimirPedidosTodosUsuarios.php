<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<link href="../css/estilo.css" rel="stylesheet" media="print" />
	<script type="text/javascript" src="../js/print.js"></script>
	<script type="text/javascript">
		function clickarRevisado(id, idPedidoActual, idProducto, check) {
			$( "#"+id ).html('...');
			try {
				$( "#"+id ).load( "clickarRevisado.php?idPedidoActual="+idPedidoActual+"&idProducto="+idProducto+"&check="+check, function() {
				});
			} catch (e) {
			}
		}
	</script>
</head>
<body>
	<div id="print" style="text-align: center;">
	<p>
		<input type="button" id="printer" value="Imprimir">&nbsp;
		<?php if (isset($_GET['lote'])) { ?>
		<input type="button" id="volver" value="Volver">
		<?php } else { ?>
		<input type="button" id="cerrar" value="Cerrar">
		<?php } ?>
	</p>
	<script>
		$(document).ready(function()
			{
				$("#printer").bind("click",function()
				{
					$("#contenidoAdmin").printArea();
				});
				<?php if (isset($_GET['lote'])) { ?>
				$("#volver").bind("click",function()
				{
					document.location='<?=$_GET['url']?>';
				});
				<?php } else { ?>
				$("#cerrar").bind("click",function()
				{
					window.close();
				});
				<?php } ?>
			});
	</script>
	</div>
	<div id="contenidoAdmin">
		<?php		
		require_once "../includes/funciones.inc.php";
		$envio = @$_GET['envio'];
		$lote = @$_GET['lote'];
		if (empty($lote)) {
			$lote = consultarLoteActual();
		}
		
		if (isset($envio) && $envio!='') {
			$descripcion_trans = consultaTipoTransporte($envio);
			echo "<h1><u><b>$descripcion_trans</b></u></h1>";
		}

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
		
		echo "<div style=\"position:relative; top: -20px\">";
			

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
				<h1 style=" margin-top: 50px"><?=$usuario['NOMBRE']?> <?=$usuario['APELLIDOS']?>&nbsp;&nbsp;(<?=$usuario['ID_USUARIO']?>, LOTE <?=$lote?>, Fecha: <?=@$fechaPedido->format('d/m/Y')?>)</h1>
				<h2 style="position: relative; top: -30px; text-align: right;"><?=$direccion?></h2>
				<div id="listadoProductos" style="position: relative; top: -30px">
				<?php 
				
					if (isset($horaIni) && $horaIni!=NULL && $horaIni!='') {
						?>
						&nbsp;&nbsp;<span style="font-size:x-large; font-weight: bolder; text-decoration: underline;">Horario de entrega:</label></span>  <?=$horaIni.' - '.$horaFin?>
						<br/>
						<?php 
					}
					
					if (isset($comentarios) && $comentarios!=NULL && $comentarios!='') {
						?>
						&nbsp;&nbsp;<span style="font-size:x-large; font-weight: bolder; text-decoration: underline;">Comentarios del usuario:</label></span>  <?=$comentarios?>
						<br/>
						<?php 
					}
				?>
					<table class="tablaResultados" style="width: 100%">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
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
							$idUsuario = $usuario['ID_USUARIO'];
							$resProductos = consulta("select P.ID_PEDIDO,  PP.*, PR.DESCRIPCION, U.DESCRIPCION AS MEDIDA
								from PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR, UNIDADES U
								where P.ID_PEDIDO=PP.ID_PEDIDO and PP.ID_PRODUCTO=PR.ID_PRODUCTO and PR.UNIDAD_MEDIDA=U.ID_UNIDAD
								and P.ESTADO='PREPARACION' and P.ID_USUARIO='$idUsuario' and P.LOTE='$lote'");
						
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
							<tr>
								<td><input type="checkbox" /></td>
								<td id="check<?=$idProducto?>"><input type="checkbox" <?php if ($producto['CHECK_REVISADO']=='1') echo 'checked' ?> onmouseup="clickarRevisado('check<?=$idProducto?>', '<?=$idPedidoActual?>', '<?=$idProducto?>', !this.checked);"/></td>
								<td><?=$producto['DESCRIPCION']?></td>
								<td align="center"><?=$producto['PRECIO']?> &euro;</td>
								<td align="center"><?=round($cantidad, 2)?> <?=$producto['MEDIDA']?></td>
								<td align="center"><?=number_format($subtotal, 2, '.', '')?> &euro;</td>
								<td align="center">
									<?=round($cantidadRevisada, 2)?>
									<?=$producto['MEDIDA']?>
								</td>
								<td align="center"><?=number_format($subtotalRevisado, 2, '.', '')?> &euro;</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					
					<div style="clear: both;"></div>
					
					<table style="width: 100%; float: right;">
						<tr>
							<td valign="middle" align="right"><b>Total Comprado:</b></td>
							<td valign="middle" align="right">
								<?=number_format($total, 2, '.', '')?> &euro;
							</td>
							<td valign="middle" align="right"><b>Total Revisado:</b></td>
							<td valign="middle" align="right">
								<?=number_format($totalRevisado, 2, '.', '')?> &euro;
							</td>
							<td valign="middle" align="right"><b>Genera Saldo:</b></td>
							<td valign="middle" align="right">
								<?=number_format((0 - $totalRevisado), 2, '.', '')?> &euro;
							</td>
						</tr>
						<tr>
							<td valign="middle" align="right" colspan="5"><b>Saldo del Usuario:</b></td>
							<td valign="middle" align="right">
								<?=number_format($saldoUsuario, 2, '.', '')?> &euro;
							</td>
						</tr>
						<tr>
							<td valign="middle" align="right" colspan="5"><b>Saldo final:</b></td>
							<td valign="middle" align="right">
								<?=number_format(($saldoUsuario - $totalRevisado), 2, '.', '')?> &euro;
							</td>
						</tr>
					</table>
					
					<div style="clear: both;"></div>
				</div>
				
				<?php 
			//echo "</div>";
			} 
	?>
	</div>
</body>
</html>
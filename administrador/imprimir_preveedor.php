<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<link href="../css/estilo.css" rel="stylesheet" media="print" />
	<script type="text/javascript" src="../js/print.js"></script>
</head>
<body>
	<?php
		require_once "../includes/funciones.inc.php";
		
		$lote = @$_GET['lote'];
		$consulta = "select P.*, PP.ID_PEDIDO_PROVEEDOR, PP.TOTAL_REVISADO from PROVEEDORES P, PEDIDOS_PROVEEDORES PP
			WHERE P.ID_PROVEEDOR = PP.ID_PROVEEDOR
			AND PP.ID_PEDIDO_PROVEEDOR = '".@$_GET['idPedidoProv']."'";
		
		$proveedores = consulta($consulta);
		
		$fechaActual = new DateTime();
		$fechaActual = $fechaActual->format("d/M/Y");
		
		if ($proveedor = extraer_registro($proveedores)) {
			$idProveedor = $proveedor['ID_PROVEEDOR'];
			$idPedidoProveedor = $proveedor['ID_PEDIDO_PROVEEDOR'];
			$nombreProveedor = $proveedor['NOMBRE'];
			$descProveedor = $proveedor['DESCRIPCION'];
			$totalRevisado = $proveedor['TOTAL_REVISADO'];
	?>
	<div id="print" style="text-align: center;">
		<p>
			<input type="button" id="printer" value="Imprimir">&nbsp;
			<input type="button" id="excel" value="Excel">&nbsp;
			<input type="button" id="cerrar" value="Cerrar">
		</p>
		<script>
			$(document).ready(function()
				{
					$("#printer").bind("click",function()
					{
						$("#contenidoAdmin").printArea();
					});
					$("#excel").bind("click",function()
					{
						document.location = 'imprimir_excel.php?idPedidoProv=<?=$_GET['idPedidoProv']?>';
					});
					$("#cerrar").bind("click",function()
					{
						window.close();
					});
				});
		</script>
		<div id="contenidoAdmin">
			<div id="tituloProveedores">
			<span>&nbsp; LOTE <?=$lote?> - Fecha: <?=$fechaActual?></span><br/>
			<span>&nbsp;PROVEEDOR <?=$nombreProveedor?> (<?=$descProveedor?>)</span>
			</div>
			<div id="listadoProductos">
				<?php 
					$consulta2 = "SELECT PP.*, PR.DESCRIPCION, U.DESCRIPCION AS DESC_UNIDAD, PR.INC_CUARTOS
								FROM PEDIDOS_PROVEEDORES_PROD PP, PRODUCTOS PR, UNIDADES U
								WHERE PP.ID_PRODUCTO = PR.ID_PRODUCTO
								and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
								AND PP.ID_PEDIDO_PROVEEDOR=$idPedidoProveedor";
					$resProductos = consulta($consulta2);
				?>
				<table style="width: 100%">
				<tr><td width="80%">
				<table class="tablaResultados" style="width: 100%">
					<thead>
						<tr>
							<th>PRODUCTO</th>
							<th align="center">CANTIDAD</th>
							<th align="center">PRECIO</th>
							<th align="center">SUBTOTAL<th>
						</tr>
					</thead>
					<tbody>
					<?php
						$total = 0.00;
						$totalRev = 0.00;
						
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
							$subtotal = round(($producto['PRECIO'] * $cantidad_rev), 2);
							$total += round(($producto['PRECIO'] * $cantidad), 2);
							$totalRev +=$subtotal
						?>
						<tr>
							<td><?=$producto['DESCRIPCION']?></td>
							<td align="right" nowrap>
								<?=round($cantidad_rev, 2)?> <?=$producto['DESC_UNIDAD']?>
							</td>
							<td align="center"><?=$producto['PRECIO']?> &euro;</td>
							<td align="center"><?=number_format($subtotal, 2, '.', '')?> &euro;</td>
						</tr>
					<?php } ?>
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3" valign="middle" align="right"><b>Total</b></td>
							<td valign="middle" align="right"><input id="total" type="text" readonly="readonly" contenteditable="false" value="<?=$totalRevisado==NULL ? number_format($totalRev, 2, '.', '') : $totalRevisado?> &euro;"/></td>
						</tr>
					</tbody>
				</table>
				</td>
				</tr>
				</table>
				<br/><br/>
			</div>
		</div>
	</div>
	<?php 
		}	
	?>
</body>
</html>
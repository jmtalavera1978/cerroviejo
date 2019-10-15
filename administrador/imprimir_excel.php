<?php
	require_once "../includes/funciones.inc.php";
	
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-Type: application/vnd.ms-excel");
	//header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=".@$_GET['idPedidoProv'].".xls" );
		
	$consulta = "select P.*, PP.ID_PEDIDO_PROVEEDOR, PP.TOTAL_REVISADO from PROVEEDORES P, PEDIDOS_PROVEEDORES PP
		WHERE P.ID_PROVEEDOR = PP.ID_PROVEEDOR
		AND PP.ID_PEDIDO_PROVEEDOR = '".@$_GET['idPedidoProveedor']."'";
	
	$proveedores = consulta($consulta);
	
	if ($proveedor = extraer_registro($proveedores)) {
		$idProveedor = $proveedor['ID_PROVEEDOR'];
		$idPedidoProveedor = $proveedor['ID_PEDIDO_PROVEEDOR'];
		$nombreProveedor = $proveedor['NOMBRE'];
		$descProveedor = $proveedor['DESCRIPCION'];
		$totalRevisado = $proveedor['TOTAL_REVISADO'];
		
		
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<?php 
		$fechaActual = new DateTime();
		$fechaActual = $fechaActual->format("d/M/Y");
	
		$consulta2 = "SELECT PP.*, PR.DESCRIPCION, U.DESCRIPCION AS DESC_UNIDAD, PR.INC_CUARTOS
					FROM PEDIDOS_PROVEEDORES_PROD PP, PRODUCTOS PR, UNIDADES U
					WHERE PP.ID_PRODUCTO = PR.ID_PRODUCTO
					and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
					AND PP.ID_PEDIDO_PROVEEDOR=$idPedidoProveedor";
		$resProductos = consulta($consulta2);
	?>
	<table>
	<tr><td>
	<table class="tablaResultados">
			<tr>
				<td colspan="4">LOTE <?=$lote?> - Fecha: <?=$fechaActual?> - PROVEEDOR <?=$nombreProveedor?> (<?=$descProveedor?>)</td>
			</tr>
			<tr>
				<th>PRODUCTO</th>
				<th align="center">PRECIO</th>
				<th align="center">CANTIDAD</th>
				<th align="center">SUBTOTAL<th>
			</tr>
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
				<td align="center"><?=$producto['PRECIO']?> &euro;</td>
				<td align="right" nowrap>
					<?=round($cantidad_rev, 2)?> <?=$producto['DESC_UNIDAD']?>
				</td>
				<td align="center"><?=number_format($subtotal, 2, '.', '')?> &euro;</td>
			</tr>
		<?php } ?>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" valign="middle" align="right"><b>Total</b></td>
				<td valign="middle" align="right"><?=$totalRevisado==NULL ? number_format($totalRev, 2, '.', '') : $totalRevisado?> &euro;</td>
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
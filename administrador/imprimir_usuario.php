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
		
		$lote = consultarLoteActual();
		$consulta = "select * from PROVEEDORES where ID_PROVEEDOR=".@$_GET['idProveedor']." and PEDIDO_PROCESADO=0";
		$proveedores = consulta($consulta);
		
		if ($proveedor = extraer_registro($proveedores)) {
			$idProveedor = $proveedor['ID_PROVEEDOR'];
			$nombreProveedor = $proveedor['NOMBRE'];
			$descProveedor = $proveedor['DESCRIPCION'];
	?>
	<div id="print" style="text-align: center;">
		<p><input type="button" id="printer" value="Imprimir">&nbsp;<input type="button" id="excel" value="Excel"></p>
		<script>
			$(document).ready(function()
				{
					$("#printer").bind("click",function()
					{
						$("#contenidoAdmin").printArea();
					});
				});
			$(document).ready(function()
				{
					$("#excel").bind("click",function()
					{
						document.location = 'imprimir_excel.php?idProveedor=<?=$_GET['idProveedor']?>';
					});
				});
		</script>
		<div id="contenidoAdmin">
			<div id="tituloProveedores">
			<span>&nbsp;PROVEEDOR <?=$nombreProveedor?> (<?=$descProveedor?>)</span>
			</div>
			<div id="listadoProductos">
				<?php 
					$consulta2 = "SELECT PR.ID_PRODUCTO, PR.DESCRIPCION, PP.PRECIO, SUM( CANTIDAD ) AS CANTIDAD_TOTAL, U.DESCRIPCION AS DESC_UNIDAD, CANTIDAD_ILIMITADA, CANTIDAD_1, CANTIDAD_2, CANTIDAD_1_REV, CANTIDAD_2_REV
									FROM PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR, UNIDADES U
									WHERE P.ID_PEDIDO = PP.ID_PEDIDO
									AND PP.ID_PRODUCTO = PR.ID_PRODUCTO
									and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
									AND P.LOTE=$lote
									AND PR.PROVEEDOR_1=$idProveedor
									GROUP BY PR.ID_PRODUCTO";
					$resProductos = consulta($consulta2);
					
					$consulta3 = "SELECT * 
									FROM (
									SELECT PR.ID_PRODUCTO, PR.DESCRIPCION, PP.PRECIO, SUM( CANTIDAD ) AS CANTIDAD_TOTAL, U.DESCRIPCION AS DESC_UNIDAD, CANTIDAD_1, CANTIDAD_2, CANTIDAD_1_REV, CANTIDAD_2_REV
									FROM PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR, UNIDADES U
									WHERE P.ID_PEDIDO = PP.ID_PEDIDO
									AND PP.ID_PRODUCTO = PR.ID_PRODUCTO
									and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
									AND P.LOTE=$lote
									AND PR.PROVEEDOR_2=$idProveedor
									AND CANTIDAD_ILIMITADA=0
									GROUP BY PR.ID_PRODUCTO
									)proveedores2
									WHERE CANTIDAD_TOTAL > CANTIDAD_1";
					$resProductos2 = consulta($consulta3);
				?>
				<table style="width: 100%">
				<tr><td width="80%">
				<h1 style=" margin-top: 50px"><?=$usuario['NOMBRE']?> <?=$usuario['APELLIDOS']?>&nbsp;&nbsp;(LOTE <?=$lote?>, Fecha: <?=@$fechaPedido->format('d/m/Y')?>)</h1>
				<table class="tablaResultados" style="width: 100%">
					<thead>
						<tr>
							<th>PRODUCTO</th>
							<th align="center">PRECIO</th>
							<th align="center">CANTIDAD</th>
							<th align="center">SUBTOTAL<th>
						</tr>
					</thead>
					<tbody>
					<?php
						$total = 0.00;
						 
						while ($producto = extraer_registro($resProductos)) {
							$ilimitada = $producto['CANTIDAD_ILIMITADA'];
							$cantidad = $producto['CANTIDAD_TOTAL'];
							if ($ilimitada==0 && $cantidad>$producto['CANTIDAD_1']) {
								$cantidad = intval ($producto['CANTIDAD_1']);
							}
							$cantidad_rev = $producto['CANTIDAD_1_REV'];;
							if ($cantidad_rev == NULL) {
								$cantidad_rev = $cantidad;
								consulta("UPDATE PRODUCTOS SET CANTIDAD_1_REV='$cantidad_rev' WHERE ID_PRODUCTO='$idProductoActual'");
							}
							$subtotal = round(($producto['PRECIO'] * $cantidad_rev), 2);
							$total += $subtotal;
					?>
						<tr>
							<td><?=$producto['DESCRIPCION']?></td>
							<td align="center"><?=$producto['PRECIO']?> &euro;</td>
							<td align="center"><?=$cantidad_rev?> <?=$producto['DESC_UNIDAD']?></td>
							<td align="center"><?=number_format($subtotal, 2, '.', '')?> &euro;</td>
						</tr>
					<?php } ?>
					<?php while ($producto = extraer_registro($resProductos2)) {
							$cantidad = intval ($producto['CANTIDAD_TOTAL'] - $producto['CANTIDAD_1']);
							$cantidad_rev = $producto['CANTIDAD_2_REV'];
							if ($cantidad_rev == NULL) {
								$cantidad_rev = $cantidad;
								consulta("UPDATE PRODUCTOS SET CANTIDAD_2_REV='$cantidad_rev' WHERE ID_PRODUCTO='$idProductoActual'");
							}
							$subtotal = round(($producto['PRECIO'] * $cantidad_rev), 2);
							$total += $subtotal;
					?>
						<tr>
							<td><?=$producto['DESCRIPCION']?></td>
							<td align="center"><?=$producto['PRECIO']?> &euro;</td>
							<td align="center"><?=$cantidad_rev?> <?=$producto['DESC_UNIDAD']?></td>
							<td align="center"><?=number_format($subtotal, 2, '.', '')?> &euro;</td>
						</tr>
					<?php } ?>
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3" valign="middle" align="right"><b>Total</b></td>
							<td valign="middle" align="right"><input id="total" type="text" readonly="readonly" contenteditable="false" value="<?=number_format($total, 2, '.', '')?> &euro;"/></td>
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
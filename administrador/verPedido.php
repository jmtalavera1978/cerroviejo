<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<div id="contenidoAdmin">
	<?php 
		if (isset($_GET['idPedido'])) {
			$idPedido = $_GET['idPedido'];
			$pedidoProductos = consulta("select distinct PE.*, PR.DESCRIPCION, U.DESCRIPCION AS DESC_MEDIDA from PEDIDOS P, PEDIDOS_PRODUCTOS PE, PRODUCTOS PR, UNIDADES U 
					where P.id_PEDIDO=PE.ID_PEDIDO and PE.id_producto= PR.id_producto and PR.UNIDAD_MEDIDA=U.ID_UNIDAD and PE.id_pedido='$idPedido'");
		} else {
			$_SESSION['mensaje_generico'] = 'Pedido no encontrado';
			header("Location: pedidos.php");
		}
	?>
			<h1 class="cal">Productos del Pedido</h1>

				<table class="tablaResultados">
					<thead>
						<tr>
							<th>Producto</th>
							<th align="center">Precio</th>
							<th align="center">Cantidad</th>
							<th align="center">Cantidad Revisada</th>
							<th align="center">Subtotal</th>
							<th align="center">Subtotal Revisado</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($pedidoProductos)==0) {
?>
					<tr>
						<td colspan="3">No hay productos en el pedido</td>
					</tr>
<?php 
					} else {
						while ($filaP = extraer_registro($pedidoProductos)) {
							$idPedidoActual = $filaP['ID_PEDIDO'];
							$idProducto = $filaP['ID_PRODUCTO'];
							$cantidad = $filaP['CANTIDAD'];
							$cantidadRevisada = $filaP['CANTIDAD_REVISADA'];
							$subtotal = round(($filaP['PRECIO'] * $cantidad), 2);
							
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
								} else {
									$subtotalRevisado = round(($filaP['PRECIO'] * $cantidadRevisada), 2);
								}
							} else {
								if (isset($peso_por_unidad) && $peso_por_unidad>0) {
									$precio_por_kg = $filaP['PRECIO'] / $peso_por_unidad;
									$cantidadRevisada = round(($cantidad * $peso_por_unidad), 2);
									$subtotalRevisado = round(($precio_por_kg * $cantidadRevisada), 2);
								} else {
									$cantidadRevisada = $cantidad;
									$subtotalRevisado = round(($filaP['PRECIO'] * $cantidadRevisada), 2);
								}
							}
							
							/*$total += $subtotal;
							$totalRevisado += $subtotalRevisado;*/
?>
					<tr>
						<td><?=$filaP['DESCRIPCION']?></td>
						<td align="center" nowrap><?=$filaP['PRECIO']?> &euro;</td>
						<td align="center"><?=round($cantidad, 2)?> <?=$productoMedida?></td>
						<td align="center"><?=round($cantidadRevisada, 2)?> <?=$productoMedidaRevisado?></td>
						<td align="center" nowrap>
							<?=number_format($subtotal, 2, '.', '')?> &euro;
						</td>
						<td align="center" nowrap><?=number_format($subtotalRevisado, 2, '.', '')?> &euro;</td>
					</tr>
<?php
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<br/>
		<div id="botonera">
			<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='pedidos.php'" />
		</div>
	</section>
</body>
</html>

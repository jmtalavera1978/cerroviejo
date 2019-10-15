<?php
include("../includes/funciones.inc.php");
compruebaSesionAdministracion();
$idPedido = $_GET['idPedido'];
$idUsuario = $_GET['idUsuario'];

$resProductos = consulta("select P.ID_PEDIDO, PP.*, PR.DESCRIPCION, U.DESCRIPCION AS MEDIDA from PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR, UNIDADES U
		where P.ID_PEDIDO=PP.ID_PEDIDO and PP.ID_PRODUCTO=PR.ID_PRODUCTO and PR.UNIDAD_MEDIDA=U.ID_UNIDAD
		and P.ESTADO='PREPARACION' and P.ID_PEDIDO='$idPedido' ");

$indice=1;
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
}
?>
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
<?php
	require_once "../../includes/funciones.inc.php";
	
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-Type: application/vnd.ms-excel");
	//header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=CatalogoProductos.xls" );
		
	$lote = @$_GET['lote'];
	$consulta = "select * FROM CATALOGO_PRODUCTOS";
	
	$productos = consulta($consulta);	
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>
	<tr><td>
	<table class="tablaResultados">
			<tr bgcolor="#000066" style="color: white; border: 2px solid #CCCCCC; font-weight: bolder;">
				<td colspan="7" align="center"><b>CATÁLOGO DE PRODUCTOS</b></td>
			</tr>
			<tr bgcolor="#000066" style="color: white; border: 2px solid #CCCCCC;">
				<th>CATEGORIA</th>
				<th>SUBCATEGORIA</th>
				<th>PROVEEDOR</th>
				<th>PRODUCTO</th>
				<th align="right">PRECIO UNITARIO</th>
				<th align="right">MEDIDA</th>
				<th align="right">DESCRIPCIÓN MEDIDA</th>
			</tr>
		<?php
			$total = 0.00;
			$totalRev = 0.00;
			
			$numProductos = numero_filas($productos);
			
			if ($numProductos==0) {
			?>
				<tr><td colspan="7">No hay productos</td></tr>
				<?php 
			}
			 
			while ($producto = extraer_registro($productos)) {
				//$precioRecargo = calculaPrecioConRecargo($producto['ID_PRODUCTO'], $producto['PRECIO']);
				$precioRecargo = calculaPVP ($producto['ID_PRODUCTO'], $producto['IMPORTE_SIN_IVA'], $producto['TIPO_IVA']);
		?>
			<tr bgcolor="white" style="color: black; border: 2px solid #CCCCCC;">
				<td><?=$producto['CATEGORIA']?></td>
				<td><?=$producto['SUBCATEGORIA']?></td>
				<td><?=$producto['PROVEEDOR']?></td>
				<td><?=$producto['DESCRIPCION']?></td>
				<td align="right" nowrap><?=$precioRecargo?>  &euro;</td>
				<td align="right" nowrap><?=$producto['MEDIDA']?></td>
				<td align="right" nowrap><?=$producto['DESCRIPCION_MEDIDA']?></td>
			</tr>
		<?php } ?>
	</table>
	</td>
	</tr>
	</table>
</body>
</html>
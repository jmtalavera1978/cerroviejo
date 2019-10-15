<?php
	require_once "../includes/funciones.inc.php";
	
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-Type: application/vnd.ms-excel");
	//header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=ProveedoresLote".@$_GET['lote'].".xls" );
		
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
		$idPedidoProv = @$_GET['idPedidoProv'];
		$idSubgrupo = @$_GET['idSubgrupo'];
		$lote = @$_GET['lote'];
		$estado = @$_GET['estado'];
		$bnombreProv = @$_GET['proveedor'];
		
		$primero = TRUE;
		
		$consulta = "select P.*, PP.ID_PEDIDO_PROVEEDOR, PP.LOTE, PP.TOTAL_REVISADO, PP.ID_SUBGRUPO, PP.PEDIDO_PROCESADO  from PROVEEDORES P, PEDIDOS_PROVEEDORES PP
			WHERE P.ID_PROVEEDOR = PP.ID_PROVEEDOR";
		
		if ($idPedidoProv && $idPedidoProv!='') {
			$consulta .= " AND PP.ID_PEDIDO_PROVEEDOR='$idPedidoProv'";
		} else {
			if ($lote && $lote!='') {
				$consulta .= " AND PP.LOTE='$lote'";
			}
			if (isset($idSubgrupo) && $idSubgrupo!='-1000' && $idSubgrupo!='') {
				$consulta .= " AND PP.ID_SUBGRUPO = '$idSubgrupo'";
			}
			if (isset($estado) && $estado!='') {
				$consulta .= " AND PP.PEDIDO_PROCESADO = '$estado'";
			}
			if (isset($bnombreProv) && strlen(@$bnombreProv)>0) {
				$consulta .= " AND P.NOMBRE like '%$bnombreProv%'";
			}
		}
		
		$consulta .= " ORDER BY P.NOMBRE ";
		
		$proveedores = consulta($consulta);
		
		while ($proveedor = extraer_registro($proveedores)) {
			$lote = $proveedor['LOTE'];
			
			$idProveedor = $proveedor['ID_PROVEEDOR'];
			$idPedidoProveedor = $proveedor['ID_PEDIDO_PROVEEDOR'];
			$nombreProveedor = $proveedor['NOMBRE'];
			$descProveedor = $proveedor['DESCRIPCION'];
			$totalRevisado = $proveedor['TOTAL_REVISADO'];
			
			$subgrupo = $proveedor['ID_SUBGRUPO'];
			$estado = $proveedor['PEDIDO_PROCESADO'];
				
			if (!$subgrupo || $subgrupo == NULL || $subgrupo=='') {
				$subgrupo = "RESTO";
			} else {
				$subgrupo = consulta("select * from SUBGRUPOS WHERE ID_SUBGRUPO='$subgrupo'");
				$subgrupo = extraer_registro($subgrupo);
				$subgrupo = $subgrupo['NOMBRE'];
			}
		
			$fechaActual = new DateTime();
			$fechaActual = $fechaActual->format("d/M/Y");
		
			$consulta2 = "SELECT PP.*, PR.DESCRIPCION, U.DESCRIPCION AS DESC_UNIDAD, PR.INC_CUARTOS, PR.IMPORTE_SIN_IVA, PR.TIPO_IVA
						FROM PEDIDOS_PROVEEDORES_PROD PP, PRODUCTOS PR, UNIDADES U
						WHERE PP.ID_PRODUCTO = PR.ID_PRODUCTO
						and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
						AND PP.ID_PEDIDO_PROVEEDOR=$idPedidoProveedor";
			
			$resProductos = consulta($consulta2);
	?>
	<table>
	<tr><td>
	<table class="tablaResultados">
			<tr bgcolor="blue" style="color: white">
				<td colspan="5"><b>LOTE <?=$lote?> - Fecha: <?=$fechaActual?> - PROVEEDOR <?=$nombreProveedor?> (<?=$descProveedor?>) - <?=$subgrupo?></b></td>
			</tr>
			<tr>
				<th bgcolor="#cccccc">PRODUCTO</th>
				<th bgcolor="#cccccc" align="center">CANTIDAD PROVEEDOR</th>
				<th bgcolor="#cccccc" align="center">PSI (PVS</th>
				<th bgcolor="#cccccc" align="center">IVA</th>
				<th bgcolor="#cccccc" align="right">SUBTOTAL<th>
			</tr>
		<?php
			$total = 0.00;
			$totalRev = 0.00;
			
			$numProductos = numero_filas($resProductos);
			
			if ($numProductos==0) {
			?>
				<tr><td colspan="5">No ha pedidos para este proveedor</td></tr>
				<?php 
			}
			 
			while ($producto = extraer_registro($resProductos)) {
				$cantidad = $producto['CANTIDAD'];
				$idProductoActual = $producto['ID_PRODUCTO'];
				$cantidad_rev = $producto['CANTIDAD_REV'];
				if ($cantidad_rev == NULL) {
					$cantidad_rev = $cantidad;
				}
				$psi = $producto['IMPORTE_SIN_IVA'];
					
				$TIPO_IVA = $producto['TIPO_IVA'];
				$precioConIvaSinRecargo = round(($psi + ($psi * $TIPO_IVA / 100)), 2);
				
				$subtotal = round(($precioConIvaSinRecargo * $cantidad_rev), 2);
				$total += round(($psi * $cantidad), 2);
				
				$totalSinIva += round(($psi * $cantidad_rev), 2);
				$totalRev +=$subtotal
		?>
			<tr>
				<td><?=$producto['DESCRIPCION']?></td>
				<td align="center" nowrap><?=round($cantidad_rev, 2)?> <?=$producto['DESC_UNIDAD']?></td>
				<td align="right" nowrap>
					<?=$psi." &euro; (".$producto['PRECIO']." &euro;)"?>
				</td>
				<td align="right"><?=$TIPO_IVA?>%</td>
				<td align="right"><?=number_format($totalSinIva, 2, '.', '')?> &euro;</td>
			</tr>
		<?php } ?>
			<tr>
				<td colspan="4" valign="middle" align="right"><b>Total (iva incl.):</b></td>
				<td bgcolor="#cccccc" valign="middle" align="right"><b><?=$totalRevisado==NULL ? number_format($totalRev, 2, '.', '') : $totalRevisado?> &euro;</b></td>
			</tr>
			<tr>
				<td colspan="5">&nbsp;</td>
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
<?php
	require_once "../../includes/funciones.inc.php";
	
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-Type: application/vnd.ms-excel");
	header ("Content-Disposition: attachment; filename=BeneficioAnualPorUsuario.xls" );
	
	/*
		CREAR UN INFORME EXCEL "VER BENEFICIO" CON LA SIGUIENTE INFORMACIÓN (COPIA DEL DEMANDA POR USUARIO PERO CALCULANDO EL BENEFICIO):
		LOTE, SUBGRUPO, USUARIO, PROVEEDOR, PRODUCTO, CANTIDAD TOTAL ADQUIRIDA, UNIDAD DE MEDIDA, TOTAL, PVP, RECARGO_APLICADO, TOTAL_SIN RECARGO, BENEFICIO
		
		ESTE INFORME EMPEZARÁ EN EL LOTE QUE YA TENGA GRABADO EL BENEFICIO EN LA TABLA DE PEDIDOS. SACARÁ SIEMPRE INFORMACIÓN UNICAMENTE DEL AÑO ACTUAL
		, PARA QUE ASÍ EN 2015 SOLO VEAMOS EL BENEFICIO DE ESE AÑO Y NO CON LOS LOTES QUE GENEREMOS EN ESTOS 2 MESES RESTANTES.
	 */
	$loteMinimo = 0;
	if (isset($_POST['anyo'])) {
		$anyo = $_POST['anyo'];
	} else {
		$anyo = date('Y');
	}
	//, (100 - (100 * ROUND (PRECIO_SIN_RECARGO / PVP ,2))) AS RECARGO_APLICADO
	$consulta = "select TOTAL_VENTAS_ANUAL.*, ROUND(TOTAL - TOTAL_SIN_RECARGO, 2) AS BENEFICIO, ROUND((TOTAL_SIN_RECARGO * TIPO_IVA /100), 2) AS IVA
				from (
				  select VENTAS_ANUAL.*
					, ROUND ( (((PVP - PRECIO_SIN_RECARGO) * 100) / PRECIO_SIN_RECARGO), 0) AS  RECARGO_APLICADO
					, ROUND(CANTIDAD_TOTAL * PVP, 2) AS TOTAL
					, ROUND(CANTIDAD_TOTAL * PRECIO_SIN_RECARGO, 2) AS TOTAL_SIN_RECARGO from (
					SELECT P.LOTE, S.NOMBRE AS SUBGRUPO, U.ID_USUARIO AS USUARIO, PR.NOMBRE AS PROVEEDOR, PO.DESCRIPCION AS PRODUCTO, PP.PRECIO AS PVP, PP.PRECIO_SIN_RECARGO, PP.TIPO_IVA,
		 				 ROUND(SUM((IF(PP.CANTIDAD_REVISADA is null, PP.CANTIDAD, 
	 						IF (PP.PESO_POR_UNIDAD > 0 , (PP.CANTIDAD_REVISADA / PP.PESO_POR_UNIDAD), PP.CANTIDAD_REVISADA)))), 2) AS CANTIDAD_TOTAL
					FROM PEDIDOS_PRODUCTOS PP, PEDIDOS P, PRODUCTOS PO, USUARIOS U, SUBGRUPOS S, PROVEEDORES PR
					WHERE PP.ID_PEDIDO = P.ID_PEDIDO
						AND PP.ID_PRODUCTO = PO.ID_PRODUCTO
						AND P.ID_USUARIO = U.ID_USUARIO
						AND U.ID_SUBGRUPO=S.ID_SUBGRUPO
						AND PO.PROVEEDOR_1 = PR.ID_PROVEEDOR
						AND U.ACTIVO='1'
						AND (PP.CANTIDAD_REVISADA IS NULL OR PP.CANTIDAD_REVISADA > 0)
						AND CAST(P.LOTE AS UNSIGNED) > $loteMinimo
						AND P.FECHA_PEDIDO>'".$anyo."-01-01 00:00:00'
						AND P.FECHA_PEDIDO<='".$anyo."-12-31 23:59:59'
						GROUP BY P.LOTE, S.NOMBRE, U.ID_USUARIO, PR.NOMBRE, PO.DESCRIPCION, PP.PRECIO, PP.PRECIO_SIN_RECARGO, PP.TIPO_IVA
				  ) VENTAS_ANUAL
				) TOTAL_VENTAS_ANUAL 
				ORDER BY CAST(LOTE AS UNSIGNED), SUBGRUPO, USUARIO, PROVEEDOR, PRODUCTO";
	
	$resultado_vista = consulta($consulta);	
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
				<td colspan="13" align="center"><b>BENEFICIO ANUAL POR USUARIO</b></td>
			</tr>
			<tr bgcolor="#000066" style="color: white; border: 2px solid #CCCCCC;">
				<th>LOTE</th>
				<th>SUBGRUPO</th>
				<th>USUARIO</th>
				<th>PROVEEDOR</th>
				<th>PRODUCTO</th>
				<th align="right">CANTIDAD TOTAL ADQUIRIDA</th>
				<th align="right">PVP</th>
				<th align="right">COSTO TOTAL</th>
				<th align="right">RECARGO APLICADO</th>
				<th align="right">TOTAL SIN RECARGO</th>
				<th align="right">TIPO IVA</th>
				<th align="right">IVA</th>
				<th align="right">BENEFICIO</th>
			</tr>
		<?php
			$total = 0.00;
			$totalSinRec = 0.00;
			$beneficio = 0.00;
			
			$numFilas = numero_filas($resultado_vista);
			
			if ($numFilas==0) {
			?>
				<tr><td colspan="13">No hay resultados</td></tr>
				<?php 
			}
			 
			while ($fila = extraer_registro($resultado_vista)) {
				$total += $fila['TOTAL'];
				$totalSinRec += $fila['TOTAL_SIN_RECARGO'];
				$beneficio += $fila['BENEFICIO'];
		?>
			<tr bgcolor="white" style="color: black; border: 2px solid #CCCCCC;">
				<td><?=$fila['LOTE']?></td>
				<td><?=$fila['SUBGRUPO']?></td>
				<td><?=$fila['USUARIO']?></td>
				<td><?=$fila['PROVEEDOR']?></td>
				<td><?=$fila['PRODUCTO']?></td>
				<td><?=$fila['CANTIDAD_TOTAL']?></td>
				<td><?=$fila['PVP']?></td>
				<td align="right" nowrap><?=$fila['TOTAL']?> &euro;</td>
				<th align="right"><?=$fila['RECARGO_APLICADO']?> %</th>
				<th align="right" nowrap><?=$fila['TOTAL_SIN_RECARGO']?> &euro;</th>
				<th align="right"><?=$fila['TIPO_IVA']?></th>
				<th align="right"><?=$fila['IVA']?> &euro;</th>
				<th align="right"><?=$fila['BENEFICIO']?> &euro;</th>
			</tr>
		<?php } ?>
		<tr bgcolor="white" style="color: black; border: 0px; font-weight: bold;">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right">&nbsp;</td>
			<td align="right" style="border: 1px solid #CCCCCC;" nowrap>COSTO TOTAL</td>
			<th align="right" style="border: 1px solid #CCCCCC;">&nbsp;</th>
			<th align="right" style="border: 1px solid #CCCCCC;" nowrap>TOTAL SIN RECARGO</th>
			<th align="right" style="border: 1px solid #CCCCCC;">BENEFICIO</th>
		</tr>
		<tr bgcolor="white" style="color: red; border: 0px">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right" style="border: 1px solid #CCCCCC;" nowrap><?=$total?> &euro;</td>
			<th align="right" style="border: 1px solid #CCCCCC;">&nbsp;</th>
			<th align="right" style="border: 1px solid #CCCCCC;" nowrap><?=$totalSinRec?> &euro;</th>
			<th align="right" style="border: 1px solid #CCCCCC;"><?=$beneficio?> &euro;</th>
		</tr>
	</table>
	</td>
	</tr>
	</table>
</body>
</html>
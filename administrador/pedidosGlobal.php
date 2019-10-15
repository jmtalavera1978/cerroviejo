<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/print.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; 
    compruebaSesionAdministracion();
	$lote = @$_GET['lote'];
	if (!$lote) {
		$lote = consultarLoteActual();
	}
	$fechaActual = new DateTime();
	$fechaActual = $fechaActual->format("d/M/Y");
	$consulta = "select ID_PROVEEDOR, PROVEEDOR, SUM(TOTAL) AS TOTAL, SUM(TOTAL_SIN_RECARGO) AS TOTAL_SIN_RECARGO, SUM(BENEFICIO) AS BENEFICIO
					from 
					(
					select TOTAL_VENTAS_ANUAL.*, ROUND(TOTAL - TOTAL_SIN_RECARGO, 2) AS BENEFICIO
									from (
									  select VENTAS_ANUAL.*
										, ROUND ( (((PVP - PRECIO_SIN_RECARGO) * 100) / PRECIO_SIN_RECARGO), 0) AS  RECARGO_APLICADO
										, ROUND(CANTIDAD_TOTAL * PVP, 2) AS TOTAL
										, ROUND(CANTIDAD_TOTAL * PRECIO_SIN_RECARGO, 2) AS TOTAL_SIN_RECARGO from (
										SELECT P.LOTE, S.NOMBRE AS SUBGRUPO, U.ID_USUARIO AS USUARIO, PR.ID_PROVEEDOR, PR.NOMBRE AS PROVEEDOR, PO.DESCRIPCION AS PRODUCTO, PP.PRECIO AS PVP, PP.PRECIO_SIN_RECARGO,
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
											AND CAST(P.LOTE AS UNSIGNED) = '$lote'
											GROUP BY P.LOTE, S.NOMBRE, U.ID_USUARIO, PR.NOMBRE, PO.DESCRIPCION, PP.PRECIO, PP.PRECIO_SIN_RECARGO
									  ) VENTAS_ANUAL
									) TOTAL_VENTAS_ANUAL 
									ORDER BY CAST(LOTE AS UNSIGNED), PROVEEDOR, SUBGRUPO, USUARIO, PRODUCTO
					) SUMAS GROUP BY ID_PROVEEDOR, PROVEEDOR";
	$proveedores = consulta($consulta);
	?>
	<section>
		<div id="contenidoAdmin">
			<?php
			 if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5>".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			 } 
			?>
			<h1 class="cal">Global de Pedidos</h1>
			<div style="position:relative; top: -20px">
			<div id="listadoProductos">
				<?php 
				if (numero_filas($proveedores)==0) {
				?>
					No hay pedidos para el lote actual
				<?php 
				} else {
				?>
				<span>LOTE <?=$lote?> - Fecha: <?=$fechaActual?></span>
				<table class="tablaResultados" style="width: 100%">
					<thead>
						<tr>
							<th style="font-size:0.7em">PROVEEDOR</th>
							<th style="font-size:0.7em" align="center">COSTE TOTAL REVISADO COMPRADO POR USUARIOS</th>
							<th style="font-size:0.7em" align="center">COSTE TOTAL PEDIDO A PROVEEDOR</th>
							<th style="font-size:0.7em" align="center">DIFERENCIA</th>
							<th style="font-size:0.7em" align="center">IMPORTE COMPRADO SIN RECARGO</th>
							<th style="font-size:0.7em" align="center">BENEFICIO</th>
						</tr>
					</thead>
					<tbody>
				<?php 
					$t1 = 0;
					$t2 = 0;
					$t3 = 0;
					$t4 = 0;
					$t5 = 0;
					while ($proveedor = extraer_registro($proveedores)) {
						$idProveedor = $proveedor['ID_PROVEEDOR'];
						$nombreProveedor = $proveedor['PROVEEDOR'];
						//$descProveedor = $proveedor['DESCRIPCION'];
						$totalesPorProveedor = array ();
						
						$subgruposList = '-1000';
						$totalesPorProveedor = clasificarPedidosPorProveedorYLote ($lote, $subgruposList);
						$totalesPorProveedor = clasificarPedidosPorProveedorYLoteResto ( $totalesPorProveedor, $lote, $subgruposList );
						
						$totalCompradoPorUsuarios = $proveedor['TOTAL'];//calcularTotalRevisadoProveedorCompradoPorUsuarios($idProveedor, $totalesPorProveedor);
						$totalCompradoPorUsuariosRecargo = $proveedor['TOTAL_SIN_RECARGO'];//calcularTotalRevisadoProveedorCompradoPorUsuariosSinRecargo($idProveedor, $totalesPorProveedor);
						
						$totalRevisadoProveedor = calcularTotalRevisadoProveedor($idProveedor, $lote);
						
						$t1 += $totalCompradoPorUsuarios;
						$t2 += $totalRevisadoProveedor;
						$t3 += round($totalCompradoPorUsuarios-$totalRevisadoProveedor, 2);
						$t4 += $totalCompradoPorUsuariosRecargo;
						$t5 += round($proveedor['BENEFICIO']/*$totalCompradoPorUsuarios-*/, 2);
				?>
					<tr>
						<td><?=$nombreProveedor?></td>
						<td align="center"><?=$totalCompradoPorUsuarios?> &euro;</td>
						<td align="center" nowrap><?=$totalRevisadoProveedor?> &euro;</td>
						<td align="center"><?=round($totalCompradoPorUsuarios-$totalRevisadoProveedor, 2)?> &euro;</td>
						<td align="center"><?=$totalCompradoPorUsuariosRecargo?> &euro;</td>
						<td align="center"><?=round($proveedor['BENEFICIO']/*$totalCompradoPorUsuarios-$totalCompradoPorUsuariosRecargo*/, 2)?> &euro;</td>
					</tr>
				<?php 
					}
				?>
					<tr>
						<td><b>TOTAL:</b></td>
						<td align="center"><?=$t1?> &euro;</td>
						<td align="center" nowrap><?=$t2?> &euro;</td>
						<td align="center"><?=$t3?> &euro;</td>
						<td align="center"><?=$t4?> &euro;</td>
						<td align="center"><?=$t5?> &euro;</td>
					</tr>
				</tbody>
			</table>
				<?php 
				}
				?>
			</div>
		</div>
	</div>
	</section>
	<br/><br/>
	<div style='clear:both;'></div>
	<div id="botonera">
		<input id="impr" name="impr" type="button" onclick="$('#listadoProductos').printArea();" value="Imprimir" />
		<input id="volver" name="volver" type="button" onclick="document.location='pedidos.php'" value="Volver" />
	</div>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
</head>
<body>
	<?php
	require_once "../includes/funciones.inc.php";
	compruebaSesionRepartidorOAdministrador();
	?>	
	<div align="left" style="margin-left: 10%">
	    <script>var pfHeaderImgUrl = '';var pfHeaderTagline = '';var pfdisableClickToDel = 0;var pfHideImages = 0;var pfImageDisplayStyle = 'right';var pfDisablePDF = 0;var pfDisableEmail = 0;var pfDisablePrint = 0;var pfCustomCSS = '';var pfBtVersion='1';(function(){var js, pf;pf = document.createElement('script');pf.type = 'text/javascript';if('https:' == document.location.protocol){js='https://pf-cdn.printfriendly.com/ssl/main.js'}else{js='http://cdn.printfriendly.com/printfriendly.js'}pf.src=js;document.getElementsByTagName('head')[0].appendChild(pf)})();</script><a href="http://www.printfriendly.com" style="color:#6D9F00;text-decoration:none;" class="printfriendly" onclick="window.print();return false;" title="Printer Friendly and PDF"><img style="border:none;-webkit-box-shadow:none;box-shadow:none;" src="http://cdn.printfriendly.com/button-print-grnw20.png" alt="Print Friendly and PDF"/></a>
	    <input type="button" id="cerrar" value="Cerrar">
		<script>
			$(document).ready(function()
				{
					$("#cerrar").bind("click",function()
					{
						window.close();
					});
				});
		</script>
	</div>   
    
	<section>
	<div style="text-align: center; width: 80%; margin-left: 10%">
	<?php
		require_once "../includes/funciones.inc.php";
		
		$idPedidoProv = @$_GET['idPedidoProv'];
		$idSubgrupo = @$_GET['idSubgrupo'];
		$lote = @$_GET['lote'];
		$estado = @$_GET['estado'];
		$bnombreProv = @$_GET['proveedor'];
		
		$primero = TRUE;
		
		$consulta = "select P.*, PP.ID_PEDIDO_PROVEEDOR, PP.LOTE, PP.TOTAL_REVISADO, PP.ID_SUBGRUPO, PP.PEDIDO_PROCESADO, PP.ENVIADO from PROVEEDORES P, PEDIDOS_PROVEEDORES PP
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
		$consulta .= " ORDER BY P.NOMBRE";
		
		$proveedores = consulta($consulta);
		
		$fechaActual = new DateTime();
		$fechaActual = $fechaActual->format("dmY");
		
		while ($proveedor = extraer_registro($proveedores)) {
			$lote = $proveedor['LOTE'];
			$idProveedor = $proveedor['ID_PROVEEDOR'];
			
			$idPedidoProveedor = $proveedor['ID_PEDIDO_PROVEEDOR'];
			$nombreProveedor = $proveedor['NOMBRE'];
			$totalRevisado = $proveedor['TOTAL_REVISADO'];
			$subgrupo = $proveedor['ID_SUBGRUPO'];
			$estado = $proveedor['PEDIDO_PROCESADO'];
			
			$enviado = ($proveedor['ENVIADO'] == 1);
			
			if (!$subgrupo || $subgrupo == NULL || $subgrupo=='') {
				$subgrupo = "RESTO";
			} else {
				$subgrupo = consulta("select * from SUBGRUPOS WHERE ID_SUBGRUPO='$subgrupo'");
				$subgrupo = extraer_registro($subgrupo);
				$subgrupo = $subgrupo['NOMBRE'];
			}
	?>
	<br/>
	<img id="logoInterno" alt="logo" src="../img/cerroViejo.png"/>
	<h1>ALBARÁN DE PEDIDOS</h1>
	<table class="tablaResultados" style="width: 100%"  <?php if (!isset($proveedorSel) && !$primero) { ?>class="page-break-before" <?php } ?> >
		<thead>
			<tr>
				<th align="left">LOTE_<?=$lote?> - Fecha_<?=$fechaActual?> - PROVEEDOR_<?=$nombreProveedor?></th>
				<title>LOTE_<?=$lote?> - Fecha_<?=$fechaActual?> - PROVEEDOR_<?=$nombreProveedor?></title>
			</tr>
			<tr>
				<th align="left">SUBGRUPO: <?=$subgrupo?> - ESTADO: <?=($estado=='1'?'CERRADO':($enviado=='1'? 'ENVIADO' : 'PENDIENTE DE ENVIO'))?></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
    </table>
			<div class="listadoProductos">
				<?php 
					$primero = FALSE;
					$consulta2 = "SELECT PP.*, PR.DESCRIPCION, U.DESCRIPCION AS DESC_UNIDAD, PR.INC_CUARTOS, PR.IMPORTE_SIN_IVA, PR.TIPO_IVA
								FROM PEDIDOS_PROVEEDORES_PROD PP, PRODUCTOS PR, UNIDADES U
								WHERE PP.ID_PRODUCTO = PR.ID_PRODUCTO
								and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
								AND PP.ID_PEDIDO_PROVEEDOR=$idPedidoProveedor";
					$resProductos = consulta($consulta2);
				?>
				<table class="tablaResultados" style="width: 100%">
					<thead>
						<tr style="background-color: gray">
							<th style="background-color: gray">PRODUCTO</th>
							<th style="background-color: gray" align="center">CANTIDAD PROVEEDOR</th>
							<th style="background-color: gray" align="center">PSI (PVS)</th>
							<th style="background-color: gray">IVA</th>
							<th style="background-color: gray" align="right">SUBTOTAL</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$total = 0.00;
						$totalRev = 0.00;
						$totalSinIva = 0.00;
						
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
							<td align="left"><?=$producto['DESCRIPCION']?></td>
							<td align="right" nowrap>
								<?=round($cantidad_rev, 2)?> <?=$producto['DESC_UNIDAD']?>
							</td>
							<td align="center" nowrap="nowrap"><?=$psi." &euro; (".$producto['PRECIO']." &euro;)"?></td>
							<td align="left"><?=$TIPO_IVA?>%</td>
							<td align="right"><?=round(($psi * $cantidad_rev), 2)?> &euro;</td>
						</tr>
					<?php } ?>
						<tr>
							<td colspan="5">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4" valign="middle" align="right"><b>Total:</b></td>
							<td valign="middle" align="right" style="background-color: gray; color: white"><?=number_format($totalSinIva, 2, '.', '')?> &euro;</td>
						</tr>
						<tr>
							<td colspan="4" valign="middle" align="right"><b>Total (iva incl.):</b></td>
							<td valign="middle" align="right" style="background-color: gray; color: white"><?=$totalRevisado==NULL ? number_format($totalRev, 2, '.', '') : $totalRevisado?> &euro;</td>
						</tr>
					</tbody>
				</table>
			</div>
	<?php 
		}	
	?>
	</section>
</body>
</html>
<?php
// CONSULTA DE LISTADO DE PROVEEDORES
$consulta = "select P.*, PP.ID_PEDIDO_PROVEEDOR, PP.TOTAL_REVISADO, S.NOMBRE AS SUBGRUPO, PP.ENVIADO, PP.PEDIDO_PROCESADO
					from PROVEEDORES P, PEDIDOS_PROVEEDORES PP, SUBGRUPOS S
					WHERE P.ID_PROVEEDOR = PP.ID_PROVEEDOR
					AND PP.LOTE='$lote'
					AND PP.ID_SUBGRUPO IS NOT NULL
					AND PP.ID_SUBGRUPO<>0
					AND PP.ID_SUBGRUPO = S.ID_SUBGRUPO";
if (isset($bnombreProv) && strlen(@$bnombreProv)>0) {
	$consulta .= " AND P.NOMBRE like '%$bnombreProv%'";
}
if (isset($idSubgrupo) && $idSubgrupo!='-1000') {
	$consulta .= " AND PP.ID_SUBGRUPO = '$idSubgrupo'";
}
if ($estadoPed=='1') {
	$consulta .= " AND PP.PEDIDO_PROCESADO = '1'";
} else if ($estadoPed=='2') {
	$consulta .= " AND PP.PEDIDO_PROCESADO = '0' AND PP.ENVIADO = '1'";
} else if ($estadoPed=='0') {
	$consulta .= " AND PP.PEDIDO_PROCESADO = '0' AND PP.ENVIADO = '0'";
}
$consulta .= " UNION ALL 
			select P.*, PP.ID_PEDIDO_PROVEEDOR, PP.TOTAL_REVISADO, 'RESTO' AS SUBGRUPO, PP.ENVIADO, PP.PEDIDO_PROCESADO
					from PROVEEDORES P, PEDIDOS_PROVEEDORES PP
					WHERE P.ID_PROVEEDOR = PP.ID_PROVEEDOR
					AND PP.LOTE='$lote'
					AND (PP.ID_SUBGRUPO IS NULL OR PP.ID_SUBGRUPO=0)";
if (isset($bnombreProv) && strlen(@$bnombreProv)>0) {
	$consulta .= " AND P.NOMBRE like '%$bnombreProv%'";
}
if (isset($idSubgrupo) && $idSubgrupo!='-1000' && $idSubgrupo!='') {
	$consulta .= " AND PP.ID_SUBGRUPO = '$idSubgrupo'";
}
if ($estadoPed=='1') {
	$consulta .= " AND PP.PEDIDO_PROCESADO = '1'";
} else if ($estadoPed=='2') {
	$consulta .= " AND PP.PEDIDO_PROCESADO = '0' AND PP.ENVIADO = '1'";
} else if ($estadoPed=='0') {
	$consulta .= " AND PP.PEDIDO_PROCESADO = '0' AND PP.ENVIADO = '0'";
}
$consulta .= " ORDER BY NOMBRE, SUBGRUPO ";

//echo $consulta;
$proveedores = consulta ( $consulta );

// ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA
$rows_per_page = consultarPaginacion ();

if (($numrows = numero_filas ( $proveedores )) > 0) {
	// AL PRINCIPIO COMPRUEBO SI HICIERON CLICK EN ALGUNA PÁGINA
	if (isset ( $_GET ['page'] )) {
		$page = $_GET ['page'];
	} else {
		// SI NO DIGO Q ES LA PRIMERA PÁGINA
		$page = 1;
	}
	
	// CALCULO LA ULTIMA PÁGINA
	$lastpage = ceil ( $numrows / $rows_per_page );
	
	// COMPRUEBO QUE EL VALOR DE LA PÁGINA SEA CORRECTO Y SI ES LA ULTIMA PÁGINA
	$page = ( int ) $page;
	
	if ($page > $lastpage) {
		$page = $lastpage;
	}
	
	if ($page < 1) {
		$page = 1;
	}
	
	// CREO LA SENTENCIA LIMIT PARA AÑADIR A LA CONSULTA QUE DEFINITIVA
	$limit = 'LIMIT ' . ($page - 1) * $rows_per_page . ',' . $rows_per_page;
	
	// REALIZO LA CONSULTA QUE VA A MOSTRAR LOS DATOS (ES LA ANTERIO + EL $limit)
	$consulta .= " $limit";
	$proveedores = consulta ( $consulta );
}

if (numero_filas ( $proveedores ) > 0) {
	?>
<div id="listadoProductos">
	<table class="tablaResultados" style="width: 100%">
		<thead>
			<tr>
				<th>NOMBRE</th>
				<th>SUBGRUPO</th>
				<th>ESTADO</th>
				<th align="right">IMP. ACUM. USU.</th>
				<th align="right">IMP. ACUM. REV. ADMIN</th>
				<th align="right">IMP. TOTAL PROV. REV. ADMIN</th>
				<th align="center">&nbsp;</th>
				<th align="center">&nbsp;</th>
				<th align="center">&nbsp;<th>
						
			
			</tr>
					</thead>
				<tbody>
				<?php
	while ( $prov = extraer_registro ( $proveedores ) ) {
		$total = 0;
		$totalRev = 0;
		$consulta2 = "SELECT PP.*
							FROM PEDIDOS_PROVEEDORES_PROD PP
							WHERE PP.ID_PEDIDO_PROVEEDOR='" . $prov ['ID_PEDIDO_PROVEEDOR'] . "'";
		$resProductos = consulta ( $consulta2 );
		
		$enviado = ($prov['ENVIADO'] == 1);
		
		while ( $producto = extraer_registro ( $resProductos ) ) {
			$cantidad = $producto ['CANTIDAD'];
			$cantidad_rev = $producto ['CANTIDAD_REV'];
			if ($cantidad_rev == NULL) {
				$cantidad_rev = $cantidad;
			}
			$total += round ( ($producto ['PRECIO'] * $cantidad), 2 );
			$subtotal = round ( ($producto ['PRECIO'] * $cantidad_rev), 2 );
			$totalRev += $subtotal;
			
			$totalUsuarios = 0;
			$totalUsuariosRev = 0;
			
			$estado = $prov ['PEDIDO_PROCESADO'] == 0 ? 'Pendiente' : 'Cerrado';
		}

		$totalTodosRev = calcularTotalRevisadoProveedor($prov ['ID_PROVEEDOR'], $lote);
		
		?>
							<tr>
								<td align="center"><?=$prov['NOMBRE']?></td>
								<td align="center"><?=$prov['SUBGRUPO']?></td>
								<td align="center"><?=$estado?></td>
								<td align="right"><?=$total?> &euro;&nbsp;</td>
								<td align="right"><?=$totalRev?> &euro;&nbsp;</td>
								<td align="right"><?=$totalTodosRev?> &euro;&nbsp;</td>
								<td align="center">
									<input style="font-size: x-small; padding-left: 5px; padding-right: 5px;" type="button" 
										onclick="document.location='detallePedidosProv.php?idPedidoProv=<?=$prov ['ID_PEDIDO_PROVEEDOR']?>&consulta=true'" 
										value="VER" />
								</td>
								<td align="center">
									<input style="font-size: x-small; padding-left: 5px; padding-right: 5px;" type="button" 
										onclick="window.open('imprimirAlbaran.php?idPedidoProv=<?=$prov ['ID_PEDIDO_PROVEEDOR']?>', 'impresionAlbaran', '');" 
										value="GENERAR" />
								</td>
								<td align="center" id="columna<?=$prov ['ID_PEDIDO_PROVEEDOR']?>">
								
								<?php if (!$enviado) { ?>
									<input style="font-size: x-small; padding-left: 5px; padding-right: 5px; margin-bottom: 5px;" id="enviado<?=$idProveedor?>" name="enviado<?=$idProveedor?>" type="button" onclick="if (confirm ('¿Desea Marcar como Enviado?')) document.location='pedidos_proveedor_marcar2.php?idPedidoProv=<?=$prov ['ID_PEDIDO_PROVEEDOR']?>';" value="MARCAR ENVIADO" />
								<?php } ?>
								<?php if ($prov['PEDIDO_PROCESADO'] == 0) { ?>
									<input style="font-size: x-small; padding-left: 5px; padding-right: 5px; <?=($enviado ? 'background: url(../css/custom-theme/images/ui-bg_inset-soft_15_2b2922_1x100.png) repeat-x scroll 50% 50% #459E00;' : '')?>" type="button" 
										onclick="document.location='detallePedidosProv.php?idPedidoProv=<?=$prov ['ID_PEDIDO_PROVEEDOR']?>&consulta=false'" 
										value="<?=($enviado ? 'MODIFICAR ENVIADO' : 'MODIFICAR')?>" />
								<?php } else { 
									echo "<span style=\"font-size: small\">CONTABILIZADO</span>"; 
								} ?>
								</td>
								</tr>
						<?php } ?>
						</tbody>
					</table>
					
					<div id="dialogConfirmDelete" title="">
						¿Desea eliminar el pedido seleccionado?</div>
						
					<script>		
						var idPedidoSel;  
						function openConfirmacion(idPedido) {
							idPedidoSel = idPedido;
							$("#dialogConfirmDelete").dialog("open");
						}
						
						$("#dialogConfirmDelete").dialog({
					      autoOpen: false,
						  height: 250,
						  width: 400,
						  modal: true,
					      buttons : {
					          "Sí" : function() {
					        	  $(this).dialog("close");
					        	  document.location='eliminarPedidoUsuario.php?idPedido='+idPedidoSel+'&url='+document.location;
					          },
					          "Cancelar" : function() {
						          $(this).dialog("close");
					          }
					        }
					    });
					</script>
					
					<div style="clear: both;"></div>
				</div>
<?php
}

// UNA VEZ Q MUESTRO LOS DATOS TENGO Q MOSTRAR EL BLOQUE DE PAGINACIÓN SIEMPRE Y CUANDO HAYA MÁS DE UNA PÁGINA
echo "<div style='clear:both;'></div>";

if (@$numrows != 0) {
	$nextpage = $page + 1;
	$prevpage = $page - 1;
	
	?><div class="grid-12 grid paginationCV" style="position:relative; margin-top: 10px; margin-bottom: 10px"><ul><?php
	// SI ES LA PRIMERA PÁGINA DESHABILITO EL BOTON DE PREVIOUS, MUESTRO EL 1 COMO ACTIVO Y MUESTRO EL RESTO DE PÁGINAS
	if ($page == 1) {
		?>
								              <li class="previous-off">&larr;</li>
								              <li class="active">1</li> 
								         <?php
		for($i = $page + 1; $i <= $lastpage; $i ++) {
			?>
								                <li><a href="pedidos.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
								        <?php
		
}
		
		// Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
		if ($lastpage > $page) {
			?>      
								                <li class="next"><a href="pedidos.php?page=<?php echo $nextpage;?>" >&rarr;</a></li><?php
		} else {
			?>
								                <li class="next-off">&rarr;</li>
								        <?php
		}
	} else {
		
		// EN CAMBIO SI NO ESTAMOS EN LA PÁGINA UNO HABILITO EL BOTON DE PREVIUS Y MUESTRO LAS DEMÁS
		?>
								            <li class="previous"><a href="pedidos.php?page=<?php echo $prevpage;?>">&larr;</a></li><?php
		for($i = 1; $i <= $lastpage; $i ++) {
			// COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
			if ($page == $i) {
				?>       <li class="active"><?php echo $i;?></li><?php
			} else {
				?>       <li><a href="pedidos.php?page=<?php echo $i;?>" ><?php echo $i;?></a></li><?php
			}
		}
		// Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT
		if ($lastpage > $page) {
			?>   
								                <li class="next"><a href="pedidos.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
		} else {
			?>       <li class="next-off">&rarr;</li><?php
		}
	}
	?></ul></div><?php
								    } 
?>
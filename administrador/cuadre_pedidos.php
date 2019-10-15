<?php
	/* 
	 * Nueva opción del menú pedidos. Lo que hace es cuadrar para un lote seleccionado y un proveedor seleccionado, todos los productos que se 
	 * le han pedido en ese lote a ese proveedor con los que ya se han revisado en los usuarios (pedido finalizado). Para entendernos, esto lo que
	 *  busca es poder ver si el proveedor ha servido de menos una vez revisadas todas las cestas. Por desgracia pasa en ocasiones y hay que reclarmar
	 *   al proveedor y obliga a ir sumando cesta a cesta. De esta forma, se podría ver lo que se ha puesto en todas las cestas de productos de ese
	 *    proveedor y cuadrarlo con el pedido orignalmente a este. Se debería obtener un listado con los siguientes campos:
	 *    
	 * PRODUCTO | CANTIDAD PROVEEDOR | UNIDAD | CANTIDAD SERVIDA (CANTIDAD REVISADA DE TODOS LOS USUARIOS DE ESE LOTE, PROVEEDOR Y PRODUCTO)
	 *  |DIFERENCIA DE UNIDADES | DIFERENCIA EN IMPORTE (DIFERENCIA * PRECIO SIN RECARGO)
	 */
	compruebaSesionAdministracion();
	
	$lote = consultarLoteActual();
	if (isset($_GET['lote'])) {
		$lote = $_GET['lote'];
		$_SESSION['loteSelCP'] = $lote;
	} else if (isset($_SESSION['loteSelCP'])) {
		$lote = $_SESSION['loteSelCP'];
	}
	
	$idProveedor = @$_GET['idProveedor'];
	if (isset($idProveedor)) {
		if (strlen($idProveedor)==0) {
			$_SESSION['idProveedorCP'] = NULL;
			$idProveedor = NULL;
		} else {
			$_SESSION['idProveedorCP'] = $idProveedor;
		}
	} else {
		$idProveedor = @$_SESSION['idProveedorCP'];
	}
	
	echo "<div style=\"position:relative; top: -20px\">";
	?>
	<div id="tituloProveedores" style="position:relative; top: -20px">
	<span>
		&nbsp;&nbsp;&nbsp;LOTE:&nbsp;
		<select id="lote" name="lote" onchange="document.location='pedidos.php?lote='+this.value">
			<?=optionsLotes($lote)?>
		</select>
		&nbsp;&nbsp;&nbsp;PROVEEDOR:&nbsp;
		<select id="idProveedor" name="idProveedor"  onchange="document.location='pedidos.php?idProveedor='+this.value">
			<option value="">Selecciona un proveedor...</option>
			<?php optionsProveedores ($idProveedor); ?>
		</select>
	</span>
	</div>
	
	<div id="tituloProveedores">
		<div id="listadoProductos">
			<?php 
				$consulta2 = "SELECT P.ID_PRODUCTO, P.DESCRIPCION AS PRODUCTO
					, IF(PPP.CANTIDAD_REV IS NOT NULL, PPP.CANTIDAD_REV, PPP.CANTIDAD) AS CANTIDAD_PROVEEDOR 
					, U.DESCRIPCION AS UNIDAD
					, PPP.PRECIO AS PRECIO_SIN_RECARGO
					FROM PEDIDOS_PROVEEDORES PP, PEDIDOS_PROVEEDORES_PROD PPP, PRODUCTOS P, UNIDADES U
					WHERE PP.ID_PEDIDO_PROVEEDOR = PPP.ID_PEDIDO_PROVEEDOR
					AND PPP.ID_PRODUCTO = P.ID_PRODUCTO
					AND P.UNIDAD_MEDIDA = U.ID_UNIDAD
					AND P.ID_SUBCATEGORIA<>'-10'
					AND PP.LOTE = '$lote'
					AND PP.ID_PROVEEDOR = '$idProveedor'";
				$resProductos = consulta($consulta2);
			?>
			<table class="tablaResultados" style="width: 100%">
				<thead>
					<tr>
						<th>PRODUCTO</th>
						<th align="center">CANTIDAD PROVEEDOR</th>
						<th align="center">PRECIO SIN RECARGO</th>
						<th align="center">COSTE SIN RECARGO</th>
						<th align="center">CANTIDAD USUARIOS</th>
						<th align="center">DIFERENCIA CANTIDAD</th>
						<th align="center">DIFERENCIA COSTE</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$total = 0.00;
					$totalRev = 0.00;
					
					$numProductos = numero_filas($resProductos);
					
					if ($numProductos==0) {
					?>
						<tr><td colspan="7">No ha pedidos para este proveedor</td></tr>
						<?php 
					}
					 
					while ($producto = extraer_registro($resProductos)) {
						$idProductoActual = $producto['ID_PRODUCTO'];
						$cantidad_rev = $producto['CANTIDAD_PROVEEDOR'];
						$subtotal = round(($producto['PRECIO_SIN_RECARGO'] * $cantidad_rev), 2);
						$pedido_usuario = calculaPedidosPorProveedorProductoYLote($lote, $idProveedor, $idProductoActual);
						$precio_usuario = $pedido_usuario[1];
						$pedido_usuario = $pedido_usuario[0];
				?>
					<tr>
						<td><?=$producto['PRODUCTO']?></td>
						<td align="center" nowrap>
							<?=round($cantidad_rev, 2)?> <?=$producto['UNIDAD']?>
						</td>
						<td align="center"><?=$producto['PRECIO_SIN_RECARGO']?> &euro;/<?=$producto['UNIDAD']?></td>
						<td align="center"><?=$subtotal?> &euro;</td>
						<td align="center">
							<?=round($pedido_usuario, 2)?> <?=$producto['UNIDAD']?>
						</td>
						<td align="center"><?=round(($pedido_usuario - $cantidad_rev), 2)?> <?=$producto['UNIDAD']?></td>
						<td align="center"><?=round((($pedido_usuario*$precio_usuario) - $subtotal), 2)?> &euro;</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			
		
		<?php 
	echo "</div>";
	
	//UNA VEZ Q MUESTRO LOS DATOS TENGO Q MOSTRAR EL BLOQUE DE PAGINACIÓN SIEMPRE Y CUANDO HAYA MÁS DE UNA PÁGINA
	echo "<div style='clear:both;'></div>";
		
	if(@$numrows != 0)
	{
		$nextpage= $page +1;
		$prevpage= $page -1;
			
		?><div class="grid-12 grid paginationCV" style="position:relative; margin-top: 10px; margin-bottom: 10px"><ul><?php
           //SI ES LA PRIMERA PÁGINA DESHABILITO EL BOTON DE PREVIOUS, MUESTRO EL 1 COMO ACTIVO Y MUESTRO EL RESTO DE PÁGINAS
           if ($page == 1) 
           {
            ?>
              <li class="previous-off">&larr;</li>
              <li class="active">1</li> 
         <?php
              for($i= $page+1; $i<= $lastpage ; $i++)
              {?>
                <li><a href="pedidos.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
        <?php }
           
           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
            if($lastpage >$page )
            {?>      
                <li class="next"><a href="pedidos.php?page=<?php echo $nextpage;?>" >&rarr;</a></li><?php
            }
            else
            {?>
                <li class="next-off">&rarr;</li>
        <?php
            }
        } 
        else
        {
     
            //EN CAMBIO SI NO ESTAMOS EN LA PÁGINA UNO HABILITO EL BOTON DE PREVIUS Y MUESTRO LAS DEMÁS
        ?>
            <li class="previous"><a href="pedidos.php?page=<?php echo $prevpage;?>">&larr;</a></li><?php
             for($i= 1; $i<= $lastpage ; $i++)
             {
                           //COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
                if($page == $i)
                {
            ?>       <li class="active"><?php echo $i;?></li><?php
                }
                else
                {
            ?>       <li><a href="pedidos.php?page=<?php echo $i;?>" ><?php echo $i;?></a></li><?php
                }
            }
             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
            if($lastpage >$page )
            {   ?>   
                <li class="next"><a href="pedidos.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
            }
            else
            {
        ?>       <li class="next-off">&rarr;</li><?php
            }
        }     
    ?></ul></div><?php
    } 
?>

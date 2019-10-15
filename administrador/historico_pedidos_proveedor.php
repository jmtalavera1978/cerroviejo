<?php
	compruebaSesionAdministracion();
	$generado = true;
	
	$lote = consultarLoteActual();
	if (isset($_GET['lote'])) {
		$lote = $_GET['lote'];
		$_SESSION['loteSel'] = $lote;
	} else if (isset($_SESSION['loteSel'])) {
		$lote = $_SESSION['loteSel'];
	}
	
	echo "<div style=\"position:relative; top: -20px\">";
	?>
	<div id="tituloProveedores" style="position:relative; top: -20px">
	<span>
		&nbsp;&nbsp;&nbsp;LOTE:&nbsp;
		<select id="lote" name="lote" onchange="document.location='pedidos.php?lote='+this.value">
			<?=optionsLotes($lote)?>
		</select>
	</span>
	</div>
	<?php 
	$consulta = "select P.*, PP.ID_PEDIDO_PROVEEDOR, PP.TOTAL_REVISADO from PROVEEDORES P, PEDIDOS_PROVEEDORES PP
					WHERE P.ID_PROVEEDOR = PP.ID_PROVEEDOR
					AND PP.LOTE='$lote'";
	$proveedores = consulta($consulta);

	if (numero_filas($proveedores)==0) {
		?>
			No hay pedidos pendientes
		<?php 
	} else {
	
	//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA
	$rows_per_page= consultarPaginacionProveedores();
	
	if (($numrows = numero_filas($proveedores))>0) {
		//AL PRINCIPIO COMPRUEBO SI HICIERON CLICK EN ALGUNA PÁGINA
		if(isset($_GET['page']))
		{
			$page= $_GET['page'];
		}
		else
		{
			//SI NO DIGO Q ES LA PRIMERA PÁGINA
			$page=1;
		}
			
		//CALCULO LA ULTIMA PÁGINA
		$lastpage= ceil($numrows / $rows_per_page);
			
		//COMPRUEBO QUE EL VALOR DE LA PÁGINA SEA CORRECTO Y SI ES LA ULTIMA PÁGINA
		$page=(int)$page;
			
		if($page > $lastpage)
		{
			$page= $lastpage;
		}
			
		if($page < 1)
		{
			$page=1;
		}
			
		//CREO LA SENTENCIA LIMIT PARA AÑADIR A LA CONSULTA QUE DEFINITIVA
		$limit= 'LIMIT '. ($page -1) * $rows_per_page . ',' .$rows_per_page;
			
		//REALIZO LA CONSULTA QUE VA A MOSTRAR LOS DATOS (ES LA ANTERIO + EL $limit)
		$consulta .=" $limit";
		$proveedores=consulta($consulta);
	}

	while ($proveedor = extraer_registro($proveedores)) {
		$idProveedor = $proveedor['ID_PROVEEDOR'];
		$idPedidoProveedor = $proveedor['ID_PEDIDO_PROVEEDOR'];
		$nombreProveedor = $proveedor['NOMBRE'];
		$descProveedor = $proveedor['DESCRIPCION'];
		$totalRevisado = $proveedor['TOTAL_REVISADO'];
		?>
		
		<div id="tituloProveedores">
		<span>&nbsp;PROVEEDOR <?=$nombreProveedor?> (<?=$descProveedor?>)</span>
		</div>
		<div id="listadoProductos">
			<?php 
				$consulta2 = "SELECT PP.*, PR.DESCRIPCION, U.DESCRIPCION AS DESC_UNIDAD, PR.INC_CUARTOS
								FROM PEDIDOS_PROVEEDORES_PROD PP, PRODUCTOS PR, UNIDADES U
								WHERE PP.ID_PRODUCTO = PR.ID_PRODUCTO
								and PR.UNIDAD_MEDIDA = U.ID_UNIDAD
								AND PP.ID_PEDIDO_PROVEEDOR=$idPedidoProveedor";
				$resProductos = consulta($consulta2);
			?>
			<table style="width: 100%">
			<tr><td width="80%">
			<table class="tablaResultados" style="width: 100%">
				<thead>
					<tr>
						<th>PRODUCTO</th>
						<th align="center">PRECIO</th>
						<th align="center">CANTIDAD</th>
						<th align="center">A PEDIR</th>
						<th align="center">SUBTOTAL<th>
					</tr>
				</thead>
				<tbody>
				<?php
					$total = 0.00;
					$totalRev = 0.00;
					
					$numProductos = numero_filas($resProductos);
					
					if ($numProductos==0) {
					?>
						<tr><td colspan="4">No ha pedidos para este proveedor</td></tr>
						<?php 
					}
					 
					while ($producto = extraer_registro($resProductos)) {
						$cantidad = $producto['CANTIDAD'];
						$idProductoActual = $producto['ID_PRODUCTO'];
						$cantidad_rev = $producto['CANTIDAD_REV'];
						if ($cantidad_rev == NULL) {
							$cantidad_rev = $cantidad;
						}
						$subtotal = round(($producto['PRECIO'] * $cantidad_rev), 2);
						$total += round(($producto['PRECIO'] * $cantidad), 2);
						$totalRev +=$subtotal;
				?>
					<tr>
						<td><?=$producto['DESCRIPCION']?></td>
						<td align="center"><?=$producto['PRECIO']?> &euro;</td>
						<td align="center"><?=$cantidad?> <?=$producto['DESC_UNIDAD']?></td>
						<td align="right" nowrap>
							<?=round($cantidad_rev, 2)?> <?=$producto['DESC_UNIDAD']?>
						</td>
						<td align="center"><?=number_format($subtotal, 2, '.', '')?> &euro;</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			</td><td width="80%" valign="top">
			<?php if ($numProductos>0) { ?>
			<table style="width: 100%">
				<tr>
					<td>&nbsp;</td>
					<td valign="middle" align="right">
						<a href="imprimir_preveedor.php?idProveedor=<?=$idProveedor?>&lote=<?=$lote?>" target="_blank">
							<img alt="Imprimir" title="Imprimir" src="../img/impresora.png" />
						</a>
					</td>
				</tr>
				<tr>
					<td valign="middle" align="right" nowrap><b>Total</b></td>
					<td valign="middle" align="right" nowrap><input id="total<?=$idProveedor?>" class="total" style="background-color: #459e00"  type="text" readonly="readonly" contenteditable="false" value="<?=number_format($total, 2, '.', '')?>"/> &euro;</td>
				</tr>
				<tr>
					<td valign="middle" align="right" nowrap><b>Revisado</b></td>
					<td valign="middle" align="right" nowrap><input id="totalRev" type="text" class="total" style="background-color: white; color: black" size="8" readonly="readonly" contenteditable="false" value="<?=$totalRevisado==NULL ? number_format($totalRev, 2, '.', '') : $totalRevisado?>"/>  &euro;</td>
				</tr>
			</table>
			<?php } ?>
			</td>
			</tr>
			</table>
			<br/><br/>
			<div id="dialogConfirmPedidoProveedor" title="">
				¿Desea finalizar el pedido del proveedor? <br/>Se generará un apunte contable con la cantidad indicada en total revisado.</div>
			<script>
				var idProveedorActual;
				var nombreActual;
				var loteActual;
				
				$(function() {
				    $( "input[type=button]" )
				      .click(function( event ) {
				        event.preventDefault();
				      });
				  });
				  
				function openConfirmacionPedidoProveedor(idProveedor, nombre, lote) {
					idProveedorActual = idProveedor;
					nombreActual=nombre;
					loteActual=lote;
					$("#dialogConfirmPedidoProveedor").dialog("open");
				}
				
				$("#dialogConfirmPedidoProveedor").dialog({
			      autoOpen: false,
				  height: 250,
				  width: 600,
				  modal: true,
			      buttons : {
			          "Sí" : function() {
			        	  finalizarPedidoProveedor(idProveedorActual, nombreActual, loteActual);
				          $(this).dialog("close");
			          },
			          "No" : function() {
				          $(this).dialog("close");
			          }
			        }
			    });
			</script>
		</div>
		
		<?php 
	}
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
			<?php } 
	
?>
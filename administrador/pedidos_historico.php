<?php
	$idUsuario = NULL;
	$idCategoria = NULL;
	$idProducto = NULL;
	$fechaInicio = NULL;
	$fechaFin = NULL;
	
	if (isset($_GET['idUsuario'])) {
		$idUsuario = $_GET['idUsuario'];
		$_SESSION['idUsuarioSelH'] = $idUsuario;
	} else if (isset($_SESSION['idUsuarioSelH'])) {
		$idUsuario = $_SESSION['idUsuarioSelH'];
	}
	if (isset($_GET['lote'])) {
		$lote = $_GET['lote'];
		$_SESSION['loteSel'] = $lote;
	} else if (isset($_SESSION['loteSel'])) {
		$lote = $_SESSION['loteSel'];
	}
	
	if (isset($_GET['idCategoria'])) {
		$idCategoria = $_GET['idCategoria'];
		$_SESSION['idCategoriaSel'] = $idCategoria;
	} else if (isset($_SESSION['idCategoriaSel'])) {
		$idCategoria = $_SESSION['idCategoriaSel'];
	}
	
	if (!$idCategoria) {
		$_SESSION['idProductoSel'] = NULL;
	}
	
	if (isset($_GET['idProducto'])) {
		$idProducto = $_GET['idProducto'];
		$_SESSION['idProductoSel'] = $idProducto;
	} else if (isset($_SESSION['idProductoSel'])) {
		$idProducto = $_SESSION['idProductoSel'];
	}
	
	if (isset($_GET['fechaInicio'])) {
		$fechaInicio = $_GET['fechaInicio'];
		$_SESSION['fechaInicioSel'] = $fechaInicio;
	} else if (isset($_SESSION['fechaInicioSel'])) {
		$fechaInicio = $_SESSION['fechaInicioSel'];
	}
	
	if (isset($_GET['fechaFin'])) {
		$fechaFin = $_GET['fechaFin'];
		$_SESSION['fechaFinSel'] = $fechaFin;
	} else if (isset($_SESSION['fechaFinSel'])) {
		$fechaFin = $_SESSION['fechaFinSel'];
	}
	
	$consulta = '';
	// Consulta pedidos segun criterios
	if ($idUsuario && $fechaInicio && $fechaFin) {
		$fechaInicioF = date_create_from_format("d/m/Y", $fechaInicio);
		$fechaFinF = date_create_from_format("d/m/Y", $fechaFin);
		if (!$idCategoria && !$idProducto && !$lote) {
			$consulta = "select P.* from PEDIDOS P where P.ID_USUARIO='$idUsuario' and P.FECHA_PEDIDO BETWEEN '".$fechaInicioF->format("Y-m-d")."' and '".$fechaFinF->format("Y-m-d")."' order by P.fecha_pedido desc";
			$pedidos = consulta($consulta);
		} else {
			$andcategoria = '';
			$andproducto = '';
			$andlote = '';
			
			if ($idCategoria) {
				$andcategoria = " and PR.ID_CATEGORIA='$idCategoria' ";
			}
			if ($idProducto) {
				$andcategoria = " and PR.ID_PRODUCTO='$idProducto' ";
			}
			if (isset($lote) && $lote) {
				$andlote = " and P.LOTE='$lote' ";
			}
			$consulta = "select distinct P.* from PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR where  P.ID_PEDIDO=PP.ID_PEDIDO and PP.ID_PRODUCTO=PR.ID_PRODUCTO $andlote $andcategoria $andproducto and P.ID_USUARIO='$idUsuario' and P.FECHA_PEDIDO BETWEEN '".$fechaInicioF->format("Y-m-d")."' and '".$fechaFinF->format("Y-m-d")."' order by P.fecha_pedido desc";
			$pedidos = consulta($consulta);
		}
	} else if ($fechaInicio && $fechaFin) {
		$fechaInicioF = date_create_from_format("d/m/Y", $fechaInicio);
		$fechaFinF = date_create_from_format("d/m/Y", $fechaFin);
		if (!$idCategoria && !$idProducto) {
			$consulta = "select P.* from PEDIDOS P where P.FECHA_PEDIDO BETWEEN '".$fechaInicioF->format("Y-m-d")."' and '".$fechaFinF->format("Y-m-d")."'";
			if (isset($lote) && $lote) {
				$consulta .= " and P.LOTE='$lote' ";
			}
			$consulta .= "   order by P.fecha_pedido desc";
			$pedidos = consulta($consulta);
		} else {
			$andcategoria = '';
			$andproducto = '';
			$andlote = '';
			
			if ($idCategoria) {
				$andcategoria = " and PR.ID_CATEGORIA='$idCategoria' ";
			}
			if ($idProducto) {
				$andcategoria = " and PR.ID_PRODUCTO='$idProducto' ";
			}
			if ($lote) {
				$andlote = " and P.LOTE='$lote' ";
			}
			$consulta = "select distinct P.* from PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR where P.ID_PEDIDO=PP.ID_PEDIDO and PP.ID_PRODUCTO=PR.ID_PRODUCTO $andlote $andcategoria $andproducto and P.FECHA_PEDIDO BETWEEN '".$fechaInicioF->format("Y-m-d")."' and '".$fechaFinF->format("Y-m-d")."' order by P.fecha_pedido desc";
			$pedidos = consulta($consulta);
		}
	}
	
	echo "<div style=\"position:relative; top: -20px\">";
	?>
	<div id="tituloProveedores">
	<span>&nbsp;USUARIO:&nbsp;
		<select id="idUsuario" name="idUsuario" onchange="document.location='pedidos.php?idUsuario='+this.value">
			<option value="">Seleccione un usuario...</option>
			<?=optionsUsuariosActivos($idUsuario)?>
		</select>&nbsp;&nbsp;&nbsp;LOTE:&nbsp;
		<select id="lote" name="lote" onchange="document.location='pedidos.php?lote='+this.value">
			<option value="">Seleccione un lote...</option>
			<?=optionsLotes($lote)?>
		</select>
	</span>
	</div>
	<div id="tituloProveedores">
	<span>
		&nbsp;FECHA INICIO:&nbsp;
		<input type="text" id="fechaInicio" name="fechaInicio" required="required" onchange="document.location='pedidos.php?fechaInicio='+this.value" size="10" value="<?=$fechaInicio?>" />
		&nbsp;&nbsp;&nbsp;FECHA FIN:&nbsp;
		<input type="text" id="fechaFin" name="fechaFin" required="required" onchange="document.location='pedidos.php?fechaFin='+this.value" size="10" value="<?=$fechaFin?>" />
		<script>
		  $(function() {
		    $( "#fechaInicio" ).datepicker({
	    	  dateFormat:'dd/mm/yy'
	    	});
		  });
		  $(function() {
		    $( "#fechaFin" ).datepicker({
	    	  dateFormat:'dd/mm/yy'
	    	});
		  });
		</script>
	</span>
	</div>
	
	<div id="tituloProveedores">
	<span>&nbsp;CATEGOR&Iacute;AS:&nbsp;
		<select name="idCategoria" id="idCategoria" onchange="document.location='pedidos.php?idCategoria='+this.value">
		<option value="">Seleccione una categor&iacute;a...</option>
		<?php optionsCategorias($idCategoria); ?>
		</select>
		&nbsp;&nbsp;&nbsp;PRODUCTOS:&nbsp;
		<select name="idProducto" id="idProducto" onchange="document.location='pedidos.php?idProducto='+this.value">
		<option value="">Seleccione un producto...</option>
		<?php optionsProductos($idCategoria, $idProducto); ?>
		</select>
	</span>
	</div>
	<?php 
	
	if (isset($pedidos)) {
	
	if (numero_filas($pedidos)==0) {
		?>
			<br/>No hay pedidos con los criterios seleccionados.
		<?php 
	} else {
		//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA , EN EL EJEMPLO PONGO 15
		$rows_per_page= consultarPaginacion ();
		
		if (($numrows = numero_filas($pedidos))>0) {
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
			$pedidos=consulta($consulta);
		}
		?>
		
		<div id="listadoProductos">
			<table class="tablaResultados">
				<thead>
					<tr>
						<th>PEDIDO</th>
						<th align="center">FECHA</th>
						<th align="center">ESTADO</th>
						<th align="center">TOTAL</th>
						<th align="center">REVISADO</th>
						<th align="center">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php
					// Consulta de pedidos
					if (numero_filas($pedidos)==0) {
?>
					<tr>
						<td colspan="3">No hay pedidos</td>
					</tr>
<?php 
					} else {
						while ($filaPed = extraer_registro($pedidos)) {
							$date = new DateTime($filaPed['FECHA_PEDIDO']);
?>
					<tr>
						<td><?=$filaPed['ID_USUARIO']?>_LOTE<?=$filaPed['LOTE']?></td>
						<td align="center"><?=$date->format('d/m/Y')?></td>
						<td align="center"><?=$filaPed['ESTADO']?></td>
						<td align="center">
							<?php 
								$total = 0;
								$totalRevisado = 0;
								$resProductos = consulta("select * from PEDIDOS_PRODUCTOS where ID_PEDIDO='".$filaPed['ID_PEDIDO']."'");
								while ($filaProd = extraer_registro($resProductos)) {
									$cantidad = $filaProd['CANTIDAD'];
									$total += round(($cantidad * $filaProd['PRECIO']), 2);
									if ($filaProd['CANTIDAD_REVISADA']!=NULL && $filaProd['CANTIDAD_REVISADA']>0) {
										$cantidad = $filaProd['CANTIDAD_REVISADA'];
									}
									$totalRevisado += round(($cantidad * $filaProd['PRECIO']), 2);
								}  
								
								echo $total;
							?> &euro;
						</td>
						<td align="center"><?=$totalRevisado;?> &euro;</td>
						<td align="center"><a title="Ver Pedido" href="verPedido.php?idPedido=<?=$filaPed['ID_PEDIDO']?>"><img src="../img/INFO.png" alt="factura" width="32"/></a></td>
					</tr>
<?php
						}
					}
					?>
				</tbody>
			</table>
			
			<?php 
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
			    ?></ul></div></div><?php
			    } 
			?>
		</div>
		
		<?php 
	echo "</div>";
	} 
} ?>
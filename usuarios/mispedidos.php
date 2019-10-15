<?php require_once "../includes/lib_carrito.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head_interno.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
	 <?php require_once "../template/cabeceraUsuarios.inc.php"; ?>
	 <div class="wrapper">
		<div class="grids top">
			<?php require_once "../template/menuLateralUsuarios.inc.php";
			
			$usuario = $_SESSION['ID_USUARIO'];
			
			$saldo = consulta("select SALDO from USUARIOS where ID_USUARIO='$usuario'");
			$saldo = extraer_registro($saldo);
			$saldo = $saldo['SALDO']; //confirmado
			$saldoPendiente = calculaSaldoPendienteUsuario ($usuario);
			$saldo = round(($saldo - $saldoPendiente), 2);
			?>
			<p class="message misaldo"><b>SALDO USUARIO:</b> <?=$saldo?> &euro;</p>
			
			<h1 class="cal">Mis Pedidos</h1>
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
					$consulta = "select * from PEDIDOS where ID_USUARIO='$usuario' order by fecha_pedido desc"; 
					
					$pedidos = consulta($consulta);
					
					//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA
					$rows_per_page = consultarPaginacion();
					
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
						$pedidos = consulta($consulta);
					}

					if (numero_filas($pedidos)==0) {
?>
					<tr>
						<td colspan="7">No hay pedidos</td>
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
								$tieneRE = $filaPed['RE'] == '1';
								
								$resProductos = consulta("select * from PEDIDOS_PRODUCTOS where ID_PEDIDO='".$filaPed['ID_PEDIDO']."'");
								while ($filaP = extraer_registro($resProductos)) {
									$idProducto = $filaP['ID_PRODUCTO'];
									$cantidad = $filaP['CANTIDAD'];
									$cantidadRevisada = $filaP['CANTIDAD_REVISADA'];
									$subtotal = round(($filaP['PRECIO'] * $cantidad), 2);
									
									//$productoMedida = $filaP['MEDIDA'];
									//$productoMedidaRevisado = $filaP['MEDIDA'];
									$peso_por_unidad = $filaP['PESO_POR_UNIDAD'];
									if (isset($peso_por_unidad) && $peso_por_unidad>0) {
										//$productoMedida = $productoMedida." ($peso_por_unidad Kg)";
										//$productoMedidaRevisado = 'Kg';
									}
									
									if (isset($cantidadRevisada) && $cantidadRevisada>=0) {
										if (isset($peso_por_unidad) && $peso_por_unidad>0) {
											$precio_por_kg = $filaP['PRECIO'] / $peso_por_unidad;
											$subtotalRevisado = round(($precio_por_kg * $cantidadRevisada), 2);
										} else {
											$subtotalRevisado = round(($filaP['PRECIO'] * $cantidadRevisada), 2);
										}
									} else {
										if (isset($peso_por_unidad) && $peso_por_unidad>0) {
											$precio_por_kg = $filaP['PRECIO'] / $peso_por_unidad;
											$cantidadRevisada = round(($cantidad * $peso_por_unidad), 2);
											$subtotalRevisado = round(($precio_por_kg * $cantidadRevisada), 2);
										} else {
											$cantidadRevisada = $cantidad;
											$subtotalRevisado = round(($filaP['PRECIO'] * $cantidadRevisada), 2);
										}
									}
									
									$total += $subtotal;
									$totalRevisado += $subtotalRevisado;
								}  
								
								if ($tieneRE) {
									$reTotal = calculaREPedido ($filaPed['ID_PEDIDO']);
									$totalRevisado += $reTotal;
								}
								
								echo $total;
							?> &euro;
						</td>
						<td align="center"><?=$totalRevisado;?> &euro;</td>
						<td align="center">
							<a title="Ver Pedido" href="verPedido.php?idPedido=<?=$filaPed['ID_PEDIDO']?>">
								<img alt="Info" title="Info" class="imgcomprar" src="../img/INFO2.png" height="25" width="25" />
							</a>
						</td>
					</tr>
<?php
						}
					}
					?>
				</tbody>
			</table>
			
		<!-- PAGINACION -->				
					<?php 
						//UNA VEZ Q MUESTRO LOS DATOS TENGO Q MOSTRAR EL BLOQUE DE PAGINACIÓN SIEMPRE Y CUANDO HAYA MÁS DE UNA PÁGINA
						if($numrows != 0)
						{
							$nextpage= $page +1;
							$prevpage= $page -1;
							 
							?><div class="grid-12 grid paginationCV"><br><ul><?php
						           //SI ES LA PRIMERA PÁGINA DESHABILITO EL BOTON DE PREVIOUS, MUESTRO EL 1 COMO ACTIVO Y MUESTRO EL RESTO DE PÁGINAS
						           if ($page == 1) 
						           {
						            ?>
						              <li><a rel="prev" href="#">&larr;</a></li>
										<li><a class="current" href="#">1</a></li> 
						         <?php
						              for($i= $page+1; $i<= $lastpage ; $i++)
						              {?>
						                <li><a
									href="mispedidos.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
						        <?php }
						           
						           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
						            if($lastpage >$page )
						            {?>      
						                <li class="next"><a
									href="mispedidos.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
						            }
						            else
						            {?>
						                <li><a rel="next" href="#">&rarr;</a></li>
						        <?php
						            }
						        } 
						        else
						        {
						     
						            //EN CAMBIO SI NO ESTAMOS EN LA PÁGINA UNO HABILITO EL BOTON DE PREVIUS Y MUESTRO LAS DEMÁS
						        ?>
						            <li class="previous"><a
									href="mispedidos.php?page=<?php echo $prevpage;?>">&larr;
										</a></li><?php
						             for($i= 1; $i<= $lastpage ; $i++)
						             {
						                           //COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
						                if($page == $i)
						                {
						            ?>       <li><a class="current" href=""><?php echo $i;?></a></li><?php
						                }
						                else
						                {
						            ?>       <li><a
									href="mispedidos.php?page=<?php echo $i;?>"><?php echo $i;?></a></li><?php
						                }
						            }
						             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
						            if($lastpage >$page )
						            {   ?>   
						                <li class="next"><a
									href="mispedidos.php?page=<?php echo $nextpage;?>">
										&raquo;</a></li><?php
						            }
						            else
						            {
						        ?>       <li><a rel="next" href="">&rarr;</a></li><?php
						            }
						        }     
						    ?></ul>
						    </div>
						<?php
						    } 
						?>
						<!-- FIN PAGINACION -->
		<br/>
		<div id="botonera" style="text-align: right;">
			<input id="HSaldo" name="HSaldo"  type="button" value="Mi Saldo" onclick="document.location='historicoSaldoUsuario.php'" />
		</div>
		 
        </div>
	</div>
	</div>
</div>

<?php require_once "../template/pie.inc.php";  ?>
</body>
</html>

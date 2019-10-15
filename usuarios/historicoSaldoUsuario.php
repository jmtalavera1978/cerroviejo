<?php require_once "../includes/lib_carrito.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head_interno.inc.php"; ?>
</head>
<body>
	 <?php require_once "../template/cabeceraUsuarios.inc.php"; ?>
	 <div class="wrapper">
		<div class="grids top">
			<?php require_once "../template/menuLateralUsuarios.inc.php"; ?>
			
		<?php
		$idUsuarioSel =$_SESSION['ID_USUARIO'];
		
		
		$pageUserSel = @$_GET['pageUser'];
		if (isset($pageUserSel)) {
			if (strlen($pageUserSel)==0) {
				$_SESSION['pageUser'] = NULL;
				$pageUserSel = NULL;
			} else {
				$_SESSION['pageUser'] = $pageUserSel;
			}
		} else {
			$pageUserSel = @$_SESSION['pageUser'];
		}
			
		$saldo = consulta("select SALDO from USUARIOS where ID_USUARIO='$idUsuarioSel'");
		$saldo = extraer_registro($saldo);
		$saldo = $saldo['SALDO'];
		
		$saldoPendiente = calculaSaldoPendienteUsuario ($idUsuarioSel);
		$saldoPendiente = round(($saldo - $saldoPendiente), 2);
		
		/* INI - Calcular el saldo con los pedidos en preparacion */
		$resUltFecha = consulta ("SELECT MAX(FECHA) FECHA FROM CONTABILIDAD_USUARIO WHERE USUARIO='$idUsuarioSel'");
		$resUltFecha = extraer_registro ($resUltFecha);
		$resUltFecha = $resUltFecha['FECHA'];
		
		$saldoActual = 0;
		if(isset($resUltFecha) && $resUltFecha!=NULL) {
			//Obtener último saldo
			$resUltSaldo = consulta ("SELECT TOTAL_SALDO FROM CONTABILIDAD_USUARIO WHERE FECHA='".$resUltFecha."' AND USUARIO='$idUsuarioSel'");
			$resUltSaldo = extraer_registro ($resUltSaldo);
			$saldoActual = $resUltSaldo['TOTAL_SALDO'];
		}
		/* FIN */
		
		if (isset($_SESSION['mensaje_generico'])) {
			echo "<h5 style=\"text-align: left;\">".$_SESSION['mensaje_generico']."</h5>";
			$_SESSION['mensaje_generico'] = NULL;
		}

		?>
		<p class="message misaldo"><b>SALDO ACTUAL DEL USUARIO:</b> <?=$saldoPendiente?> &euro;</p>
		
					<h1 class="cal">C&aacute;lculo del Saldo del Usuario</h1>
					
					<div id="listadoProductos">
					
						<?php if (isset($idUsuarioSel) && $idUsuarioSel!=NULL) {
							$consulta = "SELECT * FROM CONTABILIDAD_USUARIO WHERE USUARIO='$idUsuarioSel' ORDER BY FECHA DESC";
							
							$resContabilidad = consulta($consulta);
						?>
						
						<table class="tablaResultados">
							<thead>
								<tr>
									<th align="center">FECHA</th>
									<th align="left">CONCEPTO</th>
									<th align="center">USUARIO</th>
									<th align="center">IMPORTE</th>
									<th align="center">SALDO USUARIO</th>
								</tr>
							</thead>
							<tbody>
							<?php
							$consulta2 = "select * from (
								select ID_PEDIDO, FECHA_PEDIDO AS FECHA, CONCAT('COMPRA LOTE ', LOTE) as CONCEPTO, '0' AS IMPORTE, '1' AS ESDEBE
								FROM PEDIDOS WHERE ID_USUARIO = '".$idUsuarioSel."' AND ESTADO = 'PREPARACION'
							) tabla order by FECHA";
							$resUsuarios = consulta($consulta2);
							if (numero_filas($resContabilidad)==0 && numero_filas($resUsuarios)==0) {
			?>
								<tr>
									<td colspan="5">No hay datos de contabilidad de usuario.</td>
								</tr>
			<?php 
							} else {
								
								//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA , EN EL EJEMPLO PONGO 15
								$rows_per_page= consultarPaginacion ();
								
								if (($numrows = numero_filas($resContabilidad))>0) {
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
									$resContabilidad=consulta($consulta);
								}
								
								if (numero_filas($resUsuarios)>0 && isset($page) && $page==1) {
									while ($filaUsuarios = extraer_registro($resUsuarios)) {
										$importe = $filaUsuarios['IMPORTE'];
											
										if ($filaUsuarios['ID_PEDIDO']!='-1') {
											// Calcular importe real de pedido de usuario
											$pedidos = consulta("select * from PEDIDOS where ID_USUARIO='$idUsuarioSel' AND ID_PEDIDO='".$filaUsuarios['ID_PEDIDO']."'");
											if ($filaPed = extraer_registro($pedidos)) {
												$totalRevisado = 0;
												$resProductos = consulta("select * from PEDIDOS_PRODUCTOS where ID_PEDIDO='".$filaPed['ID_PEDIDO']."'");
												while ($filaProd = extraer_registro($resProductos)) {
													$cantidad = $filaProd['CANTIDAD'];
													if ($filaProd['CANTIDAD_REVISADA']!=NULL && $filaProd['CANTIDAD_REVISADA']>=0) {
														$cantidad = $filaProd['CANTIDAD_REVISADA'];
													}
													$totalRevisado += round(($cantidad * $filaProd['PRECIO']), 2);
												}
												$importe = $totalRevisado;
											}
										} 
										
										if ( $filaUsuarios['ESDEBE']=='0') {
											$saldoActual = round(($saldoActual + $importe), 2);
										} else {
											$saldoActual = round(($saldoActual - $importe), 2);
										}
								?>
								<tr>
									<td style="background-color:#cccccc" align="center"><?=$filaUsuarios['FECHA']?></td>
									<td style="background-color:#cccccc" align="left"><?=$filaUsuarios['CONCEPTO']?></td>
									<td style="background-color:#cccccc" align="center"><?=$idUsuarioSel?></td>
									<td style="background-color:#cccccc" align="right" nowrap><?=($filaUsuarios['ESDEBE'] == '1' ? "-".$importe. "  &euro;" : "+".$importe. "  &euro;")?></td>
									<td style="background-color:#cccccc" align="right"><?=$saldoActual?>  &euro;</td>
								</tr>
								<?php 
								}
								?>
						<?php } 
		
								while ($filaCon = extraer_registro($resContabilidad)) {
									$fecha = $filaCon['FECHA'];
									$fecha = DateTime::createFromFormat("Y-m-d H:i:s", $fecha);
			?>
								<tr>
									<td <?php if ($filaCon['ESDEBE']=='1') {echo "style=\"color:red\""; } ?> align="center" title="<?=$fecha->format("H:i:s")?>"><?=$fecha->format("d/m/Y")?></td>
									<td <?php if ($filaCon['ESDEBE']=='1') {echo "style=\"color:red\""; } ?> align="left"><?=$filaCon['CONCEPTO']?></td>
									<td <?php if ($filaCon['ESDEBE']=='1') {echo "style=\"color:red\""; } ?> align="center"><?=$filaCon['USUARIO']=='-SISTEMA-' ? 'SISTEMA' : $filaCon['USUARIO']?></td>
									<td <?php if ($filaCon['ESDEBE']=='1') {echo "style=\"color:red\""; } ?> align="right" nowrap><?=($filaCon['ESDEBE'] == '1' ? "-".$filaCon['IMPORTE']. "  &euro;" : "+".$filaCon['IMPORTE']. "  &euro;")?></td>
									<td <?php if ($filaCon['ESDEBE']=='1') {echo "style=\"color:red\""; } ?> align="right"><?=$filaCon['TOTAL_SALDO']?>  &euro;</td>
								</tr>
			<?php 
								}
			?>
							</tbody>
						</table>
					<?php 
					//UNA VEZ Q MUESTRO LOS DATOS TENGO Q MOSTRAR EL BLOQUE DE PAGINACIÓN SIEMPRE Y CUANDO HAYA MÁS DE UNA PÁGINA
					echo "<div style='clear:both;'></div>";
					
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
						href="historicoSaldoUsuario.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
			        <?php }
			           
			           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
			            if($lastpage >$page )
			            {?>      
			                <li class="next"><a
						href="historicoSaldoUsuario.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
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
						href="historicoSaldoUsuario.php?page=<?php echo $prevpage;?>">&larr;
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
						href="historicoSaldoUsuario.php?page=<?php echo $i;?>"><?php echo $i;?></a></li><?php
			                }
			            }
			             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
			            if($lastpage >$page )
			            {   ?>   
			                <li class="next"><a
						href="historicoSaldoUsuario.php?page=<?php echo $nextpage;?>">
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

			  }
		} ?>
						
						<br/>
				
						<div id="botonera" style="text-align: right;">
							<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='mispedidos.php'" />
						</div>
		
        	</div>
		</div>
	</div>
</div>

<?php require_once "../template/pie.inc.php";  ?>
</body>
</html>
		
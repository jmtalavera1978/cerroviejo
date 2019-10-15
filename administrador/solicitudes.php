<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="index.php">
		<div id="contenidoAdmin">
			<?php
				$leido = @$_GET['leido'];
				if (isset($leido)) {
					if (strlen($leido)==0) {
						$_SESSION['leido'] = NULL;
						$leido = false;
					} else {
						$_SESSION['leido'] = $leido;
					}
				} else {
					$leido = @$_SESSION['leido'];
				}
			?>
			<h1 class="cal" style="margin-bottom: -20px;">Solicitudes</h1>
			<?php
			if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5 style=\"text-align: left;\">".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			} 
			?>
			<div id="tituloProveedores">
				<span>&nbsp;Solicitudes leídas:&nbsp;</span>
				<input type="checkbox" name="leido" id="leido" <?php if (@$leido=='true') { echo "checked=\"true\""; } ?> onchange="document.location='solicitudes.php?leido='+this.checked" />
			</div>
			
			<div id="listadoProductos">
				<?php 
					$consulta = "SELECT * FROM SOLICITUDES WHERE 1=1 ";
					if ($leido=='true') {
						$consulta .= " AND leido='1'";
						$consulta .= " ORDER BY FECHA_SOLICITUD DESC";
					} else {
						$consulta .= " AND leido='0'";
						$consulta .= " ORDER BY FECHA_SOLICITUD";
					}
					
					$resUsuarios = consulta($consulta);
				?>
				<table class="tablaResultados" style="margin-bottom: -30px;">
					<thead>
						<tr>
							<th align="center">TIPO SOLICITUD</th>
							<th align="center">NOMBRE</th>
							<th align="center">APELLIDOS</th>
							<th align="center">NIF</th>
							<th align="center">EMAIL</th>
							<th align="center">TELÉFONO</th>
							<th align="center">LEÍDO</th>
							<th align="center">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($resUsuarios)==0) {
	?>
						<tr>
							<td colspan="8">No hay solicitudes para el tipo indicado</td>
						</tr>
	<?php 
					} else {
						
						//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA , EN EL EJEMPLO PONGO 15
						$rows_per_page= consultarPaginacion ();
						
						if (($numrows = numero_filas($resUsuarios))>0) {
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
							$resUsuarios=consulta($consulta);
						}

						while ($filaUsuarios = extraer_registro($resUsuarios)) {
	?>
						<tr>
							<td align="center"><?=$filaUsuarios['TIPO_SOLICITUD']?></td>
							<td align="center"><?=$filaUsuarios['NOMBRE']?></td>
							<td align="center"><?=$filaUsuarios['APELLIDOS']?></td>
							<td align="center"><?=$filaUsuarios['NIF']?></td>
							<td align="center" nowrap><?=$filaUsuarios['EMAIL']?></td>
							<td align="center" nowrap><?=$filaUsuarios['TFNO_CONTACTO']?></td>
							<td align="center">
								<input type="checkbox" readonly="readonly" disabled="disabled" title="Marca de lectura" <?=$filaUsuarios['LEIDO'] == 1 ? 'checked=\"checked\"' : ''?>/>
							</td>
							<td align="center"><a title="Ver/Editar Solicitud" href="editarSolicitud.php?idSolicitud=<?=$filaUsuarios['ID_SOLICITUD']?>"><img src="../img/DETALLE.png" alt="factura" width="32"/></a></td>
						</tr>
	<?php 
						}
	?>
					</tbody>
				</table>
			<br/>
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
			                <li><a href="solicitudes.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
			        <?php }
			           
			           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
			            if($lastpage >$page )
			            {?>      
			                <li class="next"><a href="solicitudes.php?page=<?php echo $nextpage;?>" >&rarr;</a></li><?php
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
			            <li class="previous"><a href="solicitudes.php?page=<?php echo $prevpage;?>">&larr;</a></li><?php
			             for($i= 1; $i<= $lastpage ; $i++)
			             {
			                           //COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
			                if($page == $i)
			                {
			            ?>       <li class="active"><?php echo $i;?></li><?php
			                }
			                else
			                {
			            ?>       <li><a href="solicitudes.php?page=<?php echo $i;?>" ><?php echo $i;?></a></li><?php
			                }
			            }
			             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
			            if($lastpage >$page )
			            {   ?>   
			                <li class="next"><a href="solicitudes.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
			            }
			            else
			            {
			        ?>       <li class="next-off">&rarr;</li><?php
			            }
			        }     
			    ?></ul></div></div><?php
			    } 
			?>
				<?php } ?>
			</div>
		</form>
	</section>
</body>
</html>

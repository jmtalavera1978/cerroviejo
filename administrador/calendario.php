<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="calendario.php">
		<div id="contenidoAdmin">
			<?php
			 if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5>".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			 } 
			?>
			<h1 class="cal" style="margin-bottom: -20px;">Calendario</h1>
			<div id="tituloProveedores">
				<?php
				$seleccionado = @$_GET['idCategoriaC'];
				if ($seleccionado) {
					$_SESSION['idCategoriaC'] = $seleccionado;
				} else {
					$seleccionado = @$_SESSION['idCategoriaC'];
				}
				?>
				<span>CATEGOR&Iacute;A: </span>
				<select name="categorias" id="categorias" onchange="document.location='calendario.php?idCategoriaC='+this.value;">
					<option value="-1">Seleccione una categor&iacute;a...</option>
					<?php optionsCategorias($seleccionado); ?>
				</select>
			</div>
			
			<div id="listadoProductos">
				<?php 
					$consulta = "SELECT CAL.*, CAT.DESCRIPCION from CALENDARIO CAL, CATEGORIAS CAT WHERE CAL.ID_CATEGORIA=CAT.ID_CATEGORIA and FECHA_ENTREGA>='".Date("Y-m-d H:i:s")."' AND CAL.ESTADO<>'FINALIZADO' AND CAL.ESTADO<>'REVISADO' ";
					if (isset($seleccionado)&& $seleccionado!=NULL && $seleccionado!='-1') {
						$consulta .= " AND CAL.ID_CATEGORIA='".$seleccionado."'";
					}
					$consulta .= " ORDER BY CAL.FECHA_ENTREGA DESC";
					$resCal = consulta($consulta);
				?>
				<table class="tablaResultados">
					<thead>
						<tr>
							<th align="center">FECHA PEDIDO</th>
							<th align="center">FECHA ENTREGA</th>
							<th align="center">CATEGOR&Iacute;A</th>
							<th align="center">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($resCal)==0) {
	?>
						<tr>
							<td colspan="5">No hay fechas previstas</td>
						</tr>
	<?php 
					} else {
						
						//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA , EN EL EJEMPLO PONGO 15
						$rows_per_page = consultarPaginacion ();
						
						if (($numrows = numero_filas($resCal))>0) {
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
							$resCal=consulta($consulta);
						}

						while ($filaCal = extraer_registro($resCal)) {
							$fechaPedido = $filaCal['FECHA_PEDIDO'];
							if (isset($fechaPedido)) {
								$fechaPedido = date_create_from_format('Y-m-d', $fechaPedido);
							}
							$fechaEntrega = $filaCal['FECHA_ENTREGA'];
							if (isset($fechaEntrega)) {
								$fechaEntrega = date_create_from_format('Y-m-d', $fechaEntrega);
							}
	?>
						<tr>
							<td align="center"><?=$fechaPedido->format('d/m/Y')?></td>
							<td align="center"><?=$fechaEntrega->format('d/m/Y')?></td>
							<td align="center" nowrap><?=$filaCal['DESCRIPCION']?></td>
							<td align="center"><a title="Editar fecha" href="editarCalendario.php?idCalendario=<?=$filaCal['ID_CALENDARIO']?>"><img src="../img/EDITAR.png" alt="Editar Fecha" width="32"/></a></td>
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
			                <li><a href="calendario.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
			        <?php }
			           
			           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
			            if($lastpage >$page )
			            {?>      
			                <li class="next"><a href="calendario.php?page=<?php echo $nextpage;?>" >&rarr;</a></li><?php
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
			            <li class="previous"><a href="calendario.php?page=<?php echo $prevpage;?>">&larr;</a></li><?php
			             for($i= 1; $i<= $lastpage ; $i++)
			             {
			                           //COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
			                if($page == $i)
			                {
			            ?>       <li class="active"><?php echo $i;?></li><?php
			                }
			                else
			                {
			            ?>       <li><a href="calendario.php?page=<?php echo $i;?>" ><?php echo $i;?></a></li><?php
			                }
			            }
			             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
			            if($lastpage >$page )
			            {   ?>   
			                <li class="next"><a href="calendario.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
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
		</div>
		<br/>
			<div id="botonera">
				<input id="nuevo" name="nuevo"  type="button" value="Nueva fecha de entrega" onclick="document.location='nuevoCalendario.php'" />
			</div>
		</form>
	</section>
</body>
</html>

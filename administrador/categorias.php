<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="categorias.php">
		<div id="contenidoAdmin">
			<?php
			 if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5>".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			 } 
			?>
			<h1 class="cal" style="margin-bottom: -20px;">Categor&iacute;as</h1>
			
			<div id="tituloProveedores">
				<?php
				$activo = @$_GET['activo'].@$_POST['activo'];
				if (isset($activo) && strlen($activo)>0) {
					if ($activo=='true') {
						$_SESSION['activoCateg'] = $activo;
					} else {
						$_SESSION['activoCateg'] = NULL;
						$activo = NULL;
					}
				} else {
					$activo = @$_SESSION['activoCateg'];
				}
				
				?>
				<span>&nbsp;SOLO ACTIVOS:&nbsp;</span>
				<input type="checkbox" name="activo" id="activo" value="true" <?php if (@$activo=='true') { echo "checked=\"true\""; } ?> onchange="document.location='categorias.php?activo='+this.checked" />
			</div>
			
			<div id="listadoProductos">
				<?php 
					$consulta = "SELECT * from CATEGORIAS ";
					if ($activo=='true') {
						$consulta .= " WHERE ACTIVO='1'";	
					}
					$consulta .= " ORDER BY DESCRIPCION";
					
					$resCategorias = consulta($consulta);
				?>
				<table class="tablaResultados">
					<thead>
						<tr>
							<th>DESCRIPCI&Oacute;N</th>
							<th align="center">RECARGO</th>
							<th align="center">ACTIVO</th>
							<th align="center">&nbsp;</th>
							<!-- <th align="center">&nbsp;</th> -->
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($resCategorias)==0) {
	?>
						<tr>
							<td colspan="4">No hay categorías</td>
						</tr>
	<?php 
					} else {
						
						//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA , EN EL EJEMPLO PONGO 15
						$rows_per_page= consultarPaginacion ();
						
						if (($numrows = numero_filas($resCategorias))>0) {
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
							$resCategorias=consulta($consulta);
						}

						while ($filaCat = extraer_registro($resCategorias)) {
	?>
						<tr>
							<td><?=$filaCat['DESCRIPCION']?></td>
							<td align="center"><?=($filaCat['RECARGO']==NULL || $filaCat['RECARGO']=='') ? 0 : $filaCat['RECARGO']?>%</td>
							<td align="center">
								<input type="checkbox" readonly="readonly" onclick="document.location='activarCategoria.php?idCategoria=<?=$filaCat['ID_CATEGORIA']?>&url='+document.location" <?=$filaCat['ACTIVO'] == 1 ? 'checked=\"checked\"' : ''?>"/>
							</td>
							<td align="center"><a title="Editar Categoría" href="editarCategoria.php?idCategoria=<?=$filaCat['ID_CATEGORIA']?>"><img src="../img/EDITAR.png" alt="editar" width="32"/></a></td>
							<!--  <td align="center"><a class="ui-icon ui-state-highlight ui-icon-circle-close" href='eliminar_categoria.php?idCategoria=<?=$filaCat['ID_CATEGORIA']?>&url=categorias.php'>Eliminar</a></td> -->
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
			                <li><a href="categorias.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
			        <?php }
			           
			           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
			            if($lastpage >$page )
			            {?>      
			                <li class="next"><a href="categorias.php?page=<?php echo $nextpage;?>" >&rarr;</a></li><?php
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
			            <li class="previous"><a href="categorias.php?page=<?php echo $prevpage;?>">&larr;</a></li><?php
			             for($i= 1; $i<= $lastpage ; $i++)
			             {
			                           //COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
			                if($page == $i)
			                {
			            ?>       <li class="active"><?php echo $i;?></li><?php
			                }
			                else
			                {
			            ?>       <li><a href="categorias.php?page=<?php echo $i;?>" ><?php echo $i;?></a></li><?php
			                }
			            }
			             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
			            if($lastpage >$page )
			            {   ?>   
			                <li class="next"><a href="categorias.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
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
				<input id="nuevo" name="nuevo"  type="button" value="Nueva categoría" onclick="document.location='nuevaCategoria.php'" />
			</div>
		</form>
	</section>
</body>
</html>

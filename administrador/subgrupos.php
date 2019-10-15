<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
    <script>
	  $(function() {
	    var availableTags = [
		<?php
	      $res1 = consulta("SELECT NOMBRE FROM SUBGRUPOS");
	      while ($fila1 = extraer_registro($res1)) {
			echo "\"".$fila1['NOMBRE']."\",";
		  }
	      ?>
	    ];
	    $( "#bnombreSub" ).autocomplete({
	      source: availableTags
	    });
	  });
	 </script>
	<section>
		<form method="post" action="index.php">
		<div id="contenidoAdmin">
			<h1 class="cal" style="margin-bottom: -20px;">Subgrupos</h1>
			<?php
			if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5 style=\"text-align: left;\">".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			} 
			
			$bnombreSub = @$_GET['bnombreSub'];
			if (isset($bnombreSub)) {
				if (strlen($bnombreSub)==0) {
					$_SESSION['bnombreSub'] = NULL;
					$bnombreSub = NULL;
				} else {
					$_SESSION['bnombreSub'] = $bnombreSub;
				}
			} else {
				$bnombreSub = @$_SESSION['bnombreSub'];
			}
			?>
			<div id="tituloProveedores">
				<span>&nbsp;SUBGRUPO:&nbsp;</span>
				<input type="text" id="bnombreSub" name="bnombreSub" value="<?=@$bnombreSub?>" size="8" /> 
				<input type="button" id="buscar" name="buscar" value="Buscar" onclick="document.location='subgrupos.php?bnombreSub='+$('#bnombreSub').val()" />
				<input type="button" id="limpiar" name="limpiar" value="Limpiar" onclick="document.location='subgrupos.php?bnombreSub='" />
			</div>
			
			<div id="listadoProductos">
				<?php 
					$consulta = "SELECT * FROM SUBGRUPOS ";
					if (isset($bnombreSub) && strlen($bnombreSub)>0) {
						$consulta .= " WHERE NOMBRE='$bnombreSub'";
					}
					$consulta .= " ORDER BY NOMBRE ";
					
					$resSubgrupos = consulta($consulta);
				?>
				<table class="tablaResultados" style="margin-bottom: -30px;">
					<thead>
						<tr>
							<th>COD. SUBGRUPO</th>
							<th>NOMBRE</th>
							<th align="center">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($resSubgrupos)==0) {
	?>
						<tr>
							<td colspan="3">No hay subgrupos</td>
						</tr>
	<?php 
					} else {
						
						//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA , EN EL EJEMPLO PONGO 15
						$rows_per_page= consultarPaginacion ();
						
						if (($numrows = numero_filas($resSubgrupos))>0) {
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
							$resSubgrupos=consulta($consulta);
						}

						while ($filaSubgrupo = extraer_registro($resSubgrupos)) {
	?>
						<tr>
							<td><?=$filaSubgrupo['ID_SUBGRUPO']?></td>
							<td align="center"><?=$filaSubgrupo['NOMBRE']?></td>
							<td align="center"><a title="Editar Subgrupo" href="editarSubgrupo.php?idSubgrupo=<?=$filaSubgrupo['ID_SUBGRUPO']?>"><img src="../img/EDITAR.png" alt="editar" width="32"/></a></td>
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
			                <li><a href="subgrupos.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
			        <?php }
			           
			           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
			            if($lastpage >$page )
			            {?>      
			                <li class="next"><a href="subgrupos.php?page=<?php echo $nextpage;?>" >&rarr;</a></li><?php
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
			            <li class="previous"><a href="subgrupos.php?page=<?php echo $prevpage;?>">&larr;</a></li><?php
			             for($i= 1; $i<= $lastpage ; $i++)
			             {
			                           //COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
			                if($page == $i)
			                {
			            ?>       <li class="active"><?php echo $i;?></li><?php
			                }
			                else
			                {
			            ?>       <li><a href="subgrupos.php?page=<?php echo $i;?>" ><?php echo $i;?></a></li><?php
			                }
			            }
			             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
			            if($lastpage >$page )
			            {   ?>   
			                <li class="next"><a href="subgrupos.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
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
		
		<br/><br/>
			<div id="botonera">
				<input id="nuevo" name="nuevo"  type="button" value="Nuevo Subgrupo" onclick="document.location='nuevoSubgrupo.php'" />
			</div>
		</form>
	</section>
</body>
</html>

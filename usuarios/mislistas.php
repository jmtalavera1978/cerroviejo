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
			?>			
			<h1 class="cal">Mis listas</h1>
			<?php 
			if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5";
				if (!strpos($_SESSION['mensaje_generico'], 'correctamente')) {
					echo " style=\"color:red\"";
				}
				echo ">".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			} 
			?>
				<table class="tablaResultados">
					<thead>
						<tr>
							<th style="text-align: left">Nombre</th>
							<?php if (estaAbiertoPeriodoCompra () || @$_SESSION['ID_USUARIO']=='DEMO') {  ?>
							<th width="10%">&nbsp;</th>
							<?php } ?>
							<th width="10%">&nbsp;</th>
							<th width="10%">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					// Consulta de listas
					$consulta = "select * from LISTAS where ID_USUARIO='$usuario' order by NOMBRE desc"; 
					
					$listas = consulta($consulta);
					
					//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA
					$rows_per_page = consultarPaginacion();
					
					if (($numrows = numero_filas($listas))>0) {
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
						$listas = consulta($consulta);
					}

					if (numero_filas($listas)==0) {
?>
					<tr>
						<td colspan="<?=((estaAbiertoPeriodoCompra () || @$_SESSION['ID_USUARIO']=='DEMO') ? '4' : '3')?>">No hay listas</td>
					</tr>
<?php 
					} else {
						while ($filaListas = extraer_registro($listas)) {
?>
					<tr>
						<td><?=$filaListas['NOMBRE']?></td>
						<?php if (estaAbiertoPeriodoCompra () || @$_SESSION['ID_USUARIO']=='DEMO') {  ?>
						<td align="center">
							<a title="Añadir a la cesta" href="addListaCompra.php?idLista=<?=$filaListas['ID_LISTA']?>">
								<img alt="Añadir a la cesta" title="Añadir a la cesta" class="imgcomprar" src="../img/carrito2.png" height="25" width="25" />
							</a>
						</td>
						<?php } ?>
						<td align="center">
							<a title="Ver Lista" href="verLista.php?idLista=<?=$filaListas['ID_LISTA']?>">
								<img alt="Info" title="Info" class="imgcomprar" src="../img/INFO2.png" height="25" width="25" />
							</a>
						</td>
						<td align="center">
							<a title="Borrar Lista" href="javascript:openConfirmacion('<?=$filaListas['ID_LISTA']?>')">
								<img alt="Borrar" title="Borrar" class="imgcomprar" src="../img/BORRAR.png" height="25" width="25" />
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
									href="mislistas.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
						        <?php }
						           
						           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
						            if($lastpage >$page )
						            {?>      
						                <li class="next"><a
									href="mislistas.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
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
									href="mislistas.php?page=<?php echo $prevpage;?>">&larr;
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
									href="mislistas.php?page=<?php echo $i;?>"><?php echo $i;?></a></li><?php
						                }
						            }
						             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
						            if($lastpage >$page )
						            {   ?>   
						                <li class="next"><a
									href="mislistas.php?page=<?php echo $nextpage;?>">
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
			<input id="CreaLista" name="CreaLista"  type="button" value="Crear Lista" onclick="document.location='crearLista.php'" />
		</div>
		 
        </div>
	</div>
	</div>
</div>

<div id="dialogConfirmDelete" title="Eliminaci&oacute;n" style="display: none;">
	¿Desea eliminar la lista completamente?</div>
	
<script>		
	var idListaSeleccionada;  
	function openConfirmacion(idLista) {
		idListaSeleccionada = idLista;
		$("#dialogConfirmDelete").dialog("open");
	}
	
	$("#dialogConfirmDelete").dialog({
      autoOpen: false,
	  height: 170,
	  width: 350,
	  modal: true,
      buttons : {
          "Sí" : function() {
        	  $(this).dialog("close");
        	  document.location='borrarLista.php?idLista='+idListaSeleccionada;
          },
          "Cancelar" : function() {
	          $(this).dialog("close");
          }
        }
    });
</script>

<?php require_once "../template/pie.inc.php";  ?>
</body>
</html>

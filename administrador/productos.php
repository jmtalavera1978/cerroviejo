<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript">
		function modificarPrecio(idProducto, iva, precio) {
			try {
				$( "#producto"+idProducto ).html('Guardando...');
				$( "#producto"+idProducto ).load( "modificar_precio_producto.php?idProducto="+idProducto+"&iva="+iva+"&precio="+precio, function() {
				});
				$( "#productoPVP"+idProducto ).html('Actualizando...');
				$( "#productoPVP"+idProducto ).load( "actualiza_precio_producto.php?idProducto="+idProducto+"&iva="+iva, function() {
				});
				
			} catch (e) {
			}
		}
	</script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="productos.php">
		<div id="contenidoAdmin">
			<?php
			 if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5>".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			 } 
			?>
			<h1 class="cal" style="margin-bottom: -20px;">Productos</h1>
			<div id="tituloProveedores">
				<?php
				$seleccionado = @$_GET['idCategoria'].@$_POST['idCategoria'];
				if ($seleccionado) {
					$_SESSION['idCategoria'] = $seleccionado;
					if ($seleccionado=='-1') {
						$_SESSION['idsubcategoria'] = NULL;
						$seleccionadoSub = NULL;
					}
				} else {
					$seleccionado = @$_SESSION['idCategoria'];
				}
				
				$seleccionadoSub = @$_GET['idsubcategoria'].@$_POST['idsubcategoria'];
				if ($seleccionadoSub) {
					if ($seleccionadoSub=='-1') {
						$_SESSION['idsubcategoria'] = NULL;
						$seleccionadoSub = NULL;
					} else {
						$_SESSION['idsubcategoria'] = $seleccionadoSub;
					}
				} else {
					$seleccionadoSub = @$_SESSION['idsubcategoria'];
				}
				
				$proveedor = @$_GET['idProveedor'].@$_POST['idProveedor'];
				if ($proveedor) {
					$_SESSION['idProveedorPr'] = $proveedor;
				} else {
					$proveedor = @$_SESSION['idProveedorPr'];
				}
				
				$bnombre = "";
				if (isset($_GET['bnombre'])) {
					if (strlen($_GET['bnombre'])==0) {
						$_SESSION['bnombrePro'] = NULL;
						$bnombre = NULL;
					} else {
						$_SESSION['bnombrePro'] = $_GET['bnombre'];
						$bnombre = $_GET['bnombre'];
					}
				} else {
					$bnombre = @$_SESSION['bnombrePro'];
				}
				
				$activo = @$_GET['activo'].@$_POST['activo'];
				if (isset($activo) && strlen($activo)>0) {
					if ($activo=='true') {
						$_SESSION['activo'] = $activo;
					} else {
						$_SESSION['activo'] = NULL;
						$activo = NULL;
					}
				} else {
					$activo = @$_SESSION['activo'];
				}
				
				?>
				<span>&nbsp;CATEGOR&Iacute;A: </span>
				<select name="categorias" id="categorias" onchange="document.location='productos.php?idCategoria='+this.value;">
					<option value="-1">Seleccione una categor&iacute;a...</option>
					<?php optionsCategorias($seleccionado); ?>
				</select>
				<span>&nbsp;&nbsp;&nbsp;SUBCATEGOR&Iacute;A: </span>
				<select name="subcategorias" id="subcategorias" onchange="document.location='productos.php?idsubcategoria='+this.value;">
					<?php optionsSubCategorias2($seleccionado, $seleccionadoSub); ?>
				</select>
				<br/>
				<span>&nbsp;PROVEEDOR: </span>
				<select name="proveedores" id="proveedores" onchange="document.location='productos.php?idProveedor='+this.value;">
					<option value="-1">Seleccione un proveedor...</option>
					<?php optionsProveedores($proveedor) ?>
				</select>
				<span>&nbsp;&nbsp;&nbsp;NOMBRE: </span>
				<input type="text" id="bnombre" name="bnombre" value="<?=@$_SESSION['bnombrePro']?>" size="20" /> 
				<input type="button" id="buscar" name="buscar" value="Buscar" onclick="document.location='productos.php?bnombre='+$('#bnombre').val()" />
				<input type="button" id="limpiar" name="limpiar" value="Limpiar" onclick="document.location='productos.php?bnombre='" />
				<br/>
				<span>&nbsp;SOLO ACTIVOS:&nbsp;</span>
				<input type="checkbox" name="activo" id="activo" value="true" <?php if (@$activo=='true') { echo "checked=\"true\""; } ?> onchange="document.location='productos.php?bnombre='+$('#bnombre').val()+'&activo='+this.checked" />
			</div>
			
			<div id="listadoProductos">
			
				<?php if (	(isset($seleccionado) && strlen($seleccionado)>0 && $seleccionado!='-1')
							|| (isset($proveedor) && strlen($proveedor)>0 && $proveedor!='-1')
							|| (isset($bnombre) && strlen($bnombre)>0)
						) {
					$consulta = "SELECT P.* , U.DESCRIPCION AS UNIDAD FROM PRODUCTOS P, UNIDADES U WHERE P.UNIDAD_MEDIDA = U.ID_UNIDAD";
					if (isset($seleccionado) && strlen($seleccionado)>0 && $seleccionado!='-1') {
						$consulta.="  AND ID_CATEGORIA='".$seleccionado."'";
					}
					if (isset($seleccionadoSub) && strlen($seleccionadoSub)>0 && $seleccionadoSub!='-1') {
						$consulta.=" AND ID_SUBCATEGORIA='$seleccionadoSub'";
					}
					if (isset($proveedor) && strlen($proveedor)>0 && $proveedor!='-1') {
						$consulta.=" AND (PROVEEDOR_1='$proveedor')"; // OR PROVEEDOR_2='$proveedor'
					}
					if (isset($bnombre) && strlen($bnombre)>0) {
						$consulta .= " AND P.DESCRIPCION LIKE '%$bnombre%'";
					}
					if ($activo=='true') {
						$consulta .= " AND P.ACTIVO='1'";
					}
					$consulta.=" ORDER BY P.DESCRIPCION";
					$resProductos = consulta($consulta);
				?>
				<table class="tablaResultados">
					<thead>
						<tr>
							<th>PRODUCTO</th>
							<th align="center" title="Precio sin IVA y sin Recargo">P.S.I.</th>
							<th align="center">PVP</th>
							<th align="center">MEDIDA</th>
							<th align="center">RECARGO</th>
							<th align="center" title="Cantidades disponibles">STOCK</th>
							<th align="center" title="Activo">Ac.</th>
							<th align="center" title="Novedad">No.</th>
							<th align="center" title="Oferta">Of.</th>
							<th align="center" title="Ecológico">Eco.</th>
							<th align="center">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($resProductos)==0) {
	?>
						<tr>
							<td colspan="11">No hay productos</td>
						</tr>
	<?php 
					} else {
						
						//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA , EN EL EJEMPLO PONGO 15
						$rows_per_page= consultarPaginacion ();
						
						if (($numrows = numero_filas($resProductos))>0) {
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
							$resProductos=consulta($consulta);
						}

						while ($filaProd = extraer_registro($resProductos)) {
							$recargoDesc = calculaRecargoProductoDesc($filaProd['ID_PRODUCTO']);
							//$pvp = calculaPrecioConRecargo($filaProd['ID_PRODUCTO'], $filaProd['PRECIO']);
							$pvp = calculaPVP ($filaProd['ID_PRODUCTO'], $filaProd['IMPORTE_SIN_IVA'], $filaProd['TIPO_IVA']);
	?>
						<tr>
							<td><?=$filaProd['ID_PRODUCTO']?>&nbsp;<?=$filaProd['DESCRIPCION']?></td>
							<td align="center" id="producto<?=$filaProd['ID_PRODUCTO']?>" nowrap="nowrap">
								<input type="text" style="width: 70%; text-align:right;"
									onfocus="this.value=''"
									onkeypress="return NumCheck(event, this)"
									onblur="modificarPrecio ('<?=$filaProd['ID_PRODUCTO']?>', '<?=$filaProd['TIPO_IVA']?>', this.value)" 
									value="<?=$filaProd['IMPORTE_SIN_IVA']?>"/> &euro;
							</td>
							<td align="center" id="productoPVP<?=$filaProd['ID_PRODUCTO']?>" nowrap="nowrap">
								<?=$pvp?> &euro;
							</td>
							<td align="center"><?=$filaProd['UNIDAD']?> <?=$filaProd['DESCRIPCION_MEDIDA']?></td>
							<td align="center"><?=$recargoDesc?></td>
							<td align="center">
								<?php if ($filaProd['CANTIDAD_ILIMITADA']==0) { ?>
									<input type="text" size="10" value="<?=$filaProd['CANTIDAD_1']?> <?=$filaProd['UNIDAD']?>" readonly="readonly"/>
									<?php if ($filaProd['PROVEEDOR_2']!=NULL || $filaProd['CANTIDAD_2']!='') { ?>
										<input type="text" size="10" value="<?=$filaProd['CANTIDAD_2']?> <?=$filaProd['UNIDAD']?>" readonly="readonly"/>
									<?php } ?>
								<?php } else { ?>
									Ilimitadas
								<?php }  ?>
							</td>
							<td align="center">
								<input type="checkbox" onclick="document.location='activarProducto.php?idProducto=<?=$filaProd['ID_PRODUCTO']?>&url='+document.location" <?=$filaProd['ACTIVO'] == 1 ? 'checked=\"checked\"' : ''?>"/>
							</td>
							<td align="center">
								<input type="checkbox" onclick="document.location='activarNovedad.php?idProducto=<?=$filaProd['ID_PRODUCTO']?>&url='+document.location" <?=$filaProd['NOVEDAD'] == 1 ? 'checked=\"checked\"' : ''?>"/>
							</td>
							<td align="center">
								<input type="checkbox" onclick="document.location='activarOferta.php?idProducto=<?=$filaProd['ID_PRODUCTO']?>&url='+document.location" <?=$filaProd['OFERTA'] == 1 ? 'checked=\"checked\"' : ''?>"/>
							</td>
							<td align="center">
								<input type="checkbox" onclick="document.location='activarEco.php?idProducto=<?=$filaProd['ID_PRODUCTO']?>&url='+document.location" <?=$filaProd['ECOLOGICO'] == 1 ? 'checked=\"checked\"' : ''?>"/>
							</td>
							<td align="center"><a title="Editar Producto" href="editarProducto.php?idProducto=<?=$filaProd['ID_PRODUCTO']?>"><img src="../img/EDITAR.png" alt="editar" width="32"/></a></td>
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
			                <li><a href="productos.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
			        <?php }
			           
			           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
			            if($lastpage >$page )
			            {?>      
			                <li class="next"><a href="productos.php?page=<?php echo $nextpage;?>" >&rarr;</a></li><?php
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
			            <li class="previous"><a href="productos.php?page=<?php echo $prevpage;?>">&larr;</a></li><?php
			             for($i= 1; $i<= $lastpage ; $i++)
			             {
			                           //COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
			                if($page == $i)
			                {
			            ?>       <li class="active"><?php echo $i;?></li><?php
			                }
			                else
			                {
			            ?>       <li><a href="productos.php?page=<?php echo $i;?>" ><?php echo $i;?></a></li><?php
			                }
			            }
			             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
			            if($lastpage >$page )
			            {   ?>   
			                <li class="next"><a href="productos.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
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
		</div>
		<br/>
				<div id="dialogElimNoved" title="">
					¿Desea vaciar todos los productos de novedades?
				</div>
					
				<script>
					function openConfirmacionElimNov() {
						$("#dialogElimNoved").dialog("open");
					}
					
					$("#dialogElimNoved").dialog({
					  autoOpen: false,
					  height: 250,
					  width: 400,
					  modal: true,
					  buttons : {
						  "Sí" : function() {
							  document.location='eliminarNovedades.php';
						  },
						  "No" : function() {
							  $("#dialogElimNoved").dialog("close");
						  }
						}
					});
				</script>
				<div id="dialogElimOf" title="">
					¿Desea vaciar todos los productos de ofertas?
				</div>
					
				<script>
					function openConfirmacionElimOf() {
						$("#dialogElimOf").dialog("open");
					}
					
					$("#dialogElimOf").dialog({
					  autoOpen: false,
					  height: 250,
					  width: 400,
					  modal: true,
					  buttons : {
						  "Sí" : function() {
							  document.location='eliminarOfertas.php';
						  },
						  "No" : function() {
							  $("#dialogElimOf").dialog("close");
						  }
						}
					});
				</script>
			<div id="botonera">
				<input id="vaciarNovedades" name="vaciarNovedades"  type="button" value="Vaciar novedades" onclick="openConfirmacionElimNov();" />
				<input id="vaciarOfertas" name="vaciarOfertas"  type="button" value="Vaciar ofertas" onclick="openConfirmacionElimOf();" />
				<input id="nuevo" name="nuevo"  type="button" value="Nuevo producto" onclick="document.location='nuevoProducto.php'" />
				<!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
				<?php 
				/*$resCat = consulta("select * from CATEGORIAS WHERE ID_CATEGORIA=".$_SESSION['idCategoria']);
				$fila = extraer_registro($resCat);
				if ($fila['ACTIVO']==1) { ?>
					<input id="desactivar" name="desactivar" type="button" onclick="document.location='activarCategoria.php?idCategoria=<?=$fila['ID_CATEGORIA']?>&url='+document.location" value="Desactivar Categor&iacute;a" />
				<?php } else { ?>
					<input id="activar" name="activar" type="button" onclick="document.location='activarCategoria.php?idCategoria=<?=$fila['ID_CATEGORIA']?>&url='+document.location" value="Activar Categor&iacute;a" />
				<?php }*/  ?>
			</div>
		</form>
	</section>
</body>
</html>

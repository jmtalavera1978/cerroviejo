<?php require_once "../includes/lib_carrito.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head_interno.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
	<script type="text/javascript" src="../js/jquery.ui.stars.js"></script>
	<link rel="stylesheet" href="../css/jquery.ui.stars.css" />
</head>
<body>
	 <?php require_once "../template/cabeceraUsuarios.inc.php"; ?>
	 <div class="wrapper">
		<div class="grids top">
			<?php 
				require_once "../template/menuLateralUsuarios.inc.php";
				
					$lote = consultarLoteActual();
					$idProducto = @$_GET['id'];
					$consulta = "select P.*, U.DESCRIPCION AS UNIDAD from PRODUCTOS P, UNIDADES U
						WHERE P.UNIDAD_MEDIDA=U.ID_UNIDAD AND P.ID_PRODUCTO='".$idProducto."'
						and ACTIVO=1 AND (CANTIDAD_ILIMITADA=1 OR (P.CANTIDAD_1 + P.CANTIDAD_2)>0)";
					
					$productosRes = consulta ($consulta);
					
					$packs = consulta ("select * from PRODUCTOS_PACK where ID_PRODUCTO_PACK='$idProducto'");
					$numPacks = numero_filas($packs);
					
					$producto = extraer_registro ($productosRes);
					$nombreProd = $producto['DESCRIPCION'];
					$descProd = $producto['DESCRIPCION_LARGA'];
					$precioProd = $producto['PRECIO'];
					//$precioProd = calculaPrecioConRecargo($idProducto, $precioProd);
					$precioProd = $producto['IMPORTE_SIN_IVA'];
					$iva = $producto['TIPO_IVA'];
					$precioProd = calculaPVP ($idProducto, $precioProd, $iva);
					
					$unidad = $producto['UNIDAD'];
					$descMedida = $producto['DESCRIPCION_MEDIDA'];
					//$foto = $producto['FOTO'];
					$ilimitada = $producto['CANTIDAD_ILIMITADA'];
					
					if ($ilimitada=='0' || $ilimitada==NULL) {
						$cantidad_vendida = cantidadVendidaProducto($lote, $idProducto);
						$max = ($producto['CANTIDAD_1'] + $producto['CANTIDAD_2'] - $cantidad_vendida);
					} else {
						$max = 0;
					}
					
					$cuartos = $producto['INC_CUARTOS'];
					$novedad= $producto['NOVEDAD'];
					$oferta= $producto['OFERTA'];
					$ecologico= $producto['ECOLOGICO'];
					
					$vegano = $producto['VEGANO'];
					$sin_gluten = $producto['SIN_GLUTEN'];
					$sin_lactosa = $producto['SIN_LACTOSA'];
					$gourmet = $producto['GOURMET'];
					$comercio_justo =$producto['COMERCIO_JUSTO'];
					$km0 = $producto['KM0'];
					$provincia = $producto['PROVINCIA'];
					
					$minimo = $producto['PEDIDO_MINIMO'];
					
					$proveedores = consultaProveedoresTexto ($idProducto);
					
					if ($numPacks==0) {
						?>
						<h1 class="cal">Detalle del producto</h1>

				<div class="wrapper">
					<div class="grid-12 grid">
						<header>
							<hgroup>
								<h1><?=$nombreProd?></h1>
							</hgroup>
						</header>
					</div>
					<div class="grid-12 grid">
						<div class="grid-12 grid">

							<h5><?=$precioProd?> &euro;/<?=$unidad?></h5>
							<h6><?=$descMedida?></h6>

						</div>
						<div class="grid-2 grid">
									<?php 
									if (estaAbiertoPeriodoCompra () || @$_SESSION['ID_USUARIO']=='DEMO') { 
										if ($ilimitada || $max>0) {?>
										<img alt="Comprar" title="Comprar" class="imgcomprar"
								style="cursor: pointer; cursor: hand;"
								onclick="addCesta('<?=$idProducto?>', '<?=htmlspecialchars($nombreProd, ENT_COMPAT, 'UTF-8')?>', '<?=$precioProd?>', '<?=$unidad?>', '<?=htmlspecialchars($descMedida, ENT_COMPAT, 'UTF-8')?>', '<?=$max?>', '<?=$ilimitada?>',  '<?=$cuartos?>', '<?=$minimo?>');"
								src="../img/carrito.png" />
									<?php } else { ?>
									<img alt="Agotado" title="Agotado" src="../img/agotado.png"
								height="36" width="46" style="float: left;" //>
									<?php } 
									} ?>
								</div>
						<div class="grid-2 grid ">
							<img src="../img/volver.png"
								style="cursor: pointer; cursor: hand;" onclick="history.back();" />
						</div>
					</div>
					<div class="grid-12 grid">
						<div class="grid-6 grid">

							<?php
							//$nombre = strtolower(sanear_string (str_replace(" ","_",trim($nombreProd))));
							//$ruta_imagen = "../fotos/".$nombre.".jpg";
							$ruta_imagen = "../fotos/".$idProducto.".jpg";
							
							if (@file_exists ($ruta_imagen)) {?>
								<img alt="<?=$nombreProd?>" style="cursor: pointer; cursor: hand;"
									src="<?=$ruta_imagen?>" height="231" onclick="document.location='detalleProducto.php?id=<?=$idProducto?>'"
									width="383" />
							<?php } else { ?>
								<img alt="<?=$nombreProd?>" src="../img/sinfoto.gif" style="cursor: pointer; cursor: hand;"
									height="231" width="383" onclick="document.location='detalleProducto.php?id=<?=$idProducto?>'" />
							<?php } ?>

						</div>
						<div class="grid-10 grid">
							<p>
								<b><?=$descProd?></b>
							</p>
							<ul class="check">
								<?php if ($ecologico=='1') { ?>
								<li><span><strong>Ecológico</strong></span></li>
								<?php } 
									  if ($novedad=='1') { ?>
								<li><span><strong>Novedad</strong></span></li>
								<?php } 
									  if ($oferta=='1') { ?>
								<li><span><strong>Oferta</span></li>
								<?php } 
									  if ($vegano=='1') { ?>
								<li><span><strong>Vegano</span></li>
								<?php } 
									  if ($sin_gluten=='1') { ?>
								<li><span><strong>Sin gluten</span></li>
								<?php } 
									  if ($sin_lactosa=='1') { ?>
								<li><span><strong>Sin lactosa</span></li>
								<?php } 
									  if ($comercio_justo=='1') { ?>
								<li><span><strong>Comercio Justo</span></li>
								<?php } 
									  if ($km0=='1') { ?>
								<li><span><strong>KM 0</span></li>
								<?php } 
									  if ($gourmet=='1') { ?>
								<li><span><strong>Gourmet</span></li>
								<?php } 
								?>
							</ul>
							<ul>
								<?php if ($provincia!='') { ?>
								<li><span><strong>Provincia: </strong><?=$provincia?></span></li>
								<?php } 
									  if ($proveedores!='') { ?>
								<li><span><strong>Proveedor/es: </strong><?=$proveedores?></span></li>
								<div style="width: 150px">
								<?php }  
								
								$_SESSION['idVoto']=$idProducto;
								include 'votaciones.php';  ?>
								</div>
							</ul>
						</div>
					</div>
				</div>
						<?php
					} else {
						?>
						<h1 class="cal">Detalle del pack</h1>

				<div class="wrapper">
					<div class="grid-12 grid">
						<header>
							<hgroup>
								<h1><?=$nombreProd?></h1>
							</hgroup>
						</header>
					</div>
					<div class="grid-12 grid">
						<div class="grid-12 grid">
							<h5><?=$precioProd?> &euro; (Lote de <?=$numPacks?> productos)</h5>
						</div>
						<div class="grid-2 grid">
									<?php 
									if (estaAbiertoPeriodoCompra () || @$_SESSION['ID_USUARIO']=='DEMO') { 
										if ($ilimitada || $max>0) {?>
										<img alt="Comprar" title="Comprar" class="imgcomprar"
								style="cursor: pointer; cursor: hand;"
								onclick="addCesta('<?=$idProducto?>', '<?=htmlspecialchars($nombreProd, ENT_COMPAT, 'UTF-8')?>', '<?=$precioProd?>', '<?=$unidad?>', '<?=htmlspecialchars($descMedida, ENT_COMPAT, 'UTF-8')?>', '<?=$max?>', '<?=$ilimitada?>',  '<?=$cuartos?>', '<?=$minimo?>');"
								src="../img/carrito.png" />
									<?php } else { ?>
									<img alt="Agotado" title="Agotado" src="../img/agotado.png"
								height="36" width="46" style="float: left;" //>
									<?php } 
									} ?>
								</div>
						<div class="grid-2 grid ">
							<img src="../img/volver.png"
								style="cursor: pointer; cursor: hand;" onclick="history.back();" />
						</div>
					</div>
						 	
						<?php 
							while ($filaPack = extraer_registro($packs)) {
								$consulta = "select P.*, U.DESCRIPCION AS UNIDAD from PRODUCTOS P, UNIDADES U
											WHERE P.UNIDAD_MEDIDA=U.ID_UNIDAD AND P.ID_PRODUCTO='".$filaPack['ID_PRODUCTO']."'
											and ACTIVO=1"; // AND (CANTIDAD_ILIMITADA=1 OR (P.CANTIDAD_1 + P.CANTIDAD_2)>0)
						
								$productosRes = consulta ($consulta);
									
								$producto = extraer_registro ($productosRes);
								$nombreProd = $producto['DESCRIPCION'];
								$descProd = $producto['DESCRIPCION_LARGA'];
								//$precioProd = $producto['PRECIO'];
								//$precioProd = calculaPrecioConRecargo($idProducto, $precioProd);
								$precioProd = calculaPVP ($producto['ID_PRODUCTO'], $producto['IMPORTE_SIN_IVA'], $producto['TIPO_IVA']);
								
								$unidad = $producto['UNIDAD'];
								$descMedida = $producto['DESCRIPCION_MEDIDA'];
								//$foto = $producto['FOTO'];
								$ilimitada = $producto['CANTIDAD_ILIMITADA'];
								
								$novedad= $producto['NOVEDAD'];
								$oferta= $producto['OFERTA'];
								$ecologico= $producto['ECOLOGICO'];
								
								$vegano = $producto['VEGANO'];
								$sin_gluten = $producto['SIN_GLUTEN'];
								$sin_lactosa = $producto['SIN_LACTOSA'];
								$gourmet = $producto['GOURMET'];
								$comercio_justo =$producto['COMERCIO_JUSTO'];
								$km0 = $producto['KM0'];
								$provincia = $producto['PROVINCIA'];
								
								$minimo = $producto['PEDIDO_MINIMO'];
								
								$proveedores = consultaProveedoresTexto ($filaPack['ID_PRODUCTO']);
								?>
								 <div class="grid-12 grid">
									<div class="grid-6 grid">
										<?php
										//$nombre = strtolower(sanear_string (str_replace(" ","_",trim($nombreProd))));
										//$ruta_imagen = "../fotos/".$nombre.".jpg";
										$ruta_imagen = "../fotos/".$filaPack['ID_PRODUCTO'].".jpg";
										
										if (@file_exists ($ruta_imagen)) {?>
											<img alt="<?=$nombreProd?>" style="cursor: pointer; cursor: hand;"
												src="<?=$ruta_imagen?>" height="231" onclick="document.location='detalleProducto.php?id=<?=$idProducto?>'"
												width="383" />
										<?php } else { ?>
											<img alt="<?=$nombreProd?>" src="../img/sinfoto.gif" style="cursor: pointer; cursor: hand;"
												height="231" width="383" onclick="document.location='detalleProducto.php?id=<?=$idProducto?>'" />
										<?php } ?>
									</div>
									<div class="grid-10 grid">
										<h3><?=$nombreProd?></h3>
										<h5><?=$precioProd?> &euro; <?=$unidad?> / <?=$descMedida?></h5>
										<h6><?=$filaPack['CANTIDAD']?> <?=($filaPack['CANTIDAD'] == 1 ? 'unidad': 'unidades')?> de este producto.</h6>
										<p>
											<b><?=$descProd?></b>
										</p>
										<ul class="check">
											<?php if ($ecologico=='1') { ?>
												<li><span><strong>Ecológico</strong></span></li>
												<?php } 
													  if ($novedad=='1') { ?>
												<li><span><strong>Novedad</strong></span></li>
												<?php } 
													  if ($oferta=='1') { ?>
												<li><span><strong>Oferta</span></li>
												<?php } 
													  if ($vegano=='1') { ?>
												<li><span><strong>Vegano</span></li>
												<?php } 
													  if ($sin_gluten=='1') { ?>
												<li><span><strong>Sin gluten</span></li>
												<?php } 
													  if ($sin_lactosa=='1') { ?>
												<li><span><strong>Sin lactosa</span></li>
												<?php } 
													  if ($comercio_justo=='1') { ?>
												<li><span><strong>Comercio Justo</span></li>
												<?php } 
													  if ($km0=='1') { ?>
												<li><span><strong>KM 0</span></li>
												<?php } 
													  if ($gourmet=='1') { ?>
												<li><span><strong>Gourmet</span></li>
												<?php } ?>
											</ul>
											<ul>
												<?php if ($provincia!='') { ?>
												<li><span><strong>Provincia: </strong><?=$provincia?></span></li>
												<?php } 
													  if ($proveedores!='') { ?>
												<li><span><strong>Proveedor/es: </strong><?=$proveedores?></span></li>
												<?php }  ?>
										</ul>
									</div>
								</div>
								<?php
					}
					
					echo "</div>";
				}
				?>

        		</div>
			</div>
				</div>
			</div>

<?php require_once "modal_compra.inc.php" ?>

<?php require_once "../template/pie.inc.php";  ?>


</body>
</html>
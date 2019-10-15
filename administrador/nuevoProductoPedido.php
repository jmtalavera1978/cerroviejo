<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="nuevoProductoPedido.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idPedido = @$_POST['idPedido'];
				$idProducto= @$_POST['idProducto'];
				$cantidad= @$_POST['cantidad'];
				$usuario =  @$_POST['usuario'];
				$total= @$_POST['total'];
				
				// Calcula precio con recargo
				$precioProd = consulta("select * from PRODUCTOS where ID_PRODUCTO='$idProducto'");
				$precioProd = extraer_registro($precioProd);
				
				$pesoPorUnidad = $precioProd['PESO_POR_UNIDAD'];
				$tipoIVA = $precioProd['TIPO_IVA'];
				$importeSinIVA = $precioProd['IMPORTE_SIN_IVA'];
				$precioSinRecargo = $precioProd['PRECIO'];
				

				// Comprobación de que no se sobrepase las existencias
				$cant1 = $precioProd['CANTIDAD_1'];
				$cant2 = $precioProd['CANTIDAD_2'];
				$cantIlimitada = $precioProd['CANTIDAD_ILIMITADA'];
				$lote = consultarLoteActual();
				
				if ($cantIlimitada=='0' || $cantIlimitada==NULL) {
					$cantidad_vendida = cantidadVendidaProducto($lote, $idProducto);
					$max = ($cant1 + $cant2 - $cantidad_vendida);
				
					if ($cantidad>$max) { //Se ha sobrepasado el limite maximo de unidades
						$mensaje = "No hay suficiente existencias para ".$precioProd['DESCRIPCION'].".<br/> Ajuste la cantidad, solo quedan: ".$max." unidades";
					}
				}

				if (!@isset($mensaje) || @$mensaje=='') {
				
					if (esTransporte ($idProducto)) {
						$precio = calcula_precio_transporte ($idProducto, $total);
						$pesoPorUnidad = 0;
					} else {
						$precio = calculaPVPUsuario ($idProducto, $importeSinIVA, $tipoIVA, $usuario);
					}
				
					$res = consulta ("insert INTO PEDIDOS_PRODUCTOS (ID_PEDIDO, ID_PRODUCTO, PRECIO, CANTIDAD, CANTIDAD_REVISADA, PESO_POR_UNIDAD
							, PRECIO_SIN_RECARGO, PROVEEDOR_1, CANTIDAD_1, PROVEEDOR_2, CANTIDAD_2, CANTIDAD_ILIMITADA, IMPORTE_SIN_IVA, TIPO_IVA) 
							VALUES ('$idPedido', '$idProducto', '$precio', '0', '$cantidad', '$pesoPorUnidad', '$precioSinRecargo'
							, '".$precioProd['PROVEEDOR_1']."', '".$precioProd['CANTIDAD_1']."', '".$precioProd['PROVEEDOR_2']."', '".$precioProd['CANTIDAD_2']."'
							, '".$precioProd['CANTIDAD_ILIMITADA']."', '$importeSinIVA', '$tipoIVA')");
	
					if ($res) {
							?>
								<script>		  
								 document.location='pedidos.php';
								</script>
								</form>
								</section>
								</body>
							</html>
							<?php 
							exit;
					} else {
						$mensaje = 'No se ha podido a&ntilde;adir el producto al Pedido';
					}
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Nuevo Producto para Pedido</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<div id="tituloProveedores">
				<?php
				$idProveedorSel = @$_GET['idProveedor'];
				if ($idProveedorSel) {
					$_SESSION['idProveedorPP1'] = $idProveedorSel;
				} else {
					$idProveedorSel = @$_SESSION['idProveedorPP1'];
				}
				$seleccionado = @$_GET['idCategoria'];
				if ($seleccionado) {
					$_SESSION['idCategoriaPP1'] = $seleccionado;
				} else {
					$seleccionado = @$_SESSION['idCategoriaPP1'];
				}
				$seleccionadoSub = @$_GET['idsubcategoria'];
				if ($seleccionadoSub) {
					if ($seleccionadoSub=='-1') {
						$_SESSION['idsubcategoriaPP1'] = NULL;
						$seleccionadoSub = NULL;
					} else {
						$_SESSION['idsubcategoriaPP1'] = $seleccionadoSub;
					}
				} else {
					$seleccionadoSub = @$_SESSION['idsubcategoriaPP1'];
				}
				$bnombre = @$_GET['bnombre'];
				if (isset($bnombre)) {
					if (strlen($bnombre)==0) {
						$_SESSION['bnombrePP1'] = NULL;
						$bnombre = NULL;
					} else {
						$_SESSION['bnombrePP1'] = $bnombre;
					}
				} else {
					$bnombre = @$_SESSION['bnombrePP1'];
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
					if (isset($_SESSION['activo']) && strlen(@$_SESSION['activo'])>0) {
						$activo = @$_SESSION['activo'];
					} else {
						$activo = 'true';
					}
				}
				?>
				<span>PROVEEDOR:</span>
				<select name="proveedores" id="proveedores" onchange="document.location='nuevoProductoPedido.php?idPedido=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&usuario=<?=@$_GET['usuario'].@$_POST['usuario']?>&idProveedor='+this.value;">
					<option value="-1">Seleccione un Proveedor...</option>
					<?php optionsProveedores($idProveedorSel) ?>
				</select>
				<br/>
				<span>CATEGOR&Iacute;A:</span>
				<select name="categorias" id="categorias" onchange="document.location='nuevoProductoPedido.php?idPedido=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&usuario=<?=@$_GET['usuario'].@$_POST['usuario']?>&idsubcategoria=-1&idCategoria='+this.value;">
					<option value="-1">Seleccione una categor&iacute;a...</option>
					<?php optionsCategorias($seleccionado); ?>
				</select>
				<br/>
				<span>SUBCATEGOR&Iacute;A:</span>
				<select name="subcategorias" id="subcategorias" onchange="document.location='nuevoProductoPedido.php?idPedido=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&usuario=<?=@$_GET['usuario'].@$_POST['usuario']?>&idsubcategoria='+this.value;">
					<?php optionsSubCategorias2($seleccionado, $seleccionadoSub); ?>
				</select>
				<br/>
				<span>NOMBRE:</span>
				<input type="text" id="bnombre" name="bnombre" value="<?=@$bnombre?>" size="20" /> 
				<br/>
				<span>&nbsp;SOLO ACTIVOS:&nbsp;</span>
				<input type="checkbox" name="activo" id="activo" value="true" <?php if (@$activo=='true') { echo "checked=\"true\""; } ?> onchange="document.location='nuevoProductoPedido.php?bnombre='+$('#bnombre').val()+'&activo='+this.checked" />
				<input type="button" id="buscar" name="buscar" value="Buscar" onclick="document.location='nuevoProductoPedido.php?idPedido=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&usuario=<?=@$_GET['usuario'].@$_POST['usuario']?>&bnombre='+$('#bnombre').val()" />
				<input type="button" id="limpiar" name="limpiar" value="Limpiar" onclick="document.location='nuevoProductoPedido.php?idPedido=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&usuario=<?=@$_GET['usuario'].@$_POST['usuario']?>&bnombre='" />
			</div>
			<br/><br/>
			<input id="idPedido" name="idPedido" type="hidden" value="<?=@$_GET['idPedido'].@$_POST['idPedido']?>" />
			<input id="total" name="total" type="hidden" value="<?=@$_GET['total'].@$_POST['total']?>" />
			<input id="usuario" name="usuario" type="hidden" value="<?=@$_GET['usuario'].@$_POST['usuario']?>" />
			
			<?php 
				$consulta = "SELECT P.* , U.DESCRIPCION AS UNIDAD FROM PRODUCTOS P, UNIDADES U WHERE P.UNIDAD_MEDIDA = U.ID_UNIDAD ";
				if (isset($_SESSION['idCategoriaPP1']) && $_SESSION['idCategoriaPP1']!='-1' && strlen($_SESSION['idCategoriaPP1'])>0) {
					$consulta.=" AND ID_CATEGORIA=".$_SESSION['idCategoriaPP1'];
					if (isset($seleccionadoSub) && strlen($seleccionadoSub)>0) {
						$consulta.=" AND ID_SUBCATEGORIA='$seleccionadoSub'";
					}
				}
				if (isset($_SESSION['idProveedorPP1']) && $_SESSION['idProveedorPP1']!='-1' && strlen($_SESSION['idProveedorPP1'])>0) {
					$consulta.=" AND (PROVEEDOR_1='".$_SESSION['idProveedorPP1']."' OR PROVEEDOR_2='".$_SESSION['idProveedorPP1']."')";
				}
				if ($bnombre) {
					$consulta .= " AND P.DESCRIPCION LIKE '%$bnombre%'";
				}
				if ($activo=='true') {
						$consulta .= " AND P.ACTIVO='1'";
				}
				$consulta.=" ORDER BY P.DESCRIPCION";
				$resProductos = consulta($consulta);
			?>
			<table class="tablaResultados">
				<tr>
					<td><label for="idProducto">&nbsp;Producto:</label></td>
					<td>
						<select id="idProducto" name="idProducto" required="required" style="width:98%">
							<option value="">Seleccione un producto...</option>
							<?php 
								while ($filaP = extraer_registro($resProductos)) {
									$unidad = $filaP['UNIDAD'];
									$peso_por_unidad = $filaP['PESO_POR_UNIDAD'];
									if (isset($peso_por_unidad) && $peso_por_unidad>0) {
										$unidad = 'por Kg';
									}
							?>
							<option value="<?=$filaP['ID_PRODUCTO']?>"><?=$filaP['DESCRIPCION'].', '.$unidad?></option>
							<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="cantidad">&nbsp;Cantidad:</label></td>
					<td>
						<input id="cantidad" name="cantidad" type="text" value="1" onkeypress="return NumCheck(event, this)" size="5" maxlength="7" required="required" />
					</td>
				</tr>
			</table>
		</div>
		<div style="clear: both;"></div>
		<br/>
			<div id="botonera">
				<input id="submit" name="submit"  type="submit" value="Grabar" />
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='pedidos.php'" />
			</div>
		</form>
	</section>
</body>
</html>

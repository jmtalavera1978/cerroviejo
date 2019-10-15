<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="nuevoProductoPedidoProv.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$lote = @$_POST['lote'];
				$proveedor = @$_POST['proveedor'];
				$idPedido = @$_POST['idPedido']; 
				
				$idProducto= @$_POST['idProducto'];
				$cantidad= @$_POST['cantidad'];
				
				// Calcula precio sin recargo
				$precioProd = consulta("select PRECIO, PESO_POR_UNIDAD from PRODUCTOS where ID_PRODUCTO='$idProducto'");
				$precioProd = extraer_registro($precioProd);
				$pesoPorUnidad = $precioProd['PESO_POR_UNIDAD'];
				$precioProd = $precioProd['PRECIO'];
				
				consulta("update PEDIDOS_PROVEEDORES set ENVIADO='0' where ID_PEDIDO_PROVEEDOR = '$idPedido'");
				
				$res = consulta("insert into PEDIDOS_PROVEEDORES_PROD (ID_PEDIDO_PROVEEDOR, ID_PRODUCTO, PRECIO, CANTIDAD, CANTIDAD_REV)
						values ('$idPedido', '$idProducto', '$precioProd',' $cantidad', NULL)");
		
				if ($res) {
						?>
							<script>		  
							 document.location='detallePedidosProv.php?idPedidoProv=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&consulta=<?=@$GET['consulta'].@$_POST['consulta']?>';
							</script>
							</form>
							</section>
							</body>
						</html>
						<?php 
						exit;
				} else {
					$mensaje = 'No se ha podido a&ntilde;adir el producto al Pedido de Proveedor. Compruebe que no esté añadido.';
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Nuevo Producto para Pedido al Proveedor</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<div id="tituloProveedores">
				<?php				
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
				?>
				<div style="display: none; visibility: hidden;">
				<span>PROVEEDOR:</span>
				<select name="proveedores" id="proveedores" onchange="document.location='nuevoProductoPedidoProv.php?lote=<?=@$_GET['lote'].@$_POST['lote']?>&proveedor=<?=@$_GET['proveedor'].@$_POST['proveedor']?>&idPedido=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&consulta=<?=@$_GET['consulta'].@$_POST['consulta']?>">
					<?php optionsProveedores(@$_GET['proveedor'].@$_POST['proveedor']) ?>
				</select>
				</div>
				<span>CATEGOR&Iacute;A:</span>
				<select name="categorias" id="categorias" onchange="document.location='nuevoProductoPedidoProv.php?lote=<?=@$_GET['lote'].@$_POST['lote']?>&proveedor=<?=@$_GET['proveedor'].@$_POST['proveedor']?>&idPedido=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&idsubcategoria=-1&consulta=<?=@$_GET['consulta'].@$_POST['consulta']?>&idCategoria='+this.value;">
					<option value="-1">Seleccione una categor&iacute;a...</option>
					<?php optionsCategorias($seleccionado); ?>
				</select>
				<br/>
				<span>SUBCATEGOR&Iacute;A:</span>
				<select name="subcategorias" id="subcategorias" onchange="document.location='nuevoProductoPedidoProv.php?lote=<?=@$_GET['lote'].@$_POST['lote']?>&proveedor=<?=@$_GET['proveedor'].@$_POST['proveedor']?>&idPedido=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&consulta=<?=@$_GET['consulta'].@$_POST['consulta']?>&idsubcategoria='+this.value;">
					<?php optionsSubCategorias2($seleccionado, $seleccionadoSub); ?>
				</select>
				<br/>
				<span>NOMBRE:</span>
				<input type="text" id="bnombre" name="bnombre" value="<?=@$bnombre?>" size="20" /> 
				<input type="button" id="buscar" name="buscar" value="Buscar" onclick="document.location='nuevoProductoPedidoProv.php?lote=<?=@$_GET['lote'].@$_POST['lote']?>&proveedor=<?=@$_GET['proveedor'].@$_POST['proveedor']?>&idPedido=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&consulta=<?=@$_GET['consulta'].@$_POST['consulta']?>&bnombre='+$('#bnombre').val()" />
				<input type="button" id="limpiar" name="limpiar" value="Limpiar" onclick="document.location='nuevoProductoPedidoProv.php?lote=<?=@$_GET['lote'].@$_POST['lote']?>&proveedor=<?=@$_GET['proveedor'].@$_POST['proveedor']?>&idPedido=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&consulta=<?=@$_GET['consulta'].@$_POST['consulta']?>&bnombre='" />
			</div>
			<br/><br/>
			<input id="idPedido" name="idPedido" type="hidden" value="<?=@$_GET['idPedido'].@$_POST['idPedido']?>" />
			<input id="lote" name="lote" type="hidden" value="<?=@$_GET['lote'].@$_POST['lote']?>" />
			<input id="proveedor" name="proveedor" type="hidden" value="<?=@$_GET['proveedor'].@$_POST['proveedor']?>" />
			<input id="consulta" name="consulta" type="hidden" value="<?=@$_GET['consulta'].@$_POST['consulta']?>" />
			<?php 
				$consulta = "SELECT P.* , U.DESCRIPCION AS UNIDAD FROM PRODUCTOS P, UNIDADES U WHERE P.UNIDAD_MEDIDA = U.ID_UNIDAD and P.ACTIVO='1'";
				if (isset($_SESSION['idCategoriaPP1']) && $_SESSION['idCategoriaPP1']!='-1' && strlen($_SESSION['idCategoriaPP1'])>0) {
					$consulta.=" AND ID_CATEGORIA=".$_SESSION['idCategoriaPP1'];
					if (isset($seleccionadoSub) && strlen($seleccionadoSub)>0) {
						$consulta.=" AND ID_SUBCATEGORIA='$seleccionadoSub'";
					}
				}
				$consulta.=" AND (PROVEEDOR_1='".@$_GET['proveedor'].@$_POST['proveedor']."' OR PROVEEDOR_2='".@$_GET['proveedor'].@$_POST['proveedor']."')";
				if ($bnombre) {
					$consulta .= " AND P.DESCRIPCION LIKE '%$bnombre%'";
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
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='detallePedidosProv.php?idPedidoProv=<?=@$_GET['idPedido'].@$_POST['idPedido']?>&consulta=<?=@$GET['consulta'].@$_POST['consulta']?>'" />
			</div>
		</form>
	</section>
</body>
</html>

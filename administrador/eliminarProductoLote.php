<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="eliminarProductoLote.php" onsubmit="return confirm('¿Está seguro de querer anular la venta del producto?');" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idProducto = @$_POST['idProducto'];
				$lote= @$_POST['lote'];

				$res = consulta ("UPDATE PEDIDOS_PRODUCTOS SET CHECK_REVISADO='1', CANTIDAD_REVISADA = '0' WHERE ID_PRODUCTO='$idProducto' AND ID_PEDIDO IN (SELECT ID_PEDIDO FROM PEDIDOS WHERE LOTE = '$lote' AND ESTADO='PREPARACION')");
				
				if ($res) {
					$_SESSION['mensaje_generico'] = 'Se han anulado '.$_SESSION['affected'].' pedidos del producto';
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
					$mensaje = 'No se ha podido eliminar el producto de los pedidos del lote';
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Eliminar/Anular Producto del Lote <?=@$_GET['lote'].@$_POST['lote']?></h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<div id="tituloProveedores">
				<?php
				$idProveedorSel = @$_GET['idProveedor'];
				if ($idProveedorSel) {
					$_SESSION['idProveedorPP2'] = $idProveedorSel;
				} else {
					$idProveedorSel = @$_SESSION['idProveedorPP2'];
				}
				$seleccionado = @$_GET['idCategoria'];
				if ($seleccionado) {
					$_SESSION['idCategoriaPP2'] = $seleccionado;
				} else {
					$seleccionado = @$_SESSION['idCategoriaPP2'];
				}
				$seleccionadoSub = @$_GET['idsubcategoria'];
				if ($seleccionadoSub) {
					if ($seleccionadoSub=='-1') {
						$_SESSION['idsubcategoriaPP2'] = NULL;
						$seleccionadoSub = NULL;
					} else {
						$_SESSION['idsubcategoriaPP2'] = $seleccionadoSub;
					}
				} else {
					$seleccionadoSub = @$_SESSION['idsubcategoriaPP2'];
				}
				$bnombre = @$_GET['bnombre'];
				if (isset($bnombre)) {
					if (strlen($bnombre)==0) {
						$_SESSION['bnombrePP2'] = NULL;
						$bnombre = NULL;
					} else {
						$_SESSION['bnombrePP2'] = $bnombre;
					}
				} else {
					$bnombre = @$_SESSION['bnombrePP2'];
				}
				?>
				<span>PROVEEDOR:</span>
				<select name="proveedores" id="proveedores" onchange="document.location='eliminarProductoLote.php?lote=<?=@$_GET['lote'].@$_POST['lote']?>&idProveedor='+this.value;">
					<option value="-1">Seleccione un Proveedor...</option>
					<?php optionsProveedores($idProveedorSel) ?>
				</select>
				<br/>
				<span>CATEGOR&Iacute;A:</span>
				<select name="categorias" id="categorias" onchange="document.location='eliminarProductoLote.php?lote=<?=@$_GET['lote'].@$_POST['lote']?>&idsubcategoria=-1&idCategoria='+this.value;">
					<option value="-1">Seleccione una categor&iacute;a...</option>
					<?php optionsCategorias($seleccionado); ?>
				</select>
				<br/>
				<span>SUBCATEGOR&Iacute;A:</span>
				<select name="subcategorias" id="subcategorias" onchange="document.location='eliminarProductoLote.php?lote=<?=@$_GET['lote'].@$_POST['lote']?>&idsubcategoria='+this.value;">
					<?php optionsSubCategorias2($seleccionado, $seleccionadoSub); ?>
				</select>
				<br/>
				<span>NOMBRE:</span>
				<input type="text" id="bnombre" name="bnombre" value="<?=@$bnombre?>" size="20" /> 
				<input type="button" id="buscar" name="buscar" value="Buscar" onclick="document.location='eliminarProductoLote.php?lote=<?=@$_GET['lote'].@$_POST['lote']?>&bnombre='+$('#bnombre').val()" />
				<input type="button" id="limpiar" name="limpiar" value="Limpiar" onclick="document.location='eliminarProductoLote.php?ilote=<?=@$_GET['lote'].@$_POST['lote']?>&bnombre='" />
			</div>
			<br/><br/>
			<input id="lote" name="lote" type="hidden" value="<?=@$_GET['lote'].@$_POST['lote']?>" />
			
			<?php 
				$consulta = "SELECT DISTINCT P.* , U.DESCRIPCION AS UNIDAD FROM PRODUCTOS P, UNIDADES U, PEDIDOS_PRODUCTOS PP, PEDIDOS P2 
						WHERE P.UNIDAD_MEDIDA = U.ID_UNIDAD and P.ACTIVO='1'
						AND P.ID_PRODUCTO = PP.ID_PRODUCTO
						AND PP.ID_PEDIDO = P2.ID_PEDIDO
						AND P2.LOTE = '".@$_GET['lote'].@$_POST['lote']."'";
				if (isset($_SESSION['idCategoriaPP2']) && $_SESSION['idCategoriaPP2']!='-1' && strlen($_SESSION['idCategoriaPP2'])>0) {
					$consulta.=" AND P.ID_CATEGORIA=".$_SESSION['idCategoriaPP2'];
					if (isset($seleccionadoSub) && strlen($seleccionadoSub)>0) {
						$consulta.=" AND P.ID_SUBCATEGORIA='$seleccionadoSub'";
					}
				}
				if (isset($_SESSION['idProveedorPP2']) && $_SESSION['idProveedorPP2']!='-1' && strlen($_SESSION['idProveedorPP2'])>0) {
					$consulta.=" AND (P.PROVEEDOR_1='".$_SESSION['idProveedorPP2']."' OR P.PROVEEDOR_2='".$_SESSION['idProveedorPP2']."')";
				}
				if ($bnombre) {
					$consulta .= " AND P.DESCRIPCION LIKE '%$bnombre%'";
				}
				$consulta.=" AND PP.CANTIDAD>0 AND (PP.CANTIDAD_REVISADA IS NULL OR PP.CANTIDAD_REVISADA>0) ORDER BY P.DESCRIPCION";
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
			</table>
		</div>
		<div style="clear: both;"></div>
		<br/>
			<div id="botonera">
				<input id="submit" name="submit" type="submit" value="Eliminar Producto" />
				<input id="cancel" name="cancel" type="button" value="Volver" onclick="document.location='pedidos.php'" />
			</div>
		</form>
	</section>
</body>
</html>

<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="nuevoProductoPack.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idProductoPack = @$_POST['idProductoPack'];
				$idProducto= @$_POST['idProducto'];
				$cantidad= @$_POST['cantidad'];
			
				$res = consulta ("insert INTO PRODUCTOS_PACK (ID_PRODUCTO_PACK, ID_PRODUCTO, CANTIDAD) 
							VALUES ('$idProductoPack', '$idProducto', '$cantidad')");
					
				$idNuevo = get_new_id('ID_PRODUCTO_PACK', 'PRODUCTOS_PACK');
		
				if ($res) {
						?>
							<div id="dialogNew" title="">
								Producto a&ntilde;adido al Pack correctamente.<br/>¿Desea a&ntilde;adir un nuevo producto al Pack?
							</div>
								
							<script>		  
								function openConfirmacion() {
									$("#dialogNew").dialog("open");
								}
								
								$("#dialogNew").dialog({
							      autoOpen: false,
								  height: 250,
								  width: 400,
								  modal: true,
							      buttons : {
							          "Sí" : function() {
							        	  document.location='nuevoProductoPack.php?idProductoPack=<?=@$_GET['idProductoPack'].@$_POST['idProductoPack']?>';
							          },
							          "No" : function() {
							        	  document.location='editarProducto.php?idProducto=<?=@$_GET['idProductoPack'].@$_POST['idProductoPack']?>';
							          }
							        }
							    });
							    
								openConfirmacion();
							</script>
							</form>
							</section>
							</body>
						</html>
						<?php 
						exit;
				} else {
					$mensaje = 'No se ha podido a&ntilde;adir el producto al Pack';
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Nuevo Producto Pack</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<div id="tituloProveedores">
				<?php
				$seleccionado = @$_GET['idCategoria'];
				if ($seleccionado) {
					$_SESSION['idCategoriaPP'] = $seleccionado;
				} else {
					$seleccionado = @$_SESSION['idCategoriaPP'];
				}
				$seleccionadoSub = @$_GET['idsubcategoria'];
				if ($seleccionadoSub) {
					if ($seleccionadoSub=='-1') {
						$_SESSION['idsubcategoriaPP'] = NULL;
						$seleccionadoSub = NULL;
					} else {
						$_SESSION['idsubcategoriaPP'] = $seleccionadoSub;
					}
				} else {
					$seleccionadoSub = @$_SESSION['idsubcategoriaPP'];
				}
				$bnombre = @$_GET['bnombre'];
				if (isset($bnombre)) {
					if (strlen($bnombre)==0) {
						$_SESSION['bnombrePP'] = NULL;
						$bnombre = NULL;
					} else {
						$_SESSION['bnombrePP'] = $bnombre;
					}
				} else {
					$bnombre = @$_SESSION['bnombrePP'];
				}
				?>
				<span>CATEGOR&Iacute;A:</span>
				<select name="categorias" id="categorias" onchange="document.location='nuevoProductoPack.php?idProductoPack=<?=@$_GET['idProductoPack'].@$_POST['idProductoPack']?>&idsubcategoria=-1&idCategoria='+this.value;">
					<option value="-1">Seleccione una categor&iacute;a...</option>
					<?php optionsCategorias($seleccionado); ?>
				</select>
				<br/>
				<span>SUBCATEGOR&Iacute;A:</span>
				<select name="subcategorias" id="subcategorias" onchange="document.location='nuevoProductoPack.php?idProductoPack=<?=@$_GET['idProductoPack'].@$_POST['idProductoPack']?>&idsubcategoria='+this.value;">
					<?php optionsSubCategorias2($seleccionado, $seleccionadoSub); ?>
				</select>
				<br/>
				<span>NOMBRE:</span>
				<input type="text" id="bnombre" name="bnombre" value="<?=@$bnombre?>" size="20" /> 
				<input type="button" id="buscar" name="buscar" value="Buscar" onclick="document.location='nuevoProductoPack.php?idProductoPack=<?=@$_GET['idProductoPack'].@$_POST['idProductoPack']?>&bnombre='+$('#bnombre').val()" />
				<input type="button" id="limpiar" name="limpiar" value="Limpiar" onclick="document.location='nuevoProductoPack.php?idProductoPack=<?=@$_GET['idProductoPack'].@$_POST['idProductoPack']?>&bnombre='" />
			</div>
			<br/><br/>
			<input id="idProductoPack" name="idProductoPack" type="hidden" value="<?=@$_GET['idProductoPack'].@$_POST['idProductoPack']?>" />
			<?php 
				$consulta = "SELECT P.* , U.DESCRIPCION AS UNIDAD FROM PRODUCTOS P, UNIDADES U WHERE P.UNIDAD_MEDIDA = U.ID_UNIDAD";
				if (isset($_SESSION['idCategoriaPP']) && $_SESSION['idCategoriaPP']!='-1' && strlen($_SESSION['idCategoriaPP'])>0) {
					$consulta.=" AND ID_CATEGORIA=".$_SESSION['idCategoriaPP'];
					if (isset($seleccionadoSub) && strlen($seleccionadoSub)>0) {
						$consulta.=" AND ID_SUBCATEGORIA='$seleccionadoSub'";
					}
				}
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
							?>
							<option value="<?=$filaP['ID_PRODUCTO']?>"><?=$filaP['DESCRIPCION'].' '.$filaP['UNIDAD']?></option>
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
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='editarProducto.php?idProducto=<?=@$_GET['idProductoPack'].@$_POST['idProductoPack']?>'" />
			</div>
		</form>
	</section>
</body>
</html>

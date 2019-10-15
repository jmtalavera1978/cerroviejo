<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<link href="../css/jquery-te-1.4.0.css" rel="stylesheet" media="all" />
	<script type="text/javascript" src="../js/compra.js"></script>
	<script type="text/javascript" src="../js/funciones.js"></script>
	<script type="text/javascript" src="../js/jquery-te-1.4.0.min.js"></script>
	<script>
		$(function() {
			 $(function() {
				 $( "#tabs" ).tabs();
			 });
		});
	</script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="editarProducto.php" enctype="multipart/form-data" >
		<?php 
			// Comprobar si hay pedidos de este producto, en cuyo caso no se podrán editar algunos campos
			$hayPedidos = consulta ("select count(*) as numPedidos from PEDIDOS_PRODUCTOS where ID_PRODUCTO='".@$_GET['idProducto'].@$_POST['idProducto']."'");
			$hayPedidos = extraer_registro($hayPedidos);
			$hayPedidos = $hayPedidos['numPedidos'];
			if ($hayPedidos>0) {
				$hayPedidos = TRUE;
			} else {
				$hayPedidos = FALSE;
			}
		
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idProducto = @$_POST['idProducto'];
				$descripcion = @$_POST['descripcion'];
				$descripcion_larga = @$_POST['descripcion_larga'];
				$categorias = @$_POST['categorias'];
				$subcategorias = @$_POST['subcategorias'];
				$clasificacion = @$_POST['clasificacion'];
				$unidad = @$_POST['unidad'];
				$descMedida = @$_POST['descMedida'];
				$recargo = @$_POST['recargo'];
				$ilimitado = @$_POST['ilimitado'];
				$proveedor1 = @$_POST['proveedor1'];
				$cantidad1 = @$_POST['cantidad1'];
				$proveedor2 = @$_POST['proveedor2'];
				$cantidad2 = @$_POST['cantidad2'];
				$activo = @$_POST['activo'];
				$cuartos = @$_POST['cuartos'];
				$novedad = @$_POST['novedad'];
				$oferta = @$_POST['oferta'];
				$ecologico = @$_POST['ecologico'];
				$peso_unidad = @$_POST['peso_unidad'];
				
				$vegano = @$_POST['vegano'];
				$sin_gluten = @$_POST['sin_gluten'];
				$comercio_justo = @$_POST['comercio_justo'];
				$km0 = @$_POST['km0'];
				$provincia = @$_POST['provincia'];
				$sin_lactosa = @$_POST['sin_lactosa'];
				$gourmet = @$_POST['gourmet'];
				
				$dependeCesta =  @$_POST['dependeCesta'];
				$precioCesta1 =  @$_POST['precioCesta1'];
				$precioCesta2 =  @$_POST['precioCesta2'];
				$precio1 =  @$_POST['precio1'];
				$precio2 =  @$_POST['precio2'];
				$precio3 =  @$_POST['precio3'];
				
				$minimo =  @$_POST['minimo'];

				$precio = @$_POST['precio'];
				$iva =  @$_POST['iva'];
				$importeSinIVA =  @$_POST['importeSinIVA'];
				$precio = round(($importeSinIVA + ($importeSinIVA * $iva / 100)), 2);
				
				try {
					if ($hayPedidos) {
						$res = consulta ("update PRODUCTOS set DESCRIPCION='$descripcion', DESCRIPCION_LARGA='$descripcion_larga',
							ID_CATEGORIA='$categorias', ID_SUBCATEGORIA='$subcategorias', ID_CLASE_PRODUCTO='$clasificacion', PRECIO='$precio',
								DESCRIPCION_MEDIDA='$descMedida', RECARGO='$recargo',
							CANTIDAD_ILIMITADA='".(isset($ilimitado) ? '1' : '0')."',
							CANTIDAD_1='$cantidad1',
							CANTIDAD_2='$cantidad2',
							ACTIVO='".(isset($activo) ? '1' : '0')."',
							INC_CUARTOS='".(isset($cuartos) ? '1' : '0')."',
							NOVEDAD='".(isset($novedad) ? '1' : '0')."',
							OFERTA='".(isset($oferta) ? '1' : '0')."',
							ECOLOGICO='".(isset($ecologico) ? '1' : '0')."',
							PESO_POR_UNIDAD='$peso_unidad',
							VEGANO='".(isset($vegano) ? '1' : '0')."',
							SIN_GLUTEN='".(isset($sin_gluten) ? '1' : '0')."',
							SIN_LACTOSA='".(isset($sin_lactosa) ? '1' : '0')."',
							GOURMET='".(isset($gourmet) ? '1' : '0')."',
							COMERCIO_JUSTO='".(isset($comercio_justo) ? '1' : '0')."',
							KM0='".(isset($km0) ? '1' : '0')."',
							PROVINCIA='$provincia',
							DEPENDE_CESTA='".(isset($dependeCesta) ? '1' : '0')."',
							PRECIO_CESTA_1='$precioCesta1',
							PRECIO_CESTA_2='$precioCesta2',
							PRECIO_1='$precio1',
							PRECIO_2='$precio2',
							PRECIO_3='$precio3',
							PEDIDO_MINIMO='$minimo',
							TIPO_IVA='$iva',
							IMPORTE_SIN_IVA='$importeSinIVA'
							WHERE ID_PRODUCTO='$idProducto'");
					} else {
						$res = consulta ("update PRODUCTOS set DESCRIPCION='$descripcion', DESCRIPCION_LARGA='$descripcion_larga', 
							ID_CATEGORIA='$categorias', ID_SUBCATEGORIA='$subcategorias', ID_CLASE_PRODUCTO='$clasificacion', PRECIO='$precio',
							UNIDAD_MEDIDA='$unidad', DESCRIPCION_MEDIDA='$descMedida', RECARGO='$recargo',
							CANTIDAD_ILIMITADA='".(isset($ilimitado) ? '1' : '0')."',
							PROVEEDOR_1='$proveedor1', CANTIDAD_1='$cantidad1',
							PROVEEDOR_2='$proveedor2', CANTIDAD_2='$cantidad2',
							ACTIVO='".(isset($activo) ? '1' : '0')."',
							INC_CUARTOS='".(isset($cuartos) ? '1' : '0')."',
							NOVEDAD='".(isset($novedad) ? '1' : '0')."',
							OFERTA='".(isset($oferta) ? '1' : '0')."',
							ECOLOGICO='".(isset($ecologico) ? '1' : '0')."',
							PESO_POR_UNIDAD='$peso_unidad',
							VEGANO='".(isset($vegano) ? '1' : '0')."',
							SIN_GLUTEN='".(isset($sin_gluten) ? '1' : '0')."',
							SIN_LACTOSA='".(isset($sin_lactosa) ? '1' : '0')."',
							GOURMET='".(isset($gourmet) ? '1' : '0')."',
							COMERCIO_JUSTO='".(isset($comercio_justo) ? '1' : '0')."',
							KM0='".(isset($km0) ? '1' : '0')."',
							PROVINCIA='$provincia',
							DEPENDE_CESTA='".(isset($dependeCesta) ? '1' : '0')."',
							PRECIO_CESTA_1='$precioCesta1',
							PRECIO_CESTA_2='$precioCesta2',
							PRECIO_1='$precio1',
							PRECIO_2='$precio2',
							PRECIO_3='$precio3',
							PEDIDO_MINIMO='$minimo',
							TIPO_IVA='$iva',
							IMPORTE_SIN_IVA='$importeSinIVA'
							WHERE ID_PRODUCTO='$idProducto'");
					}
					$mensaje = '';
					if ($res == 1 && isset($_FILES['foto']) && $_FILES['foto']['size'] > 0) {
						
						// Temporary file name stored on the server
						$tmpName  = $_FILES['foto']['tmp_name'];
						$ext = strtolower(substr($_FILES['foto']['name'], strlen($_FILES['foto']['name'])-3));
						
						if ($ext!='jpg' && $ext!='jpeg' && $ext!='jpe' && $ext!='gif' && $ext!='png') {
							$mensaje = 'Solo se pueden subir ficheros de tipo imagen. ';
						} else {
							copy($tmpName,'../fotos/'.$idProducto.'.jpg');
						}
					}
			
					if ($res == 1) {
						$mensaje .= 'Producto modificado correctamente.';
					} else {
						$mensaje .= 'No se ha podido modificar el producto';
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje .= $ex->qetMessage();
				}
			}
		?>
		<div id="contenidoAdmin">
			<?php
			 if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5>".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			 } 
			?>
			<h1 class="cal">Modificaci&oacute;n de Producto</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			
			$resultado = consulta("select * from PRODUCTOS WHERE ID_PRODUCTO=".@$_GET['idProducto'].@$_POST['idProducto']);
			$producto = extraer_registro($resultado);
			$pvp = calculaPVP ($producto['ID_PRODUCTO'], $producto['IMPORTE_SIN_IVA'], $producto['TIPO_IVA']);
			?>
			<div id="tabs">
			<ul>
				<li><a href="#tabs-1">Principal</a></li>
				<li><a href="#tabs-2">Stock</a></li>
				<li><a href="#tabs-3">Propiedades</a></li>
				<li><a href="#tabs-4">Coste seg&uacute;n pedido</a></li>
			</ul>
			<div id="tabs-1">
			<table class="tablaResultados">
				<tr>
					<td><label for="idProducto">Id. Producto:</label></td>
					<td colspan="2"><input type="text" id="idProducto" name="idProducto" readonly="readonly" value="<?=$producto['ID_PRODUCTO']?>" size="5" required="required"/></td>
				</tr>
				<tr>
					<td><label for="descripcion">Nombre del Producto:</label></td>
					<td colspan="2"><input type="text" id="descripcion" name="descripcion" value="<?=$producto['DESCRIPCION']?>" size="40" maxlength="100" required="required"/></td>
				</tr>
				<tr>
					<td><label for="descripcion_larga">Descripción del Producto:</label></td>
					<td colspan="2">
						<textarea class="jqte-test" id="descripcion_larga" name="descripcion_larga" cols="120" rows="10"><?=$producto['DESCRIPCION_LARGA']?></textarea>
					</td>
				</tr>
				<tr>
					<td><label for="categorias">Categor&iacute;a:</label></td>
					<td colspan="2">
						<select name="categorias" id="categorias" required="required" onchange="seleccionarCategoria (this.value, 'subcategorias');">
						<?php optionsCategorias($producto['ID_CATEGORIA']); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="subcategorias">SubCategor&iacute;a:</label></td>
					<td colspan="2">
						<select name="subcategorias" id="subcategorias" required="required" onchange="seleccionarClasificacion (this.value, 'clasificacion');">
						<?php optionsSubCategorias($producto['ID_CATEGORIA'], $producto['ID_SUBCATEGORIA']); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="clasificacion">Clasificaci&oacute;n:</label></td>
					<td colspan="2">
						<select name="clasificacion" id="clasificacion" required="required">
						<?php optionsClasificacion($producto['ID_CATEGORIA'], $producto['ID_SUBCATEGORIA'], $producto['ID_CLASE_PRODUCTO']); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="importeSinIva">Importe Sin IVA:</label></td>
					<td colspan="2"><input type="text" id="importeSinIVA" name="importeSinIVA"  onblur="calculaPrecioConIVA();" value="<?=$producto['IMPORTE_SIN_IVA']?>" size="5" required="required"/> &euro;</td>
				</tr>
				<tr>
					<td><label for="iva">Tipo IVA:</label></td>
					<td colspan="2"><input type="text" id="iva" name="iva" onkeypress="return NumCheck(event, this)" onblur="calculaPrecioConIVA();" value="<?=$producto['TIPO_IVA']?>" size="5" required="required"/> %</td>
				</tr>
				<tr>
					<td><label for="precio">Precio:</label></td>
					<td colspan="2"><input type="text" id="precio" name="precio" onkeypress="return NumCheck(event, this)" min="0" readonly="readonly" value="<?=$producto['PRECIO']?>" size="5" required="required"/> &euro;</td>
				</tr>
				<tr>
					<td><label for="recargo">Recargo:</label></td>
					<td colspan="2"><input type="text" id="recargo" name="recargo" onkeypress="return NumCheck(event, this)" onblur="calculaPrecioConIVA();" value="<?=$producto['RECARGO']?>" size="5"/> %</td>
				</tr>
				<tr>
					<td><label for="precio">P.V.P:</label></td>
					<td colspan="2">
						<input type="hidden" id="recargosubcat" value="<?=calculaRecargoProductoCatYSubCat($idProducto)?>"/>
						<input type="text" id="pvp" name="pvp" onkeypress="return NumCheck(event, this)" min="0" readonly="readonly" value="<?=$pvp?>" size="5" required="required"/> &euro;
					</td>
				</tr>
				<tr>
					<td><label for="minimo">Pedido M&iacute;nimo:</label></td>
					<td colspan="2"><input type="text" id="minimo" name="minimo" onkeypress="return NumCheck(event, this)" value="<?=$producto['PEDIDO_MINIMO']?>" size="5"/></td>
				</tr>
				<tr>
					<td><label for="unidad">Unidad:</label></td>
					<td colspan="2">
						<select name="unidad" id="unidad" required="required" <?=($hayPedidos == TRUE ? "disabled=\"disabled\"" : "")?> <?=($hayPedidos == TRUE ? "readonly=\"readonly\"" : "")?>>
						<?php optionsUnidades($producto['UNIDAD_MEDIDA']); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="descMedida">Descripción de la Unidad:</label></td>
					<td colspan="2"><input type="text" id="descMedida" name="descMedida" value="<?=$producto['DESCRIPCION_MEDIDA']?>" size="40" maxlength="100" title="Descripción de la unidad, p.ej.: (Lata) de 250 gramos"/></td>
				</tr>
				<tr>
					<td><label for="peso_unidad">Peso por unidad (0 si no aplica):</label></td>
					<td colspan="2"><input type="text" id="peso_unidad" name="peso_unidad" onkeypress="return NumCheck(event, this)" value="<?=$producto['PESO_POR_UNIDAD']?>" size="5"/> Kg</td>
				</tr>
				<tr>
					<td><label for="foto">Imagen:</label></td>
					<td valign="middle">
						<input id="foto" name="foto" accept="image/jpeg" type="file">
					</td>
					<td valign="middle" rowspan="8">
						<?php  $ruta_imagen = "../fotos/".$producto['ID_PRODUCTO'].".jpg";
								
						if (@file_exists ($ruta_imagen)) {?>
							<img alt="<?=$producto['DESCRIPCION']?>" style="cursor: pointer; cursor: hand;"
								src="<?=$ruta_imagen?>"	/>
						<?php } else { ?>
							<img alt="<?=$producto['DESCRIPCION']?>" src="../img/sinfoto.gif" style="cursor: pointer; cursor: hand;"/>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td><label for="activo">Activo:</label></td>
					<td><input id="activo" name="activo" type="checkbox" <?=$producto['ACTIVO'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label for="activo">Incremento en cuartos:</label></td>
					<td><input id="cuartos" name="cuartos" type="checkbox" <?=$producto['INC_CUARTOS'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
			</table>
			</div>
			
			<div id="tabs-2">
			<table class="tablaResultados">
				<tr>
					<td><label for="ilimitado">Cantidad ilimitada:</label></td>
					<td colspan="2">
						<input id="ilimitado" name="ilimitado" type="checkbox" <?=$producto['CANTIDAD_ILIMITADA'] == 1 ? 'checked=\"checked\"' : ''?> value="1" onchange="verificaCheckIlimitado()" />
					</td>
				</tr>
				<tr>
					<td><label for="proveedor1">Proveedor 1:</label></td>
					<td colspan="2">
						<select name="proveedor1" id="proveedor1" required="required" <?=($hayPedidos == TRUE ? "disabled=\"disabled\"" : "")?> <?=($hayPedidos == TRUE ? "readonly=\"readonly\"" : "")?>>
						<?php optionsProveedores($producto['PROVEEDOR_1']); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="cantidad1">M&aacute;ximo Proveedor 1:</label></td>
					<td colspan="2"><input type="text" id="cantidad1" name="cantidad1" required="required" onkeypress="return NumCheck(event, this)"  onblur="cambiaValorCantidad (this.value)" value="<?=$producto['CANTIDAD_1']?>" size="5"/></td>
				</tr>
				<tr>
					<td><label for="proveedor2">Proveedor 2:</label></td>
					<td colspan="2">
						<select name="proveedor2" id="proveedor2" <?=($hayPedidos == TRUE ? "disabled=\"disabled\"" : "")?> <?=($hayPedidos == TRUE ? "readonly=\"readonly\"" : "")?>>
							<option value="">Seleccione un segundo proveedor opcional..</option>
						<?php optionsProveedores($producto['PROVEEDOR_2']); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="cantidad2">M&aacute;ximo Proveedor 2:</label></td>
					<td colspan="2"><input type="text" id="cantidad2" name="cantidad2" onkeypress="return NumCheck(event, this)"  onblur="cambiaValorCantidad (this.value)" value="<?=$producto['CANTIDAD_2']?>" size="5"/></td>
				</tr>
			</table>
			</div>
			
			<div id="tabs-3">
			<table class="tablaResultados">
				<tr>
					<td><label for="activo">Novedad:</label></td>
					<td><input id="novedad" name="novedad" type="checkbox" <?=$producto['NOVEDAD'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label for="activo">Oferta:</label></td>
					<td><input id="oferta" name="oferta" type="checkbox" <?=$producto['OFERTA'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label for="activo">Ecológico:</label></td>
					<td><input id="ecologico" name="ecologico" type="checkbox" <?=$producto['ECOLOGICO'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				
				<tr>
					<td><label for="vegano">Vegano:</label></td>
					<td><input id="vegano" name="vegano" type="checkbox" <?=$producto['VEGANO'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label for="sin_gluten">Sin gluten:</label></td>
					<td ><input id="sin_gluten" name="sin_gluten" type="checkbox" <?=$producto['SIN_GLUTEN'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label for="comercio_justo">Comercio Justo:</label></td>
					<td colspan="2"><input id="comercio_justo" name="comercio_justo" type="checkbox" <?=$producto['COMERCIO_JUSTO'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label for="km0">KM 0:</label></td>
					<td colspan="2"><input id="km0" name="km0" type="checkbox" <?=$producto['KM0'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label for="sin_lactosa">Sin Lactosa:</label></td>
					<td ><input id="sin_lactosa" name="sin_lactosa" type="checkbox" <?=$producto['SIN_LACTOSA'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label for="gourmet">Gourmet:</label></td>
					<td ><input id="gourmet" name="gourmet" type="checkbox" <?=$producto['GOURMET'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label for="provincia">Provincia:</label></td>
					<td colspan="2"><input type="text" id="provincia" name="provincia" value="<?=$producto['PROVINCIA']?>" size="30" maxlength="30"/></td>
				</tr>
			</table>
			</div>
			
			<div id="tabs-4">
			<table class="tablaResultados">
				<tr>
					<td><label for="dependeCesta">Depende del Total del Pedido:</label></td>
					<td colspan="2"><input id="dependeCesta" name="dependeCesta" type="checkbox" <?=$producto['DEPENDE_CESTA'] == 1 ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label>Total Pedido (&euro;):</label></td>
					<td colspan="2">
						0 &lt;
						<input type="text" id="precioCesta1" name="precioCesta1" onkeypress="return NumCheck(event, this)"  value="<?=$producto['PRECIO_CESTA_1']?>" size="5"/>
						&lt;
						<input type="text" id="precioCesta2" name="precioCesta2" onkeypress="return NumCheck(event, this)"  value="<?=$producto['PRECIO_CESTA_2']?>" size="5"/>
						&lt;
						Más
					</td>
				</tr>
				<tr>
					<td><label>Precio según Rango Pedido (&euro;):</label></td>
					<td colspan="2">
						<input type="text" id="precio1" name="precio1" onkeypress="return NumCheck(event, this)"  value="<?=$producto['PRECIO_1']?>" size="5"/>
						-
						<input type="text" id="precio2" name="precio2" onkeypress="return NumCheck(event, this)"  value="<?=$producto['PRECIO_2']?>" size="5"/>
						-
						<input type="text" id="precio3" name="precio3" onkeypress="return NumCheck(event, this)"  value="<?=$producto['PRECIO_3']?>" size="5"/>
					</td>
				</tr>
			</table>
			</div>
			</div>
			<br/>
			
			<h1 class="cal">Productos del Pack</h1>
			<div id="listadoProductos" style="position: relative; top: -30px">
				<?php 
					$consulta = "SELECT P.*, PP.CANTIDAD from PRODUCTOS_PACK PP, PRODUCTOS P WHERE PP.ID_PRODUCTO=P.ID_PRODUCTO AND ID_PRODUCTO_PACK='".@$_GET['idProducto'].@$_POST['idProducto']."' ORDER BY P.DESCRIPCION";
					$resSubcategorias = consulta($consulta);
				?>
				<table class="tablaResultados">
					<thead>
						<tr>
							<th align="center">ID.</th>
							<th>DESCRIPCI&Oacute;N</th>
							<th>CANTIDAD</th>
							<th align="center">ACTIVO</th>
							<th align="center">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($resSubcategorias)==0) {
	?>
						<tr>
							<td colspan="5">No hay productos asociados como pack</td>
						</tr>
	<?php 
					} else {

						while ($filaSubcat = extraer_registro($resSubcategorias)) {
	?>
						<tr>
							<td align="center"><?=$filaSubcat['ID_PRODUCTO']?></td>
							<td><?=$filaSubcat['DESCRIPCION']?></td>
							<td align="center"><?=$filaSubcat['CANTIDAD']?></td>
							<td align="center">
								<input type="checkbox" readonly="readonly" disabled="disabled" <?=$filaSubcat['ACTIVO'] == 1 ? 'checked=\"checked\"' : ''?>"/>
							</td>
							<td align="center"><a href='javascript:openConfirmacion(<?=$filaSubcat['ID_PRODUCTO']?>);'><img src="../img/BORRAR.png" alt="eliminar" width="32"/></a>&nbsp;</td>
						</tr>
	<?php 
						}
					}
	?>
					</tbody>
				</table>
				<div id="dialogElim" title="">
					¿Desea eliminar el producto del Pack?
				</div>
					
				<script>
					var idProducto = '';
					function openConfirmacion(idProductoSel) {
						idProducto = idProductoSel;
						$("#dialogElim").dialog("open");
					}
					
					$("#dialogElim").dialog({
					  autoOpen: false,
					  height: 250,
					  width: 400,
					  modal: true,
					  buttons : {
						  "Sí" : function() {
							  document.location='eliminar_producto_pack.php?idProductoPack=<?=@$_GET['idProducto'].@$_POST['idProducto']?>&idProducto='+idProducto+'&url=editarProducto.php?idProducto=<?=@$_GET['idProducto'].@$_POST['idProducto']?>';
						  },
						  "No" : function() {
							  $("#dialogElim").dialog("close");
						  }
						}
					});
				</script>
			</div>
		</div>
		
		<br/>
		<div id="botonera">
			<input id="addProducto" name="addProducto"  type="button" value="A&ntilde;adir Producto al Pack" onclick="document.location='nuevoProductoPack.php?idProductoPack=<?=@$_GET['idProducto'].@$_POST['idProducto']?>'" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input id="nuevo" name="nuevo"  type="button" value="Nuevo producto" onclick="document.location='nuevoProducto.php'" />
			<input id="submit" name="submit"  type="submit" value="Grabar" />
			<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='productos.php'" />
		</div>
		</form>
	</section>
</body>
</html>

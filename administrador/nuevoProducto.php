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
		<form method="post" action="nuevoProducto.php" enctype="multipart/form-data">
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
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
				$precio = round( ($importeSinIVA + ($importeSinIVA*$iva/100)) , 2);
			
				try {
					$res = consulta ("insert INTO PRODUCTOS (DESCRIPCION, DESCRIPCION_LARGA, ID_CATEGORIA, 
							ID_SUBCATEGORIA, ID_CLASE_PRODUCTO, PRECIO, UNIDAD_MEDIDA, DESCRIPCION_MEDIDA, RECARGO,
							CANTIDAD_ILIMITADA, PROVEEDOR_1, CANTIDAD_1, PROVEEDOR_2, CANTIDAD_2, ACTIVO, 
							INC_CUARTOS, NOVEDAD, OFERTA, ECOLOGICO, PESO_POR_UNIDAD, VEGANO, SIN_GLUTEN, 
							SIN_LACTOSA, COMERCIO_JUSTO, KM0, PROVINCIA,
							DEPENDE_CESTA, PRECIO_CESTA_1, PRECIO_CESTA_2, PRECIO_1, PRECIO_2, PRECIO_3, 
							GOURMET, PEDIDO_MINIMO, TIPO_IVA, IMPORTE_SIN_IVA) 
							VALUES ('$descripcion', '$descripcion_larga', '$categorias', '$subcategorias', '$clasificacion'
							, '$precio', '$unidad', '$descMedida', '$recargo'
							, '".(isset($ilimitado) ? '1' : '0')."', '$proveedor1',
							'$cantidad1', '$proveedor2', '$cantidad2', '".(isset($activo) ? '1' : '0')."'
							, '".(isset($cuartos) ? '1' : '0')."', '".(isset($novedad) ? '1' : '0')."'
							, '".(isset($oferta) ? '1' : '0')."',
							'".(isset($ecologico) ? '1' : '0')."', '$peso_unidad'
							, '".(isset($vegano) ? '1' : '0')."'
							, '".(isset($sin_gluten) ? '1' : '0')."', '".(isset($sin_lactosa) ? '1' : '0')."'
							, '".(isset($comercio_justo) ? '1' : '0')."'
							, '".(isset($km0) ? '1' : '0')."'
							, '$provincia', '".(isset($dependeCesta) ? '1' : '0')."', '$precioCesta1'
							, '$precioCesta2', '$precio1', '$precio2', '$precio3'
							, '".(isset($gourmet) ? '1' : '0')."', '$minimo', '$iva', '$importeSinIVA')");
					
					$idNuevo = get_new_id('ID_PRODUCTO', 'PRODUCTOS');
					
					$mensaje = '';
					if ($res == 1 && isset($_FILES['foto']) && $_FILES['foto']['size'] > 0) {
					
						// Temporary file name stored on the server
						$tmpName  = $_FILES['foto']['tmp_name'];
						$ext = strtolower(substr($_FILES['foto']['name'], strlen($_FILES['foto']['name'])-3));
					
						if ($ext!='jpg' && $ext!='jpeg' && $ext!='jpe' && $ext!='gif' && $ext!='png') {
							$mensaje = 'Solo se pueden subir ficheros de tipo imagen. ';
						} else {
							copy($tmpName,'../fotos/'.$idNuevo.'.jpg');
						}
					}
			
					if ($res) {
						?>
							<div id="dialogNew" title="">
								Producto creado correctamente.<br/>¿Desea dar de alta un nuevo producto?</div>
								
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
							        	  document.location='nuevoProducto.php';
							          },
							          "No" : function() {
							        	  document.location='editarProducto.php?idProducto=<?=$idNuevo?>';
							          }
							        }
							    });
							    
								openConfirmacion();
							</script>
							</form></section>
							</body>
						</html>
						<?php 
						exit;
						
					} else {
						$mensaje .= 'No se ha podido crear el producto';
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje .= $ex->qetMessage();
				}
			}
		?>
		
		<div id="contenidoAdmin">
			<h1 class="cal">Nuevo Producto</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
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
					<td><label for="descripcion">Nombre del Producto:</label></td>
					<td><input type="text" id="descripcion" name="descripcion" value="" size="40" maxlength="100" required="required"/></td>
				</tr>
				<tr>
					<td><label for="descripcion_larga">Descripción del Producto:</label></td>
					<td>
						<textarea class="jqte-test" id="descripcion_larga" name="descripcion_larga" rows="3" cols="60" maxlength="255"></textarea>
					</td>
				</tr>
				<tr>
					<td><label for="categorias">Categor&iacute;a:</label></td>
					<td>
						<select name="categorias" id="categorias" required="required" onchange="seleccionarCategoria (this.value, 'subcategorias');">
							<option value="">Seleccione una categor&iacute;a...</option>
						<?php optionsCategorias(''); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="subcategorias">SubCategor&iacute;a:</label></td>
					<td colspan="2">
						<select id="subcategorias" name="subcategorias" required="required" onchange="seleccionarClasificacion (this.value, 'clasificacion');">
							<option value="">Selecciones una subcategor&iacute;a...</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="clasificacion">Clasificaci&oacute;n:</label></td>
					<td colspan="2">
						<select id="clasificacion" name="clasificacion" required="required">
						<?php optionsClasificacion('', '', ''); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="importeSinIVA">Importe Sin IVA:</label></td>
					<td><input type="text" id="importeSinIVA" name="importeSinIVA" onblur="calculaPrecioConIVA();" value="" size="5" required="required"/> &euro;</td>
				</tr>
				<tr>
					<td><label for="iva">Tipo IVA:</label></td>
					<td><input type="text" id="iva" name="iva" onkeypress="return NumCheck(event, this)" onblur="calculaPrecioConIVA();" value="" size="5" required="required"/> %</td>
				</tr>
				<tr>
					<td><label for="precio">Precio con IVA (sin recargo):</label></td>
					<td><input type="text" id="precio" name="precio" onkeypress="return NumCheck(event, this)" readonly="readonly" value="" size="5" required="required"/> &euro;</td>
				</tr>
				<tr>
					<td><label for="recargo">Recargo:</label></td>
					<td><input type="text" id="recargo" name="recargo" onkeypress="return NumCheck(event, this)" onblur="calculaPrecioConIVA();" value="0" size="5"/> %</td>
				</tr>
				<tr>
					<td><label for="precio">P.V.P:</label></td>
					<td colspan="2">
						<input type="hidden" id="recargosubcat" value="0"/>
						<input type="text" id="pvp" name="pvp" onkeypress="return NumCheck(event, this)" min="0" readonly="readonly" value="0" size="5" required="required"/> &euro;
					</td>
				</tr>
				<tr>
					<td><label for="minimo">Pedido M&iacute;nimo:</label></td>
					<td><input type="text" id="minimo" name="minimo" onkeypress="return NumCheck(event, this)" value="0" size="5"/></td>
				</tr>
				<tr>
					<td><label for="unidad">Unidad:</label></td>
					<td>
						<select name="unidad" id="unidad" required="required">
							<option value="">Seleccione una unidad de medida...</option>
						<?php optionsUnidades(''); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="descMedida">Descripción de la Unidad:</label></td>
					<td><input type="text" id="descMedida" name="descMedida" value="" size="40" maxlength="100" title="Descripción de la unidad, p.ej.: (Lata) de 250 gramos"/></td>
				</tr>
				<tr>
					<td><label for="peso_unidad">Peso por unidad (0 si no aplica):</label></td>
					<td><input type="text" id="peso_unidad" name="peso_unidad" onkeypress="return NumCheck(event, this)"  value="0" size="5"/> Kg</td>
				</tr>
				<tr>
					<td><label for="foto">Imagen:</label></td>
					<td><input id="foto" name="foto" accept="image/jpeg" type="file"></td>
				</tr>
				<tr>
					<td><label for="activo">Activo:</label></td>
					<td><input id="activo" name="activo" type="checkbox" checked="checked" value="1" /></td>
				</tr>
				<tr>
					<td><label for="cuartos">Incremento en cuartos:</label></td>
					<td colspan="2"><input id="cuartos" name="cuartos" type="checkbox" value="1" /></td>
				</tr>
			</table>
			</div>
			
			<div id="tabs-2">
			<table class="tablaResultados">
				<tr>
					<td><label for="ilimitado">Cantidad ilimitada:</label></td>
					<td colspan="2">
						<input id="ilimitado" name="ilimitado" type="checkbox" checked="checked" value="1" onchange="verificaCheckIlimitado()" />
					</td>
				</tr>
				<tr>
					<td><label for="proveedor1">Proveedor 1:</label></td>
					<td>
						<select name="proveedor1" id="proveedor1" required="required">
							<option value="">Seleccione un proveedor principal...</option>
						<?php optionsProveedores(''); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="cantidad1">M&aacute;ximo Proveedor 1:</label></td>
					<td><input type="text" id="cantidad1" name="cantidad1" onkeypress="return NumCheck(event, this)"  onblur="cambiaValorCantidad (this.value)" required="required" value="0" size="5"/></td>
				</tr>
				<tr>
					<td><label for="proveedor2">Proveedor 2:</label></td>
					<td>
						<select name="proveedor2" id="proveedor2">
							<option value="">Seleccione un segundo proveedor opcional..</option>
						<?php optionsProveedores(''); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="cantidad2">M&aacute;ximo Proveedor 2:</label></td>
					<td><input type="text" id="cantidad2" name="cantidad2" onkeypress="return NumCheck(event, this)"  onblur="cambiaValorCantidad (this.value)" value="0" size="5"/></td>
				</tr>
			</table>
			</div>
			
			<div id="tabs-3">
			<table class="tablaResultados">
				<tr>
					<td><label for="novedad">Novedad:</label></td>
					<td colspan="2"><input id="novedad" name="novedad" checked="checked" type="checkbox" value="1" /></td>
				</tr>
				<tr>
					<td><label for="oferta">Oferta:</label></td>
					<td colspan="2"><input id="oferta" name="oferta" type="checkbox" value="1" /></td>
				</tr>
				<tr>
					<td><label for="ecologico">Ecológico:</label></td>
					<td colspan="2"><input id="ecologico" name="ecologico" checked="checked" type="checkbox" value="1" /></td>
				</tr>
				
				<tr>
					<td><label for="vegano">Vegano:</label></td>
					<td colspan="2"><input id="vegano" name="vegano" type="checkbox" value="1" /></td>
				</tr>
				<tr>
					<td><label for="sin_gluten">Sin gluten:</label></td>
					<td colspan="2"><input id="sin_gluten" name="sin_gluten" type="checkbox" value="1" /></td>
				</tr>
				<tr>
					<td><label for="comercio_justo">Comercio Justo:</label></td>
					<td colspan="2"><input id="comercio_justo" name="comercio_justo" type="checkbox" value="1" /></td>
				</tr>
				<tr>
					<td><label for="km0">KM 0:</label></td>
					<td colspan="2"><input id="km0" name="km0" type="checkbox" value="1" /></td>
				</tr>
				<tr>
					<td><label for="sin_lactosa">Sin lactosa:</label></td>
					<td colspan="2"><input id="sin_lactosa" name="sin_lactosa" type="checkbox" value="1" /></td>
				</tr>
				<tr>
					<td><label for="gourmet">Gourmet:</label></td>
					<td colspan="2"><input id="gourmet" name="gourmet" type="checkbox" value="1" /></td>
				</tr>
				<tr>
					<td><label for="provincia">Provincia:</label></td>
					<td><input type="text" id="provincia" name="provincia" value="" size="30" maxlength="30"/></td>
				</tr>
			</table>
			</div>
			
			<div id="tabs-4">
			<table class="tablaResultados">
				<tr>
					<td><label for="dependeCesta">Depende del Total del Pedido:</label></td>
					<td colspan="2"><input id="dependeCesta" name="dependeCesta" type="checkbox" value="0" /></td>
				</tr>
				<tr>
					<td><label>Total Pedido (&euro;):</label></td>
					<td colspan="2">
						0 &lt;
						<input type="text" id="precioCesta1" name="precioCesta1" onkeypress="return NumCheck(event, this)"  value="" size="5"/>
						&lt;
						<input type="text" id="precioCesta2" name="precioCesta2" onkeypress="return NumCheck(event, this)"  value="" size="5"/>
						&lt;
						Más
					</td>
				</tr>
				<tr>
					<td><label>Precio según Rango Pedido (&euro;):</label></td>
					<td colspan="2">
						<input type="text" id="precio1" name="precio1" onkeypress="return NumCheck(event, this)"  value="" size="5"/>
						-
						<input type="text" id="precio2" name="precio2" onkeypress="return NumCheck(event, this)"  value="" size="5"/>
						-
						<input type="text" id="precio3" name="precio3" onkeypress="return NumCheck(event, this)"  value="" size="5"/>
					</td>
				</tr>
			</table>
			</div>
			</div>
		</div>
		<br/>
			<div id="botonera">
				<input id="submit" name="submit"  type="submit" value="Grabar" />
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='productos.php'" />
			</div>
		</form>
	</section>
</body>
</html>

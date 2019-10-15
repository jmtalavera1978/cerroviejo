<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="nuevaSubcategoria.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idCategoria = @$_POST['idCategoria'];
				$descripcion = @$_POST['descripcion'];
				$recargo = @$_POST['recargo'];
				$activo= @$_POST['activo'];
			
				$res = consulta ("insert INTO SUBCATEGORIAS (ID_CATEGORIA, DESCRIPCION, RECARGO, ACTIVO) 
							VALUES ('$idCategoria', '$descripcion', '$recargo', '".(isset($activo) ? '1' : '0')."')");
					
				$idNuevo = get_new_id('ID_SUBCATEGORIA', 'SUBCATEGORIAS');
		
				if ($res) {
						?>
							<div id="dialogNew" title="">
								Subcategoría creada correctamente.<br/>¿Desea dar de alta una nueva subcategoría?</div>
								
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
							        	  document.location='nuevaSubcategoria.php?idCategoria=<?=$idCategoria?>';
							          },
							          "No" : function() {
							        	  document.location='editarCategoria.php?idCategoria=<?=$idCategoria?>';
							          }
							        }
							    });
							    
								openConfirmacion();
							</script>
							</body>
						</html>
						<?php 
						exit;
				} else {
					$mensaje = 'No se ha podido crear la subcategoria';
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Nueva Subcategoria</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<input id="idCategoria" name="idCategoria" type="hidden" value="<?=@$_GET['idCategoria'].@$_POST['idCategoria']?>" />
			<table class="tablaResultados">
				<tr>
					<td><label for="descripcion">Nombre de la Subcategor&iacute;a:</label></td>
					<td><input type="text" id="descripcion" name="descripcion" value="" size="50" maxlength="50" required="required"/></td>
				</tr>
				<tr>
					<td><label for="recargo">Recargo:</label></td>
					<td><input type="text" id="recargo" name="recargo" onkeypress="return NumCheck(event, this)" value="0" size="5" required="required"/> %</td>
				</tr>
				<tr>
					<td><label for="activo">Activo:</label></td>
					<td><input id="activo" name="activo" type="checkbox" checked="checked" value="1" /></td>
				</tr>
			</table>
		</div>
		<div style="clear: both;"></div>
		<br/>
			<div id="botonera">
				<input id="submit" name="submit"  type="submit" value="Grabar" />
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='editarCategoria.php?idCategoria=<?=@$_GET['idCategoria'].@$_POST['idCategoria']?>'" />
			</div>
		</form>
	</section>
</body>
</html>

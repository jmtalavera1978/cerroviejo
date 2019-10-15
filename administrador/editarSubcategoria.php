<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="editarSubcategoria.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idCategoria = @$_POST['idCategoria'];
				$idSubcategoria = @$_POST['idSubcategoria'];
				$descripcion = @$_POST['descripcion'];
				$recargo = @$_POST['recargo'];
				$activo= @$_POST['activo'];
			
				$res = consulta ("update SUBCATEGORIAS set DESCRIPCION='$descripcion', RECARGO='$recargo', ACTIVO='".(isset($activo) ? '1' : '0')."'
						 WHERE ID_SUBCATEGORIA='$idSubcategoria'");
		
				if ($res == 1) {
					?>
					<script type="text/javascript">
					 	document.location='editarCategoria.php?idCategoria=<?=$idCategoria?>';
					</script>
					<?php
					//Header("Location: editarCategoria.php?idCategoria=$idCategoria");
				} else {
					$mensaje = 'No se ha podido modificar la subcategoria';
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Modificaci&oacute;n de Subcategor&iacute;a</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			
			$resultado = consulta("select * from SUBCATEGORIAS WHERE ID_SUBCATEGORIA=".@$_GET['idSubcategoria'].@$_POST['idSubcategoria']);
			$subcat = extraer_registro($resultado);
			?>
			<input id="idCategoria" name="idCategoria" type="hidden" value="<?=@$_GET['idCategoria'].@$_POST['idCategoria']?>" />
			<table class="tablaResultados">
				<tr>
					<td><label for="idSubcategoria">Id. Subcategor&iacute;a:</label></td>
					<td colspan="2"><input type="text" id="idSubcategoria" name="idSubcategoria" readonly="readonly" value="<?=$subcat['ID_SUBCATEGORIA']?>" size="5" required="required"/></td>
				</tr>
				<tr>
					<td><label for="descripcion">Nombre de la Subcategor&iacute;a:</label></td>
					<td><input type="text" id="descripcion" name="descripcion" value="<?=$subcat['DESCRIPCION']?>" size="50" maxlength="50" required="required"/></td>
				</tr>
				<tr>
					<td><label for="recargo">Recargo:</label></td>
					<td><input type="text" id="recargo" name="recargo" onkeypress="return NumCheck(event, this)" value="<?=$subcat['RECARGO']?>" size="5" required="required"/> %</td>
				</tr>
				<tr>
					<td><label for="activo">Activo:</label></td>
					<td><input id="activo" name="activo" type="checkbox" <?=$subcat['ACTIVO'] == '1' ? 'checked=\"checked\"' : ''?>  value="1" /></td>
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

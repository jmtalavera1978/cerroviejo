<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="editarCategoria.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idCategoria = @$_POST['idCategoria'];
				$descripcion = @$_POST['descripcion'];
				$recargo = @$_POST['recargo'];
				$activo= @$_POST['activo'];
			
				$res = consulta ("update CATEGORIAS set DESCRIPCION='$descripcion', RECARGO='$recargo', ACTIVO='".(isset($activo) ? '1' : '0')."' WHERE ID_CATEGORIA='$idCategoria'");
		
				if ($res == 1) {
					$mensaje = 'Categoría modificada correctamente.';
				} else {
					$mensaje = 'No se ha podido modificar la categoría.';
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
			<h1 class="cal">Modificaci&oacute;n de Categor&iacute;a</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			
			$resultado = consulta("select * from CATEGORIAS WHERE ID_CATEGORIA=".@$_GET['idCategoria'].@$_POST['idCategoria']);
			$categoria = extraer_registro($resultado);
			?>
			<table class="tablaResultados">
				<tr>
					<td><label for="idCategoria">Id. Categor&iacute;a:</label></td>
					<td colspan="2"><input type="text" id="idCategoria" name="idCategoria" readonly="readonly" value="<?=$categoria['ID_CATEGORIA']?>" size="5" required="required"/></td>
				</tr>
				<tr>
					<td><label for="descripcion">Nombre de la Categor&iacute;a:</label></td>
					<td><input type="text" id="descripcion" name="descripcion" value="<?=$categoria['DESCRIPCION']?>" size="50" maxlength="50" required="required"/></td>
				</tr>
				<tr>
					<td><label for="recargo">Recargo:</label></td>
					<td><input type="text" id="recargo" name="recargo" onkeypress="return NumCheck(event, this)" value="<?=$categoria['RECARGO']?>" size="5" required="required"/> %</td>
				</tr>
				<tr>
					<td><label for="activo">Activo:</label></td>
					<td><input id="activo" name="activo" type="checkbox" <?=$categoria['ACTIVO'] == '1' ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
			</table>
			<br/>
			
			<h1 class="cal">SubCategor&iacute;as asociadas</h1>
			<div id="listadoProductos" style="position: relative; top: -30px">
				<?php 
					$consulta = "SELECT * from SUBCATEGORIAS WHERE ID_CATEGORIA='".@$_GET['idCategoria'].@$_POST['idCategoria']."' ORDER BY DESCRIPCION";
					$resSubcategorias = consulta($consulta);
				?>
				<table class="tablaResultados">
					<thead>
						<tr>
							<th align="center">ID.</th>
							<th>DESCRIPCI&Oacute;N</th>
							<th align="center">RECARGO</th>
							<th align="center">ACTIVO</th>
							<th align="center">&nbsp;</th>
							<th align="center">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($resSubcategorias)==0) {
	?>
						<tr>
							<td colspan="5">No hay subcategor&iacute;as asociadas</td>
						</tr>
	<?php 
					} else {

						while ($filaSubcat = extraer_registro($resSubcategorias)) {
	?>
						<tr>
							<td align="center"><?=$filaSubcat['ID_SUBCATEGORIA']?></td>
							<td><?=$filaSubcat['DESCRIPCION']?></td>
							<td align="center"><?=($filaSubcat['RECARGO']==NULL || $filaSubcat['RECARGO']=='') ? 0 : $filaSubcat['RECARGO']?>%</td>
							<td align="center">
								<input type="checkbox" readonly="readonly" onclick="document.location='activarSubcategoria.php?idSubcategoria=<?=$filaSubcat['ID_SUBCATEGORIA']?>&url=editarCategoria.php?idCategoria=<?=@$_GET['idCategoria'].@$_POST['idCategoria']?>'" <?=$filaSubcat['ACTIVO'] == 1 ? 'checked=\"checked\"' : ''?>"/>
							</td>
							<td align="center"><a title="Editar Subcategoría" href="editarSubcategoria.php?idCategoria=<?=$filaSubcat['ID_CATEGORIA']?>&idSubcategoria=<?=$filaSubcat['ID_SUBCATEGORIA']?>"><img src="../img/EDITAR.png" alt="editar" width="32"/></a></td>
							<td align="center"><!--<a class="ui-icon ui-state-highlight ui-icon-circle-close" href='eliminar_subcategoria.php?idSubcategoria=<?=$filaSubcat['ID_SUBCATEGORIA']?>&url=editarCategoria.php?idCategoria=<?=@$_GET['idCategoria'].@$_POST['idCategoria']?>'>Eliminar</a>-->&nbsp;</td>
						</tr>
	<?php 
						}
					}
	?>
					</tbody>
				</table>
			</div>
		</div>
		<div style="clear: both;"></div>
		<br/>
			<div id="botonera">
				<input id="nuevo" name="nuevo"  type="button" value="Nueva subcategoría" onclick="document.location='nuevaSubcategoria.php?idCategoria=<?=@$_GET['idCategoria'].@$_POST['idCategoria']?>'" />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input id="submit" name="submit"  type="submit" value="Grabar" />
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='categorias.php'" />
			</div>
		</form>
	</section>
</body>
</html>

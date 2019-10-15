<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="nuevoSubgrupo.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idSubgrupo = @$_POST['idSubgrupo'];
				$nombre = @$_POST['nombre'];
			
				try {
					$res = consulta ("insert INTO SUBGRUPOS (NOMBRE) VALUES ('".$nombre."') ");
					
					$idNuevo = get_new_id('ID_SUBGRUPO', 'SUBGRUPOS');
						
					if ($res) {
						$_SESSION['mensaje_generico'] = 'Se ha creado el subgrupo correctamente.';
						echo "<script>document.location='editarSubgrupo.php?idSubgrupo=$idNuevo'</script>";
					} else {
						$mensaje = "No se ha podido crear el subgrupo";
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje = $ex->qetMessage();
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Nuevo subgrupo</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<table class="tablaResultados">
				<tr>
					<td><label for="nombre">Nombre:</label></td>
					<td><input type="text" id="nombre" name="nombre" value="" size="50" maxlength="255" required="required"/></td>
				</tr>
			</table>
		</div>
		<div style="clear: both;"></div>
		<br/>
			<div id="botonera">
				<input id="submit" name="submit"  type="submit" value="Grabar" />
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='subgrupos.php'" />
			</div>
		</form>
	</section>
</body>
</html>

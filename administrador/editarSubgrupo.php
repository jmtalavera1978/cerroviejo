<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="editarSubgrupo.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idSubgrupo = @$_POST['idSubgrupo'];
				$nombre = @$_POST['nombre'];
			
				try {
					$res = consulta ("UPDATE SUBGRUPOS set NOMBRE = '".$nombre."' WHERE ID_SUBGRUPO='".$idSubgrupo."' ");
						
					if ($res) {
						$mensaje = 'Se ha modificado el subgrupo correctamente.';
					} else {
						$mensaje = "No se ha podido modificar el subgrupo";
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje = $ex->qetMessage();
				}
			} else {
				$resultado = consulta("select * from SUBGRUPOS WHERE ID_SUBGRUPO='".@$_GET['idSubgrupo'].@$_POST['idSubgrupo']."'");
				$subgrupos = extraer_registro($resultado);
				$idSubgrupo = @$subgrupos['ID_SUBGRUPO'];
				$nombre = @$subgrupos['NOMBRE'];
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Modificación de Subgrupo</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5 style=\"text-align: left;\">".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			}
			?>
			
			<table class="tablaResultados">
				<tr>
					<td><label for="nombre">Id.:</label></td>
					<td><input type="text" id="idSubgrupo" name="idSubgrupo" value="<?=@$idSubgrupo?>" size="5" required="required" readonly="readonly"/></td>
				</tr>
				<tr>
					<td><label for="nombre">Nombre:</label></td>
					<td><input type="text" id="nombre" name="nombre" value="<?=@$nombre?>" size="50" maxlength="255" required="required"/></td>
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

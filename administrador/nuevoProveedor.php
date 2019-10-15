<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="nuevoProveedor.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idProveedor = @$_POST['idProveedor'];
				$nombre = @$_POST['nombre'];
				$descripcion = @$_POST['descripcion'];
			
				try {
					$res = consulta ("insert INTO PROVEEDORES (NOMBRE, DESCRIPCION) VALUES ('".$nombre."', '".$descripcion."') ");
					
					$idNuevo = get_new_id('ID_PROVEEDOR', 'PROVEEDORES');
						
					if ($res) {
						$_SESSION['mensaje_generico'] = 'Se ha creado el proveedor correctamente.';
						echo "<script>document.location='editarProveedor.php?idProveedor=$idNuevo'</script>";
					} else {
						$mensaje = "No se ha podido crear el proveedor";
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje = $ex->qetMessage();
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Nuevo proveedor</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<table class="tablaResultados">
				<tr>
					<td><label for="nombre">Nombre:</label></td>
					<td><input type="text" id="nombre" name="nombre" value="" size="50" maxlength="50" required="required"/></td>
				</tr>
				<tr>
					<td><label for="descripcion">Descripción:</label></td>
					<td><input type="text" id="descripcion" name="descripcion" value="" size="50" maxlength="100"/></td>
				</tr>
			</table>
		</div>
		<div style="clear: both;"></div>
		<br/>
			<div id="botonera">
				<input id="submit" name="submit"  type="submit" value="Grabar" />
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='proveedores.php'" />
			</div>
		</form>
	</section>
</body>
</html>

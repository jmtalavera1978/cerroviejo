<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="editarProveedor.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idProveedor = @$_POST['idProveedor'];
				$nombre = @$_POST['nombre'];
				$descripcion = @$_POST['descripcion'];
			
				try {
					$res = consulta ("UPDATE PROVEEDORES set NOMBRE = '".$nombre."', DESCRIPCION = '".$descripcion."' WHERE ID_PROVEEDOR='".$idProveedor."' ");
						
					if ($res) {
						$mensaje = 'Se ha modificado el proveedor correctamente.';
					} else {
						$mensaje = "No se ha podido modificar el proveedor";
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje = $ex->qetMessage();
				}
			} else {
				$resultado = consulta("select * from PROVEEDORES WHERE ID_PROVEEDOR='".@$_GET['idProveedor'].@$_POST['idProveedor']."'");
				$proveedoresR = extraer_registro($resultado);
				$idProveedor = @$proveedoresR['ID_PROVEEDOR'];
				$nombre = @$proveedoresR['NOMBRE'];
				$descripcion = @$proveedoresR['DESCRIPCION'];
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Modificación de Proveedor</h1>
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
					<td><input type="text" id="idProveedor" name="idProveedor" value="<?=@$idProveedor?>" size="5" required="required" readonly="readonly"/></td>
				</tr>
				<tr>
					<td><label for="nombre">Nombre:</label></td>
					<td><input type="text" id="nombre" name="nombre" value="<?=@$nombre?>" size="50" maxlength="50" required="required"/></td>
				</tr>
				<tr>
					<td><label for="descripcion">Descripción:</label></td>
					<td><input type="text" id="descripcion" name="descripcion" value="<?=@$descripcion?>" size="50" maxlength="100"/></td>
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

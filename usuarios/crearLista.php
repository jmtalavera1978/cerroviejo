<?php require_once "../includes/lib_carrito.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head_interno.inc.php"; ?>
</head>
<body>
	 <?php require_once "../template/cabeceraUsuarios.inc.php"; ?>
	 <div class="wrapper">
		<div class="grids top">
		<?php require_once "../template/menuLateralUsuarios.inc.php"; ?>

			
		<form method="post" action="crearLista.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$nombre = @$_POST['nombre'];
				$usuario = $_SESSION['ID_USUARIO'];
				
				try {
					$res = consulta ("INSERT INTO LISTAS (ID_USUARIO, NOMBRE) values ('$usuario', '$nombre')");
						
					if ($res) {
						$mensaje = 'Lista creada correctamente.';
					} else {
						$mensaje = 'No se ha podido crear la lista.';
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje = $ex->qetMessage();
				}			
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Crear Lista de la Compra</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<table class="tablaResultados" style="width: 100%">
				<tr>
					<td><label for="nombre">&nbsp;Nombre de la lista:</label></td>
					<td><input type="nombre" id="nombre" name="nombre" value="" size="60" maxlength="100" required="required"/></td>
				</tr>
			</table>
		</div>
		<br/>
			<div id="botonera" style="margin-right: 11%">
				<input id="submit" name="submit"  type="submit" value="Guardar" />
				<input id="volver" name="volver"  type="button" onclick="document.location='mislistas.php'" value="Volver" />
			</div>
		</form>
	

        	</div>	
		</div>
	</div>
</div>

<?php require_once "../template/pie.inc.php";  ?>
</body>
</html>
		
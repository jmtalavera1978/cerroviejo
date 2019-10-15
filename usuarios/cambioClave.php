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

			
		<form method="post" action="cambioClave.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$claveOld = @$_POST['claveOld'];
				$claveNew = @$_POST['claveNew'];
				$claveNewRep = @$_POST['claveNewRep'];
				$usuario = $_SESSION['ID_USUARIO'];
				
				if (strcmp ($claveNew, $claveNewRep)!=0 || strlen($claveNew)<6) {
					$mensaje = 'La contraseña nueva no coincide o tiene menos de 6 caracteres.';
				} else {
					try {
						consulta ("update USUARIOS set CLAVE='$claveNew' WHERE ID_USUARIO='$usuario' AND CLAVE='$claveOld'");
						
						$res = consulta ("select * from USUARIOS WHERE ID_USUARIO='$usuario' AND CLAVE='$claveNew'");
							
						if (numero_filas($res)==1) {
							$mensaje = 'Clave modificada correctamente.';
						} else {
							$mensaje = 'No se ha podido modificar la clave o no coincide la clave anterior.';
						}
					} catch (Exception $ex) {
						//Devuelve el mensaje de error
						$mensaje = $ex->qetMessage();
					}
				}				
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Modificaci&oacute;n de Clave</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<table class="tablaResultados" style="width: 50%">
				<tr>
					<td><label for="clave">&nbsp;Clave Anterior:</label></td>
					<td><input type="password" id="claveOld" name="claveOld" value="" size="15" maxlength="20" required="required"/></td>
				</tr>
				<tr>
					<td><label for="clave">&nbsp;Nueva Clave:</label></td>
					<td><input type="password" id="claveNew" name="claveNew" value="" size="15" maxlength="20" required="required"/></td>
				</tr>
				<tr>
					<td><label for="clave">&nbsp;Repetir Nueva Clave:</label></td>
					<td><input type="password" id="claveNewRep" name="claveNewRep" value="" size="15" maxlength="20" required="required"/></td>
				</tr>
			</table>
		</div>
		<br/>
			<div id="botonera" style="margin-right: 11%">
				<input id="submit" name="submit"  type="submit" value="Grabar" />
			</div>
		</form>
	

        	</div>	
		</div>
	</div>
</div>

<?php require_once "../template/pie.inc.php";  ?>
</body>
</html>
		
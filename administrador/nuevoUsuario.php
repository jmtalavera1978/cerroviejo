<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="nuevoUsuario.php" enctype="multipart/form-data" autocomplete="off" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idUsuario = @$_POST['idUsuario'];
				$clave = @$_POST['clave'];
				$tipoUsuario = @$_POST['tipoUsuario'];
				$nombre = @$_POST['nombre'];
				$apellidos = @$_POST['apellidos'];
				$email = @$_POST['email'];
				$direccion = @$_POST['direccion'];
				$tfno = @$_POST['tfno'];
				$movil = @$_POST['movil'];
				$wts = @$_POST['wts'];
				$codPostal = @$_POST['codPostal'];
				$poblacion = @$_POST['poblacion'];
				$provincia = @$_POST['provincia'];
				$activo = @$_POST['activo'];
				$saldo = 0;
				$fechaAlta = new DateTime();
				$nif =  @$_POST['nif'];
				$idSubgrupo =  @$_POST['idSubgrupo'];
				$descRecargo = @$_POST['descRecargo'];
				$recargoEq = @$_POST['recargoEq'];
				$verFactura = @$_POST['verFactura'];
			
				try {
					write_log("NUEVO USUARIO - ".$idUsuario.": ".$saldo, "Info");
					$res = consulta ("insert INTO USUARIOS (ID_USUARIO, CLAVE, TIPO_USUARIO, NOMBRE, APELLIDOS, EMAIL, DIRECCION, TFNO_CONTACTO, TFNO_MOVIL
							, WTS_APP, CODIGO_POSTAL, POBLACION, PROVINCIA, SALDO, ACTIVO, FECHA_ALTA, NIF, ID_SUBGRUPO, DESCUENTO_RECARGO, RECARGO_EQ, VER_FACTURA) 
							VALUES ('$idUsuario', '$clave', '$tipoUsuario', '$nombre', '$apellidos', '$email', '$direccion',
							'$tfno', '$movil', '".(isset($wts) ? '1' : '0')."', '$codPostal', '$poblacion', '$provincia', '$saldo', '".(isset($activo) ? '1' : '0')."'
							, '".($fechaAlta->format('Y-m-d'))."', '$nif', '$idSubgrupo', '$descRecargo', '$recargoEq', '$verFactura')");
			
					if ($res) {
						echo "<script>document.location='editarUsuario.php?idUsuario=$idUsuario';</script>";
						//Header("Location: editarUsuario.php?idUsuario=$idUsuario");
					} else {
						$mensaje = 'No se ha podido modificar el usuario. Compruebe que el Cód. de usuario no esté repetido.';
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje = $ex->qetMessage();
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Nuevo Usuario</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<table class="tablaResultados">
				<tr>
					<td><label for="idUsuario">C&oacute;d. Usuario:</label></td>
					<td><input type="text" id="idUsuario" name="idUsuario" value="" size="7" maxlength="5" required="required"/></td>
				</tr>
				<tr>
					<td><label for="clave">Clave:</label></td>
					<td><input type="text" id="clave" name="clave" value="" size="10" maxlength="20" required="required"/></td>
				</tr>
				<tr>
					<td><label for="tipoUsuario">Tipo de Usuario:</label></td>
					<td>
						<select name="tipoUsuario" id="tipoUsuario" required="required">
							<option value="">Seleccione una tipo de usuario...</option>
							<?php optionsTiposUsuarios('') ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="nombre">Nombre:</label></td>
					<td><input type="text" id="nombre" name="nombre" value="" size="40" maxlength="100" required="required"/></td>
				</tr>
				<tr>
					<td><label for="apellidos">Apellidos:</label></td>
					<td><input type="text" id="apellidos" name="apellidos" value="" size="50" maxlength="120" required="required"/></td>
				</tr>
				<tr>
					<td><label for="nif">N.I.F.:</label></td>
					<td><input type="text" id="nif" name="nif" value="" size="12" maxlength="10"/></td>
				</tr>
				<tr>
					<td><label for="email">Email:</label></td>
					<td><input type="email" id="email" name="email" value="" size="50" maxlength="120" required="required"/></td>
				</tr>
				<tr>
					<td><label for="tfno">Tfno. Contacto:</label></td>
					<td><input type="tel" id="tfno" name="tfno" value="" size="12" maxlength="9"/></td>
				</tr>
				<tr>
					<td><label for="movil">Tfno. M&oacute;vil:</label></td>
					<td><input type="tel" id="movil" name="movil" value="" size="12" maxlength="9"/></td>
				</tr>
				<tr>
					<td><label for="direccion">Direcci&oacute;n:</label></td>
					<td><input type="text" id="direccion" name="direccion" value="" size="60" maxlength="255"/></td>
				</tr>
				<tr>
					<td><label for="codPostal">Cod. Postal:</label></td>
					<td><input type="text" id="codPostal" name="codPostal" value="" size="8" maxlength="5" /></td>
				</tr>
				<tr>
					<td><label for="poblacion">Poblaci&oacute;n:</label></td>
					<td><input type="text" id="poblacion" name="poblacion" value="" size="40" maxlength="100"/></td>
				</tr>
				<tr>
					<td><label for="provincia">Provincia:</label></td>
					<td><input type="text" id="provincia" name="provincia" value="" size="30" maxlength="50"/></td>
				</tr>
				<tr>
					<td><label for="saldoUsuario">Saldo:</label></td>
					<td><input type="text" id="saldoUsuario" name="saldo" value="0" size="6" maxlength="6" readonly="readonly" required="required" /> &euro;</td>
					
				</tr>
				<tr>
					<td><label for="wts">Wts app:</label></td>
					<td colspan="2"><input id="wts" name="wts" type="checkbox" value="1"/></td>
				</tr>
				<tr>
					<td><label for="idSubgrupo">Subgrupo:</label></td>
					<td>
						<select name="idSubgrupo" id="idSubgrupo" required="required">
							<?php optionsSubgrupos(''); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="descRecargo">Descuento sobre recargo:</label></td>
					<td><input type="number" id="descRecargo" name="descRecargo" value="0" size="3" style="width: 55px" maxlength="3" min="0" max="100" step="5" required="required" /> %</td>					
				</tr>
				<tr>
					<td><label for="recargoEq">Recargo Equivalencia:</label></td>
					<td><input id="recargoEq" name="recargoEq" type="checkbox" value="1" /></td>
				</tr>
				<tr>
					<td><label for="verFactura">Puede ver las facturas:</label></td>
					<td><input id="verFactura" name="verFactura" type="checkbox" value="1" /></td>
				</tr>
				<tr>
					<td><label for="activo">Activo:</label></td>
					<td><input id="activo" name="activo" type="checkbox" checked="checked" value="1" /></td>
				</tr>
			</table>
		</div>
		<br/>
			<div id="botonera">
				<input id="submit" name="submit"  type="submit" value="Grabar" />
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='usuarios.php'" />
			</div>
		</form>
	</section>
</body>
</html>

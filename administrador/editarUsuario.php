<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="editarUsuario.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idUsuario = @$_POST['idUsuario'];
				$clave = @$_POST['clave'];
				$numeroUsuario = @$_POST['numeroUsuario'];
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
				//$saldo = @$_POST['saldo'];
				//$fechaAlta =  @$_POST['fechaAlta'];
				$nif =  @$_POST['nif'];
				$idSubgrupo =  @$_POST['idSubgrupo'];
				$descRecargo = @$_POST['descRecargo'];
				$recargoEq = @$_POST['recargoEq'];
				$verFactura = @$_POST['verFactura'];
				
				if (isset($fechaAlta)) {
					$fechaAlta = date_create_from_format("d/m/Y", $fechaAlta);
					$fechaAlta = $fechaAlta->format("Y-m-d");
				}
			
				try {
					write_log("EDITAR USUARIO - ".$idUsuario.": ".@$_POST['saldo'], "Info");
					$res = consulta ("update USUARIOS set CLAVE='$clave', NUMERO_USUARIO='$numeroUsuario', ID_SUBGRUPO='$idSubgrupo'
							, TIPO_USUARIO='$tipoUsuario', NOMBRE= '$nombre', APELLIDOS='$apellidos', EMAIL='$email', DIRECCION='$direccion'
							, TFNO_CONTACTO='$tfno', TFNO_MOVIL='$movil' , WTS_APP='".(isset($wts) ? '1' : '0')."', CODIGO_POSTAL='$codPostal', POBLACION='$poblacion'
							, PROVINCIA='$provincia', ACTIVO='".(isset($activo) ? '1' : '0')."', NIF='$nif', DESCUENTO_RECARGO='$descRecargo', RECARGO_EQ='$recargoEq', VER_FACTURA='$verFactura'
							 WHERE ID_USUARIO='$idUsuario'");
			
				if ($res == 1) {
						$mensaje = 'Usuario modificado correctamente.';
					} else {
						$mensaje = 'No se ha podido modificar el usuario';
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje = $ex->qetMessage();
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Modificaci&oacute;n de Usuario</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			
			$resultado = consulta("select * from USUARIOS WHERE ID_USUARIO='".@$_GET['idUsuario'].@$_POST['idUsuario']."'");
			$usuario = extraer_registro($resultado);
			$fechaAlta = '';
			if (isset($usuario['FECHA_ALTA'])) {
				$fechaAlta = date_create_from_format("Y-m-d", $usuario['FECHA_ALTA']);
				$fechaAlta = $fechaAlta->format("d/m/Y");
			}
			
			$saldoPendiente = calculaSaldoPendienteUsuario (@$_GET['idUsuario'].@$_POST['idUsuario']);
			$saldoPendiente = round(($usuario['SALDO'] - $saldoPendiente), 2);
			?>
			<table class="tablaResultados">
				<tr>
					<td><label for="idUsuario">C&oacute;d. Usuario:</label></td>
					<td><input type="text" id="idUsuario" name="idUsuario" value="<?=$usuario['ID_USUARIO']?>" size="7" maxlength="5" readonly="readonly" required="required"/></td>
				</tr>
				<tr>
					<td><label for="clave">Clave:</label></td>
					<td><input type="text" id="clave" name="clave" value="<?=$usuario['CLAVE']?>" size="10" maxlength="20" required="required"/></td>
				</tr>
				<tr>
					<td><label for="numeroUsuario">Num. Usuario:</label></td>
					<td><input type="text" id="numeroUsuario" name="numeroUsuario" value="<?=$usuario['NUMERO_USUARIO']?>" size="3" maxlength="11" onkeypress="return NumCheck(event, this)" required="required"/></td>
				</tr>
				<tr>
					<td><label for="tipoUsuario">Tipo de Usuario:</label></td>
					<td>
						<select name="tipoUsuario" id="tipoUsuario" required="required">
							<option value="">Seleccione una tipo de usuario...</option>
							<?php optionsTiposUsuarios($usuario['TIPO_USUARIO']) ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="nombre">Nombre:</label></td>
					<td><input type="text" id="nombre" name="nombre" value="<?=$usuario['NOMBRE']?>" size="40" maxlength="100" required="required"/></td>
				</tr>
				<tr>
					<td><label for="apellidos">Apellidos:</label></td>
					<td><input type="text" id="apellidos" name="apellidos" value="<?=$usuario['APELLIDOS']?>" size="50" maxlength="120" required="required"/></td>
				</tr>
				<tr>
					<td><label for="nif">N.I.F.:</label></td>
					<td><input type="text" id="nif" name="nif" value="<?=$usuario['NIF']?>" size="12" maxlength="10"/></td>
				</tr>
				<tr>
					<td><label for="email">Email:</label></td>
					<td><input type="email" id="email" name="email" value="<?=$usuario['EMAIL']?>" size="50" maxlength="120" required="required"/></td>
				</tr>
				<tr>
					<td><label for="tfno">Tfno. Contacto:</label></td>
					<td><input type="tel" id="tfno" name="tfno" value="<?=$usuario['TFNO_CONTACTO']?>" size="12" maxlength="9"/></td>
				</tr>
				<tr>
					<td><label for="movil">Tfno. M&oacute;vil:</label></td>
					<td><input type="tel" id="movil" name="movil" value="<?=$usuario['TFNO_MOVIL']?>" size="12" maxlength="9"/></td>
				</tr>
				<tr>
					<td><label for="direccion">Direcci&oacute;n:</label></td>
					<td><input type="text" id="direccion" name="direccion" value="<?=$usuario['DIRECCION']?>" size="60" maxlength="255"/></td>
				</tr>
				<tr>
					<td><label for="codPostal">Cod. Postal:</label></td>
					<td><input type="text" id="codPostal" name="codPostal" value="<?=$usuario['CODIGO_POSTAL']?>" size="8" maxlength="5" /></td>
				</tr>
				<tr>
					<td><label for="poblacion">Poblaci&oacute;n:</label></td>
					<td><input type="text" id="poblacion" name="poblacion" value="<?=$usuario['POBLACION']?>" size="40" maxlength="100"/></td>
				</tr>
				<tr>
					<td><label for="provincia">Provincia:</label></td>
					<td><input type="text" id="provincia" name="provincia" value="<?=$usuario['PROVINCIA']?>" size="30" maxlength="50"/></td>
				</tr>
				<tr>
					<td><label for="saldo">Saldo Confirmado:</label></td>
					<td><input type="text" id="saldoUsuario" name="saldo" value="<?=$usuario['SALDO']?>" readonly="readonly" size="6" maxlength="6" required="required" /> &euro;</td>
				</tr>
				<tr>
					<td><label for="saldo">Saldo tras pedidos no finalizados:</label></td>
					<td><input type="text" id="saldoUsuario2" name="saldo2" value="<?=$saldoPendiente?>" readonly="readonly" size="6" maxlength="6" required="required" /> &euro;</td>
				</tr>
				<tr>
					<td><label for="fechaAlta">Fecha Alta:</label></td>
					<td><input type="text" id="fechaAlta" name="fechaAlta" value="<?=$fechaAlta?>" readonly="readonly" contenteditable="false"/></td>
				</tr>
				<tr>
					<td><label for="wts">Wts app:</label></td>
					<td colspan="2"><input id="wts" name="wts" type="checkbox" value="1" <?=$usuario['WTS_APP'] == '1' ? 'checked=\"checked\"' : ''?>/></td>
				</tr>
				<tr>
					<td><label for="idSubgrupo">Subgrupo:</label></td>
					<td>
						<select name="idSubgrupo" id="idSubgrupo" required="required">
							<?php optionsSubgrupos($usuario['ID_SUBGRUPO']); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="descRecargo">Descuento sobre recargo:</label></td>
					<td><input type="number" id="descRecargo" name="descRecargo" value="<?=$usuario['DESCUENTO_RECARGO']?>" size="3" style="width: 55px" maxlength="3" min="0" max="100" step="5" required="required" /> %</td>					
				</tr>
				<tr>
					<td><label for="recargoEq">Recargo Equivalencia:</label></td>
					<td><input id="recargoEq" name="recargoEq" type="checkbox" <?=$usuario['RECARGO_EQ'] == '1' ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label for="verFactura">Puede ver las facturas:</label></td>
					<td><input id="verFactura" name="verFactura" type="checkbox" <?=$usuario['VER_FACTURA'] == '1' ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
				<tr>
					<td><label for="activo">Activo:</label></td>
					<td><input id="activo" name="activo" type="checkbox" <?=$usuario['ACTIVO'] == '1' ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
			</table>
			<script>
			  $(function() {
			    $( "#fechaAlta" ).datepicker({
			    	  dateFormat:'dd/mm/yy'
			    	});
			  });
			</script>
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

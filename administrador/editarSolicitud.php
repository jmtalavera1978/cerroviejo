<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="editarSolicitud.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idSolicitud = @$_POST['idSolicitud'];
				$leido = @$_POST['leido'];
			
				try {
					$res = consulta ("update SOLICITUDES set LEIDO='".(isset($leido) ? '1' : '0')."' WHERE ID_SOLICITUD='$idSolicitud'");
			
				if ($res == 1) {
						$mensaje = 'Solicitud modificada correctamente.';
					} else {
						$mensaje = 'No se ha podido modificar la solicitud';
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje = $ex->qetMessage();
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Consulta/Modificaci&oacute;n de Solicitud</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			
			$resultado = consulta("select * from SOLICITUDES WHERE ID_SOLICITUD='".@$_GET['idSolicitud'].@$_POST['idSolicitud']."'");
			$usuario = extraer_registro($resultado);
			$fechaAlta = '';
			if (isset($usuario['FECHA_SOLICITUD'])) {
				$fechaAlta = date_create_from_format("Y-m-d", $usuario['FECHA_SOLICITUD']);
				$fechaAlta = $fechaAlta->format("d/m/Y");
			}
			?>
			<table class="tablaResultados">
				<tr>
					<td><label for="idUsuario">C&oacute;d. Solicitud:</label></td>
					<td><input type="text" id="idSolicitud" name="idSolicitud" value="<?=$usuario['ID_SOLICITUD']?>" size="7" maxlength="5" readonly="readonly" required="required"/></td>
				</tr>
				<tr>
					<td><label for="tipoSolicitud">Tipo de Solicitud:</label></td>
					<td>
						<select name="tipoSolicitud" id="tipoSolicitud" required="required" readonly="readonly" disabled="disabled">
							 <option value="INFORMACIÓN">Información</option>
                             <option value="SOCIO" <?php if (@$usuario['TIPO_SOLICITUD']=='SOCIO') { echo "selected"; } ?>>Socio</option>
                              <option value="CONSULTORÍA" <?php if (@$usuario['TIPO_SOLICITUD']=='CONSULTORÍA') { echo "selected"; } ?>>Consultor&iacute;a</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="fechaAlta">Fecha Solicitud:</label></td>
					<td><input type="text" id="fechaAlta" name="fechaAlta" value="<?=$fechaAlta?>" contenteditable="false" readonly="readonly"/></td>
				</tr>
				<tr>
					<td><label for="nombre">Nombre:</label></td>
					<td><input type="text" id="nombre" name="nombre" value="<?=$usuario['NOMBRE']?>" size="40" maxlength="100" required="required" readonly="readonly"/></td>
				</tr>
				<tr>
					<td><label for="apellidos">Apellidos:</label></td>
					<td><input type="text" id="apellidos" name="apellidos" value="<?=$usuario['APELLIDOS']?>" size="50" maxlength="120" required="required" readonly="readonly"/></td>
				</tr>
				<tr>
					<td><label for="nif">N.I.F.:</label></td>
					<td><input type="text" id="nif" name="nif" value="<?=$usuario['NIF']?>" size="12" maxlength="10" readonly="readonly"/></td>
				</tr>
				<tr>
					<td><label for="email">Email:</label></td>
					<td><input type="email" id="email" name="email" value="<?=$usuario['EMAIL']?>" size="50" maxlength="120" required="required" readonly="readonly"/></td>
				</tr>
				<tr>
					<td><label for="tfno">Tfno. Contacto:</label></td>
					<td><input type="tel" id="tfno" name="tfno" value="<?=$usuario['TFNO_CONTACTO']?>" size="12" maxlength="9" readonly="readonly"/></td>
				</tr>
				<tr>
					<td><label for="observaciones">Observaciones:</label></td>
					<td>
						<textarea id="observaciones" name="observaciones" maxlength="1000" rows="8" cols="60" readonly="readonly"><?=$usuario['OBSERVACIONES']?></textarea>
                    </td>
				</tr>
				<tr>
					<td><label for="leido" title="Deseleccionar cuando se verifique la solicitud">Leido:</label></td>
					<td><input id="leido" name="leido" type="checkbox" <?=$usuario['LEIDO'] == '1' ? 'checked=\"checked\"' : ''?> value="1" /></td>
				</tr>
			</table>
		</div>
		<br/>
			<div id="botonera">
				<input id="submit" name="submit"  type="submit" value="Grabar" />
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='solicitudes.php'" />
			</div>
		</form>
	</section>
</body>
</html>

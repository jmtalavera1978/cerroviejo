<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="nuevoApunteContable.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$concepto = @$_POST['concepto'];
				$importe = @$_POST['importe'];
				$esdebe= @$_POST['esdebe'];
				$usuario = @$_POST['usuario'];
			
				try {
					$mensaje = addApunteContable ($concepto, $importe, $esdebe, $usuario);
						
					if ($mensaje=='OK') {
						$_SESSION['mensaje_generico'] = 'Se ha creado el nuevo apunte correctamente.';
						?>
						<script>document.location='contabilidad.php';</script>
						<?php 
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje = $ex->qetMessage();
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Nuevo Asiento Contable</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<table class="tablaResultados">
				<tr>
					<td><label for="concepto">CONCEPTO:</label></td>
					<td><input type="text" id="concepto" name="concepto" value="" size="50" maxlength="255" required="required"/></td>
				</tr>
				<tr>
					<td><label for="importe">IMPORTE:</label></td>
					<td>
						<input type="text" onkeypress="return NumCheck(event, this)" id="importe" name="importe" min="0" max="9999" value="" size="6" required="required"/> &euro;
					</td>
				</tr>
				<tr>
					<td><label for="esdebe">Es Debe:</label></td>
					<td><input id="esdebe" name="esdebe" type="checkbox" value="1" /></td>
				</tr>
				<tr>
					<td><label for="usuario">Usuario:</label></td>
					<td>
						<select id="usuario" name="usuario" required="required">
							<option value="">Seleccione un usuario...</option>
							<option value="-SISTEMA-">-SISTEMA-</option>
							<?=optionsUsuariosActivos('')?>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<div style="clear: both;"></div>
		<br/>
			<div id="botonera">
				<input id="submit2" name="submit2"  type="button" value="Grabar" onclick="openConfirmacion()" />
				<input id="submit" name="submit"  type="submit" value="submit" style="display: none"/>
				<input id="cancel" name="cancel"  type="reset" value="Volver" onclick="document.location='contabilidad.php'" />
			</div>
		</form>
		<div id="dialogConfirmApunte" title="">
			¿Desea generar un nuevo apunte?</div>
			
		<script>
			$(function() {
			    $( "input[type=button]" )
			      .button()
			      .click(function( event ) {
			        event.preventDefault();
			      });
			  });
			  
			function openConfirmacion() {
				$("#dialogConfirmApunte").dialog("open");
			}
			
			$("#dialogConfirmApunte").dialog({
		      autoOpen: false,
			  height: 250,
			  width: 400,
			  modal: true,
		      buttons : {
		          "Sí" : function() {
		        	  $("#submit").click();
			          $(this).dialog("close");
		          },
		          "Cancelar" : function() {
			          $(this).dialog("close");
		          }
		        }
		    });
		</script>
	</section>
</body>
</html>

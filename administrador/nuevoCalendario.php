<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="nuevoCalendario.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idCategoria = @$_POST['idCategoria'];
				$fechaPedido = @$_POST['fechaPedido'];
				$fechaEntrega = @$_POST['fechaEntrega'];
				if (isset($fechaPedido)) {
					$fechaPedido = date_create_from_format('d/m/Y', $fechaPedido);
					$fechaPedido = $fechaPedido->format('Y-m-d');
				}
				if (isset($fechaEntrega)) {
					$fechaEntrega = date_create_from_format('d/m/Y', $fechaEntrega);
					$fechaEntrega = $fechaEntrega->format('Y-m-d');
				}
				//$estado = @$_POST['estado'];
			
				try {
					$res = consulta ("insert INTO CALENDARIO (ID_CATEGORIA, FECHA_PEDIDO, FECHA_ENTREGA) VALUES ('$idCategoria', '".$fechaPedido."', '".$fechaEntrega."') ");
					
					$idNuevo = get_new_id('ID_CALENDARIO', 'CALENDARIO');
						
					if ($res) {
						$_SESSION['mensaje_generico'] = 'Se ha creado la fecha de entrega correctamente.';
						echo "<script>document.location='editarCalendario.php?idCalendario=$idNuevo'</script>";
					} else {
						$mensaje = "No se ha podido crear la nueva fecha de entrega";
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje = $ex->qetMessage();
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Nueva Fecha de Entrega</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			?>
			<table class="tablaResultados">
				<tr>
					<td><label for="idCategoria">Categor&iacute;a:</label></td>
					<td>
						<select name="idCategoria" id="idCategoria" required="required">
							<option value="">Seleccione una categor&iacute;a...</option>
						<?php optionsCategorias(''); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="fechaPedido">Fecha apertura pedido:</label></td>
					<td><input type="text" id="fechaPedido" name="fechaPedido" value="<?=consultarSoloFechaApertura()?>" size="10" contenteditable="false" required="required"/></td>
				</tr>
				<tr>
					<td><label for="fechaEntrega">Fecha entrega:</label></td>
					<td><input type="text" id="fechaEntrega" name="fechaEntrega" value="" size="10" contenteditable="false" required="required"/></td>
				</tr>
<!-- 				<tr> -->
<!-- 					<td><label for="estado">Estado:</label></td> -->
<!-- 					<td> -->
<!-- 						<select name="estado" id="estado" required="required"> -->
<!-- 							<option value="EN TRAMITACI&Oacute;N" selected="selected">EN TRAMITACI&Oacute;N</option> -->
<!-- 							<option value="EN PREPARACI&Oacute;N">EN PREPARACI&Oacute;N</option> -->
<!-- 							<option value="EN REPARTO">EN REPARTO</option> -->
<!-- 							<option value="EN REVISI&Oacute;N">EN REVISI&Oacute;N</option> -->
<!-- 							<option value="FINALIZADO">FINALIZADO</option> -->
<!-- 							<option value="REVISADO">REVISADO</option> -->
<!-- 						</select> -->
<!-- 					</td> -->
<!-- 				</tr> -->
			</table>
			<script>
			  $(function() {
				  $( "#fechaPedido" ).datepicker({
			    	  dateFormat:'dd/mm/yy'
			    	});
			    $( "#fechaEntrega" ).datepicker({
			    	  dateFormat:'dd/mm/yy'
			    	});
			  });
			</script>
		</div>
		<div style="clear: both;"></div>
		<br/>
			<div id="botonera">
				<input id="submit" name="submit"  type="submit" value="Grabar" />
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='calendario.php'" />
			</div>
		</form>
	</section>
</body>
</html>

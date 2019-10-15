<!DOCTYPE html>
<html lang="es-ES">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<form method="post" action="editarCalendario.php" enctype="multipart/form-data" >
		<?php 
			//Comprobar si se guarda
			if (@$_POST['submit']) {
				$idCalendario = @$_POST['idCalendario'];
				$idCategoria = @$_POST['idCategoria'];
				//$estado = @$_POST['estado'];
				$fechaPedido = @$_POST['fechaPedido'];
				$fechaEntrega = @$_POST['fechaEntrega'];
				try {
					if (isset($fechaPedido)) {
						$fechaPedido = date_create_from_format('d/m/Y', $fechaPedido);
						$fechaPedido = @$fechaPedido->format('Y-m-d');
					}
					if (isset($fechaEntrega)) {
						$fechaEntrega = date_create_from_format('d/m/Y', $fechaEntrega);
						$fechaEntrega = @$fechaEntrega->format('Y-m-d');
					}
			
				
					$res = consulta ("UPDATE CALENDARIO set ID_CATEGORIA='$idCategoria', FECHA_PEDIDO='".$fechaPedido."', FECHA_ENTREGA='".$fechaEntrega."' WHERE ID_CALENDARIO='$idCalendario'");
					
					if ($res == 1) {
						$mensaje = 'Se ha modificado la fecha de entrega correctamente.';
					} else {
						$mensaje = 'No se ha podido modificar la fecha de entrega';
					}
				} catch (Exception $ex) {
					//Devuelve el mensaje de error
					$mensaje = $ex->qetMessage();
				}
			}
		?>
		<div id="contenidoAdmin">
			<h1 class="cal">Modificaci&oacute;n de Fecha de Entrega</h1>
			<?php 
			if (isset($mensaje)) {
				echo "<h5>$mensaje</h5>";
			}
			
			$resultado = consulta("select * from CALENDARIO WHERE ID_CALENDARIO=".@$_GET['idCalendario'].@$_POST['idCalendario']);
			$cal = extraer_registro($resultado);
			$fechaPedido = $cal['FECHA_PEDIDO'];
			$fechaEntrega = $cal['FECHA_ENTREGA'];
			if (isset($fechaPedido)) {
				$fechaPedido = date_create_from_format('Y-m-d', $fechaPedido);
				$fechaPedido = $fechaPedido->format('d/m/Y');
			}
			if (isset($fechaEntrega)) {
				$fechaEntrega = date_create_from_format('Y-m-d', $fechaEntrega);
				$fechaEntrega = $fechaEntrega->format('d/m/Y');
			}		
			?>
			<table class="tablaResultados">
				<tr>
					<td><label for="idCalendario">Id. :</label></td>
					<td colspan="2"><input type="text" id="idCalendario" name="idCalendario" readonly="readonly" value="<?=$cal['ID_CALENDARIO']?>" size="6" required="required"/></td>
				</tr>
				<tr>
					<td><label for="idCategoria">Categor&iacute;a:</label></td>
					<td>
						<select name="idCategoria" id="idCategoria" required="required">
							<option value="">Seleccione una categor&iacute;a...</option>
							<?php optionsCategorias($cal['ID_CATEGORIA']); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="fechaPedido">Fecha apertura pedido:</label></td>
					<td><input type="text" id="fechaPedido" name="fechaPedido" value="<?=$fechaPedido?>" size="10" contenteditable="false" required="required"/></td>
				</tr>
				<tr>
					<td><label for="fechaEntrega">Fecha entrega:</label></td>
					<td><input type="text" id="fechaEntrega" name="fechaEntrega" value="<?=$fechaEntrega?>" size="10" contenteditable="false" required="required"/></td>
				</tr>
<!-- 				<tr> -->
<!-- 					<td><label for="estado">Estado:</label></td> -->
<!-- 					<td> -->
<!-- 						<select name="estado" id="estado" required="required">
							<option value="EN TRAMITACI&Oacute;N"" <?=$cal['ESTADO']=='EN TRAMITACIÓN' ? ' selected' : '' ?>>EN TRAMITACI&Oacute;N</option>
							<option value="EN PREPARACI&Oacute;N" <?=$cal['ESTADO']=='EN PREPARACIÓN' ? ' selected' : '' ?>>EN PREPARACI&Oacute;N</option>
							<option value="EN REPARTO" <?=$cal['ESTADO']=='EN REPARTO' ? ' selected' : '' ?>>EN REPARTO</option>
							<option value="EN REVISI&Oacute;N" <?=$cal['ESTADO']=='EN REVISIÓN' ? ' selected' : '' ?>>EN REVISI&Oacute;N</option>
							<option value="FINALIZADO" <?=$cal['ESTADO']=='FINALIZADO' ? ' selected' : '' ?>>FINALIZADO</option>
							<option value="REVISADO" <?=$cal['ESTADO']=='REVISADO' ? ' selected' : '' ?>>REVISADO</option>
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

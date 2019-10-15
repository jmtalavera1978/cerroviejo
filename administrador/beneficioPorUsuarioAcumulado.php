<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
    <script>
	  $(function() {
	    var availableTags = [
		<?php
	      $res1 = consulta("SELECT ID_USUARIO FROM USUARIOS WHERE TIPO_USUARIO='USUARIO'");
	      while ($fila1 = extraer_registro($res1)) {
			echo "\"".$fila1['ID_USUARIO']."\",";
		  }
	      ?>
	    ];
	    $( "#idUsuario" ).autocomplete({
	      source: availableTags
	    });
	  });
	 </script>
	<section>
		<form method="post" action="informes/excelBeneficioPorUsuarioAcumulado.php">
		<div id="contenidoAdmin">
			<?php
			$loteActual = consultarLoteActual();
			 if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5>".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			 } 
			?>
			<h1 class="cal" style="margin-bottom: -20px;">Beneficio Por Usuario Acumulado</h1>
			<div id="tituloProveedores">
			<span>
				&nbsp;<b>AÑO:</b>&nbsp;
				<select id="anyo" name="anyo">
					<?php 
						$anyo = date('Y');
						while ($anyo > 2013) {
							echo '<option value="'.$anyo.'">'.$anyo.'</option>';
							$anyo = ($anyo-1);
						}
					?>
				</select>
			</span>
			</div>
		</div>
		<br/>
			<div id="botonera">
				<input id="nuevo" name="nuevo"  type="submit" value="Generar Excel" />
			</div>
		</form>
	</section>
</body>
</html>

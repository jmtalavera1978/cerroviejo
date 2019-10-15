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
		<form method="post" action="informes/excelDemandaPorUsuarioLote.php">
		<div id="contenidoAdmin">
			<?php
			$loteActual = consultarLoteActual();
			 if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5>".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			 } 
			?>
			<h1 class="cal" style="margin-bottom: -20px;">Demanda por Usuario y Lote</h1>
			<div id="tituloProveedores">
			<span>
			&nbsp;LOTE:&nbsp;
				<input type="number" id="lote" name="lote" size="8" /> (&Uacute;ltimo: <?=$loteActual?>)
				<br/>
				&nbsp;SUBGRUPO:&nbsp;
				<select id="idSubgrupo" name="idSubgrupo">
					<option value="">Seleccione un subgrupo..</option>
					<?=optionsSubgrupos($subgrupo);?>
				</select>
				<br/>
				&nbsp;USUARIO:&nbsp;
				<input type="text" id="idUsuario" name="idUsuario" size="8" /> 
				<br/>
				&nbsp;<b>TIPO de ENV&Iacute;O:</b>&nbsp;
				<select id="transporte" name="transporte">
					<option value="">Seleccione un tipo de env&iacute;o...</option>
					<?php 
						optionsTransporte ('');
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

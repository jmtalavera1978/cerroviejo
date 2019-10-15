<?php require_once "../includes/lib_carrito.php"; 
$lote = consultarLoteActual();

if ($_SESSION['idCategoria'] != '-1') {
	$bnombre = "";
} else {
	$bnombre = @$_GET['bnombre'];
}

$_SESSION['idCategoria'] = '-1';
$_SESSION['idsubcategoria'] = NULL;

if (isset($bnombre)) {
	if (strlen($bnombre)==0) {
		$_SESSION['bnombre'] = NULL;
		$bnombre = NULL;
	} else {
		$_SESSION['bnombre'] = $bnombre;
	}
} else {
	$bnombre = @$_SESSION['bnombre'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head_interno.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
	<script type="text/javascript" src="../js/jquery.ui.stars.js"></script>
	<link rel="stylesheet" href="../css/jquery.ui.stars.css" />
</head>
<body>
    <?php require_once "../template/cabeceraUsuarios.inc.php";  ?>
    <div class="wrapper">
		<div class="grids top">
			<?php require_once "../template/menuLateralUsuarios.inc.php"; ?>
			</div>
				<div class="grid-12 grid"> 
	            	<div class="flexsearch">
						<div class="flexsearch--wrapper">
							<form class="flexsearch--form" action="novedades.php" method="get">
								<div class="flexsearch--input-wrapper">
									<input id="bnombre" name="bnombre" class="flexsearch--input" type="search" value="<?=$bnombre?>" placeholder="Introduzca nombre del producto o parte del mismo para su búsqueda...">
								</div>
								<input class="flexsearch--submit" type="submit" value="&#10140;" title="Buscar" />
							</form>
						</div>
					</div>
			        <br>  
			     </div> 
				<div style="clear: both;"></div>
				 <!--  FILTROS ECOLÓGICO | VEGANO | COMERCIO JUSTO | KM0 | GOURMET | SIN GLUTEN | SIN LACTOSA -->
				 Ecologico: <input type="checkbox" id="ecologico" name="ecologico" value="1" onmouseup="filtrarProductos('ecologico', !this.checked);" <?=(@$_SESSION['ecologicoSel']==TRUE ? 'checked' : '')?>>
				 &nbsp;&nbsp;Vegano: <input type="checkbox" id="vegano" name="vegano" value="1" onmouseup="filtrarProductos('vegano', !this.checked);" <?=(@$_SESSION['veganoSel']==TRUE ? 'checked' : '')?>>
				 &nbsp;&nbsp;Comercio Justo: <input type="checkbox" id="comercio_justo" name="comercio_justo" value="1" onmouseup="filtrarProductos('comercio_justo', !this.checked);" <?=(@$_SESSION['comercioJustoSel']==TRUE ? 'checked' : '')?>>
				 &nbsp;&nbsp;Km 0: <input type="checkbox" id="km0" name="km0" value="1" onmouseup="filtrarProductos('km0', !this.checked);" <?=(@$_SESSION['km0Sel']==TRUE ? 'checked' : '')?>>
				 &nbsp;&nbsp;Gourmet: <input type="checkbox" id="gourmet" name="gourmet" value="1" onmouseup="filtrarProductos('gourmet', !this.checked);" <?=(@$_SESSION['gourmetSel']==TRUE ? 'checked' : '')?>>
				 &nbsp;&nbsp;Sin Gluten: <input type="checkbox" id="sin_gluten" name="sin_gluten" value="1" onmouseup="filtrarProductos('sin_gluten', !this.checked);" <?=(@$_SESSION['sinGlutenSel']==TRUE ? 'checked' : '')?>>
				 &nbsp;&nbsp;Sin Lactosa: <input type="checkbox" id="sin_lactosa" name="sin_lactosa" value="1" onmouseup="filtrarProductos('sin_lactosa', !this.checked);" <?=(@$_SESSION['sinLactosaSel']==TRUE ? 'checked' : '')?>>
				<!-- FIN FILTROS -->
			</div>
			
			<div class="wrapper">
				<div id="listadoProductoYPag" class="grid-12 grid">
					<?php require_once "listadoProductos.php" ?>
				</div>
			</div>
			
        	
		</div>
	</div>
	
	<?php require_once "modal_compra.inc.php" ?>

	<?php require_once "../template/pie.inc.php";  ?>
</body>
</html>

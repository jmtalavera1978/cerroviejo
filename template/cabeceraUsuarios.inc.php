<?php
require_once "../includes/funciones.inc.php";
compruebaSesionUsuario();

$fechaApertura = consultarFechaApertura();
$fechaCierre = consultarFechaCierre();

if (isset($fechaApertura) && isset($fechaCierre)) {
	$fechaApertura = date_create_from_format("d/m/Y H:i", $fechaApertura);
	$fechaCierre = date_create_from_format("d/m/Y H:i", $fechaCierre);
	$fechaActual = new DateTime();
	
	if ($fechaActual>=$fechaApertura && $fechaActual<=$fechaCierre) {
		$interval = date_diff($fechaCierre, $fechaActual);
		$numDias = $interval->format('%d días %h horas %i minutos');
	}
}
?>

<div class="wrapper">
	<a href="javascript:logout()" id="logo"><img
		src="../img/cerroViejo.png" alt="something" />
		<h1 class="accessibility">GRUPO CERRO VIEJO</h1>
	</a>
		
	<div class="social">
		<H6><?=@$_SESSION['NOMBRE_COMPLETO']?>   <a
				href="javascript:logout()">[Desconectar]</a>
		</h6>
	</div>

	<ul id="nav" class="main">
		<li><a href="index.php"><img src="../img/calendario.png" alt="" />CALENDARIO</a></li>
		<li><a href="cesta.php"><img src="../img/carrito.png" alt="" />MI CESTA
				<font id="numProductosCestaId"><?php $numPRODS = $_SESSION["ocarrito"]->num_productos (); if ($numPRODS>0) { echo "($numPRODS)"; } ?></font>
			</a>
		</li>
		<li><a href="mispedidos.php"><img src="../img/camion.png" alt="" />MIS PEDIDOS</a></li>
		<li><a href="mislistas.php"><img src="../img/list.png" alt="" />MIS LISTAS</a></li>
		<li><a href="cambioClave.php"><img src="../img/llave.png" alt="" />MI CLAVE</a></li>
	</ul>
</div>

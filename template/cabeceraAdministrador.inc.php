<?php
require_once "../includes/funciones.inc.php";
compruebaSesionRepartidorOAdministrador();
?>	<header id="header">
		<table style="width: 100%" class="tablacabecera">
			<tbody>
			<tr>
				<td rowspan="2" style="width: 15%; text-align: center; position: relative;"><img id="logoInterno" alt="logo" src="../img/cerroViejo.png"/></td>
				<td style="width: 85%; vertical-align: bottom;"><div id="datosUsuario"><?=@$_SESSION['NOMBRE_COMPLETO']?>&nbsp;<a href="../includes/logout.php">Desconectar</a></div></td>
			</tr>
			<tr>
				<td style="width: 100%; height: 60px; vertical-align:top;">
				<div class="mainWrap">
					<a id="touch-menu" class="mobile-menu" href="#"><i class="icon-reorder"></i>Menú</a>
						<nav>
						<ul class="menu">
				        	<li><a href="#"><i class="icon-home"></i>Pedidos</a>
				        		<ul class="sub-menu">
				        			<?php if (@$_SESSION['TIPO_USUARIO']=='ADMINISTRADOR') { ?>
									<li><a href="index.php">Apertura y Cierre</a></li>
									<?php } ?>
									<li><a href="pedidos.php?submenuPedido=3">Buscador</a></li>
									<li><a href="pedidos.php?submenuPedido=1">Pedidos a Proveedor</a></li>
									<li><a href="pedidos.php?submenuPedido=2">Pedidos de Usuarios</a></li>
									<li><a href="pedidos.php?submenuPedido=6">Cuadre de Pedidos</a></li>
				        		</ul>
				        	</li>
				        	<?php if (@$_SESSION['TIPO_USUARIO']=='ADMINISTRADOR') { ?>
				        	<li><a href="#">Contabilidad</a>
				        		<ul class="sub-menu">
				        			<li><a href="contabilidad.php">Mi Contabilidad</a></li>
									<li><a href="usuarios.php">Contabilidad de Usuarios</a></li>
									<li><a href="facturas.php">Facturas</a></li>
				        		</ul>
				        	</li>
				        	<?php } ?>
				        	<li><a href="#">Informes</a>
				        		<ul class="sub-menu">
				        			<li><a href="imprimirAlbaran.php" target="_blank">Albaranes Proveedores</a></li>
									<?php /* <li><a href="imprimirPedidosTodosUsuarios.php" target="_blank">Pedidos de Usuarios</a></li> */ ?>
									<li><a href="informes/excelCatalogoProductos.php">Cat&aacute;logo de Productos</a></li>
									<?php /* <li><a href="informes/excelDemandaPorUsuario.php">Demanda Por Usuario</a></li> */ ?>
									<li><a href="demandaPorUsuarioLote.php">Demanda Por Usuario y Lote</a></li>
									<li><a href="informes/excelProductosMasDemandados.php">Productos m&aacute;s demandados</a></li>
									<li><a href="informes/excelDemandaProveedores.php">Demanda a Proveedores</a></li>
									<li><a href="informes/excelConsumoPorUsuario.php">Consumo por usuario</a></li>
									<li><a href="beneficioPorUsuario.php">Beneficio Anual por Usuario</a></li>
									<li><a href="beneficioPorUsuarioAcumulado.php">Beneficio Anual por Usuario Acumulado</a></li>
				        		</ul>
				        	</li>
				        	<li><a href="#">Histórico</a>
				        		<ul class="sub-menu">
				        			<li><a href="pedidos.php?submenuPedido=3">Buscador</a></li>
									<li><a href="pedidos.php?submenuPedido=4">Pedidos a Proveedor</a></li>
									<li><a href="pedidos.php?submenuPedido=5">Pedidos de Usuarios</a></li>
				        		</ul>
				        	</li>
				        	<li><a href="#">Mantenimiento</a>
				        		<ul class="sub-menu">
				        		<?php if (@$_SESSION['TIPO_USUARIO']=='ADMINISTRADOR') { ?>
				        			<li><a href="categorias.php">Categorías y Subcategorías</a></li>
				        		<?php } ?>
									<li><a href="productos.php?idCategoria=-1">Productos</a></li>
									<!-- <li><a href="proveedores.php">Proveedores</a></li> -->
								<?php if (@$_SESSION['TIPO_USUARIO']=='ADMINISTRADOR') { ?>
									<li><a href="usuarios.php">Usuarios</a></li>
								<?php } ?>
									<li><a href="calendario.php">Calendario</a></li>
								<?php if (@$_SESSION['TIPO_USUARIO']=='ADMINISTRADOR') { ?>
									<li><a href="solicitudes.php">Solicitudes Web</a></li>
								<?php } ?>
								<?php if (@$_SESSION['TIPO_USUARIO']=='ADMINISTRADOR') { ?>
									<li><a href="subgrupos.php">Subgrupos</a></li>
								<?php } ?>
								<?php if (@$_SESSION['TIPO_USUARIO']=='ADMINISTRADOR') { ?>
									<li><a href="proveedores.php">Proveedores</a></li>
								<?php } ?>
				        		</ul>
				        	</li>
				        </ul>
						</nav>
					</div>
				</td>
			</tr>
			</tbody>
    	</table>
    </header>
    <script type="text/javascript">
	    $(document).ready(function(){ 
	    	var touch 	= $('#touch-menu');
	    	var menu 	= $('.menu');
	     
	    	$(touch).on('click', function(e) {
	    		e.preventDefault();
	    		menu.slideToggle();
	    	});
	    	
	    	$(window).resize(function(){
	    		var w = $(window).width();
	    		if(w > 767 && menu.is(':hidden')) {
	    			menu.removeAttr('style');
	    		}
	    	});
	    	
	    });		
	</script>
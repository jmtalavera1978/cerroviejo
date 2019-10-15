<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
	<script type="text/javascript" src="../js/print.js"></script>
	<script type="text/javascript">
	function finalizarPedidoProveedor (idPedidoProv, nombre) {
		document.location='finalizar_pedido_proveedor.php?idPedidoProveedor='+idPedidoProv+'&nombre='+nombre+'&url='+document.location;
	}
	</script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<div id="contenidoAdmin">
			<?php
			$idPedidoProv = @$_GET ['idPedidoProv'].@$_POST ['idPedidoProv'];
			$esConsulta = @$_GET ['consulta'].@$_POST ['consulta'];
			
			
			if (isset ( $_SESSION ['mensaje_generico'] )) {
				echo "<h5>" . $_SESSION ['mensaje_generico'] . "</h5>";
				$_SESSION ['mensaje_generico'] = NULL;
			}
			?>
			
			<h1 class="cal">Detalle de Pedido a Proveedores</h1>
			<?php
			include_once 'detalle_pedidos_prov.php';
			?>
	
	
	</section>
	<br />
	<div id="botonera">
		<?php if ($esConsulta!='true') { ?>
			<input id="addProducto" name="addProducto"  type="button" value="A&ntilde;adir Producto al Pedido" onclick="document.location='nuevoProductoPedidoProv.php?lote=<?=$lote?>&proveedor=<?=$idProveedor?>&idPedido=<?=$idPedidoProveedor?>&consulta=<?=$esConsulta?>'" />
		<?php } ?>
		<input id="volver" name="volver" type="button"
			onclick="document.location='pedidos.php'" value="Volver" />
	</div>
</body>
</html>

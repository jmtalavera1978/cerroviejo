<?php
include("../includes/funciones.inc.php");
compruebaSesionAdministracion();
$cantidad = $_GET['cantidad'];
$resProd = consulta("update PEDIDOS_PRODUCTOS SET CANTIDAD_REVISADA='$cantidad', CHECK_REVISADO='1', CHECK_FINALIZADO='1' WHERE ID_PEDIDO='".$_GET['idPedido']."' and ID_PRODUCTO='".$_GET['idProducto']."'");

$res3 = consulta("select PP.CANTIDAD_REVISADA from PEDIDOS_PRODUCTOS PP WHERE PP.ID_PEDIDO='".$_GET['idPedido']."' and PP.ID_PRODUCTO='".$_GET['idProducto']."' ");
$fila3 = @extraer_registro($res3);
?>
<input type="text" style="width: 50%; text-align:right;"
	onfocus="this.value=''"
	onkeypress="return NumCheck(event, this)"
	onblur="modificarRevisado('<?=$_GET['idCampo']?>', '<?=$_GET['idPedido']?>', '<?=$_GET['idProducto']?>', '<?=$_GET['precio']?>', '<?=$_GET['idUsuario']?>', '<?=@$_GET['medida']?>',  this)" 
	value="<?=round(@$fila3['CANTIDAD_REVISADA'], 2)?>"/>
<?=@$_GET['medida']?>
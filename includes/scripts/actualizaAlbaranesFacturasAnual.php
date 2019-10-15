<?php
include_once ("../funciones.inc.php");
compruebaSesionRepartidorOAdministrador();

actualizarAlbaranesFacturas(2015);
actualizarAlbaranesFacturas(2016);
echo "<br/><br/>";
//actualizarAlbaranes(2013);
//actualizarAlbaranes(2014);
//actualizarAlbaranes(2015);
//actualizarAlbaranes(2016);

function actualizarAlbaranesFacturas($anyo) {
	$error = 0;
	
	try {
		$resPedidos = consulta ("select * from (
	select @rownum:=@rownum+1 AS NUM_FACTURA, p.ID_PEDIDO, p.FECHA_FACTURA, p.LOTE, p.ID_USUARIO FROM (SELECT @rownum:=0) r, PEDIDOS p
	where p.COBRADO=1 AND p.FECHA_FACTURA like '$anyo-%' ORDER BY p.FECHA_FACTURA
) as t");


		while ($pedidoProducto = extraer_registro($resPedidos)) {
			$id_pedido = $pedidoProducto['ID_PEDIDO'];
			$numFactura = $pedidoProducto['NUM_FACTURA'];
			$fechaFactura = $pedidoProducto['FECHA_FACTURA'];
			
			$upda = consulta ("UPDATE PEDIDOS SET NUM_FACTURA_ANUAL=$numFactura WHERE ID_PEDIDO='$id_pedido'");
			
			if ($upda)
				echo "Nuevo número de factura ".str_pad($numFactura, 4, "0", STR_PAD_LEFT)."/$anyo: Pedido ".$id_pedido.", con fecha ".$fechaFactura."<br>";
			else 
				echo "Error";
		}
	
		$mensaje = "CORRECTO";

	} catch (Exception $ex) {
		//Devuelve el mensaje de error
		$mensaje = $ex->qetMessage();
	}
	
	return $mensaje;
}

function actualizarAlbaranes($anyo) {
	$error = 0;
	
	try {
		$resPedidos = consulta("select * from (
			select @rownum:=@rownum+1 AS NUM_ALBARAN, p.ID_PEDIDO, p.FECHA_PEDIDO FROM (SELECT @rownum:=0) r, PEDIDOS p
			where p.FECHA_PEDIDO like '$anyo-%' order by p.FECHA_PEDIDO
		) as t");


		while ($pedidoProducto = extraer_registro($resPedidos)) {
			$id_pedido = $pedidoProducto['ID_PEDIDO'];
			$numAlbaran = $pedidoProducto['NUM_ALBARAN'];
			$fecha = $pedidoProducto['FECHA_PEDIDO'];
			
			$upda = consulta ("UPDATE PEDIDOS SET NUM_ALBARAN_ANUAL=$numAlbaran WHERE ID_PEDIDO='$id_pedido'");
			
			if ($upda)
				echo "Nuevo número de albarán ".str_pad($numAlbaran, 5, "0", STR_PAD_LEFT)."/$anyo: Pedido ".$id_pedido.", con fecha ".$fecha."<br>";
			else 
				echo "Error";
		}
	
		$mensaje = "CORRECTO";

	} catch (Exception $ex) {
		//Devuelve el mensaje de error
		$mensaje = $ex->qetMessage();
	}
	
	return $mensaje;
}
?>
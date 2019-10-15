<?php
require_once "../includes/funciones.inc.php";

$fechaActual = new DateTime();
$res = consulta("select MAX(NUM_FACTURA_ANUAL) as NUM_FACTURA from PEDIDOS where FECHA_FACTURA like '".$fechaActual->format("Y")."%' and COBRADO='1' and NUM_FACTURA_ANUAL is not null");
$fila = extraer_registro($res);
$numFactura = $fila['NUM_FACTURA'] + 1;
consulta("insert into PEDIDOS(`ID_PEDIDO`, `ID_USUARIO`, `LOTE`, `FECHA_PEDIDO`, `ESTADO`, `COMENTARIOS`, `HORA_INI`, `HORA_FIN`, `DESCUENTO_RECARGO`, `COBRADO`, `NUM_ALBARAN`, `NUM_FACTURA`, `FECHA_FACTURA`, `RE`, `FECHA_ENTREGA`, `NUM_ALBARAN_ANUAL`, `NUM_FACTURA_ANUAL`) 
	VALUES (NULL, 'ADMIN', '', '', 'FINALIZADO', NULL, NULL, NULL, '0', '1', NULL, NULL, '".$fechaActual->format("Y-m-d H:i:s")."', '0', '".$fechaActual->format("Y-m-d H:i:s")."', -1, '$numFactura')");
	
Header ("Location: facturas.php");
?>
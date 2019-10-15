<?php
	require_once "../../includes/funciones.inc.php";
	
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-Type: application/vnd.ms-excel");
	header ("Content-Disposition: attachment; filename=DemandaPorUsuario.xls" );
	
	/* 
	 * EN LUGAR DE GENERARLO DIRECTAMENTE EN EXCEL IR A UNA PANTALLA QUE PERMITA SELECCIONAR: LOTE (TODOS O UNO CONCRETO), SUBGRUPO DE USUARIOS 
	 * (TODOS O UNO EN CONCRETO), USUARIO (TODOS O UNO CONCRETO), TIPO DE TRANSPORTE (TODOS O UNO CONCRETO). TODOS LOS FILTROS SON INDEPENDIENTES ENTRE SÍ. 
	 * UNA VEZ INDICADA LAS OPCIONES DESEADAS, PULSAR BOTÓN GENERAR INFORME Y QUE YA SALGA EL EXCEL. SEAN CUAL SEAN LOS FILTROS, LAS COLUMNAS A SACAR SERÁN:
	 * 
	 * LOTE, SUBGRUPO, USUARIO, PROVEEDOR, PRODUCTO, CANTIDAD TOTAL ADQUIRIDA, UNIDAD DE MEDIDA, COSTO TOTAL
	 */
	
	if (isset($_POST['idUsuario'])) {
		$idUsuario = $_POST['idUsuario'];
	} 

	if (isset($_POST['idSubgrupo'])) {
		$idSubgrupo = $_POST['idSubgrupo'];
	} 
	
	if (isset($_POST['lote'])) {
		$lote = $_POST['lote'];
	}
	
	if (isset($_POST['transporte'])) {
		$envio = $_POST['transporte'];
	}

	
	// MONTAR CONSULTA
	$consulta = "select * FROM VW_DEMANDA_X_USUARIO_LOTE WHERE 1=1";
	
	//Filtros
	if (isset($lote) && $lote!='') {
		$consulta .= " and LOTE='$lote'";
	}
	if (isset($idSubgrupo) && $idSubgrupo!='') {
		$consulta .= " and ID_SUBGRUPO='$idSubgrupo'";
	}
	if (isset($idUsuario) && $idUsuario!='') {
		$consulta .= " and USUARIO='$idUsuario'";
	}
	if (isset($envio) && $envio!='') {
		$consulta .= " and (
			select count(P.ID_PRODUCTO )
			from pedidos_productos PP, pedidos p2, productos P
			where p2.ID_PEDIDO=PP.ID_PEDIDO 
			and PP.ID_PRODUCTO=P.ID_PRODUCTO 
			and P.ID_SUBCATEGORIA='-10'
			and P2.ID_PEDIDO = VW_DEMANDA_X_USUARIO_LOTE.ID_PEDIDO
			and P.ID_PRODUCTO = '$envio'
		)>0 ";
	}
	
	$resultado_vista = consulta($consulta);	
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>
	<tr><td>
	<table class="tablaResultados">
			<tr bgcolor="#000066" style="color: white; border: 2px solid #CCCCCC; font-weight: bolder;">
				<td colspan="8" align="center"><b>DEMANDA POR USUARIO POR LOTE</b></td>
			</tr>
			<tr bgcolor="#000066" style="color: white; border: 2px solid #CCCCCC;">
				<th>LOTE</th>
				<th>SUBGRUPO</th>
				<th>USUARIO</th>
				<th>PROVEEDOR</th>
				<th>ID_PRODUCTO</th>
				<th>PRODUCTO</th>
				<th>CANTIDAD TOTAL ADQUIRIDA</th>
				<th align="right">COSTO TOTAL</th>
			</tr>
		<?php
			$total = 0.00;
			$totalRev = 0.00;
			
			$numFilas = numero_filas($resultado_vista);
			
			if ($numFilas==0) {
			?>
				<tr><td colspan="8">No hay resultados</td></tr>
				<?php 
			} else {
			 
			while ($fila = extraer_registro($resultado_vista)) {
		?>
			<tr bgcolor="white" style="color: black; border: 2px solid #CCCCCC;">
				<td>LOTE <?=$fila['LOTE']?></td>
				<td><?=$fila['SUBGRUPO']?></td>
				<td><?=$fila['USUARIO']?></td>
				<td><?=$fila['PROVEEDOR']?></td>
				<td><?=$fila['ID_PRODUCTO']?></td>
				<td><?=$fila['PRODUCTO']?></td>
				<td><?=$fila['CANTIDAD_TOTAL']?></td>
				<td align="right" nowrap><?=$fila['COSTO']?> &euro;</td>
			</tr>
		<?php } 
		}
		?>
	</table>
	</td>
	</tr>
	</table>
</body>
</html>
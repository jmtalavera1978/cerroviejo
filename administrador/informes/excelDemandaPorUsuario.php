<?php
	require_once "../../includes/funciones.inc.php";
	
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-Type: application/vnd.ms-excel");
	//header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=DemandaPorUsuario.xls" );
		
	$lote = @$_GET['lote'];
	$consulta = "select * FROM VW_DEMANDA_X_USUARIO";
	
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
				<td colspan="5" align="center"><b>DEMANDA POR USUARIO</b></td>
			</tr>
			<tr bgcolor="#000066" style="color: white; border: 2px solid #CCCCCC;">
				<th>SUBGRUPO</th>
				<th>USUARIO</th>
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
				<tr><td colspan="5">No hay resultados</td></tr>
				<?php 
			}
			 
			while ($fila = extraer_registro($resultado_vista)) {
		?>
			<tr bgcolor="white" style="color: black; border: 2px solid #CCCCCC;">
				<td><?=$fila['SUBGRUPO']?></td>
				<td><?=$fila['USUARIO']?></td>
				<td><?=$fila['PRODUCTO']?></td>
				<td><?=$fila['CANTIDAD_TOTAL']?></td>
				<td align="right" nowrap><?=$fila['COSTO']?> &euro;</td>
			</tr>
		<?php } ?>
	</table>
	</td>
	</tr>
	</table>
</body>
</html>
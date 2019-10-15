<?php require_once "../includes/lib_carrito.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head_interno.inc.php"; ?>
</head>
<body>
	 <?php require_once "../template/cabeceraUsuarios.inc.php"; ?>
	 <div class="wrapper">
		<div class="grids top">
		<?php require_once "../template/menuLateralUsuarios.inc.php"; 
		
		if (isset($_GET['idLista'])) {
			$idLista = $_GET['idLista'];
			$usuario = $_SESSION['ID_USUARIO'];
			
			$consulta = "select NOMBRE from LISTAS where ID_USUARIO='$usuario' and ID_LISTA='$idLista' ";
			$listas = consulta($consulta);
			$lista = extraer_registro($listas);
			
			$listaProductos = consulta("select LP.*, P.*, U.DESCRIPCION as MEDIDA from LISTAS L, LISTAS_PRODUCTOS LP, PRODUCTOS P, UNIDADES U
				where L.ID_USUARIO='$usuario' and L.ID_LISTA='$idLista' and L.ID_LISTA=LP.ID_LISTA and LP.ID_PRODUCTO=P.ID_PRODUCTO
					AND P.UNIDAD_MEDIDA = U.ID_UNIDAD");
		} else {
			$_SESSION['mensaje_generico'] = 'Lista no encontrada';
			header("Location: mislistas.php");
		}
	?>
			<h1 class="cal"><?=@$lista['NOMBRE']?></h1>
			<?php 
			if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5";
				if (!strpos($_SESSION['mensaje_generico'], 'correctamente')) {
					echo " style=\"color:red\"";
				}
				echo ">".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			} 
			?>
				<table class="tablaResultados">
					<thead>
						<tr>
							<th style="text-align: left">Producto</th>
							<th align="center">Cantidad</th>
							<th align="center" width="10%">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($listaProductos)==0) {
						
?>
					<tr>
						<td colspan="3">No hay productos en la lista</td>
					</tr>
					<?php 
					} else {
						
						while ($filaP = extraer_registro($listaProductos)) {
							$idProducto = $filaP['ID_PRODUCTO'];
							$cantidad = $filaP['CANTIDAD'];
							
							$productoMedida = $filaP['MEDIDA']." ".$filaP['DESCRIPCION_MEDIDA'];
							$peso_por_unidad = $filaP['PESO_POR_UNIDAD'];
							if (isset($peso_por_unidad) && $peso_por_unidad>0) {
								$productoMedida .= " ($peso_por_unidad Kg)";
							}
					?>
					<tr>
						<td><?=$filaP['DESCRIPCION']?></td>
						<td align="center"><?=round($cantidad, 2)?> <?=$productoMedida?></td>
						<td align="center" style="white-space: nowrap">
							<a title="Borrar Producto Lista" href="borrarProductoLista.php?idLista=<?=$filaP['ID_LISTA']?>&idProducto=<?=$filaP['ID_PRODUCTO']?>">
								<img alt="Borrar" title="Borrar" class="imgcomprar" src="../img/BORRAR.png" height="25" width="25" />
							</a>
						</td>
					</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>
			
			<div id="botonera" style="margin-right: 11%">
				<input id="cancel" name="cancel"  type="button" value="Volver" onclick="document.location='mislistas.php'" />
			</div>	
	
        	</div>
		</div>
	</div>
</div>

<?php require_once "../template/pie.inc.php";  ?>
</body>
</html>
<?php require_once "../includes/lib_carrito.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head_interno.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
	 <?php require_once "../template/cabeceraUsuarios.inc.php"; ?>
	 <div class="wrapper">
		<div class="grids top">
			<?php require_once "../template/menuLateralUsuarios.inc.php"; ?>
			

			<h1 class="cal">Calendario de entregas</h1>
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
							<th>CATEGORÍA</th>
							<th align="center">FECHA DE APERTURA DE PEDIDO</th>
							<th align="center">FECHA DE ENTREGA</th>
							<th align="center">ESTADO</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$_SESSION['idCategoria'] = NULL;
					$usuario = $_SESSION['ID_USUARIO'];

					// Consulta de calendario
					$calendario = consulta("select cl.ID_CALENDARIO, cl.FECHA_PEDIDO, cl.FECHA_ENTREGA, cl.ID_CATEGORIA, cl.ESTADO, ct.DESCRIPCION from CALENDARIO cl, CATEGORIAS ct where cl.ID_CATEGORIA = ct.ID_CATEGORIA and FECHA_ENTREGA>='".Date("Y-m-d H:i:s")."'"); 

					if (numero_filas($calendario)==0) {
?>
					<tr>
						<td colspan="4">No hay entregas previstas</td>
					</tr>
<?php 
					} else {
						$fechaActual = new DateTime();

						while ($filaCal = extraer_registro($calendario)) {
							$dateP = new DateTime($filaCal['FECHA_PEDIDO']);
							$date = new DateTime($filaCal['FECHA_ENTREGA']);

							$idCategoria = $filaCal['ID_CATEGORIA'];
							/*$fechaUltPedido = consulta ("select * from (select P.FECHA_PEDIDO from PEDIDOS P, PEDIDOS_PRODUCTOS PP, PRODUCTOS PR WHERE P.ID_PEDIDO=PP.ID_PEDIDO AND PP.ID_PRODUCTO=PR.ID_PRODUCTO AND P.ID_USUARIO='$usuario' AND PR.ID_CATEGORIA='$idCategoria' ORDER BY P.FECHA_PEDIDO DESC) t LIMIT 0, 1");
							if (numero_filas($fechaUltPedido)==1) {
								$fechaUltPedido =  extraer_registro ($fechaUltPedido);
								$fechaUltPedido = date_create_from_format('Y-m-d H:i:s', $fechaUltPedido['FECHA_PEDIDO']);
								$fechaUltPedido = @$fechaUltPedido->format('d/m/Y');
							} else {
								$fechaUltPedido = '';
							}*/

							
							if ($dateP<=$fechaActual && $fechaActual<=$date) {
								$estado = 'EN TRAMITACIÓN';
							} else if ($dateP>$fechaActual ) {
								$estado = 'NO DISPONIBLE';
							} else {
								$estado = NULL;
							}

							if ($estado!=NULL) {
?>
					<tr>
						<td><?=$filaCal['DESCRIPCION']?></td>
						<td align="center"><?=$dateP->format('d/m/Y')?></td>
						<td align="center"><?=$date->format('d/m/Y')?></td>
						<td align="center"><?=$estado?></td>
					</tr>
<?php
							}
						}
					}
					?>
				</tbody>
			</table>
			
			
        	</div>
		</div>
	</div>
</div>

<?php require_once "../template/pie.inc.php";  ?>
</body>
</html>

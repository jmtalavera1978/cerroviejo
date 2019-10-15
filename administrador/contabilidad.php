<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<div id="contenidoAdmin">
			<?php
				if (isset($_SESSION['mensaje_generico'])) {
					echo "<h5 style=\"text-align: left;\">".$_SESSION['mensaje_generico']."</h5>";
					$_SESSION['mensaje_generico'] = NULL;
				} 

				$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
 
				$fechaActual = new DateTime();
				$idUsuario = NULL;
				
				$fechaActualFormateada = $meses[date($fechaActual->format("m"))-1]." ".$fechaActual->format("Y");
				$mes = @$_GET['mes'];
				if (isset($mes)) {
					$_SESSION['mes'] = $mes;
				} else {
					if (isset($_SESSION['mes'])) {
						$mes = @$_SESSION['mes'];
					} else {
						$_SESSION['mes'] = $fechaActual->format("Y-m");
						$mes = $_SESSION['mes'];
					}
				}
				
				if (isset($_GET['idUsuario'])) {
					$idUsuario = $_GET['idUsuario'];
					$_SESSION['idUsuarioSel'] = $idUsuario;
				} else if (isset($_SESSION['idUsuarioSel'])) {
					$idUsuario = $_SESSION['idUsuarioSel'];
				}
				
				$resSaldoCaja = consultarCajaActual();
			?>
			<div style="position: relative; text-align: right;"><span><b>SALDO GLOBAL CAJA:</b>&nbsp;&nbsp;&nbsp;<?=$resSaldoCaja?> &euro;</span></div>
							
			<h1 class="cal" style="margin-bottom: -20px;">Contabilidad</h1>
			<div id="tituloProveedores">
			<span>&nbsp;MES:&nbsp;
				<select name="mes" id="mes" onchange="document.location='contabilidad.php?mes='+this.value;">
					<option value="">Seleccione...</option>
					<?php
					for ($i=0; $i<12; $i++) {
						?>
						<option value="<?=$fechaActual->format("Y-m")?>" <?php if ($mes==($fechaActual->format("Y-m"))) echo "selected=\"selected\"";?>><?=$fechaActualFormateada?></option>
						<?php
						$fechaActual = $fechaActual->modify("-1 month");
						$fechaActualFormateada = $meses[date($fechaActual->format("m"))-1]." ".$fechaActual->format("Y");
					} 
					?>
				</select>
				&nbsp;&nbsp;&nbsp;USUARIO:&nbsp;
				<select id="idUsuario" name="idUsuario" onchange="document.location='contabilidad.php?idUsuario='+this.value">
					<option value="">Seleccione un usuario...</option>
					<option value="-SISTEMA-" <?php if ($idUsuario=='-SISTEMA-') { echo "selected";} ?>>-SISTEMA-</option>
					<?=optionsUsuariosActivos($idUsuario)?>
				</select>
			</span>
			</div>
			
			<div id="listadoProductos">
			
				<?php if ((isset($mes) && $mes!=NULL) || (isset($idUsuario) && $idUsuario!=NULL)) {
					if (isset($mes) && $mes!=NULL) {
						$mesInicio = $mes."-01 00:00:00";
						$mesInicio = DateTime::createFromFormat("Y-m-d H:i:s", $mesInicio);
						$mesFin = getUltimoDiaMes($mesInicio->format("Y"),$mesInicio->format("m"));
						
						$consulta = "SELECT * FROM CONTABILIDAD WHERE FECHA BETWEEN '".$mesInicio->format("Y-m-d H:i:s")."' AND '".$mesFin->format("Y-m-d H:i:s")."'";
						if (isset($idUsuario) && $idUsuario!=NULL) {
							$consulta .= " AND USUARIO='$idUsuario'";
						}
					}  else {
						$consulta = "SELECT * FROM CONTABILIDAD WHERE USUARIO='$idUsuario'";
					}
					$consulta .= " ORDER BY ID_APUNTE DESC";
					
					$resContabilidad = consulta($consulta);
				?>
				<table class="tablaResultados">
					<thead>
						<tr>
							<th align="center">FECHA</th>
							<th align="left">CONCEPTO</th>
							<th align="center">USUARIO</th>
							<th align="center">IMPORTE</th>
							<th align="center">SALDO CAJA</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($resContabilidad)==0) {
	?>
						<tr>
							<td colspan="5">No hay datos de contabilidad para este mes.</td>
						</tr>
	<?php 
					} else {
						
						//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA , EN EL EJEMPLO PONGO 15
						$rows_per_page= consultarPaginacion ();
						
						if (($numrows = numero_filas($resContabilidad))>0) {
							//AL PRINCIPIO COMPRUEBO SI HICIERON CLICK EN ALGUNA PÁGINA
							if(isset($_GET['page']))
							{
								$page= $_GET['page'];
							}
							else
							{
								//SI NO DIGO Q ES LA PRIMERA PÁGINA
								$page=1;
							}
								
							//CALCULO LA ULTIMA PÁGINA
							$lastpage= ceil($numrows / $rows_per_page);
								
							//COMPRUEBO QUE EL VALOR DE LA PÁGINA SEA CORRECTO Y SI ES LA ULTIMA PÁGINA
							$page=(int)$page;
								
							if($page > $lastpage)
							{
								$page= $lastpage;
							}
								
							if($page < 1)
							{
								$page=1;
							}
								
							//CREO LA SENTENCIA LIMIT PARA AÑADIR A LA CONSULTA QUE DEFINITIVA
							$limit= 'LIMIT '. ($page -1) * $rows_per_page . ',' .$rows_per_page;
								
							//REALIZO LA CONSULTA QUE VA A MOSTRAR LOS DATOS (ES LA ANTERIO + EL $limit)
							$consulta .=" $limit";
							$resContabilidad=consulta($consulta);
						}

						while ($filaCon = extraer_registro($resContabilidad)) {
							$fecha = $filaCon['FECHA'];
							$fecha = DateTime::createFromFormat("Y-m-d H:i:s", $fecha);
	?>
						<tr>
							<td <?php if ($filaCon['USUARIO']=='-SISTEMA-') {echo "style=\"color:blue\""; } ?> align="center" title="<?=$fecha->format("H:i:s")?>"><?=$fecha->format("d/m/Y")?></td>
							<td <?php if ($filaCon['USUARIO']=='-SISTEMA-') {echo "style=\"color:blue\""; } ?> align="left"><?=$filaCon['CONCEPTO']?></td>
							<td <?php if ($filaCon['USUARIO']=='-SISTEMA-') {echo "style=\"color:blue\""; } ?> align="center"><?=$filaCon['USUARIO']=='-SISTEMA-' ? 'SISTEMA' : $filaCon['USUARIO']?></td>
							<td <?php if ($filaCon['USUARIO']=='-SISTEMA-') {echo "style=\"color:blue\""; } ?> align="right" nowrap><?=($filaCon['ESDEBE'] == '1' ? "-".$filaCon['IMPORTE']. "  &euro;" : "+".$filaCon['IMPORTE']. "  &euro;")?></td>
							<!-- <td <?php if ($filaCon['USUARIO']=='-SISTEMA-') {echo "style=\"color:blue\""; } ?> align="right" nowrap><?=($filaCon['ESDEBE'] == '0' ? "+".$filaCon['IMPORTE']. "  &euro;" : '&nbsp;')?></td> -->
							<td <?php if ($filaCon['USUARIO']=='-SISTEMA-') {echo "style=\"color:blue\""; } ?> align="right"><?=$filaCon['TOTAL_CAJA']?>  &euro;</td>
						</tr>
	<?php 
						}
	?>
					</tbody>
				</table>
			<?php 
			//UNA VEZ Q MUESTRO LOS DATOS TENGO Q MOSTRAR EL BLOQUE DE PAGINACIÓN SIEMPRE Y CUANDO HAYA MÁS DE UNA PÁGINA
			echo "<div style='clear:both;'></div>";
			
			if(@$numrows != 0)
			{
				$nextpage= $page +1;
				$prevpage= $page -1;
				 
				?><div class="grid-12 grid paginationCV" style="position:relative; margin-top: 10px; margin-bottom: 10px"><ul><?php
			           //SI ES LA PRIMERA PÁGINA DESHABILITO EL BOTON DE PREVIOUS, MUESTRO EL 1 COMO ACTIVO Y MUESTRO EL RESTO DE PÁGINAS
			           if ($page == 1) 
			           {
			            ?>
			              <li class="previous-off">&larr;</li>
			              <li class="active">1</li> 
			         <?php
			              for($i= $page+1; $i<= $lastpage ; $i++)
			              {?>
			                <li><a href="contabilidad.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
			        <?php }
			           
			           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
			            if($lastpage >$page )
			            {?>      
			                <li class="next"><a href="contabilidad.php?page=<?php echo $nextpage;?>" >&rarr;</a></li><?php
			            }
			            else
			            {?>
			                <li class="next-off">&rarr;</li>
			        <?php
			            }
			        } 
			        else
			        {
			     
			            //EN CAMBIO SI NO ESTAMOS EN LA PÁGINA UNO HABILITO EL BOTON DE PREVIUS Y MUESTRO LAS DEMÁS
			        ?>
			            <li class="previous"><a href="contabilidad.php?page=<?php echo $prevpage;?>">&larr;</a></li><?php
			             for($i= 1; $i<= $lastpage ; $i++)
			             {
			                           //COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
			                if($page == $i)
			                {
			            ?>       <li class="active"><?php echo $i;?></li><?php
			                }
			                else
			                {
			            ?>       <li><a href="contabilidad.php?page=<?php echo $i;?>" ><?php echo $i;?></a></li><?php
			                }
			            }
			             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
			            if($lastpage >$page )
			            {   ?>   
			                <li class="next"><a href="contabilidad.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
			            }
			            else
			            {
			        ?>       <li class="next-off">&rarr;</li><?php
			            }
			        }     
			    ?></ul></div></div><?php
			    } 
			?>
				<?php }
				} ?>
		</div>
		<div style="clear:both;"></div>
		<br/>
		<div id="botonera">
			<input id="nuevo" name="nuevo"  type="button" value="Nuevo Apunte" onclick="document.location='nuevoApunteContable.php'" />
		</div>
	</section>
</body>
</html>

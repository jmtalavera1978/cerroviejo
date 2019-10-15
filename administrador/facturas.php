<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
	<script type="text/javascript">
	function generarAlbaranFactura(idPedido, tipo) {
		window.open('imprimirFacturaPedido.php?idPedido='+idPedido+'&tipo='+tipo, 'impresionFactura'+idPedido, '');
	}
	</script>
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
				
				$anyo = @$_GET['anyo'];
				if (isset($anyo)) {
					$_SESSION['anyoFac'] = $anyo;
				} else {
					if (isset($_SESSION['anyoFac'])) {
						$anyo = @$_SESSION['anyoFac'];
					} else {
						$_SESSION['anyoFac'] = $fechaActual->format("Y");
						$anyo = $_SESSION['anyoFac'];
					}
				}
				
				$mes = @$_GET['mes'];
				if (isset($mes)) {
					$_SESSION['mesFac'] = $mes;
				} else {
					if (isset($_SESSION['mesFac'])) {
						$mes = @$_SESSION['mesFac'];
					} else {
						$_SESSION['mesFac'] = $fechaActual->format("m");
						$mes = $_SESSION['mesFac'];
					}
				}
				
				$lote = @$_GET['lote'];
				if (isset($lote)) {
					$_SESSION['loteFac'] = $lote;
				} else {
					if (isset($_SESSION['loteFac'])) {
						$lote = @$_SESSION['loteFac'];
					}
				}
				
				$idUsuario = NULL;
				if (isset($_GET['idUsuario'])) {
					$idUsuario = $_GET['idUsuario'];
					$_SESSION['idUsuarioSelPedUsuF'] = $idUsuario;
				} else if (isset($_SESSION['idUsuarioSelPedUsuF'])) {
					$idUsuario = $_SESSION['idUsuarioSelPedUsuF'];
				}
				
				$trimestre = @$_GET['trimestre'];
				if (isset($trimestre)) {
					$_SESSION['trimestre'] = $trimestre;
				} else {
					if (isset($_SESSION['trimestre'])) {
						$trimestre = @$_SESSION['trimestre'];
					}
				}
			?>
							
			<h1 class="cal" style="margin-bottom: -20px;">Facturas</h1>
			<div id="tituloProveedores">
			<!-- 
				Filtrar por Trimestre: 1T/2T/3T/4T/ANUAL (1T+2T+3T+4T).-->
			<span>&nbsp;AÑO:&nbsp;
				<select name="anyo" id="anyo" onchange="document.location='facturas.php?anyo='+this.value;">
					<option value="">Seleccione...</option>
					<?php
					for ($i=0; $i<5; $i++) {
						?>
						<option value="<?=$fechaActual->format("Y")?>" <?php if ($anyo==($fechaActual->format("Y"))) echo "selected=\"selected\"";?>><?=$fechaActual->format("Y")?></option>
						<?php
						$fechaActual = $fechaActual->modify("-1 year");
					} 
					?>
				</select>
			</span>
			<span>&nbsp;&nbsp;&nbsp;MES:&nbsp;
				<select name="mes" id="mes" onchange="document.location='facturas.php?mes='+this.value;">
					<option value="">Seleccione...</option>
					<option value="01" <?php if ($mes=='01') echo "selected=\"selected\"";?>>Enero</option>
					<option value="02" <?php if ($mes=='02') echo "selected=\"selected\"";?>>Febrero</option>
					<option value="03" <?php if ($mes=='03') echo "selected=\"selected\"";?>>Marzo</option>
					<option value="04" <?php if ($mes=='04') echo "selected=\"selected\"";?>>Abril</option>
					<option value="05" <?php if ($mes=='05') echo "selected=\"selected\"";?>>Mayo</option>
					<option value="06" <?php if ($mes=='06') echo "selected=\"selected\"";?>>Junio</option>
					<option value="07" <?php if ($mes=='07') echo "selected=\"selected\"";?>>Julio</option>
					<option value="08" <?php if ($mes=='08') echo "selected=\"selected\"";?>>Agosto</option>
					<option value="09" <?php if ($mes=='09') echo "selected=\"selected\"";?>>Septiembre</option>
					<option value="10" <?php if ($mes=='10') echo "selected=\"selected\"";?>>Octubre</option>
					<option value="11" <?php if ($mes=='11') echo "selected=\"selected\"";?>>Noviembre</option>
					<option value="12" <?php if ($mes=='12') echo "selected=\"selected\"";?>>Diciembre</option>
				</select>
			</span>
			<span>&nbsp;&nbsp;&nbsp;TRIMESTRE:&nbsp;
				<select name="trimestre" id="trimestre" onchange="document.location='facturas.php?trimestre='+this.value;">
					<option value="">ANUAL</option>
					<option value="1" <?php if ($trimestre=='1') echo "selected=\"selected\"";?>>1T</option>
					<option value="2" <?php if ($trimestre=='2') echo "selected=\"selected\"";?>>2T</option>
					<option value="3" <?php if ($trimestre=='3') echo "selected=\"selected\"";?>>3T</option>
					<option value="4" <?php if ($trimestre=='4') echo "selected=\"selected\"";?>>4T</option>
				</select>
			</span>
			<span>&nbsp;&nbsp;&nbsp;LOTE:&nbsp;
				<select id="lote" name="lote" onchange="document.location='facturas.php?lote='+this.value">
					<option value="">Seleccione un lote...</option>
					<?=optionsLotes($lote)?>
				</select>
			</span><br>
			<span>&nbsp;USUARIO:&nbsp;
				<select id="idUsuario" name="idUsuario" onchange="document.location='facturas.php?idUsuario='+this.value">
					<option value="">Seleccione un usuario...</option>
					<?=optionsUsuariosPorTipo('USUARIO', $idUsuario)?>
				</select>
			</span>
			</div>
			
			<div id="listadoProductos">
			
				<?php if (isset($anyo) && $anyo!=NULL) {
						
						$consulta = "select p.* FROM PEDIDOS p
									where p.COBRADO=1 AND p.FECHA_FACTURA like '$anyo-$mes%'";
						if (isset($lote) && $lote!=NULL) {
							$consulta.=" AND p.LOTE='$lote'";
						}
						if (isset($trimestre) && $trimestre!=NULL) {
							if ($trimestre=='1')
								$consulta.=" AND (p.FECHA_FACTURA like '$anyo-01%' OR p.FECHA_FACTURA like '$anyo-02%' OR p.FECHA_FACTURA like '$anyo-03%')";
							else if ($trimestre=='2')
								$consulta.=" AND (p.FECHA_FACTURA like '$anyo-04%' OR p.FECHA_FACTURA like '$anyo-05%' OR p.FECHA_FACTURA like '$anyo-06%')";
							else if ($trimestre=='3')
								$consulta.=" AND (p.FECHA_FACTURA like '$anyo-07%' OR p.FECHA_FACTURA like '$anyo-08%' OR p.FECHA_FACTURA like '$anyo-09%')";
							else if ($trimestre=='4')
								$consulta.=" AND (p.FECHA_FACTURA like '$anyo-10%' OR p.FECHA_FACTURA like '$anyo-11%' OR p.FECHA_FACTURA like '$anyo-12%')";
						}
						if (isset($idUsuario) && $idUsuario!=NULL) {
							$consulta.=" AND p.ID_USUARIO='$idUsuario'";
						}
						$consulta.=" ORDER BY p.NUM_FACTURA_ANUAL";
					$resContabilidad = consulta($consulta);
				?>
				<table class="tablaResultados">
					<thead>
						<tr>
							<th align="center">Nº FACTURA</th>
							<th align="center">FECHA FACTURA</th>
							<th align="center">LOTE</th>
							<th align="center">USUARIO</th>
							<th align="center">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($resContabilidad)==0) {
	?>
						<tr>
							<td colspan="5">No hay datos de facturas para este mes.</td>
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
							$fecha = $filaCon['FECHA_FACTURA'];
							$fecha = DateTime::createFromFormat("Y-m-d H:i:s", $fecha);
	?>
						<tr>
							<td  align="center"><?=str_pad($filaCon['NUM_FACTURA_ANUAL'], 4, "0", STR_PAD_LEFT)?>/<?=$fecha->format("Y")?></td>
							<td  align="center"><?=$fecha->format("d/m/Y")?></td>
							<td  align="center"><?=$filaCon['LOTE']?></td>
							<td  align="center"><?=$filaCon['ID_USUARIO']?></td>
							<td  align="center">
							<?php if ($filaCon['ID_USUARIO']!='ADMIN') { ?>
								<a id="factura<?=$filaCon['ID_PEDIDO']?>" title="Factura" style="display:block" 
						 href="#" onclick="generarAlbaranFactura('<?=$filaCon['ID_PEDIDO']?>', 3);"><img src="../img/FACTURA.png" alt="factura" width="32"/></a>
						 	<?php } ?>
							</td>
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
			                <li><a href="facturas.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
			        <?php }
			           
			           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
			            if($lastpage >$page )
			            {?>      
			                <li class="next"><a href="facturas.php?page=<?php echo $nextpage;?>" >&rarr;</a></li><?php
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
			            <li class="previous"><a href="facturas.php?page=<?php echo $prevpage;?>">&larr;</a></li><?php
			             for($i= 1; $i<= $lastpage ; $i++)
			             {
			                           //COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
			                if($page == $i)
			                {
			            ?>       <li class="active"><?php echo $i;?></li><?php
			                }
			                else
			                {
			            ?>       <li><a href="facturas.php?page=<?php echo $i;?>" ><?php echo $i;?></a></li><?php
			                }
			            }
			             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
			            if($lastpage >$page )
			            {   ?>   
			                <li class="next"><a href="facturas.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
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
		<div style="clear:both;"></div><br/>
		<div id="botonera">
			<input type="button" id="facturaManual" value="Factura Manual" onclick="openConfirmacionFacturar()"/>
			<input id="imprimirTodas" name="imprimirTodas"  type="button" value="Imprimir Todas" onclick="imprimirTodas();" />
		</div>
		<div id="dialogConfirmFacturar" title="Facturación" style="display: none;">
		<?php 
		$fechaActual = new DateTime();
		$res = consulta("select MAX(NUM_FACTURA_ANUAL) as NUM_FACTURA from PEDIDOS where FECHA_FACTURA like '".$fechaActual->format("Y")."%' and COBRADO='1' and NUM_FACTURA_ANUAL is not null");
		$fila = extraer_registro($res);
		$ultimaFactura = $fila['NUM_FACTURA'];
		?>
			¿Desea generar un número de factura manual? La última factura es la <?=str_pad($ultimaFactura, 4, "0", STR_PAD_LEFT)."/".$fechaActual->format("Y")?>
		</div>
		<script>		
		var idPedidoSel;  
		function openConfirmacionFacturar() {
			$("#dialogConfirmFacturar").dialog("open");
		}
		
		$("#dialogConfirmFacturar").dialog({
	      autoOpen: false,
		  height: 300,
		  width: 400,
		  modal: true,
	      buttons : {
	          "Sí" : function() {
	        	  $(this).dialog("close")
	        	  document.location = "facturaManual.php";
	          },
	          "Cancelar" : function() {
		          $(this).dialog("close");
	          }
	        }
	    });
		function imprimirTodas () {
			window.open('imprimirFacturas.php?trimestre=<?=$trimestre?>&usuario=<?=$idUsuario?>&lote=<?=$lote?>&anyo=<?=$anyo?>&mes=<?=$mes?>', 'impresionFacturas', '');
		}
		</script>
	</section>
</body>
</html>

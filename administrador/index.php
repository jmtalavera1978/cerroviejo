<?php 
	require_once "../includes/funciones.inc.php";
    compruebaSesionAdministracion();
    
    //Comprobar si se guarda
    if (@$_POST['submit']) {
    	$fechaApertura = @$_POST['fechaApertura'];
    	$fechaCierre = @$_POST['fechaCierre'];
    
    	try {
    		$res = consulta ("update CONFIGURACION set VALOR='".$fechaApertura."' WHERE PARAMETRO='FECHA_APERTURA'");
    		$res = $res + consulta ("update CONFIGURACION set VALOR='".$fechaCierre."' WHERE PARAMETRO='FECHA_CIERRE'");
    
    		if ($res == 2) {
    			if (@$_POST['generarLote']=='true') {
    				$_SESSION['S_LOTE_ACTUAL'] = NULL;
    				$_SESSION['mensaje_generico'] = generarNuevoLote();
    			} else {
    				$_SESSION['mensaje_generico'] = 'Fechas modificadas correctamente';
    			}
    		} else {
    			$_SESSION['mensaje_generico'] = 'No se han podido modificar las fechas';
    		}
    	} catch (Exception $ex) {
    		//Devuelve el mensaje de error
    		$_SESSION['mensaje_generico'] = $ex->qetMessage();
    	}
    	
    	Header ("Location: index.php");
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php";
    compruebaSesionAdministracion();
    compruebaLogSaldos();
    
    $fechaApertura = consultarFechaAperturaNoCache();
    $fechaCierre = consultarFechaCierreNoCache();
    
    if (isset($fechaApertura) && isset($fechaCierre)) {
    	$fechaApertura = date_create_from_format("d/m/Y H:i", $fechaApertura);
    	$fechaCierre = date_create_from_format("d/m/Y H:i", $fechaCierre);
    	$fechaActual = new DateTime();
    
    	if ($fechaActual>=$fechaApertura && $fechaActual<=$fechaCierre) {
    		$interval = date_diff($fechaCierre, $fechaActual);
    		$numDias = $interval->format('%d días %h horas %i minutos');
    	}
    }
     ?>
     <div class="grids" style="text-align: center;">
     <?php
		if (isset($interval)) {
			if ($interval->format('%d')>0) {
				echo "<p class=\"message tiempo\">Queda ".$interval->format('%d día/s, %h hora/s y %i minuto/s')." para tramitar la compra.</p>";
			} else {
				echo "<p class=\"message tiempo\">Queda ".$interval->format('%h hora/s y %i minuto/s')." para tramitar la compra.</p>";
			}
		} else {
			echo "<p class=\"message tiempo\">Periodo de compra cerrado.</p>";
		} 
	 ?>
	</div>
	<section>
		<form id="formularioFechas" method="post" action="index.php">
		<input type="hidden" id="generarLote" name="generarLote" value="false"/>
		<div id="contenidoAdmin">
			<?php
				if (isset($_SESSION['mensaje_generico'])) {
					echo "<h5>".$_SESSION['mensaje_generico']."</h5>";
					if (!@$_POST['submit']) {
						$_SESSION['mensaje_generico'] = NULL;
					}
				}
			?>
			<img alt="Calendario" src="../img/calendario.png" align="left" />
			<h1 class="cal" style="top:-10px">Apertura y cierre</h1>
			<br/><br/>
			<?php
			$ultimoLote = consultarLoteActualNoCache ();
			$subgruposList = '-1000';
			$totalesPorProveedor = @clasificarPedidosPorProveedorYLote ($ultimoLote, $subgruposList);
			$totalesPorProveedor = @clasificarPedidosPorProveedorYLoteResto ($totalesPorProveedor, $ultimoLote, $subgruposList);
			
			$totalCompradoPorUsuarios = @calcularTotalRevisadoProveedorCompradoPorUsuarios('-1', $totalesPorProveedor);
			?>
			<table class="infoGeneral">
				<tr>
					<td><span>Lote <?=$ultimoLote?></span></td>
					<td><span><?=consultarNumVentasLote()?> pedidos</span></td>
					<td><span><?=$totalCompradoPorUsuarios?> &euro;</span></td>
				</tr>
			</table>
			<br/><br/>
			<center>
				<table border=0>
					<tbody>
						<tr>
							<td><label>Fecha de Apertura</label></td>
							<td><input type="text" id="fechaApertura" name="fechaApertura" size="18" required="required" value="<?=consultarFechaAperturaNoCache ()?>" /></td>
						</tr>
						<tr>
							<td><label>Fecha de Cierre</label></td>
							<td><input type="text" id="fechaCierre" name="fechaCierre" size="18" required="required" value="<?=consultarFechaCierreNoCache ()?>" /></td>
						</tr>
					</tbody>
				</table>
				 <script>
				 $(function($){
					    $.datepicker.regional['es'] = {
					        closeText: 'Cerrar',
					        prevText: '<Ant',
					        nextText: 'Sig>',
					        currentText: 'Hoy',
					        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
					        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
					        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
					        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
					        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
					        weekHeader: 'Sm',
					        dateFormat: 'dd/mm/yy',
					        firstDay: 1,
					        isRTL: false,
					        showMonthAfterYear: false,
					        yearSuffix: ''
					    };
					    $.datepicker.setDefaults($.datepicker.regional['es']);
					});
				  $(function() {
				    $( "#fechaApertura" ).datetimepicker({
				    	  dateFormat:'dd/mm/yy'
				    	});
				  });
				  $(function() {
				    $( "#fechaCierre" ).datetimepicker({
				    	  dateFormat:'dd/mm/yy'
				    	});
				  });
				  </script>
			</center>
		</div>
		<br/>
			<div id="botonera" style="margin-right: 1%">
				<input id="submit2" name="submit2"  type="button" value="Grabar" onclick="openConfirmacion()" />
				<input id="submit" name="submit"  type="submit" value="submit" style="display: none"/>
			</div>
			<br/>
			
			<div id="dialogConfirmGenerarLote" title="">
				¿Desea generar un nuevo lote?</div>
				
			<script>
				$(function() {
				    $( "input[type=button]" )
				      .button()
				      .click(function( event ) {
				        event.preventDefault();
				      });
				  });
				  
				function openConfirmacion() {
					$("#dialogConfirmGenerarLote").dialog("open");
				}
				
				$("#dialogConfirmGenerarLote").dialog({
			      autoOpen: false,
				  height: 250,
				  width: 400,
				  modal: true,
			      buttons : {
			          "Sí" : function() {
				          $("#generarLote").val('true');
			        	  $("#submit").click();
				          $(this).dialog("close");
			          },
			          "No" : function() {
				          $("#generarLote").val('false');
			        	  $("#submit").click();
				          $(this).dialog("close");
			          }
			        }
			    });
			</script>
		</form>
		
	</section>
</body>
</html>

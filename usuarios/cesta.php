<?php require_once "../includes/lib_carrito.php"; 
if (isset($_GET["pestanya"]) && $_GET["pestanya"]!=NULL) {
	$_SESSION["pestanya"] = $_GET["pestanya"];
}
if (!isset($_SESSION["pestanya"])) {
	$_SESSION["pestanya"] = 0;
} 

?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head_interno.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
	<link href="../css/jquery-te-1.4.0.css" rel="stylesheet" media="all" />
	<script type="text/javascript" src="../js/jquery-te-1.4.0.min.js"></script>
	<script>
		$(function() {
			$( "#tabs" ).tabs({ disabled: [0, 1, 2, 3] });
			$( "#tabs" ).tabs('enable', <?=@$_SESSION["pestanya"]?>);
			var index = $('#tabs a[href="#tabs-<?=@$_SESSION["pestanya"]?>"]').parent().index();
			$( "#tabs" ).tabs("option", "active", index);

			var pestanya = <?=@$_SESSION["pestanya"]?>;

			if (pestanya==0) {
				$( "#imageTab" ).attr("src", "../img/01_Cesta.gif");
			} else if (pestanya==1) {
				$( "#imageTab" ).attr("src", "../img/02_Entrega.gif");
			} else if (pestanya==2) {
				$( "#imageTab" ).attr("src", "../img/03_Pago.gif");
			} else if (pestanya==3) {
				$( "#imageTab" ).attr("src", "../img/04_Confirmar.gif");
			}
		});

	    function soloLetras(e){
	       key = e.keyCode || e.which;
	       tecla = String.fromCharCode(key).toLowerCase();
	       letras = " áéíóúabcdefghijklmnñopqrstuvwxyzQWERTYUIOPÑLKJHGFDSAZXCVBNMÁÉÍÓÚ0123456789:,. ";
	       especiales = "8-37-39-46";

	       tecla_especial = false
	       for(var i in especiales){
	            if(key == especiales[i]){
	                tecla_especial = true;
	                break;
	            }
	        }

	        if(letras.indexOf(tecla)==-1 && !tecla_especial){
	            return false;
	        }
	    }

		$('textarea[maxlength]').keyup(function(){  
	        //get the limit from maxlength attribute  
	        var limit = parseInt($(this).attr('maxlength'));  
	        //get the current text inside the textarea  
	        var text = $(this).val();  
	        //count the number of characters in the text  
	        var chars = text.length;  
	  
	        //check if there are more characters then allowed  
	        if(chars > limit){  
	            //and if there are use substr to get the text before the limit  
	            var new_text = text.substr(0, limit);  
	  
	            //and change the current text with the new text  
	            $(this).val(new_text);  
	        }  
	    });  

	    function irAPago () {
	    	var url = 'cesta.php?pestanya=2&horaIni=' + $("#horaIni").val() + '&horaFin=' + $("#horaFin").val() + '#tabs';
	    	document.location=url;
	    }

	    function irAConfirmacion () {
	    	var url = 'cesta.php?pestanya=3&horaIni=' + $("#hhoraIni").val() + '&horaFin=' + $("#hhoraFin").val() + '#tabs';
	    	document.location=url;
	    }

	    function actualizarHorario (horario) {
		    if (horario!='') {
		    	var horarioS =  horario.split("-");
		    	horaIni = horarioS[0];
		    	horaFin = horarioS[1];
		    	$('#horaIni').val(horaIni);
		    	$('#horaFin').val(horaFin);
		    } else {
		    	$('#horaIni').val();
		    	$('#horaFin').val();
		    }
	    }
	</script>
</head>
<body>
	 <?php require_once "../template/cabeceraUsuarios.inc.php"; ?>
	 <div class="wrapper">
		<div class="grids top">
				<?php require_once "../template/menuLateralUsuarios.inc.php"; 
				
				/* AÑADIR TRANSPORTE */
				if (isset($_GET['envio']) && isset($_GET['total'])) {
					$_SESSION["ocarrito"]->elimina_producto_transporte ();
					$costeTransporte = $_SESSION["ocarrito"]->introduce_producto_transporte ($_GET['envio'], $_GET['total']);
				}
				
				if (isset($_SESSION['mensaje_generico'])) {
					echo "<h5";
					if (!strpos($_SESSION['mensaje_generico'], 'correctamente')) {
						echo " style=\"color:red\"";
					}
					echo ">".$_SESSION['mensaje_generico']."</h5>";
					$_SESSION['mensaje_generico'] = NULL;
				}
					
				$usuario = $_SESSION['ID_USUARIO'];
				$saldo = consulta("select SALDO from USUARIOS where ID_USUARIO='$usuario'");
				$saldo = extraer_registro($saldo);
				$saldo = $saldo['SALDO'];
				
				//Comprobación RE
				$tieneRE = FALSE;
				$resRE = consulta("select RECARGO_EQ from USUARIOS where ID_USUARIO='$usuario'");
				$resRE = extraer_registro($resRE);
				$tieneRE = ($resRE['RECARGO_EQ'] == '1');
				
				$saldoPendiente = calculaSaldoPendienteUsuario ($usuario);
				$saldo = round(($saldo - $saldoPendiente), 2);
				
				$debe = number_format(0-$saldo, 2);
				if ($_SESSION["ocarrito"]->num_productos()==0 && isset($debe) && $debe>0) {
				 ?>
				<p class="message saldo"><b>TRASFERENCIA PENDIENTE DE <?=$debe?>&euro;.</b><br>
					Recuerde que debe realizar su transferencia a la cuenta ES61 3187 0604 2928 4002 3119 de la entidad Caja Rural del Sur. 
					Indicar el código de usuario y el lote para su identificación.</p>
				<?php 
				}
				
				$lotet = consultarLoteActual();
				$resComprados = consulta("select PP.*, PR.DESCRIPCION_MEDIDA as DESCRIPCION_MEDIDA, U.DESCRIPCION as MEDIDA, PR.PEDIDO_MINIMO, PR.INC_CUARTOS, PR.DESCRIPCION
				from PEDIDOS_PRODUCTOS PP, PEDIDOS P, PRODUCTOS PR, UNIDADES U
				where PP.ID_PEDIDO=P.ID_PEDIDO AND PP.ID_PRODUCTO=PR.ID_PRODUCTO AND PR.UNIDAD_MEDIDA=U.ID_UNIDAD
				AND P.ID_USUARIO='".$_SESSION['ID_USUARIO']."' and P.LOTE='$lotet'");
						if (numero_filas($resComprados)>0) { ?>
						<p class="message success">A continuación se visualiza el pedido que ya ha realizado para la semana actual. Si necesita ampliarlo con nuevos alimentos o productos, puede añadirlos desde el catálogo como siempre. Si necesita variar una cantidad, utilice el icono de editar e indique la nueva. Si no desea un alimento, utilice el botón eliminar. Recuerde que debe volver a confirmar el pedido para que quede actualizado.</p>
						<?php 
				} 
				
if (estaAbiertoPeriodoCompra ()) { ?>
			<div id="tabs" style="background: none">
			<img id="imageTab" alt="tab" src="../img/01_Cesta.gif"/>
			<ul style="display: none; visibility: hidden;">
				<li><a href="#tabs-0">1. MI CESTA</a></li>
				<li><a href="#tabs-1">2. ENTREGA</a></li>
				<li><a href="#tabs-2">3. PAGO</a></li>
				<li><a href="#tabs-3">4. CONFIRMACI&Oacute;N</a></li>
			</ul>
			<div id="tabs-0">
				<br/>
				<table class="tablaResultados">
					<thead>
						<tr class="cabeceratabla">
							<th>PRODUCTO</th>
							<th align="center"><?=($tieneRE ? 'P.V.C.' : 'P.V.S')?></th>
							<th align="center">CANTIDAD</th>
							<th align="center">SUBTOTAL</th>
							<th align="center">&nbsp;</th>
							<th align="center">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$totalCesta = $_SESSION["ocarrito"]->imprime_carrito();
						?>
					</tbody>
				</table>
				<br/>
			</div>
			<div id="tabs-1">
				<br/><br/>
				<label for="transporte"><b>Tipo de env&iacute;o:</b></label>&nbsp;
				<select id="transporte" name="transporte" required="required" onchange="document.location='cesta.php?envio='+this.value+'&horaIni=' + $('#horaIni').val() + '&horaFin=' + $('#horaFin').val() + '&total=<?=@$totalCesta?>'">
					<option value="-1">Seleccione un tipo de env&iacute;o...</option>
					<?php 
						$envio = NULL;
						$horaIni = NULL;
						$horaFin = NULL;
						if (isset($_GET['envio'])) {
							$envio = @$_GET['envio'];
							$horaIni = @$_GET['horaIni'];
							$horaFin = @$_GET['horaFin'];
						} else {
							$envio = $_SESSION["ocarrito"]->envio_pedido_realizado(@$totalCesta);
							if (isset($envio) && $envio!=NULL) {
								$horaIni = $envio[1];
								$horaFin = $envio[2];
								$envio = $envio[0];
							}
						}
						
						optionsTransporte ($envio);
						
					?>
				</select>
				
				<?php 
					if (isset($costeTransporte) && $costeTransporte>0.0)  {
						echo "<span style=\"color:red\">Este tipo de envío tiene un coste de $costeTransporte &euro;</span>";
					}
				?><br/><br/>
				
				<label for="horaIni"><b>Horario disponible de entrega, no preferente:</b></label>&nbsp;<br/>
					<select id="horario" name="horario" onchange="actualizarHorario(this.value);">
						<option value="">Seleccione horario de entrega...</option>
						<?php optionsHorarios ($horaIni, $horaFin); ?>
					</select>
				   <input type="hidden" id="horaIni" name="horaIni" value="<?=$horaIni?>" size="8" maxlength="5" />
				   <input type="hidden" id="horaFin" name="horaFin" value="<?=$horaFin?>" size="8" maxlength="5" />
				<br/><br/><br/><br/>
			</div>
			<div id="tabs-2">
				<br/>
				<input name="tipoPago" type="checkbox" checked="checked" required="required" disabled="disabled" readonly="readonly" value="cuenta"/> <label for="tipoPago"><b>Ingreso en la Cuenta Bancaria</b></label><br/>
				<input name="tipoPago" type="checkbox" disabled="disabled" value="cuenta"/> <label for="tipoPago"><b>PayPal (Próximamente)</b></label><br/><br/>
				<input type="hidden" id="hhoraIni" name="horaIni" value="<?=@$_GET['horaIni']?>" />
				<input type="hidden" id="hhoraFin" name="horaFin" value="<?=@$_GET['horaFin']?>" />
			</div>
			<div id="tabs-3">
				<br/>
				<label for="comentarios">Si necesitas indicarnos algo sobre el pedido actual aprovecha y escribelo a continuaci&oacute;n...<br/>
					Si, adem&aacute;s, solicitas <b>env&iacute;o a domicilio</b>, que no se te olvide indicar el horario preferente de entrega en el <b>formulario anterior</b>.
					</label>
					<textarea id="comentarios" name="comentarios" rows="2" cols="60" maxlength="300" spellcheck="true" onkeypress="return soloLetras(event)"></textarea>
					<input type="hidden" id="hhhoraIni" name="horaIni" value="<?=@$_GET['horaIni']?>" />
				<input type="hidden" id="hhhoraFin" name="horaFin" value="<?=@$_GET['horaFin']?>" />
				<br/><br/>
			</div>
			</div>
			<br/>
				<?php require_once "modal_compra.inc.php" ?>

				<div id="botonera">
					<?php if ($_SESSION["pestanya"] == 0) { ?>
					<input type="button" value="Repetir Última Compra"
						onclick="document.location='repetirUltCompra.php'" />
					<input type="button" value="Repetir No Servidos"
						onclick="document.location='productosNoServidos.php'" />
					<input type="button" value="Seguir Comprando"
						onclick="document.location='compraPorCategoria.php?idCategoria=<?=@$_SESSION['idCategoria']?>'" />
						<?php if ($_SESSION["ocarrito"]->num_productos()>0) { ?>
					<input type="button" value="HE TERMINADO. IR A ENTREGA >>"
						onclick="document.location='cesta.php?pestanya=1#tabs'" />
						<?php } ?>
					<?php } ?>
					<?php if ($_SESSION["pestanya"] == 1) { ?>
					<input type="button" value="<< REVISAR CESTA"
						onclick="document.location='cesta.php?pestanya=0#tabs'" />
					<input type="button" value="HE TERMINADO. IR A PAGO >>"
						onclick="if (''==$('#transporte').val() || '-1'==$('#transporte').val()) { alert ('Seleccione un Tipo de Envío'); } else { irAPago (); }" />
					<?php } ?>
					<?php if ($_SESSION["pestanya"] == 2) { ?>
					<input type="button" value="<< REVISAR ENTREGA"
						onclick="document.location='cesta.php?pestanya=1#tabs'" />
					<input type="button" value="HE TERMINADO. CONFIRMAR COMPRA >>"
						onclick="irAConfirmacion ();" />
					<?php } ?>
					<?php if ($_SESSION["pestanya"] == 3 && $_SESSION["ocarrito"]->num_productos() && @$_SESSION['ID_USUARIO']!='DEMO')  {?>
					<input type="button" value="<< REVISAR PAGO"
						onclick="document.location='cesta.php?pestanya=2#tabs'" />
					<input type="button" value="Confirmar Pedido"
						onclick="openConfirmacion()" />
					<?php } else if ($_SESSION["pestanya"] == 3) { ?>
						<input type="button" value="<<< REINICIAR COMPRA"
						onclick="document.location='cesta.php?pestanya=0#tabs'" />
					<?php } ?>
				</div>
				<br/>
			
				<div id="dialogConfirmPedido" title="">
					<b>¿Desea confirmar el pedido?</b>
				</div>
				<script>
			    
					function openConfirmacion() {
						/*if (''=='<?=@$_GET['envio']?>' || '-1'=='<?=@$_GET['envio']?>') {
					         alert ('Seleccione un Tipo de Envío');
				        } else { */
							$("#dialogConfirmPedido").dialog("open");
				        /*}*/
					}
					$(function() {
					    $( "input[type=button]" )
					      .button()
					      .click(function( event ) {
					        event.preventDefault();
					      });
					  });
					$("#dialogConfirmPedido").dialog({
				      autoOpen: false,
					  height: 160,
					  width: 280,
					  modal: true,
				      buttons : {
				          "Confirmar" : function() {
				        	 confirmarPedido($("#comentarios").val(), $("#hhhoraIni").val(), $("#hhhoraFin").val());
				          },
				          "Cancelar" : function() {
				            $(this).dialog("close");
				          }
				        }
				    });
				</script>
				<?php } ?>
		
		
        	</div>
		</div>
	</div>
</div></div>

<?php require_once "../template/pie.inc.php";  ?>
</body>
</html>
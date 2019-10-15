<div id="dialog-form">
	<form>
		<input id="productoId" type="hidden" />
		<input id="minimo" type="hidden" />
		<fieldset>
			<table border="0">
				<tbody>
					<tr>
						<td align="center" width="30%" valign="middle" style="width: 30%">
							<img id="imagenProductoId" alt="Imagen" style="cursor: pointer; cursor: hand;"
							src="../template/imagenes.php?id=" height="383" width="231" />
						</td>
						<td>
							<table border="0">
								<tbody>
									<tr>
										<td nowrap="nowrap"><label for="precioProd" id="unidades1"></label></td>
										<td><input id="precioProd" type="text" readonly="readonly"
											contenteditable="false" value=""
											style="width: 80%; background-color: silver;" />&euro;</td>
									</tr>
									<tr>
										<td><label for="cantidadProd">Meter en la cesta</label></td>
										<td>
											<input id="cantidadProd" readonly="readonly"
												contenteditable="false" type="text" value=""
												style="width: 40%;" />
											<span id="unidades2"></span> 
											<br/>
											<input type="button" onclick="menos()" class="miniboton" value="-" />
											<input id="minibotonmas" type="button" class="miniboton" value="+" />
											<input id="checkCuartos" class="ui-content"
												type="checkbox"
												style="vertical-align: middle; display: none"
												title="Diferencia de Kg en cuartos" />
										</td>
									</tr>
									<tr>
										<td><label for="subtotalProd">Subtotal</label></td>
										<td><input id="subtotalProd" type="text" readonly="readonly"
											contenteditable="false" value=""
											style="width: 80%; background-color: #52a411; color: white" />&euro;</td>
									</tr>
									<tr>
										<td colspan="2" id="maxUnidadesDisp"></td>
									</tr>
								</tbody>
							</table>
							
							<div id="botonera" style="text-align: right;">
								<input type="button" value="Añadir"
									onclick="addCarrito(); " />
								<input type="button" value="Anular"
									onclick="$('#dialog-form').dialog('close');" />
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</form>
</div>
<script>
	$( "#dialog-form" ).dialog({
	  autoOpen: false,
	  hide: 'fold',
      show: 'blind',
	  height: 450,
	  width: 600,
	  modal: true,
	  close: function() {
	    //allFields.val( "" ).removeClass( "ui-state-error" );
	  }
	});
</script>

<div id="dialog-alert">
</div>
<script>
	$( "#dialog-alert" ).dialog({
	  autoOpen: false,
	  hide: 'fold',
      show: 'blind',
	  height: 180,
	  width: 300,
	  modal: true,
	  buttons: {
		  Cerrar: function() {
		  $( this ).dialog( "close" );
		  }
	  }
	});
</script>
	
<div id="dialog-form2">
		<form>
			<input id="productoId2" type="hidden" />
			<input id="minimo2" type="hidden" />
			<fieldset>
				<table border="0">
					<tbody>
						<tr>
							<td>
								<table border="0">
									<tbody>
										<tr>
											<td><label for="cantidadProd2">A&ntilde;adir a la lista</label></td>
											<td>
												<input id="cantidadProd2" readonly="readonly"
													contenteditable="false" type="text" value=""
													style="width: 40%;" />
												<span id="unidades22"></span>
												<input type="button" onclick="menos2()" class="miniboton" value="-" />
												<input id="minibotonmas2" type="button" class="miniboton" value="+" />
											</td>
										</tr>
										<tr>
											<td><label for="listaId">Lista</label></td>
											<td>
												<?php 
												$usuario = @$_SESSION['ID_USUARIO'];
												$listas = consulta("select * from LISTAS where ID_USUARIO='$usuario'");
												
												if (numero_filas($listas)>0) {
												?>
													<select id="listaId">
													<?php while ($filaList = extraer_registro($listas)) { ?>
														<option value="<?=$filaList['ID_LISTA']?>"><?=$filaList['NOMBRE']?></option>
													<?php } ?>
													</select>
												<?php } else { ?>
													No tiene listas de compra, para crearlas vaya a la secci&oacute;n <a href="mislistas.php">MIS LISTAS</a>
												<?php } ?>
											</td>
										</tr>
									</tbody>
								</table>
								
								<div id="botonera" style="text-align: right;">
									<?php if (numero_filas($listas)>0) { ?>
									<input type="button" value="A&ntilde;adir a Lista" onclick="addToList(); " />
									<?php } ?>
									<input type="button" value="Anular" onclick="$('#dialog-form2').dialog('close');" />
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</form>
	</div>
	<script>
		$( "#dialog-form2" ).dialog({
		  autoOpen: false,
		  hide: 'fold',
	      show: 'blind',
		  height: 350,
		  width: 500,
		  modal: true,
		  close: function() {
		    //allFields.val( "" ).removeClass( "ui-state-error" );
		  }
		});
	</script>
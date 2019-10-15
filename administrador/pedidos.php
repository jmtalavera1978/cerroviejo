<?php 
require_once "../includes/funciones.inc.php";
compruebaSesionRepartidorOAdministrador();
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
	<script type="text/javascript" src="../js/compra.js"></script>
	<script type="text/javascript" src="../js/print.js"></script>
	<script type="text/javascript">
		function clickarRevisado(id, idPedidoActual, idProducto, cantidad, check) {
			$( "#"+id ).html('...');
			try {
				$( "#"+id ).load( "clickarRevisado.php?idPedidoActual="+idPedidoActual+"&idProducto="+idProducto+"&cantidad="+cantidad+"&check="+check, function() {
					if (check) {
						if (eval(cantidad)==0) {
							$( "#fila"+idProducto ).css( "background-color", "#FFAAAA" );
						} else {
							$( "#fila"+idProducto ).css( "background-color", "#FFFF66" );
						}
					} else {
						if (eval(cantidad)==0) {
							$( "#fila"+idProducto ).css( "background-color", "#FFAAAA" );
						} else {
							$( "#fila"+idProducto+":nth-of-type(odd)" ).css( "background", "rgba(0,0,0,0.05)" );
							$( "#fila"+idProducto+":nth-of-type(even)" ).css( "background", "rgba(0,0,0,0)" );
						}
						$( "#"+id.replace('check', 'check2') ).html('...');
						$( "#"+id.replace('check', 'check2') ).load( "clickarFinalizado.php?idPedidoActual="+idPedidoActual+"&idProducto="+idProducto+"&cantidad="+cantidad+"&check="+check, function() {});
					}
				});
			} catch (e) {
			}
		}

		function clickarFinalizado(id, idPedidoActual, idProducto, cantidad, check) {
			$( "#check2"+id ).html('...');
			try {
				$( "#check2"+id ).load( "clickarFinalizado.php?idPedidoActual="+idPedidoActual+"&idProducto="+idProducto+"&cantidad="+cantidad+"&check="+check, function() {
					if (check) {
						if (eval(cantidad)==0) {
							$( "#fila"+idProducto ).css( "background-color", "#FFAAAA" );
						} else {
							$( "#fila"+idProducto ).css( "background-color", "#A2F461" );
						}
						$( "#check"+id ).html('...');
						$( "#check"+id ).load( "clickarRevisado.php?idPedidoActual="+idPedidoActual+"&idProducto="+idProducto+"&cantidad="+cantidad+"&check="+check, function() {});
					} else {
						if (eval(cantidad)==0) {
							$( "#fila"+idProducto ).css( "background-color", "#FFAAAA" );
						} else {
							$( "#fila"+idProducto ).css( "background-color", "#FFFF66" );
						}
					}
				});
			} catch (e) {
			}
		}

		function modificarRevisado(id, idPedidoActual, idProducto, precio, idUsuario, medida, campo) {
			try {
				if (campo.value=='') campo.value='0'; 
				$( "#"+id ).html('Guardando...');
				$( "#"+id ).load( "modificar_cantidad_revisada.php?idUsuario="+idUsuario+"&idCampo="+id+"&idPedido="+idPedidoActual+"&idProducto="+idProducto+"&precio="+precio+"&medida="+medida+"&cantidad="+campo.value, function() {
					var total = parseFloat(eval(precio)*eval(campo.value)).round(2);
					$( "#"+id+"_total" ).html(total + " &euro;");
					$( "#checkA"+idProducto ).prop( "checked", true );
					$( "#checkB"+idProducto ).prop( "checked", true );
					if (campo.value=='0' || campo.value =='0.0') {
						$( "#fila"+idProducto ).css( "background-color", "#FFAAAA" );
					} else {
						$( "#fila"+idProducto ).css( "background-color", "#A2F461" );
					}
					$( "#tablaTotales").html('Actualizando...');
					$( "#tablaTotales" ).load( "calcularTotalesPedidoUsuario.php?idUsuario="+idUsuario+"&idPedido="+idPedidoActual, function() {
					});
				});
				
			} catch (e) {
			}
		}

		function clickarCobrado(idPedidoActual, check) {
			$( "#cobrado"+idPedidoActual ).html('...');
			try {
				$( "#cobrado"+idPedidoActual  ).load( "clickarCobrado.php?idPedidoActual="+idPedidoActual+"&check="+check, function() {
					if (check) {
						$( "#filaCobrada"+idPedidoActual ).css( "background-color", "#ddffff" );
						$( "#factura"+idPedidoActual ).css( "display", "block" );
					} else {
						$( "#filaCobrada"+idPedidoActual+":nth-of-type(odd)" ).css( "background", "rgba(0,0,0,0.05)" );
						$( "#filaCobrada"+idPedidoActual+":nth-of-type(even)" ).css( "background", "rgba(0,0,0,0)" );
						$( "#factura"+idPedidoActual ).css( "display", "none" );
					}
				});
			} catch (e) {
			}
		}

		function clickarVerde (idPedidoActual, check) {
			$( "#verde"+idPedidoActual ).html('...');
			try {
				$( "#verde"+idPedidoActual  ).load( "clickarVerde.php?idPedidoActual="+idPedidoActual+"&check="+check, function() {
					if (check) {
						$( "#filaCobrada"+idPedidoActual ).css( "background-color", "#A2F461" );
					} else {
						$( "#filaCobrada"+idPedidoActual+":nth-of-type(odd)" ).css( "background", "rgba(0,0,0,0.05)" );
						$( "#filaCobrada"+idPedidoActual+":nth-of-type(even)" ).css( "background", "rgba(0,0,0,0)" );
					}
				});
			} catch (e) {
			}
		}

		function generarAlbaranFactura(idPedido, tipo) {
			window.open('imprimirFacturaPedido.php?idPedido='+idPedido+'&tipo='+tipo, 'impresionFactura', '');
		}
	</script>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
	<section>
		<div id="contenidoAdmin">
			<?php
			 if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5>".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			 } 
			?>
			
			<?php
				$submenuPedido = @$_GET['submenuPedido'];
				if (isset($submenuPedido)) {
					$_SESSION['submenuPedido'] = $submenuPedido;
				} else {
					if (isset($_SESSION['submenuPedido'])) {
						$submenuPedido = @$_SESSION['submenuPedido'];
					} else {
						$_SESSION['submenuPedido'] = 2;
						$submenuPedido = 2;
						/*if (@$_SESSION['TIPO_USUARIO']=='ADMINISTRADOR') {
							$_SESSION['submenuPedido'] = 1;
							$submenuPedido = 1;
						} else {
							$_SESSION['submenuPedido'] = 2;
							$submenuPedido = 2;
						}*/
					}
				}
			?>
			<h1 class="cal">Pedidos Semanales por 
				<select name="submenuPedido" id="submenuPedido" onchange="document.location='pedidos.php?submenuPedido='+this.value;"> <!--  disabled="disabled" -->
				<?php if (@$_SESSION['TIPO_USUARIO']=='ADMINISTRADOR') { ?>
					<option value="1" <?php if ($submenuPedido==1) echo "selected=\"selected\"";?>>Proveedor</option>
				<?php } ?>
					<option value="2" <?php if ($submenuPedido==2) echo "selected=\"selected\"";?>>Usuario</option>
					<option value="3" <?php if ($submenuPedido==3) echo "selected=\"selected\"";?>>Buscador</option>
					<option value="4" <?php if ($submenuPedido==4) echo "selected=\"selected\"";?>>Hist&oacute;rico Proveedor</option>
					<option value="5" <?php if ($submenuPedido==5) echo "selected=\"selected\"";?>>Hist&oacute;rico Usuario</option>
					<option value="6" <?php if ($submenuPedido==6) echo "selected=\"selected\"";?>>Cuadre de Pedidos</option>
				</select>
			</h1>
			<?php
				if ($submenuPedido==1) {
					include_once 'pedidos_proveedor.php';
				} else if ($submenuPedido==2) {
					include_once 'pedidos_usuario.php';
				} else if ($submenuPedido==3) {
					include_once 'pedidos_historico.php';
				} else if ($submenuPedido==4) {
					include_once 'historico_pedidos_proveedor.php';
				} else if ($submenuPedido==5) {
					include_once 'historico_pedidos_usuario.php';
				}else if ($submenuPedido==6) {
					include_once 'cuadre_pedidos.php';
				}
			?>
	</section>
	<br/>
	<div id="botonera">
		<?php
			if ($submenuPedido==1 || $submenuPedido==4) {
			?>
				<input id="imprimirAlbaran" name="imprimirAlbaran" type="button" onclick="window.open('imprimirAlbaran.php?lote=<?=$lote?>&idSubgrupo=<?=$idSubgrupo?>&estado=<?=$estadoPed?>&proveedor=<?=$bnombreProv?>', 'impresionAlbaran', '');" value="Imprimir Albaranes" />
				<input id="imprimirPedidos" name="imprimirPedidos" type="button" onclick="window.open('imprimirPedidosProveedoresExcel.php?lote=<?=$lote?>&idSubgrupo=<?=$idSubgrupo?>&estado=<?=$estadoPed?>&proveedor=<?=$bnombreProv?>', 'impresionExcel', '');" value="Exportar Albaranes a Excel" />
				<input id="verGlobal" name="verGlobal" type="button" onclick="document.location='pedidosGlobal.php?lote=<?=$lote?>'" value="Ver Global" />
			<?php 
			} else if (($submenuPedido==2) && isset($idPedidoActual)) {
				?>
				<input id="addProducto" name="addProducto"  type="button" value="A&ntilde;adir Producto al Pedido" onclick="document.location='nuevoProductoPedido.php?idPedido=<?=$idPedidoActual?>&usuario=<?=$idUsuario?>&total=<?=$totalRevisado?>'" />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input id="Volver" name="Volver" type="button" onclick="document.location='pedidos.php?idUsuario='" value="Volver" />
				<input id="elimPedido" name="elimPedido" type="button" onclick="openConfirmacion();" value="Eliminar Pedido Completo" />
				<input id="pedidoPreparado" name="pedidoPreparado" type="button" onclick="openConfirmacionPedidoUsuario()" value="Pedido Preparado" />
				
				<div id="dialogConfirmDelete" title="">
					¿Desea eliminar el pedido seleccionado?</div>
					
				<script>		
					function openConfirmacion() {
						$("#dialogConfirmDelete").dialog("open");
					}
					
					$("#dialogConfirmDelete").dialog({
				      autoOpen: false,
					  height: 250,
					  width: 400,
					  modal: true,
				      buttons : {
				          "Sí" : function() {
				        	  $(this).dialog("close");
				        	  document.location='eliminarPedidoUsuario.php?idPedido=<?=$idPedidoActual?>&url='+document.location;
				          },
				          "Cancelar" : function() {
					          $(this).dialog("close");
				          }
				        }
				    });
				</script>
				
				<div id="dialogConfirmPedidoUsuario" title="">
					¿Desea finalizar el pedido del usuario?</div>
				<script>
					$(function() {
					    $( "input[type=button]" )
					      .button()
					      .click(function( event ) {
					        event.preventDefault();
					      });
					  });
					  
					function openConfirmacionPedidoUsuario() {
						$("#dialogConfirmPedidoUsuario").dialog("open");
					}
					
					$("#dialogConfirmPedidoUsuario").dialog({
				      autoOpen: false,
					  height: 250,
					  width: 400,
					  modal: true,
				      buttons : {
				          "Sí" : function() {
				        	  document.location='pedidoPreparado.php?idPedido=<?=$idPedidoActual?>&url='+document.location;
					          $(this).dialog("close");
				          },
				          "No" : function() {
					          $(this).dialog("close");
				          }
				        }
				    });
				</script>
				<?php
			} else if (($submenuPedido==2 || $submenuPedido==5) && isset($lote) && $lote) {
?>
				<input id="imprimirTodose" name="imprimirTodose" type="button" onclick="document.location='imprimirPedidosTodosUsuariosExcel.php?lote=<?=$lote?>&envio=<?=@$envio?>&url='+document.location;" value="Imprimir Todos Excel" />
				
				<?php if ($submenuPedido==2) { ?>
				<input id="cancelarProductoLote" name="cancelarProductoLote" type="button" onclick="document.location='eliminarProductoLote.php?lote=<?=$lote?>'" value="Eliminar un Producto del LOTE" />
				<!--  <input id="imprimirTodos" name="imprimirTodos" type="button" onclick="document.location='imprimirPedidosTodosUsuarios.php?lote=<?=$lote?>&envio=<?=@$envio?>&url='+document.location;" value="Imprimir Todos" /> -->
				<?php } ?>
<?php 
			} else if ($submenuPedido==6) {
?>
				<!-- ACCIONES -->
<?php 
			}
		?>
	</div>
</body>
</html>

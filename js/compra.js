// <!--
/**
 * Permite añadir a la cesta una cantidad de producto
 */
function addCesta(idProducto, nombreProd, precioProd, unidad, descMedida, max, ilimitada, cuartos, minimo) {
	$("#imagenProductoId").attr("src","../template/imagenes.php?id="+idProducto);
	$('#imagenProductoId').unbind('click');
	$("#imagenProductoId").click(function() {
		document.location='detalleProducto.php?id='+idProducto;
	});
	
	$("#unidades1").html("Precio/"+unidad);
	$("#unidades2").html(unidad);
	$("#unidades1").attr('title', descMedida);
	$("#unidades2").attr('title', descMedida);
	$("#productoId").val(idProducto);
	$("#precioProd").val(precioProd);
	
	if (eval(minimo)>0) {
		$("#cantidadProd").val(eval(minimo));
		$("#minimo").val(minimo);
	} else {
		$("#cantidadProd").val('1');
		$("#minimo").val('0');
	}
	
	if (ilimitada==0) {
		$("#cantidadProd").attr('max', max);
		
		if (eval(max)<eval($("#cantidadProd").val())) {
			$("#cantidadProd").val(eval(max));
		}
	}
	$('#minibotonmas').unbind('click');
	$("#minibotonmas").click(function() {
		mas (max, ilimitada);
	});
	$("#subtotalProd").val(precioProd);
	$( "#ui-id-1" ).html(nombreProd);
	
	if (ilimitada==0) {
		$("#maxUnidadesDisp").html("Quedan " + max + " " + unidad);
	} else {
		$("#maxUnidadesDisp").html("");
	}

	if (cuartos == '1') {
		$("#checkCuartos").attr('checked', 'checked');
	} else { 
		$("#checkCuartos").attr('checked', false);
	}

	/*if (unidad.toUpperCase() != 'KG' && unidad.toUpperCase() != 'KILO' && unidad.toUpperCase() != 'KILOS') {
		$("#checkCuartos").toggle();
	}*/

	recalcular ();
	
	$( "#dialog-form" ).dialog( "open" );
}

/**
 * Añadir a la cesta una cantidad de producto
 */
function addCarrito() {
	idProd = $("#productoId").val();
	precio = $("#precioProd").val();
	nombre = $( "#ui-id-1" ).html();
	cantidad = $("#cantidadProd").val();
	
	//document.location='../includes/meter_producto.php?id='+idProd+'&nombre='+nombre+'&precio='+precio+'&cantidad='+cantidad+'&url='+document.location;
	$('#dialog-form').dialog('close');
	$( "#dialog-alert" ).html( "Guardando en la cesta..." );
	$( "#dialog-alert" ).dialog( "open" );
	$( "#dialog-alert" ).load( '../includes/meter_producto.php?id='+idProd+'&precio='+precio+'&cantidad='+cantidad+'&nombre='+encodeURIComponent(nombre), function(datos) {
		if (datos=='correcto') {
			if (document.location.href.indexOf('cesta.php')>0) {
				document.location = 'cesta.php';
			}
			$( "#dialog-alert" ).html( "Producto añadido correctamente." );
			numProductos = $( "#numProductosCestaId" ).html();
			if (numProductos == '') {
				$( "#numProductosCestaId" ).html('(1)');
			} else {
				numProductos = numProductos.substr(1, numProductos.length-2);
				numProductos = eval(numProductos) + 1;
				$( "#numProductosCestaId" ).html('('+numProductos+')');
			}
			num_productos_cesta = eval(num_productos_cesta) + 1;
		} else {
			$( "#dialog-alert" ).html( "<font color=\"red\">No se ha podido añadir el producto.</font>" );
		}
	}); 
}

function menos () {
	unidades = $("#unidades2").html();
	nombre = $( "#ui-id-1" ).html();
	minimo = $("#minimo").val();
	
	diferencia = 1;
	if (unidades.toUpperCase() == 'KG' || unidades.toUpperCase() == 'KILO' 
			|| unidades.toUpperCase() == 'KILOS' || unidades.toUpperCase() == 'KG.' 
			|| nombre.toUpperCase().indexOf('HUEVOS') != -1) {
		if($("#checkCuartos").is(':checked') && !(nombre.toUpperCase().indexOf('HUEVOS') != -1)) { 
			diferencia = 0.25;
		} else {
			diferencia = 0.5;
		}		
	}
	cantidad = $("#cantidadProd").val();
	precio = $("#precioProd").val();
	
	if ((eval(cantidad) - diferencia) <eval(minimo)) {
		cantidad = eval(minimo);
	} else if (cantidad>0) {
		cantidad = eval(cantidad) - diferencia;
	}
	
	if (cantidad<0) {
		cantidad = 0;
	}
	$("#cantidadProd").val(cantidad);
	recalcular ();
}

function mas (max, ilimitada) {
	unidades = $("#unidades2").html();
	nombre = $( "#ui-id-1" ).html();
	diferencia = 1;
	if (unidades.toUpperCase() == 'KG' || unidades.toUpperCase() == 'KILO' 
			|| unidades.toUpperCase() == 'KILOS' || unidades.toUpperCase() == 'KG.' 
			|| nombre.toUpperCase().indexOf('HUEVOS') != -1) {
		if($("#checkCuartos").is(':checked') && !(nombre.toUpperCase().indexOf('HUEVOS') != -1)) { 
			diferencia = 0.25;
		} else {
			diferencia = 0.5;
		}
	}
	cantidad = $("#cantidadProd").val();
	precio = $("#precioProd").val();
	
	cantidad = eval(cantidad) + diferencia;

	if (ilimitada==1 || cantidad<=max) {
		$("#cantidadProd").val(cantidad);
		recalcular ();
	}
}

function recalcular () {
	cantidad = $("#cantidadProd").val();
	precio = $("#precioProd").val();
	$("#subtotalProd").val(Math.round(cantidad*precio * 100) / 100);
}

function recalcularTodo (field, indice) {
	nuevaCantidad = field.value;
	document.location='../includes/actualiza_producto.php?indice='+indice+'&cantidad='+nuevaCantidad+'&url='+document.location;
}


function NumCheck(e, field) {
  key = e.keyCode ? e.keyCode : e.which;

  // backspace
  if (key == 13) {
	  field.blur();
      return true;
  }
  if (key == 8) return true;
  // 0-9
  if (key > 47 && key < 58) {
    if (field.value == "") return true;
    regexp = /.[0-9]{3}$/;
    return !(regexp.test(field.value));
  }
  // .
  if (key == 46) {
    if (field.value == "") return false;
    regexp = /^[0-9]+$/;
    return regexp.test(field.value);
  }
  // other key
  return false;
}

function confirmarPedido(comentarios, horaIni, horaFin) {
	document.location='../includes/confirmar_pedido.php?horaIni='+horaIni+'&horaFin='+horaFin+'&comentarios='+comentarios;
}


// - Admin de productos
function verificaCheckIlimitado() {
	if($("#ilimitado").is(':checked')) { 
		$("#cantidad1").val('0');
		$("#cantidad2").val('0');
	}
}

function cambiaValorCantidad (valor) {
	if (eval(valor)>0) {
		$("#ilimitado").attr('checked', false);
	}
}

function disminuir (id, idPedido, idProducto, decremento, lote) {
	valorNuevo = eval($("#"+id).val())-eval(decremento);
	if (valorNuevo>=0) {
		$("#"+id).val(valorNuevo);
		document.location='modificar_cantidad_revisada_proveedores.php?idPedido='+idPedido+'&idProducto='
			+idProducto+'&lote='+lote+'&cantidad='+valorNuevo+'&url='+document.location;
	}
}

function aumentar (id, idPedido, idProducto, incremento, lote) {
	valorNuevo = eval($("#"+id).val())+eval(incremento);
	$("#"+id).val(valorNuevo);
	document.location='modificar_cantidad_revisada_proveedores.php?idPedido='+idPedido+'&idProducto='
		+idProducto+'&lote='+lote+'&cantidad='+valorNuevo+'&url='+document.location;
}

/**
 * Funcion para filtrar productos por checks
 * @param campo
 * @param check
 */
function filtrarProductos(campo, check) {
	$( "#listadoProductoYPag").html('<br>Un momento por favor...');
	try {
		$( "#listadoProductoYPag").load( "listadoProductos.php?"+campo+"="+check, function() {
		});
	} catch (e) {
	}
}

/**
 * Funcion para calcular el precio con iva de un producto (sin recargo)
 * @param check
 */
function calculaPrecioConIVA() {
	var precio = $("#importeSinIVA").val();
	var iva = $("#iva").val();
	var recargo = $("#recargo").val();
	var recargosubcat = $("#recargosubcat").val();
	var precioConIVA = precio;
	var pvp = precio;
	
	if (iva!="" && eval(iva)>0 && precioConIVA!="" && eval(precioConIVA)>0) {
		//precioConIVA = parseFloat((precio*100)/(eval(iva)+100)).round(2);
		precioConIVA = parseFloat(parseFloat(precio) + (parseFloat(precio) * parseFloat(iva) / 100)).round(2);
	}
	
	$("#precio").val(precioConIVA);
	
	if (precio!="" && eval(precio)>0) {
		if (recargo!="" && eval(recargo)>0) {
			pvp = parseFloat(parseFloat(precio) + (parseFloat(precio) * parseFloat(recargo) / 100)).round(2);
			if (iva!="" && eval(iva)>0) {
				pvp = parseFloat(parseFloat(pvp) + (parseFloat(pvp) * parseFloat(iva) / 100)).round(2);
			}
		} else {
			if (recargosubcat!="" && eval(recargosubcat)>0) {
				pvp = parseFloat(parseFloat(precio) + (parseFloat(precio) * parseFloat(recargosubcat) / 100)).round(2);
			}
			if (iva!="" && eval(iva)>0) {
				pvp = parseFloat(parseFloat(pvp) + (parseFloat(pvp) * parseFloat(iva) / 100)).round(2);
			}
		}
	}
	
	$("#pvp").val(pvp);
}

Number.prototype.round = function(places) {
  return +(Math.round(this + "e+" + places)  + "e-" + places);
}

/**
 * Funciones que permiten añadir a una lista una cantidad de producto
 */
function toList(idProducto, nombreProd, unidad, descMedida, minimo) {
	$("#unidades22").html(unidad);
	$("#unidades22").attr('title', descMedida);
	$("#productoId2").val(idProducto);
	
	if (eval(minimo)>0) {
		$("#cantidadProd2").val(eval(minimo));
		$("#minimo2").val(minimo);
	} else {
		$("#cantidadProd2").val('1');
		$("#minimo2").val('0');
	}

	$('#minibotonmas2').unbind('click');
	$("#minibotonmas2").click(function() {
		mas2 (0, 1);
	});
	$( "#ui-id-3" ).html(nombreProd);


	recalcular ();
	
	$( "#dialog-form2" ).dialog( "open" );
}

function menos2 () {
	unidades = $("#unidades22").html();
	nombre = $( "#ui-id-3" ).html();
	minimo = $("#minimo2").val();
	
	diferencia = 1;
	if (unidades.toUpperCase() == 'KG' || unidades.toUpperCase() == 'KILO' 
			|| unidades.toUpperCase() == 'KILOS' || unidades.toUpperCase() == 'KG.' 
			|| nombre.toUpperCase().indexOf('HUEVOS') != -1) {
		diferencia = 0.5;
	}
	cantidad = $("#cantidadProd2").val();
	
	if ((eval(cantidad) - diferencia) <eval(minimo)) {
		cantidad = eval(minimo);
	} else if (cantidad>0) {
		cantidad = eval(cantidad) - diferencia;
	}
	
	if (cantidad<0) {
		cantidad = 0;
	}
	$("#cantidadProd2").val(cantidad);
}

function mas2 (max, ilimitada) {
	unidades = $("#unidades22").html();
	nombre = $( "#ui-id-3" ).html();
	diferencia = 1;
	if (unidades.toUpperCase() == 'KG' || unidades.toUpperCase() == 'KILO' 
			|| unidades.toUpperCase() == 'KILOS' || unidades.toUpperCase() == 'KG.' 
			|| nombre.toUpperCase().indexOf('HUEVOS') != -1) {
		diferencia = 0.5;
	}
	cantidad = $("#cantidadProd2").val();
	
	cantidad = eval(cantidad) + diferencia;

	if (ilimitada==1 || cantidad<=max) {
		$("#cantidadProd2").val(cantidad);
	}
}

function addToList() {
	idProd = $("#productoId2").val();
	idLista = $("#listaId").val();
	cantidad = $("#cantidadProd2").val();
	
	$('#dialog-form2').dialog('close');
	$( "#dialog-alert" ).html( "Asignando en la lista..." );
	$( "#dialog-alert" ).dialog( "open" );
	$( "#dialog-alert" ).load( '../includes/addToList.php?idLista='+idLista+'&idProd='+idProd+'&cantidad='+cantidad, function(datos) {
		if (datos=='correcto') {
			$( "#dialog-alert" ).html( "Se ha añadido el producto a la lista correctamente." );
		} else if (datos=='existe') {
			$( "#dialog-alert" ).html( "Se ha actualizado el producto en la lista correctamente." );
		} else {
			$( "#dialog-alert" ).html( "<font color=\"red\">No se ha podido añadir el producto a la lista.</font>" );
		}
	}); 
}
// -->

<?php
/**
 * Devuelve la descripción de tipo de transporte
 *
 * @param unknown $tipo
 * @return string
 */
function consultaTipoTransporte ($tipo) {
	$resP = consulta ("select ID_PRODUCTO, DESCRIPCION from PRODUCTOS where ID_SUBCATEGORIA='-10' and ID_PRODUCTO='$tipo'");
	if ($fila = extraer_registro ($resP)) {
		return $fila['DESCRIPCION'];
	} else {
		return 'No encontrado';
	}
}

/**
 * Calcular el precio de transporte
 *
 * @param unknown $id_prod
 * @param unknown $total_cesta
 */
function calcula_precio_transporte ($id_prod, $total_cesta) {

	$resC = consulta("select PRECIO, DEPENDE_CESTA, PRECIO_CESTA_1, PRECIO_CESTA_2, PRECIO_1, PRECIO_2, PRECIO_3 from PRODUCTOS where ID_PRODUCTO='$id_prod'");
	$filaC = extraer_registro($resC);
	$precio = $filaC['PRECIO'];
	$precio1 = $filaC['PRECIO_1'];
	$precio2 = $filaC['PRECIO_2'];
	$precio3 = $filaC['PRECIO_3'];
	$precioCesta1 = $filaC['PRECIO_CESTA_1'];
	$precioCesta2 = $filaC['PRECIO_CESTA_2'];
	$dependeCesta = $filaC['DEPENDE_CESTA'];

	if ($dependeCesta==1) {
		if (0<=$total_cesta && $total_cesta<$precioCesta1) {
			$precio = $precio1;
		} else if ($precioCesta1<=$total_cesta && $total_cesta<$precioCesta2) {
			$precio = $precio2;
		} else {
			$precio = $precio3;
		}
	}

	return $precio;
}

/**
 * Calcular si es producto de tipo transporte
 */
function esTransporte ($id_prod) {

	$resC = consulta("select ID_PRODUCTO from PRODUCTOS where ID_PRODUCTO='$id_prod' and ID_SUBCATEGORIA='-10'");
	if (numero_filas($resC)>0) {
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * Consulta la dirección del usuario
 * @param unknown $idUsuario
 */
function consultaDireccionCompletaUsuario ($idUsuario) {
	$direccion = '';
	$usu = consulta("select * from USUARIOS where ID_USUARIO='$idUsuario'");
	$usu = extraer_registro($usu);
	$direccion = @$usu['DIRECCION'].', '.@$usu['CODIGO_POSTAL'].' '.@$usu['POBLACION'].', '.@$usu['PROVINCIA'].' (tlfs: '.@$usu['TFNO_CONTACTO'].' / '.@$usu['TFNO_MOVIL'].')';
	return $direccion;
}
?>
<?php

function consultarNumProductos () {
	if (!isset($_SESSION['S_NUM_PRODUCTOS'])) {
		$resProd = consulta ("select VALOR from CONFIGURACION where PARAMETRO='NUM_PRODUCTOS'");
		$numProd = @extraer_registro($resProd);
		$_SESSION['S_NUM_PRODUCTOS'] = @$numProd['VALOR'];
	}
	return $_SESSION['S_NUM_PRODUCTOS'];
}

function consultarPaginacion () {
	if (!isset($_SESSION['S_PAGINACION'])) {
		$resProd = consulta ("select VALOR from CONFIGURACION where PARAMETRO='PAGINACION'");
		$numProd = @extraer_registro($resProd);
		$_SESSION['S_PAGINACION'] = @$numProd['VALOR'];
	}
	return $_SESSION['S_PAGINACION'];
}

function consultarPaginacionProveedores () {
	if (!isset($_SESSION['S_PAGINACION_PROV'])) {
		$resProd = consulta ("select VALOR from CONFIGURACION where PARAMETRO='PAGINACION_PROV'");
		$numProd = @extraer_registro($resProd);
		$_SESSION['S_PAGINACION_PROV'] = @$numProd['VALOR'];
	}
	return $_SESSION['S_PAGINACION_PROV'];
}

function consultarFechaApertura () {
	if (!isset($_SESSION['S_FECHA_APERTURA'])) {
		$resFechaApertura = consulta ("select VALOR from CONFIGURACION where PARAMETRO='FECHA_APERTURA'");
		$fechaApertura = @extraer_registro($resFechaApertura);
		$_SESSION['S_FECHA_APERTURA'] = @$fechaApertura['VALOR'];
	}
	return $_SESSION['S_FECHA_APERTURA'];
}

function consultarFechaCierre () {
	if (!isset($_SESSION['S_FECHA_CIERRE'])) {
		$resFechaCierre = consulta ("select VALOR from CONFIGURACION where PARAMETRO='FECHA_CIERRE'");
		$fechaCierre = @extraer_registro($resFechaCierre);
		$_SESSION['S_FECHA_CIERRE'] = @$fechaCierre['VALOR'];
	} 
	return $_SESSION['S_FECHA_CIERRE'];
}

function consultarFechaAperturaNoCache () {
	$resFechaApertura = consulta ("select VALOR from CONFIGURACION where PARAMETRO='FECHA_APERTURA'");
	$fechaApertura = @extraer_registro($resFechaApertura);
	$_SESSION['S_FECHA_APERTURA'] = @$fechaApertura['VALOR'];
		
	return $_SESSION['S_FECHA_APERTURA'];
}

function consultarFechaCierreNoCache () {
	$resFechaCierre = consulta ("select VALOR from CONFIGURACION where PARAMETRO='FECHA_CIERRE'");
	$fechaCierre = @extraer_registro($resFechaCierre);
	$_SESSION['S_FECHA_CIERRE'] = @$fechaCierre['VALOR'];
	
	return $_SESSION['S_FECHA_CIERRE'];
}

function consultarSoloFechaCierre () {
	if (!isset($_SESSION['S_SOLO_FECHA_CIERRE'])) {
		$resFechaCierre = consulta ("select VALOR from CONFIGURACION where PARAMETRO='FECHA_CIERRE'");
		$fechaCierre = @extraer_registro($resFechaCierre);
		$_SESSION['S_SOLO_FECHA_CIERRE'] = explode(" ", @$fechaCierre['VALOR'])[0];
	}
	return $_SESSION['S_SOLO_FECHA_CIERRE'];
}

function consultarSoloFechaApertura () {
	if (!isset($_SESSION['S_SOLO_FECHA_APERTURA'])) {
		$resFechaCierre = consulta ("select VALOR from CONFIGURACION where PARAMETRO='FECHA_APERTURA'");
		$fechaCierre = @extraer_registro($resFechaCierre);
		$_SESSION['S_SOLO_FECHA_APERTURA'] = explode(" ", @$fechaCierre['VALOR'])[0];
	} 
	return $_SESSION['S_SOLO_FECHA_APERTURA'];
}

function consultarLoteActual () {
	if (!isset($_SESSION['S_LOTE_ACTUAL'])) {
		$resLote = consulta ("select VALOR from CONFIGURACION where PARAMETRO='LOTE'");
		$lote = @extraer_registro($resLote);
		$_SESSION['S_LOTE_ACTUAL'] = @$lote['VALOR'];
	} 
	return $_SESSION['S_LOTE_ACTUAL'];
}

function consultarLoteActualNoCache () {
	$resLote = consulta ("select VALOR from CONFIGURACION where PARAMETRO='LOTE'");
	$lote = @extraer_registro($resLote);
	$_SESSION['S_LOTE_ACTUAL'] = @$lote['VALOR'];
		
	return $_SESSION['S_LOTE_ACTUAL'];
}

function consultarNumVentasLote () {
	$lote = consultarLoteActualNoCache ();
	$resLote = consulta ("select COUNT(*) AS TOTAL from PEDIDOS where LOTE='$lote'");
	$resLote = @extraer_registro($resLote);
	return @$resLote['TOTAL'];
}

function consultarImporteVentasLote () {
	$lote = consultarLoteActualNoCache ();
	return $total;
}

function consultarCajaActual () {
	$resCaja = consulta ("select VALOR from CONFIGURACION where PARAMETRO='CAJA'");
	$caja = @extraer_registro($resCaja);
	return @$caja['VALOR'];
}

/**
 * Permite consultar la fecha desde de cálculo del saldo de usuarios
 */
function consultarFechaDesdeCalculoSaldoUsuarios() {
	if (!isset($_SESSION['F_CALC_SALDO_USUARIOS'])) {
		$resFechaCalcSaldo = consulta ("select VALOR from CONFIGURACION where PARAMETRO='F_CALC_SALDO_USUARIOS'");
		$fechaCalcSaldo = @extraer_registro($resFechaCalcSaldo);
		$_SESSION['F_CALC_SALDO_USUARIOS'] =  @$fechaCalcSaldo['VALOR'];
	}

	return $_SESSION['F_CALC_SALDO_USUARIOS'];
}
?>
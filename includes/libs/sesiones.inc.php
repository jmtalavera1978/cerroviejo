<?php
/** 
 * fuerza el cierre de la sesion
 * 
 */
function logout() {
	header ("Location: ../includes/logout.php");
	exit -1;
}


/** 
 * Comprueba la sesion para usuarios, tendremos q mostrar un aviso
 * 
 */
function compruebaSesionUsuario() {
	if (@$_SESSION['TIPO_USUARIO']!='USUARIO')
	   logout();
}


/** 
 * Comprueba la sesion para el administrador
 * 
 */
function compruebaSesionAdministracion() {
	if (@$_SESSION['TIPO_USUARIO']!='ADMINISTRADOR')
	   logout();
}


/**
 *  Comprueba la sesion para el repartidor
 * 
 */
function compruebaSesionRepartidor() {
	if (@$_SESSION['TIPO_USUARIO']!='REPARTIDOR')
	   logout();
}/**
 *  Comprueba la sesion para el repartidor
 * 
 */
function compruebaSesionRepartidorOAdministrador() {
	if (@$_SESSION['TIPO_USUARIO']!='REPARTIDOR' && @$_SESSION['TIPO_USUARIO']!='ADMINISTRADOR')
	   logout();
}

?>

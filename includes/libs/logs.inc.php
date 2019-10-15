<?php
/**
 * Escribe lo que le pase a un archivo de logs (saldos)
 * @param string $cadena texto a escribir en el log
 * @param string $tipo texto que indica el tipo de mensaje. 
 * Los valores normales son Info, Error, Warn Debug, Critical
 */
function write_log($cadena, $tipo)
{
	$arch = fopen(realpath( '..' )."/logs/saldos_".date("Y-m").".txt", "a+");
	fwrite($arch, "[".date("Y-m-d H:i")." ".@$_SERVER['REMOTE_ADDR']." ".
			@$_SERVER['HTTP_X_FORWARDED_FOR']." - $tipo ] ".$cadena."\n");
	@fclose($arch);
}

/**
 * Inicializa el log de saldos por cada mes,
 * con todos los saldos actuales de usuarios
 */
function compruebaLogSaldos() {
	if (!file_exists(realpath( '..' )."/logs/saldos_".date("Y-m").".txt")) {
		$usuarios = consulta("select ID_USUARIO, SALDO from USUARIOS where TIPO_USUARIO='USUARIO' ORDER BY ID_USUARIO");
		while($fila = extraer_registro($usuarios)) {
			write_log("SALDO INICIAL MES - ".$fila['ID_USUARIO']. ": ".$fila['SALDO'], "Info");
		} 
	}
}
?>

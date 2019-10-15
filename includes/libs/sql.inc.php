<?php
/****************************************************
 La siguiente funcion realiza una consulta sobre la
 base de datos, dada una sentencia sql que se le pasa
 como parametro. Utiliza para ello las variables
 globales de conexion a la base de datos.
 ****************************************************/
function consulta ($consulta_sql) {
	global $bbdd_basedatos, $bbdd_host, $bbdd_usuario, $bbdd_password;
	$correcto = TRUE;
	$res = NULL;
	
	/*INI_SEGURIDAD*/
	seguridad_en_sql($consulta_sql);
	/*FIN_SEGURIDAD*/
	
	//accedemos a la base de datos
	$correcto = $correcto & (($conexion=mysql_connect($bbdd_host, $bbdd_usuario, $bbdd_password))!=FALSE);
	mysql_set_charset('utf8', $conexion);
	$correcto = $correcto & mysql_select_db($bbdd_basedatos,$conexion);
	//realizamos la consulta de la tabla
	$correcto = $correcto & (($res=mysql_query($consulta_sql,$conexion))!=FALSE);
	$_SESSION['affected'] = NULL;
	if (@mysql_affected_rows()>0) {
		$_SESSION['affected'] = mysql_affected_rows();
	}
	$correcto = $correcto & mysql_close($conexion);
	if (!$correcto) {
		?>
<div id="dialogError" title="Errores">
	<p>ERROR de Base de Datos</p>
	No se ha posido realizar la petición <?=$consulta_sql?>
</div>
<script>
		  $(function() {
		    $( "#dialogError" ).dialog();
		  });
		</script>
<?php
	}

	//devolvemos el resultado de la consulta
	return $res;
}

/**
 * Obtiene el ultimo id creado
 * @param unknown $id_name
 * @param unknown $table
 */
function get_new_id($id_name, $table) {
	$select = "select max($id_name) as id from $table";
	$result = consulta($select);
	$fila = mysql_fetch_array($result);
	return $fila['id'];
}

/**
 * Obtiene el ultimo id creado
 * @param unknown $id_name
 * @param unknown $table
 * @param conexion $conexion
 */
function get_new_id_transacional($id_name, $table, $conexion) {
	$select = "select max($id_name) as id from $table";
	$result =  mysql_query($select, $conexion);
	$fila = mysql_fetch_array($result);
	return $fila['id'];
}

/**
 * Permite conectar con la BBDD
 * @return resource
 */
function conectar()
{
	global $bbdd_basedatos, $bbdd_host, $bbdd_usuario, $bbdd_password;
	
	$descriptor = mysql_connect($bbdd_host, $bbdd_usuario, $bbdd_password) ;
	mysql_set_charset('utf8', $descriptor);
	mysql_select_db($bbdd_basedatos, $descriptor);
	mysql_query("BEGIN", $descriptor);
	
	return $descriptor;
}

/**
 * Permite la desconexion de la BBDD
 * @param unknown $descriptor
 */
function desconectar($descriptor)
{
	mysql_close($descriptor);
}

/**
 * Obtiene el siguiente registro de un resultado o resultset
 * @param unknown $resultado
 * @return multitype:|boolean
 */
function extraer_registro ($resultado)
{
	if ($fila = mysql_fetch_array($resultado, MYSQL_ASSOC)) {
		return $fila;
	} else {
		return false;
	}
}

/**
 * Devuelve el numero de filas de una consulta
 * @param unknown $resultado
 * @return number
 */
function numero_filas($resultado)
{
	return @mysql_num_rows($resultado);
}

/**
 * Indica las filas afectadas de una consulta de modificacion, creacion o eliminacion
 * @param unknown $descriptor
 * @return number
 */
function filas_afectadas($descriptor)
{
	return mysql_affected_rows($descriptor);
}

function ultima_fila($descriptor)
{
	return mysql_insert_id($descriptor);
}

/** 
 * Comprueba codigo malicioso introducido contra la base de datos
 * 
 * @param unknown $consulta_sql
 */
function seguridad_en_sql($consulta_sql) {
	if ((strstr($consulta_sql,'#') || strstr($consulta_sql,"/*")) && (strstr(strtoupper($consulta_sql),'DROP') || strstr(strtoupper($consulta_sql),'TABLE') || strstr(strtoupper($consulta_sql),'DATABASE'))) {
		exit -1;
	}
}

?>

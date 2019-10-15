<?php
/****************************************************
  MODULO DE CONEXION A LA BASE DE DATOS MYSQL
  -------------------------------------------
  
  Este modulo contiene los datos de conexion a la base
  de datos Mysql y de configuracion de la aplicacion.
  Todas las variables seran globales.
 ****************************************************/

	@header("Content-type: text/html; charset=utf-8");

	$ruta_aplicacion = "./";
	
	$bbdd_basedatos="cerroviejo";
	$bbdd_host="localhost";
	$bbdd_usuario="cerroviejo";
	$bbdd_password="cerroviejo";
	
	/* Constantes correos */
	$cabeceras = 'From: gestion@cerroviejo.org' . "\r\n" .
	'Reply-To: gestion@cerroviejo.org' . "\r\n" .
	"Bcc: gestion@cerroviejo.org" . "\r\n" .
	'X-Mailer: PHP/' . phpversion();
	
	$produccion = 0;
	
	/*
	 * Clase de manejo de conexiones con la BBDD
	 */
	class BBDD
	{
		private $servidor;
		private $usuario;
		private $pass;
		private $base_datos;
		private $descriptor;
		
		function construct()
		{
			$this->servidor = 'localhost';
			$this->usuario = 'root';
			$this->pass = 'root';
			$this->base_datos = 'cerroviejo';
			$this->conectar();
		}
		
		private function conectar()
		{
			$this->descriptor = mysql_connect($this->servidor,$this->usuario,$this->pass) ;
			mysql_set_charset('utf8');
			mysql_select_db($this->base_datos, $this->descriptor);
		}
		
		private function desconectar()
		{
			mysql_close($this->descriptor);
		}
		
		public function consulta($consulta)
		{
			$this->resultado = mysql_query($consulta, $this->descriptor) ;
			return $this->resultado;
		}
		
		public function extraer_registro ()
		{
			if ($fila = mysql_fetch_array($this->resultado, MYSQL_ASSOC)) {
				return $fila;
			} else {
				return false;
			}
		}
		
		public function numero_filas()
		{
			return mysql_num_rows($this->resultado);
		}
		
		public function filas_afectadas()
		{
			return mysql_affected_rows($this->descriptor);
		}
		
		public function ultima_fila()
		{
			return mysql_insert_id($this->descriptor);
		}
	}
	
	/*BBDD($host,$usuario,$password,$basedatos);*/
?>

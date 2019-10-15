<?php
include_once '../includes/funciones.inc.php';
$consulta_sql= "SELECT DESCRIPCION FROM PRODUCTOS WHERE ID_PRODUCTO=".@$_GET['id'];
$resultImg= consulta($consulta_sql);
$row=extraer_registro($resultImg);
$nombreProd = $row['DESCRIPCION'];

//$nombre = strtolower(sanear_string (str_replace(" ","_",trim($nombreProd))));
//$ruta_imagen = "../fotos/".$nombre.".jpg";
$ruta_imagen = "../fotos/".@$_GET['id'].".jpg";

if (@file_exists ($ruta_imagen)) {
	header( 'Location: '.$ruta_imagen ) ;
} else { 
	header( 'Location: ../img/sinfoto.gif' ) ;
} 

//ahora colocamos la cabeceras correcta segun el tipo de imagen
/*header("Content-type: image/jpg");
if (isset($row['FOTO'])) {
	echo ($row['FOTO']);
} else {
	$data = file_get_contents("../img/sinfoto.gif");
	echo $data;
}*/
?>
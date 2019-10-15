<?php

/***********************************************
 MODULO GENERAL PARA CERRAR SESIONES DE USUARIOS
 -----------------------------------------------

 Este modulo cierra la sesion de un usuario,
 destruyendola y saliendo a la pagina principal
 de la aplicacion.
 ***********************************************/

@session_start();
@session_destroy();
Header ("Location: ..");
exit;
?>

<?php
// Iniciar sesión
session_start();
 
// Quitar variables
$_SESSION = array();
 
// Cerrar sesión
session_destroy();
 
// Volver a página de inicio
header("location: ../");
exit;
?>
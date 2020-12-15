<?php
/* ESTE ARCHIVO ES PARA CERRAR LA SESIONES ABIERTAS */
session_start();
session_destroy();

header('Location:login.control.php');

?>
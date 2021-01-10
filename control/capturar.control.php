<?php
session_start();
include('../funciones/funciones.php');

switch($_SESSION['mod1']){
    case "0":
        session_destroy();
        header('Location:login.control.php');
        die();
    break;
    
    case "1":
        $xcrud = buildTablaEventos();
        $xcrud->unset_remove()->unset_edit()->unset_add(); 
    break;
    
    case "2":
        $xcrud = buildTablaEventos();
        $xcrud->unset_remove();
        $xcrud->unset_edit(true,'estatus','=','Confirmado');
    break;

    default:
    session_destroy();
    header('Location:login.control.php');
    die();
    
    
}
    require("../vistas/views/capturar.view.php");

?>
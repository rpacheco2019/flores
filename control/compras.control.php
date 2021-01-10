<?php
session_start();
include('../funciones/funciones.php');

/* VALIDAMOS SI YA ESTA LOGUEADO */

switch($_SESSION['mod2']){
    case "0":
        session_destroy();
        header('Location:login.control.php');
        die();
    break;
    
    case "1":
        $TablaCompras = buildTablaCompras();
        $TablaCompras->unset_remove()->unset_edit()->unset_add();

        $TablaComprasOtros = buildTablaOtros();
        $TablaComprasOtros->unset_remove()->unset_edit()->unset_add();
    break;
    
    case "2":
        $TablaCompras = buildTablaCompras();
        $TablaCompras->unset_remove();
        $TablaCompras->unset_edit(true,'estatus','=','Entregado');

        $TablaComprasOtros = buildTablaOtros();
        $TablaComprasOtros->unset_remove();
        $TablaComprasOtros->unset_edit(true,'estatus','=','Entregado');
    break;

    default:
    session_destroy();
    header('Location:login.control.php');
    die();
    
    
}

require("../vistas/views/compras.view.php");


?>
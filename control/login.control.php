<?php
session_start();

/* VALIDAMOS SI YA ESTA LOGUEADO Y LO MANDAMOS AL PANEL DE REGISTROS */
if(@$_SESSION['usuario']){
    header('Location:capturar.control.php');
        die();  
}

/* EVALUAMOS SI VENIMOS DE POST */
if($_POST){
    /* EVALUAMOS CAMPOS EN BLANCO EN EL FORMULARIO */
    if(empty($_POST['user']) || empty($_POST['password'])){
        header('Location:login.control.php');
        die();
    }

    /* Cargamos archivo de funciones */
    require("../funciones/funciones.php");
    $usuario = checkUser($_POST['user'],$_POST['password']);

    /* EVALUAMOS SI EXISTE EL USUARIO PARA CARGAR VARIABLE DE SESION */
    if(!empty($usuario)){
        $_SESSION['usuario'] = $usuario['usuario'];
        $_SESSION['tipo'] = $usuario['tipo'];

        $_SESSION['departamento'] = $usuario['departamento'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['mod1'] = $usuario['mod1'];
        $_SESSION['mod2'] = $usuario['mod2'];
        $_SESSION['mod3'] = $usuario['mod3'];
        $_SESSION['mod4'] = $usuario['mod4'];
        $_SESSION['mod5'] = $usuario['mod5'];

        header('Location:capturar.control.php');
    }else{
        $error = "Usuario o password incorrectos";
        header('Location:login.control.php?error='.$error);//SI NO EXISTE, MANDAMOS DE NUEVO A LOGIN
        /* die(); */
    }
}else{//SI NO VENIMOS DE POST, LLAMAMOS LA VISTA
    require("../vistas/views/login.view.php");
}

?>
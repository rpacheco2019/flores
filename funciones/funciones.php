<?php

/* Obtenemos los datos de la orden mediante su ID */
function getOrderData($idOrden){
    $db = Xcrud_db::get_instance();
    $db->query("SELECT * FROM registroevento WHERE id =" . $idOrden);
    $resultados = $db->row();

    /* echo "Se asociaran al id-> " . $resultados['id'];
    echo "<br>Folio de Evento-> " . $resultados['folioEP'];
    echo "<br>Articulo-> " . $resultados['nombreItem'];
    echo "<br>Cantidad-> " . $resultados['cantidadItem']; */

    return $resultados;  
}

/* Obtenemos los datos de la orden mediante su ID */
function checkUser($user,$password){
    $db = Xcrud_db::get_instance();
    $db->query("SELECT * FROM usuarios WHERE usuario = '" . $user . "' AND password='" . $password ." '");
    $resultados = $db->row();

    return $resultados;  
}

?>
<!-- Control de captura de items -->
<?php
session_start();
include('../xcrud/xcrud.php');
include('../funciones/funciones.php');

/* VALIDAMOS SI YA ESTA LOGUEADO */
if(@!$_SESSION['usuario']){
    header('Location:login.control.php');
}else{

    if ($_POST) {
        
        //No hay post

    }else{

        $infoOrden = getOrderData($_GET['id']);

        /* XCRUD Table Lista de flores */
        $xcrud= Xcrud::get_instance()->table('registroflores')/* ->unset_remove()->unset_edit() */;
        $xcrud->where('idRegistro', $_GET['id']);

        /* Mostrar ID */
        $xcrud->show_primary_ai_field(true);

        $xcrud->fields('idRegistro,cantidadTotal,precioTotal,stamp,user',true);

        /* Campos de solo lectura */
        $xcrud->readonly('cantidadTotal,precioTotal,user,stamp');

        /* Mapeo de columnas */
        $xcrud->columns('idItem,flor,color,cantidadFlor,cantidadTotal,unidad,precio,precioTotal,proveedor,estatus');

        /* Quitar numero de fila */
        $xcrud->unset_numbers();

        /* Cambiar nombres a las columnas */
        $xcrud->label('idItem','# Pedido');
        $xcrud->label('idRegistro','# Orden Maestra');
        $xcrud->label('cantidadFlor','Flores por item');
        $xcrud->label('cantidadTotal','Flores en Total');
        $xcrud->label('precioTotal','Precio Total (MXN)');
        $xcrud->label('precio','Precio por item (MXN)');

        /* Validaciones */
        $xcrud->validation_required('cantidadFlor');
        $xcrud->validation_required('flor',2);
        $xcrud->validation_required('precio');
        $xcrud->validation_required('proveedor',2);
        $xcrud->validation_required('estatus',2);

        /* No puede editar los solicitados en pedido */
        $xcrud->unset_edit(true,'estatus','!=','Cotizado');
        $xcrud->unset_remove(true,'estatus','!=','Cotizado');


        /* Cambiar nombre de la tabla */
        $xcrud->table_name("Numero de Orden: " . $infoOrden['id'] . " </br> Folio EP: " . $infoOrden['folioEP'] .' </br> Lista de flores para ' . $infoOrden['nombreItem'] . ' </br> Unidades: ' . $infoOrden['cantidadItem']);

        /* Listas de campos */
        $xcrud->change_type('unidad','select','black,white',array('values'=>'Pieza,Caja,Paquete'));
        $xcrud->change_type('estatus','select','black,white',array('values'=>'-,Cotizado,Solicitado'));
        $xcrud->change_type('flor','select','black,white',array(
        'values'=>'
            -,
            CRASPEDIA,
            GIPSOFILIA,
            RANUNCULO,
            MINI ROSA,
            MINI HIEDRA,
            MINI EUCALIPTO,
            ROSA,
            ROSA DE JARDIN,
            [MATERIALES]
        '));
        $xcrud->change_type('proveedor','select','black,white',array(
        'values'=>'
            -,
            Chiltepec,
            Proveedor 2,
            Proveedor 3
        '));

        /* Monedas */
        /* $xcrud->change_type('precio', 'price', '0', array('prefix'=>'$'));
        $xcrud->change_type('precioTotal', 'price', '0', array('prefix'=>'$')); */

        /* Suma de precio total */
        $xcrud->sum('precioTotal');

        /* Tooltips - Mensajes de ayuda */
        $xcrud->field_tooltip('cantidadFlor', 'El numero de flores por cada item a producir. Si son materiales considerelo como el numero de kits.');

        /* estampamos id maestro */
        $xcrud->pass_var('idRegistro',$infoOrden['id']);

        /* calculos chinos */
        $xcrud->before_insert('calculosChinos');
        $xcrud->before_update('calculosChinos');

        /* Insertamos el usuario que modificó */
        $xcrud->pass_var('user', $_SESSION['usuario']);  


        require('../vistas/views/agregarFlor.view.php');

    }

}

?>
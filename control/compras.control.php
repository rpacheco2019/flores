<!-- Control de captura de items -->
<?php
session_start();
include('../xcrud/xcrud.php');

/* VALIDAMOS SI YA ESTA LOGUEADO */
if(@!$_SESSION['usuario']){
    header('Location:login.control.php');
}else{

    if ($_POST) {
        
        //No hay post

    }else{

        /* ---------------------Tabla de flores solicitadas-------------------------- */

        $xcrud= Xcrud::get_instance()->table('registroevento')->unset_remove()->unset_add();
        $xcrud->fields('id,fechaEvento,hotel,nombreItem,cantidadItem,descripcionItem,comentarioItem,imagen,stamp,user,registroflores.stamp,registroflores.user',true);
        $xcrud->join('id','registroflores','idRegistro');
        $xcrud->where('registroflores.estatus !=','Cotizado');

        /* Columnas a mostrar */
        $xcrud->columns('registroflores.idItem,id,folioEP,fechaEvento,nombreItem,cantidadItem,registroflores.flor,registroflores.color,registroflores.unidad,registroflores.cantidadFlor,registroflores.cantidadTotal,registroflores.precio,registroflores.precioTotal,registroflores.proveedor,registroflores.estatus');

        /* Cambiar nombres a las columnas */
        $xcrud->label('id','Orden');
        $xcrud->label('registroflores.idItem','Pedido');
        $xcrud->label('folioEP','Folio EP');
        $xcrud->label('fechaEvento','Fecha de evento');
        $xcrud->label('nombreItem','Item');
        $xcrud->label('cantidadItem','Unidades');
        $xcrud->label('registroflores.cantidadFlor','Flores por Item');
        $xcrud->label('registroflores.cantidadTotal','Flores totales');
        $xcrud->label('registroflores.precio','Precio por Item');
        $xcrud->label('registroflores.precioTotal','Precio Total');

        /* Validaciones */
        $xcrud->validation_required('registroflores.proveedor',2);
        $xcrud->validation_required('registroflores.estatus',2);

        /* Campos de solo lectura */
        $xcrud->readonly('folioEP,registroflores.flor,registroflores.color,registroflores.unidad,registroflores.cantidadFlor,registroflores.cantidadTotal,registroflores.precio,registroflores.precioTotal');

        /* Quitar numero de fila */
        $xcrud->unset_numbers();

        /* Mostrar ID en campos */
        $xcrud->show_primary_ai_field(true);

        /* Cambiar nombre de la tabla */
        $xcrud->table_name('Listado de compras de flores');

        /* Imagen / Modal*/
        $xcrud->change_type('imagen','image','', array(
            'width' => 450,
            'path' => '../uploads',
            'thumbs' => array(array(
                    'height' => 55,
                    'width' => 120,
                    'crop' => true,
                    'marker' => '_th'))));
            $xcrud->modal('imagen');

        /* Lista de opciones de solicitud para compras */
        $xcrud->change_type('registroflores.estatus','select','black,white',array('values'=>'-,Solicitado,Pedido,Entregado'));

        /* Proveedores para cambio en compras */
        $xcrud->change_type('registroflores.proveedor','select','black,white',array(
            'values'=>'
                -,
                Chiltepec,
                Proveedor 2,
                Proveedor 3
            '));

        require('../vistas/views/compras.view.php');

    }

}

?>
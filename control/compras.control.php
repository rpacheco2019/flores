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

        $xcrud= Xcrud::get_instance()->table('registroflores')->unset_remove()->unset_add();
        $xcrud->fields('user,stamp,registroevento.hotel,registroevento.descripcionItem,registroevento.comentarioItem,registroevento.user,registroevento.stamp,registroevento.fechaEvento,registroevento.imagen',true);
        $xcrud->join('idRegistro','registroevento','id');
        $xcrud->where('estatus !=','Cotizado');

        /* Columnas a mostrar */
        $xcrud->columns('idItem,idRegistro,registroevento.folioEP,registroevento.fechaEvento,registroevento.nombreItem,flor,color,unidad,cantidadFlor,cantidadTotal,precio,precioTotal,proveedor,estatus');

        /* Cambiar nombres a las columnas */
        $xcrud->label('idItem','Pedido');
        $xcrud->label('idRegistro','Orden');
        $xcrud->label('registroevento.folioEP','Folio EP');
        $xcrud->label('registroevento.fechaEvento','Fecha de evento');
        $xcrud->label('nombreItem','Item');
        $xcrud->label('cantidadItem','Unidades');
        $xcrud->label('cantidadFlor','Flores por Item');
        $xcrud->label('cantidadTotal','Flores totales');
        $xcrud->label('precio','Precio por Item');
        $xcrud->label('precioTotal','Precio Total');

        /* Validaciones */
        $xcrud->validation_required('proveedor',2);
        $xcrud->validation_required('estatus',2);

        /* Highligt */
        $xcrud->highlight('estatus','=','Pedido','#c0f0cd');
        $xcrud->highlight('estatus','=','Entregado','#c0e4f0');
        $xcrud->highlight('estatus','=','Cancelado','#f7cdf3');

        /* Campos de solo lectura */
        $xcrud->readonly('idItem,idRegistro,flor,color,unidad,cantidadFlor,cantidadTotal,precio,precioTotal,user,stamp,registroevento.folioEP,registroevento.fechaEvento,registroevento.nombreItem,registroevento.hotel,registroevento.cantidadItem,registroevento.descripcionItem,registroevento.comentarioItem,registroevento.imagen,registroevento.user,registroevento.stamp');

        /* Quitar numero de fila */
        $xcrud->unset_numbers();

        /* Mostrar ID en campos */
        $xcrud->show_primary_ai_field(true);
        $xcrud->show_primary_ai_column(true);

        /* Cambiar nombre de la tabla */
        $xcrud->table_name('Listado de compras de flores');

        /* Imagen / Modal*/
        $xcrud->change_type('registroevento.imagen','image','', array(
            'width' => 450,
            'path' => '../uploads',
            'thumbs' => array(array(
                    'height' => 55,
                    'width' => 120,
                    'crop' => true,
                    'marker' => '_th'))));
            $xcrud->modal('registroevento.imagen');

        /* Lista de opciones de solicitud para compras */
        $xcrud->change_type('estatus','select','black,white',array('values'=>'-,Solicitado,Pedido,Entregado,Cancelado'));

        /* Proveedores para cambio en compras */
        $xcrud->change_type('registroflores.proveedor','select','black,white',array(
            'values'=>'
                -,
                Chiltepec,
                Flores del campo,
                Flamingos
            '));
        
        /* No puede modificar despues de poner entregado */
        $xcrud->unset_edit(true,'estatus','=','Entregado');

        require("../vistas/views/compras.view.php");

    }

}

?>
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
        $xcrud->columns('idItem,idRegistro,registroevento.folioEP,registroevento.fechaEvento,registroevento.nombreItem,flor,color,unidad,cantidadFlor,cantidadTotal,precio,precioTotal,proveedor,estatus,registroevento.tipo');

        /* Cambiar nombres a las columnas */
        $xcrud->label('idItem','Pedido');
        $xcrud->label('idRegistro','Orden');
        $xcrud->label('registroevento.folioEP','Folio EP');
        $xcrud->label('registroevento.fechaEvento','Fecha de evento');
        $xcrud->label('registroevento.nombreItem','Articulo');
        $xcrud->label('cantidadItem','Unidades');
        $xcrud->label('cantidadFlor','Flores por Item');
        $xcrud->label('cantidadTotal','Flores totales');
        $xcrud->label('precio','Precio por Item');
        $xcrud->label('precioTotal','Precio Total');

        /* Validaciones */
        $xcrud->validation_required('proveedor',2);
        $xcrud->validation_required('estatus',2);

        /* Highligt */
        $xcrud->highlight('estatus','=','Pedido','#fa9973');
        $xcrud->highlight('estatus','=','Entregado','#86f584');
        $xcrud->highlight('estatus','=','Cancelado','#f7cdf3');
        $xcrud->highlight('estatus','=','Cotizado','#ebe9e4');
        $xcrud->highlight('estatus','=','Solicitado','#f5c651');
        $xcrud->highlight('registroevento.tipo','=','Urgente','#ff8645');

        /* Campos de solo lectura */
        $xcrud->readonly('idItem,idRegistro,flor,color,unidad,cantidadFlor,cantidadTotal,precioPorFlor,precio,precioTotal,user,stamp,registroevento.estatus,registroevento.folioEP,registroevento.fechaEvento,registroevento.nombreItem,registroevento.hotel,registroevento.cantidadItem,registroevento.descripcionItem,registroevento.comentarioItem,registroevento.imagen,registroevento.user,registroevento.stamp,registroevento.tipo');

        /* Quitar numero de fila */
        /* $xcrud->unset_numbers(); */

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
        $xcrud->change_type('estatus','select','black,white',array('values'=>'-,Pedido,Entregado,Cancelado,Incompleto'));

        /* Proveedores para cambio en compras */
        $xcrud->change_type('registroflores.proveedor','select','black,white',array(
            'values'=>'
            -,
            FLORES DE MEXICO ( SRA. MAURA ),
            FLORES LA FINCA,
            FLORES ANDREA ( CECILIA ),
            CHILTEPEC,
            FLORES DE HOLANDA,
            EUROFLORES,
            FLORANET,
            IMEX
            '));
        
        /* No puede modificar despues de poner entregado */
        $xcrud->unset_edit(true,'estatus','=','Entregado');
        $xcrud->unset_edit(true,'estatus','=','Incompleto');

        /*-------------------------- Tabla de Otros y Follajes ----------------------------------*/
        $comprasOtros= Xcrud::get_instance()->table('follaje')->unset_remove()->unset_add();
        $comprasOtros->fields('user,stamp,registroevento.hotel,registroevento.descripcionItem,registroevento.comentarioItem,registroevento.user,registroevento.stamp,registroevento.fechaEvento,registroevento.imagen',true);
        $comprasOtros->join('idOrden','registroevento','id');
        $comprasOtros->where('estatus !=','Cotizado');

        /* Nombre de tabla */
        $comprasOtros->table_name('Follaje y Otros');

        /* Cambiar nombres a las columnas */
        $comprasOtros->label('id','Pedido');
        $comprasOtros->label('idOrden','Orden');
        $comprasOtros->label('registroevento.folioEP','Folio EP');
        $comprasOtros->label('registroevento.fechaEvento','Fecha de evento');
        $comprasOtros->label('registroevento.nombreItem','Articulo');
        $comprasOtros->label('item','Follaje/Otro');
        $comprasOtros->label('precioUnitario','Precio Unitario');
        $comprasOtros->label('presupuestoTotal','Presupuesto Total');

        /* Mostrar ID en campos */
        $comprasOtros->show_primary_ai_field(true);
        $comprasOtros->show_primary_ai_column(true);

        /* Quitar numeros de fila */
        /* $comprasOtros->unset_numbers(); */    
        
        /* Mapeo de columnas */
        $comprasOtros->columns('id,idOrden,registroevento.folioEP,registroevento.fechaEvento,registroevento.nombreItem,item,cantidad,unidad,precioUnitario,presupuestoTotal,estatus,registroevento.tipo');
        
        /* Campos de solo lectura */
        $comprasOtros->readonly('id,idOrden,item,unidad,cantidad,precioUnitario,presupuestoTotal,notas,registroevento.estatus,registroevento.folioEP,registroevento.fechaEvento,registroevento.nombreItem,registroevento.cantidadItem,registroevento.tipo');
        
        /* Lista de opciones de solicitud para compras */
        $comprasOtros->change_type('estatus','select','black,white',array('values'=>'-,Pedido,Entregado,Cancelado,Incompleto'));

        /* Highligt */
        $comprasOtros->highlight('estatus','=','Pedido','#fa9973');
        $comprasOtros->highlight('estatus','=','Entregado','#86f584');
        $comprasOtros->highlight('estatus','=','Cancelado','#f7cdf3');
        $comprasOtros->highlight('estatus','=','Cotizado','#ebe9e4');
        $comprasOtros->highlight('estatus','=','Solicitado','#f5c651');
        $comprasOtros->highlight('registroevento.tipo','=','Urgente','#ff8645');

        /* No puede modificar despues de poner entregado */
        $comprasOtros->unset_edit(true,'estatus','=','Entregado');
        $comprasOtros->unset_edit(true,'estatus','=','Incompleto');

        require("../vistas/views/compras.view.php");

    }

}

?>
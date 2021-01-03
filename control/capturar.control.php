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

        $xcrud = Xcrud::get_instance();
        $xcrud->table('registroevento')->unset_remove();
        $xcrud->default_tab('Información de la Orden');
        $xcrud->subselect('Presupuesto','SELECT SUM(precioTotal) FROM registroflores WHERE idRegistro = {id}');
        $xcrud->subselect('Otros','SELECT SUM(presupuestoTotal) FROM follaje WHERE idOrden = {id}');

          /* Mostrar ID */
        $xcrud->show_primary_ai_field(true);
        $xcrud->show_primary_ai_column(true);

        /* Campos de solo lectura */
        $xcrud->readonly('id,user,stamp');

        /* Deshabilidar campos en Edit */
        $xcrud->disabled('folioEP,fechaEvento,hotel,nombreItem,descripcionItem,user,stamp','edit');

        /* Mapeo de columnas */
        $xcrud->columns('id,estatus,folioEP,fechaEvento,cantidadItem,nombreItem,Presupuesto,Otros,stamp,imagen');

        /* Quitar numero de fila */
        $xcrud->unset_numbers();

        /* Cambiar nombres a las columnas */
        $xcrud->label('id','Orden');
        $xcrud->label('folioEP','Folio Easy Planner');
        $xcrud->label('fechaEvento','Fecha de evento');
        $xcrud->label('cantidadItem','Piezas');
        $xcrud->label('nombreItem','Articulo');
        $xcrud->label('descripcionItem','Descripción del artículo');
        $xcrud->label('comentarioItem','Comentarios adicionales');
        $xcrud->label('stamp','Creado');

        /* Validaciones */
        $xcrud->validation_required('folioEP');
        $xcrud->validation_required('fechaEvento');
        $xcrud->validation_required('hotel');
        $xcrud->validation_required('nombreItem');
        $xcrud->validation_required('cantidadItem');
        $xcrud->validation_required('descripcionItem');
        $xcrud->validation_required('imagen');

        /* Monedas */
        $xcrud->change_type('Presupuesto', 'price', '0', array('prefix'=>'$'));
        $xcrud->change_type('Otros', 'price', '0', array('prefix'=>'$'));

        /* Highlight */
        $xcrud->highlight('estatus','=','Confirmado','#86f584');

        $xcrud->unset_edit(true,'estatus','=','Confirmado');

        /* Listas de campos */
        $xcrud->change_type('estatus','select','black,white',array('values'=>'Cotizado,Confirmado'));

        /* Cambiar nombre de la tabla */
        $xcrud->table_name('Ordenes de producción floral');

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

        /* Insertamos el usuario que modificó */
        $xcrud->pass_var('user', $_SESSION['usuario']);

        /* Funciones */
        $xcrud->after_update('automata');
     

        /* -----------------TABLA DE FLORES ------------- */

        $orderdetails = $xcrud->nested_table('Flores requeridas','id','registroflores','idRegistro'); // 2nd level
        $orderdetails->subselect('FX','SELECT cantidadItem FROM registroevento WHERE id = {idRegistro}');
        $orderdetails->default_tab('Agregar Flor');

        /* Mostrar ID */
        $orderdetails->show_primary_ai_field(true);
        
        /* Campos ocultos */
        $orderdetails->fields('idRegistro,cantidadTotal,precio,precioTotal,stamp,user',true);

        /* Campos de solo lectura */
        $orderdetails->readonly('cantidadTotal,precioTotal,user,stamp');

        /* Mapeo de columnas */
        $orderdetails->columns('idItem,flor,color,unidad,precioPorFlor,cantidadFlor,precio,FX,cantidadTotal,precioTotal,proveedor,estatus');

        /* Quitar numero de fila */
        $orderdetails->unset_numbers();

        /* Cambiar nombres a las columnas */
        $orderdetails->label('idItem','# Pedido');
        $orderdetails->label('idRegistro','# Orden Maestra');
        $orderdetails->label('precioPorFlor','Precio por flor (MXN)');
        $orderdetails->label('cantidadFlor','Flores por Artículo');
        $orderdetails->label('cantidadTotal','Flores en Total');
        $orderdetails->label('precioTotal','Presupuesto Total (MXN)');
        $orderdetails->label('precio','Presupuesto de Artículo (MXN)');

        /* Validaciones */
        $orderdetails->validation_required('precioPorFlor');
        $orderdetails->validation_required('cantidadFlor');
        $orderdetails->validation_required('flor',2);
        $orderdetails->validation_required('proveedor',2);
        $orderdetails->validation_required('estatus',2);

        /* Highligt */
        $orderdetails->highlight('estatus','=','Pedido','#fa9973');
        $orderdetails->highlight('estatus','=','Entregado','#86f584');
        $orderdetails->highlight('estatus','=','Cancelado','#f7cdf3');
        $orderdetails->highlight('estatus','=','Cotizado','#ebe9e4');
        $orderdetails->highlight('estatus','=','Solicitado','#f5c651');
        $orderdetails->highlight('FX','>','0','#dca1f7');

        /* Clases de columna */
        $orderdetails->column_class('precioTotal','align-center font-bold');
        $orderdetails->column_class('cantidadTotal','align-center font-bold');

        /* No puede editar los solicitados en pedido */
        $orderdetails->unset_edit(true,'estatus','!=','Cotizado');
        $orderdetails->unset_remove(true,'estatus','!=','Cotizado');

        /* quitamos caracteres extraños para no afectar el CSV */
        $orderdetails->validation_pattern('color', 'alpha');


        /* Cambiar nombre de la tabla */
        $orderdetails->table_name(' ');
        /* $orderdetails->table_name("Numero de Orden: " . $infoOrden['id'] . " </br> Folio EP: " . $infoOrden['folioEP'] .' </br> Lista de flores para ' . $infoOrden['nombreItem'] . ' </br> Unidades: ' . $infoOrden['cantidadItem']); */

        /* Listas de campos */
        $orderdetails->change_type('unidad','select','black,white',array('values'=>'Pieza,Caja,Paquete'));
        $orderdetails->change_type('estatus','select','black,white',array('values'=>'Cotizado'));
        $orderdetails->change_type('proveedor','select','black,white',array(
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

        /* Monedas */
        $orderdetails->change_type('precioPorFlor', 'price', '0', array('prefix'=>'$'));
        $orderdetails->change_type('precio', 'price', '0', array('prefix'=>'$'));
        $orderdetails->change_type('precioTotal', 'price', '0', array('prefix'=>'$'));

        /* Suma de precio total */
        $orderdetails->sum('precioTotal');

        /* Tooltips - Mensajes de ayuda */
        $orderdetails->field_tooltip('cantidadFlor', 'El numero de flores por cada artículo.');

        /* estampamos id maestro */
        /* $orderdetails->pass_var('idRegistro',$infoOrden['id']); */

        /* calculos chinos */
        $orderdetails->before_insert('calculosChinos');
        $orderdetails->before_update('calculosChinos');

        /* Insertamos el usuario que modificó */
        $orderdetails->pass_var('user', $_SESSION['usuario']);

        /* -----------------TABLA DE FOLLAJE ------------- */

        $follaje = $xcrud->nested_table('Follaje y Otros','id','follaje','idOrden'); // 2nd level
        $follaje->default_tab('Agregar');

        /* Campos Ocultos */
        $follaje->fields('idOrden',true);

        /* Nombre de la tabla */
        $follaje->table_name('Follaje y otros');

        /* Mapeo de columnas */
        $follaje->columns('id,item,cantidad,precioUnitario,presupuestoTotal,notas');

        /* Mostrar ID */
        $follaje->show_primary_ai_field(true);

        /* Quitar numero de fila */
        $follaje->unset_numbers();

        /* Campos de solo lectura */
        $follaje->readonly('presupuestoTotal');

        /* Campos ocultos */
        $follaje->fields('presupuestoTotal',true);

        /* Validaciones */
        $follaje->validation_required('item');
        $follaje->validation_required('cantidad');
        $follaje->validation_required('precioUnitario');

        /* Monedas */
        $follaje->change_type('precioUnitario', 'price', '0', array('prefix'=>'$'));
        $follaje->change_type('prespuestoTotal', 'price', '0', array('prefix'=>'$'));

        /* Cambiar nombres a las columnas */
        $follaje->label('item','Follaje / Otro');
        $follaje->label('precioUnitario','Precio unitario');
        $follaje->label('presupuestoTotal','Prespuesto de artículo');
        $follaje->label('id','# Pedido');

        /* calculos chinos Follaje*/
        $follaje->before_insert('calculosChinosFollaje');
        $follaje->before_update('calculosChinosFollaje');

        
        require("../vistas/views/capturar.view.php");
            
    }

}

?>
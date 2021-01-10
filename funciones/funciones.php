<?php
include('../xcrud/xcrud.php');

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

function buildTablaEventos(){

        $xcrud = Xcrud::get_instance();
        $xcrud->table('registroevento')->unset_remove();
        $xcrud->default_tab('Información de la Orden');
        $xcrud->order_by('id','desc');
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
        $xcrud->columns('id,estatus,tipo,folioEP,fechaEvento,cantidadItem,nombreItem,Presupuesto,Otros,stamp,imagen');

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
        $xcrud->highlight('estatus','=','Rechazado','#f7abe5');
        $xcrud->highlight('tipo','=','Urgente','#ff8645');

        /* $xcrud->unset_edit(true,'estatus','=','Confirmado'); */

        /* Listas de campos */
        $xcrud->change_type('estatus','select','black,white',array('values'=>'Cotizado,Confirmado,Rechazado'));
        $xcrud->change_type('tipo','select','black,white',array('values'=>'Normal,Urgente'));

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

        /* Boton de imprimir responsiva */
        $xcrud->button('../imprimir.php?id={id}','Imprimir responsiva','icon-print','',array('target'=>'_blank'));
     

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
        $orderdetails->change_type('unidad','select','black,white',array('values'=>'Pieza'));
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
        $follaje->columns('id,item,unidad,cantidad,precioUnitario,presupuestoTotal,notas,estatus');

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

        /* Highligt */
        $follaje->highlight('estatus','=','Pedido','#fa9973');
        $follaje->highlight('estatus','=','Entregado','#86f584');
        $follaje->highlight('estatus','=','Cancelado','#f7cdf3');
        $follaje->highlight('estatus','=','Cotizado','#ebe9e4');
        $follaje->highlight('estatus','=','Solicitado','#f5c651');

        /* Monedas */
        $follaje->change_type('precioUnitario', 'price', '0', array('prefix'=>'$'));
        $follaje->change_type('prespuestoTotal', 'price', '0', array('prefix'=>'$'));

        /* Cambiar nombres a las columnas */
        $follaje->label('item','Follaje / Otro');
        $follaje->label('precioUnitario','Precio unitario');
        $follaje->label('presupuestoTotal','Prespuesto de artículo');
        $follaje->label('id','# Pedido');

        /* Listas de opciones */
        $follaje->change_type('estatus','select','black,white',array('values'=>'Cotizado'));
        $follaje->change_type('unidad','select','black,white',array('values'=>'Pieza,Caja,Paquete'));

        /* calculos chinos Follaje*/
        $follaje->before_insert('calculosChinosFollaje');
        $follaje->before_update('calculosChinosFollaje');

    return $xcrud;
}

function buildTablaCompras(){
    /* ---------------------Tabla de flores solicitadas-------------------------- */

    $TablaCompras= Xcrud::get_instance()->table('registroflores')->unset_remove()->unset_add();
    $TablaCompras->fields('registroevento.hotel,registroevento.descripcionItem,registroevento.comentarioItem,registroevento.fechaEvento,registroevento.imagen',true);
    $TablaCompras->join('idRegistro','registroevento','id');
    $TablaCompras->where('estatus !=','Cotizado');
    $TablaCompras->order_by('idItem','desc');

    /* Columnas a mostrar */
    $TablaCompras->columns('idItem,idRegistro,registroevento.folioEP,registroevento.fechaEvento,registroevento.nombreItem,flor,color,unidad,cantidadFlor,cantidadTotal,precio,precioTotal,proveedor,estatus,registroevento.tipo');

    /* Cambiar nombres a las columnas */
    //Tabla de flores
    $TablaCompras->label('idItem','# Pedido');
    $TablaCompras->label('idRegistro','# Orden');
    $TablaCompras->label('precioPorFlor','Precio por flor');
    $TablaCompras->label('registroevento.folioEP','Folio EP');
    $TablaCompras->label('user','Pedido Creado por');
    $TablaCompras->label('stamp','Pedido Creado en');
    $TablaCompras->label('estatus','Estatus Pedido');
    $TablaCompras->label('cantidadItem','Unidades');
    $TablaCompras->label('cantidadFlor','Flores por Item');
    $TablaCompras->label('cantidadTotal','Flores totales');
    $TablaCompras->label('precio','Precio por Item');
    $TablaCompras->label('precioTotal','Precio Total');

    //Tabla Eventos
    $TablaCompras->label('registroevento.fechaEvento','Fecha de evento');
    $TablaCompras->label('registroevento.nombreItem','Articulo');
    $TablaCompras->label('registroevento.estatus','Estatus Evento');
    $TablaCompras->label('registroevento.stamp','Evento creado en');
    $TablaCompras->label('registroevento.user','Evento creado por');

    /* Validaciones */
    $TablaCompras->validation_required('proveedor',2);
    $TablaCompras->validation_required('estatus',2);

    /* Highligt */
    $TablaCompras->highlight('estatus','=','Pedido','#fa9973');
    $TablaCompras->highlight('estatus','=','Entregado','#86f584');
    $TablaCompras->highlight('estatus','=','Cancelado','#f7cdf3');
    $TablaCompras->highlight('estatus','=','Confirmado','#ebe9e4');
    $TablaCompras->highlight('estatus','=','Incompleto','#f7f497');
    $TablaCompras->highlight('registroevento.tipo','=','Urgente','#ff8645');

    /* Campos de solo lectura */
    $TablaCompras->readonly('idItem,idRegistro,flor,color,unidad,cantidadFlor,cantidadTotal,precioPorFlor,precio,precioTotal,user,stamp,registroevento.estatus,registroevento.folioEP,registroevento.fechaEvento,registroevento.nombreItem,registroevento.hotel,registroevento.cantidadItem,registroevento.descripcionItem,registroevento.comentarioItem,registroevento.imagen,registroevento.user,registroevento.stamp,registroevento.tipo');
    $TablaCompras->disabled('stamp,registroevento.stamp');

    /* Quitar numero de fila */
    /* $xcrud->unset_numbers(); */

    /* Mostrar ID en campos */
    $TablaCompras->show_primary_ai_field(true);
    $TablaCompras->show_primary_ai_column(true);

    /* Cambiar nombre de la tabla */
    $TablaCompras->table_name('Listado de compras de flores');

    /* Imagen / Modal*/
    $TablaCompras->change_type('registroevento.imagen','image','', array(
        'width' => 450,
        'path' => '../uploads',
        'thumbs' => array(array(
                'height' => 55,
                'width' => 120,
                'crop' => true,
                'marker' => '_th'))));
        $TablaCompras->modal('registroevento.imagen');

    /* Lista de opciones de solicitud para compras */
    $TablaCompras->change_type('estatus','select','black,white',array('values'=>'-,Pedido,Entregado,Cancelado,Incompleto'));

    /* Proveedores para cambio en compras */
    $TablaCompras->change_type('registroflores.proveedor','select','black,white',array(
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
    /* $TablaCompras->unset_edit(true,'estatus','=','Entregado'); */

    return $TablaCompras;
}

function buildTablaOtros(){

    /*-------------------------- Tabla de Otros y Follajes ----------------------------------*/
    $comprasOtros= Xcrud::get_instance()->table('follaje')->unset_remove()->unset_add();
    $comprasOtros->fields('user,stamp,registroevento.hotel,registroevento.descripcionItem,registroevento.comentarioItem,registroevento.fechaEvento,registroevento.imagen',true);
    $comprasOtros->join('idOrden','registroevento','id');
    $comprasOtros->where('estatus !=','Cotizado');
    $comprasOtros->order_by('id','desc');

    /* Nombre de tabla */
    $comprasOtros->table_name('Follaje y Otros');

    /* Cambiar nombres a las columnas */
    //tabla Follaje
    $comprasOtros->label('id','# Pedido');
    $comprasOtros->label('idOrden','# Orden');
    $comprasOtros->label('item','Follaje/Otro');
    $comprasOtros->label('user','Pedido creado por');
    $comprasOtros->label('stamp','Pedido creado en');
    $comprasOtros->label('precioUnitario','Precio Unitario');
    $comprasOtros->label('presupuestoTotal','Presupuesto Total');
    //tabla Evento
    $comprasOtros->label('registroevento.fechaEvento','Fecha de evento');
    $comprasOtros->label('registroevento.nombreItem','Articulo');
    $comprasOtros->label('registroevento.folioEP','Folio EP');
    $comprasOtros->label('registroevento.stamp','Evento creado en');
    $comprasOtros->label('registroevento.user','Evento creado por');


    /* Mostrar ID en campos */
    $comprasOtros->show_primary_ai_field(true);
    $comprasOtros->show_primary_ai_column(true);

    /* Quitar numeros de fila */
    /* $comprasOtros->unset_numbers(); */    
    
    /* Mapeo de columnas */
    $comprasOtros->columns('id,idOrden,registroevento.folioEP,registroevento.fechaEvento,registroevento.nombreItem,item,cantidad,unidad,precioUnitario,presupuestoTotal,estatus,registroevento.tipo');
    
    /* Campos de solo lectura */
    $comprasOtros->readonly('id,idOrden,item,unidad,cantidad,precioUnitario,presupuestoTotal,notas,registroevento.estatus,registroevento.folioEP,registroevento.fechaEvento,registroevento.nombreItem,registroevento.cantidadItem,registroevento.tipo');
    $comprasOtros->disabled('stamp,registroevento.stamp');
    
    /* Lista de opciones de solicitud para compras */
    $comprasOtros->change_type('estatus','select','black,white',array('values'=>'-,Pedido,Entregado,Cancelado,Incompleto'));

    /* Highligt */
    $comprasOtros->highlight('estatus','=','Pedido','#fa9973');
    $comprasOtros->highlight('estatus','=','Entregado','#86f584');
    $comprasOtros->highlight('estatus','=','Cancelado','#f7cdf3');
    $comprasOtros->highlight('estatus','=','Confirmado','#ebe9e4');
    $comprasOtros->highlight('estatus','=','Incompleto','#f7f497');
    $comprasOtros->highlight('registroevento.tipo','=','Urgente','#ff8645');

    /* No puede modificar despues de poner entregado */
    /* $comprasOtros->unset_edit(true,'estatus','=','Entregado'); */

    return $comprasOtros;

}

?>
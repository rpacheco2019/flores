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

        $xcrud= Xcrud::get_instance()->table('registroevento')->unset_remove()->unset_edit();
        /* Ocultar campos */
        $xcrud->fields('user,stamp',true);

        /* Mostrar ID */
        $xcrud->show_primary_ai_field(true);
        $xcrud->show_primary_ai_column(true);

        /* Campos de solo lectura */
        $xcrud->readonly('id,user,stamp');

        /* Mapeo de columnas */
        $xcrud->columns('id,folioEP,fechaEvento,cantidadItem,nombreItem,descripcionItem,stamp,imagen');

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

        /* Cambiar nombre de la tabla */
        $xcrud->table_name('Tabla de producción de flores');

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


        /* Cargamos botones por fila*/
        $xcrud->button('../control/agregarFlor.control.php?id={id}');

        /* Insertamos el usuario que modificó */
        $xcrud->pass_var('user', $_SESSION['usuario']);    


        require("../vistas/views/capturar.view.php");

    }

}

?>
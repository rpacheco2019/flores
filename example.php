<?php
    include('xcrud/xcrud.php');
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Some page title</title>
</head>
 
<body>
 
<?php
    $xcrud= Xcrud::get_instance()->table('usuarios')->unset_remove()->unset_edit();
    $xcrud->fields('id',true);
    $xcrud->change_type('empresa','select','black,white',array('values'=>'Events Pro Mexico S.A. de C.V.,Grupo Planner 1 S.A. de C.V.,HYRUMTEC SA DE CV'));
    $xcrud->change_type('depto','select','black,white',array('values'=>'Ventas,Administracion,Operaciones,Sistemas'));
    echo $xcrud;
    /* echo $xcrud->render('create'); */
?>
 
</body>
</html>
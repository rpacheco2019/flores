<?php
function publish_action($xcrud)
{
    if ($xcrud->get('primary'))
    {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE base_fields SET `bool` = b\'1\' WHERE id = ' . (int)$xcrud->get('primary');
        $db->query($query);
    }
}
function unpublish_action($xcrud)
{
    if ($xcrud->get('primary'))
    {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE base_fields SET `bool` = b\'0\' WHERE id = ' . (int)$xcrud->get('primary');
        $db->query($query);
    }
}

function exception_example($postdata, $primary, $xcrud)
{
    // get random field from $postdata
    $postdata_prepared = array_keys($postdata->to_array());
    shuffle($postdata_prepared);
    $random_field = array_shift($postdata_prepared);
    // set error message
    $xcrud->set_exception($random_field, 'This is a test error', 'error');
}

function test_column_callback($value, $fieldname, $primary, $row, $xcrud)
{
    return $value . ' - nice!';
}

function after_upload_example($field, $file_name, $file_path, $params, $xcrud)
{
    $ext = trim(strtolower(strrchr($file_name, '.')), '.');
    if ($ext != 'pdf' && $field == 'uploads.simple_upload')
    {
        unlink($file_path);
        $xcrud->set_exception('simple_upload', 'This is not PDF', 'error');
    }
}

function movetop($xcrud)
{
    if ($xcrud->get('primary') !== false)
    {
        $primary = (int)$xcrud->get('primary');
        $db = Xcrud_db::get_instance();
        $query = 'SELECT `officeCode` FROM `offices` ORDER BY `ordering`,`officeCode`';
        $db->query($query);
        $result = $db->result();
        $count = count($result);

        $sort = array();
        foreach ($result as $key => $item)
        {
            if ($item['officeCode'] == $primary && $key != 0)
            {
                array_splice($result, $key - 1, 0, array($item));
                unset($result[$key + 1]);
                break;
            }
        }

        foreach ($result as $key => $item)
        {
            $query = 'UPDATE `offices` SET `ordering` = ' . $key . ' WHERE officeCode = ' . $item['officeCode'];
            $db->query($query);
        }
    }
}
function movebottom($xcrud)
{
    if ($xcrud->get('primary') !== false)
    {
        $primary = (int)$xcrud->get('primary');
        $db = Xcrud_db::get_instance();
        $query = 'SELECT `officeCode` FROM `offices` ORDER BY `ordering`,`officeCode`';
        $db->query($query);
        $result = $db->result();
        $count = count($result);

        $sort = array();
        foreach ($result as $key => $item)
        {
            if ($item['officeCode'] == $primary && $key != $count - 1)
            {
                unset($result[$key]);
                array_splice($result, $key + 1, 0, array($item));
                break;
            }
        }

        foreach ($result as $key => $item)
        {
            $query = 'UPDATE `offices` SET `ordering` = ' . $key . ' WHERE officeCode = ' . $item['officeCode'];
            $db->query($query);
        }
    }
}

function show_description($value, $fieldname, $primary_key, $row, $xcrud)
{
    $result = '';
    if ($value == '1')
    {
        $result = '<i class="fa fa-check" />' . 'OK';
    }
    elseif ($value == '2')
    {
        $result = '<i class="fa fa-circle-o" />' . 'Pending';
    }
    return $result;
}

function custom_field($value, $fieldname, $primary_key, $row, $xcrud)
{
    return '<input type="text" readonly class="xcrud-input" name="' . $xcrud->fieldname_encode($fieldname) . '" value="' . $value .
        '" />';
}
function unset_val($postdata)
{
    $postdata->del('Paid');
}

function format_phone($new_phone)
{
    $new_phone = preg_replace("/[^0-9]/", "", $new_phone);

    if (strlen($new_phone) == 7)
        return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $new_phone);
    elseif (strlen($new_phone) == 10)
        return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $new_phone);
    else
        return $new_phone;
}

function before_list_example($list, $xcrud)
{
    var_dump($list);
}

function after_update_test($pd, $pm, $xc)
{
    $xc->search = 0;
}

/* function after_upload_test($field, &$filename, $file_path, $upload_config, $this)
{
    $filename = 'bla-bla-bla';
} */

function calculosChinos($postdata,$xcrud){

    $idOrden = $postdata->get('idRegistro');
    $cantidadFlor = $postdata->get('cantidadFlor');
    $precioFlor = $postdata->get('precioPorFlor');
    
    $presupuestoItem = $cantidadFlor * $precioFlor;

    $db = Xcrud_db::get_instance();
    $db->query('SELECT cantidadItem FROM registroevento WHERE id = ' . $idOrden);
    $cantidadOrigen = $db->row();

    $totalFlores = $cantidadOrigen['cantidadItem'] * $cantidadFlor;
    $totalPresupuesto = $cantidadOrigen['cantidadItem'] * $presupuestoItem;
    
    $postdata->set('cantidadTotal',$totalFlores);
    $postdata->set('precioTotal',$totalPresupuesto);
    $postdata->set('precio',$presupuestoItem);

    /* $idOrden = $postdata->get('idRegistro');
    $cantidadFlor = $postdata->get('cantidadFlor');
    $precioFlor = $postdata->get('precio');
    
    $db = Xcrud_db::get_instance();
    $db->query('SELECT cantidadItem FROM registroevento WHERE id = ' . $idOrden);
    $cantidadOrigen = $db->row();

    $totalFlores = $cantidadOrigen['cantidadItem'] * $cantidadFlor;
    $totalPrecio = $cantidadOrigen['cantidadItem'] * $precioFlor;
    $postdata->set('cantidadTotal',$totalFlores);
    $postdata->set('precioTotal',$totalPrecio); */

}

/* function setConfirmado($postdata,$primary,$xcrud){
    
    $estatusOrden = $postdata->get('estatus');

    if($estatusOrden == "Confirmado"){
        $db = Xcrud_db::get_instance();
        $db->query('UPDATE registroflores SET estatus="' .$estatusOrden. '" WHERE idRegistro = ' . $primary);
    }

} */

function automata($postdata,$primary,$xcrud){
    
    $estatusOrden = $postdata->get('estatus');

    if($estatusOrden == "Confirmado"){
        $db = Xcrud_db::get_instance();
        $db->query('UPDATE registroflores SET estatus="' .$estatusOrden. '" WHERE idRegistro = ' . $primary);
        $db->query('UPDATE follaje SET estatus="' .$estatusOrden. '" WHERE idOrden = ' . $primary);
    }

    $nuevaCantidadArticulos = $postdata->get('cantidadItem');

    $db = Xcrud_db::get_instance();
    $db->query('SELECT * from registroflores WHERE idRegistro = ' . $primary);
    $resultados = $db->result();

    $update = Xcrud_db::get_instance();

    foreach ($resultados as $pedido) {
    
        $floresTotales = $pedido['cantidadFlor'] * $nuevaCantidadArticulos;
        $precioTotal = $pedido['precio'] * $nuevaCantidadArticulos; 
        
        $update->query('UPDATE registroflores SET cantidadTotal= '. $floresTotales .' WHERE idItem = ' . $pedido['idItem']);
        $update->query('UPDATE registroflores SET precioTotal= '. $precioTotal .' WHERE idItem = ' . $pedido['idItem']);
    }
}

function calculosChinosFollaje($postdata,$xcrud){

    
    $cantidadFollaje = $postdata->get('cantidad');
    $precioUnitario = $postdata->get('precioUnitario');
    
    $presupuestoTotal = $cantidadFollaje * $precioUnitario;
    
    $postdata->set('presupuestoTotal',$presupuestoTotal);

}



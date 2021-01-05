<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formato de impresión</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>

<?php
    	include('xcrud/xcrud.php');
        $db = Xcrud_db::get_instance();
        $db->query("SELECT * FROM registroevento WHERE id =".$_GET['id']); // executes query, returns count of affected rows
		$resultados = $db->row(); // loads results as list of arrays
		
		$db->query("SELECT * FROM registroflores WHERE idRegistro =".$_GET['id']); // executes query, returns count of affected rows
		$flores = $db->result(); // loads results as list of arrays
		
		$db->query("SELECT * FROM follaje WHERE idOrden =".$_GET['id']); // executes query, returns count of affected rows
        $otros = $db->result(); // loads results as list of arrays
?>

<div class="container-fluid" style="width: 85%;">
	<br>
<div class="row">
    <div class="col">
      <h2>Grupo Planner 1</h2>
	  <h4>Orden de producción de Flores.</h4>
	  <br>
	  <h5>Numero de Orden: [ <?php echo $resultados['id']; ?> ] - Folio EP [ <?php echo $resultados['folioEP']; ?> ] </h5>
	  <h5>Artículo: <?php echo $resultados['nombreItem'];?></h5>
	  <h5>Cantidad: <?php echo $resultados['cantidadItem'];?></h5>
	  <h5>Fecha de Evento: <?php echo $resultados['fechaEvento'];?> - Hotel: <?php echo $resultados['hotel']; ?></h5>
	  <h5>Tipo: <?php echo $resultados['tipo'];?> - Estatus: <?php echo $resultados['estatus']; ?> </h5>
    </div>
    <div class="col-md-auto">
      <img src="./img/logo.JPG" class="rounded float-right" alt="">
    </div>
  </div>


<h6> </h6>
		<table class="table table-sm">
			<thead class="thead-dark">
				<tr>
				<th scope="col">Atributo:</th>
				<th scope="col">Descripción:</th>
				<th scope="col">Atributo:</th>
				<th scope="col">Descripción:</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="table-active">Descripción:</td>
					<td><?php echo $resultados['descripcionItem']; ?></td>
					<td class="table-active">Creado:</td>
					<td><?php echo $resultados['stamp']; ?></td>
				</tr>

				<tr>
					<td class="table-active">Comentarios:</td>
					<td><?php echo $resultados['comentarioItem']; ?></td>
					<td class="table-active">Creado por:</td>
					<td><?php echo $resultados['user']; ?></td>
				</tr>

			</tbody>
		</table>
<h5>Flores</h5>
		<table class="table table-sm">
			<thead class="thead-dark">
				<tr>
				<th scope="col"># Pedido Flor:</th>
				<th scope="col">Flor:</th>
				<th scope="col">Color:</th>
				<th scope="col">Flores por Artículo:</th>
				<th scope="col">Flores Totales:</th>
				<th scope="col">Estatus:</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($flores as $pedido) { ?>
				<tr>
					<td><?php echo $pedido['idItem']; ?></td>
					<td><?php echo $pedido['flor']; ?></td>
					<td><?php echo $pedido['color']; ?></td>
					<td><?php echo $pedido['cantidadFlor']; ?></td>
					<td><?php echo $pedido['cantidadTotal']; ?></td>
					<td><?php echo $pedido['estatus']; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
<h5>Follaje / Otros</h5>
		<table class="table table-sm">
			<thead class="thead-dark">
				<tr>
				<th scope="col"># Pedido Follaje/Otros:</th>
				<th scope="col">Item:</th>
				<th scope="col">Cantidad:</th>
				<th scope="col">Unidad:</th>
				<th scope="col">Notas:</th>
				<th scope="col">Estatus:</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($otros as $otro) { ?>
				<tr>
					<td><?php echo $otro['id']; ?></td>
					<td><?php echo $otro['item']; ?></td>
					<td><?php echo $otro['cantidad']; ?></td>
					<td><?php echo $otro['unidad']; ?></td>
					<td><?php echo $otro['notas']; ?></td>
					<td><?php echo $otro['estatus']; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

<hr>

<div class="container">
  <div class="row">
    <div class="col-sm-4">
		<p><b>Imagen de inspiración: </b> <?php ?></p>
		<img src="uploads/<?php echo $resultados['imagen'];?>" alt="" width="300" height="300">
    </div>
    <div class="col-sm-8">
    </div>
  </div>
</div>

<div class="float-right">
	<a href="" class="btn btn-danger">ODP Flores GP1 V.1</a>
	<br><br>
</div>
	

</div>

</body>
</html>
	

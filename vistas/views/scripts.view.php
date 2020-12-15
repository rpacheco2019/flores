<!-- jQuery -->
<!-- <script src="../vistas/plugins/jquery/jquery.min.js"></script> -->
<!-- Bootstrap 4 -->
<script src="../vistas/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="../vistas/plugins/datatables/jquery.dataTables.js"></script>
<script src="../vistas/plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- AdminLTE App -->
<script src="../vistas/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../vistas/dist/js/demo.js"></script>
<!-- page script -->

<!-- Esto es para hacer jalar los botones de exportación de data tables, no vienen con adminLTE -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>

<script>
  $(function () {
    /* Tabla Registros FYM*/
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      /* Esto es para agregar los botones y el orden de mas nuevo a viejo en registros */
      dom: 'Bfrtip',
            order: [[ 8, "desc" ]],
            pageLength : 10,
            buttons: ['copy', 'csv', 'excel']
    });

    /* Tabla de registros Pagos */
    $('#pagos').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      /* Esto es para agregar los botones y el orden de mas nuevo a viejo en registros */
      dom: 'Bfrtip',
            order: [[ 0, "desc" ]],
            pageLength : 10,
            buttons: ['copy', 'csv', 'excel']
    });

  });

</script>

<!-- Script JS para buscar sugerencias de factura duplicada -->
<script>
        $(document).ready(function() {
            $('#numFactura').on('keyup', function() {
                var key = $(this).val();		
                var dataString = 'numFactura='+key;
            $.ajax({
                    type: "POST",
                    url: "../helpers/checkFacturaDuplicada.php",
                    data: dataString,
                    success: function(data) {
                        //Rellenamos la lista de sugerencias con el result del Qry
                        $('#suggestions').fadeIn(1000).html(data);
                        //Al hacer click en alguna de las sugerencias
                        $('.suggest-element').on('click', function(){
                                //Obtenemos la id unica de la sugerencia seleccionada
                                var id = $(this).attr('id');
                                //Rellenamos el textbox con data="" de la sugerencia seleccionada
                                $('#key').val($('#'+id).attr('data'));
                                $('#labelKey').val($('#'+id).attr('data2'));
                                $('#labelDepto').val($('#'+id).attr('data3'));
                                $('#labelEmpresa').val($('#'+id).attr('data4'));
                                //Limpiamos la lista de sugerencias
                                $('#suggestions').fadeOut(1000);
                                /* Mandamos un alert solo para comprobar */
                                /* alert('Has seleccionado el ID: '+id+' con Serial: '+$('#'+id).attr('data')); */
                                return false;
                        });
                    }
                });
            });
        }); 
</script>

<!-- Script para calcular el IVA al 16 al momento de agregar un PP -->
<script>

  $(document).ready(function(){
    $( "#incluirIVA-0" ).prop( "checked", false );
  })
    
  function calcularIVA() {
    // Obtenemos el elemento checkbox
    var checkBox = document.getElementById("incluirIVA-0");
    
    // Guardamos el valor de factura en una variable
    var subtotal = document.getElementById("valor").value;

    // Calulamos el valor de la factura con IVA
    var total = (subtotal * 1.16);
    //Aplicamos de decimales fixed a los resultados
    var totalFixed = total.toFixed(2);

    // Aqui se comprueba si el checkbox esta activo
    if (checkBox.checked == true){
      document.getElementById("totalConIVA").value = totalFixed;
    } else{
      document.getElementById("totalConIVA").value = 0;
    }
    
  }
  
</script>

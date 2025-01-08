<?php
include '../include/header.php';
include '../../config/config_bd.php';

$conn = obtenerConexion();


// Verificar si se pudo obtener la conexión
if (!$conn) {
    die("Error al conectar con la base de datos.");
}

// Consulta SQL
$sql = "SELECT id, nombre_cliente, celular_cliente, correo_cliente, departamento, ciudad, total_numeros, total_pagado, payment_id_mercadopago, external_reference_codigo_transaccion, vendido_por, fecha_venta, codigo_sorteo FROM respaldo ORDER BY id DESC";

// Ejecutar la consulta
$resultado = $conn->query($sql);


include 'plantilla.php';
?>
<!-- termina plantilla -->


<!-- inicia contenido -->
<div id="layoutSidenav_content">
    <main>
        <h1 class="mt-4 mb-4 mx-4 d-flex justify-content-center">Respaldo</h1>
        <div class="container-fluid px-4">
            <div class="card-body">
                <table id="respaldo" class="table-striped">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Nombre</th>
                            <th>Celular</th>
                            <th>Correo</th>
                            <th>Departamento</th>
                            <th>Ciudad</th>
                            <th>Total números</th>
                            <th>Pago</th>
                            <th>Id Pasarela</th>
                            <th>Id Transacción</th>
                            <th>Vendido Por</th>
                            <th>Fecha</th>
                            <th></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultado->num_rows > 0) {
                            // Iterar sobre los resultados usando foreach
                            foreach ($resultado as $fila) { ?>
                                <tr>
                                    <td><?php echo $fila["id"]; ?></td>
                                    <td><?php echo $fila["nombre_cliente"]; ?></td>
                                    <td><a target="_blank" href="https://api.whatsapp.com/send?phone=<?php echo $fila["celular_cliente"]; ?>&text=%C2%A1Hola!%20%F0%9F%91%8B%F0%9F%8F%BB%20Hemos%20notado%20que%20has%20intentado%20realizar%20una%20compra%20en%20nuestra%20p%C3%A1gina%20web,%20El%20d%C3%ADa%20de%20Tu%20Suerte%20%F0%9F%8D%80%20%C2%BFTienes%20alguna%20pregunta%20o%20necesitas%20ayuda?%20Estoy%20aqu%C3%AD%20para%C2%A0ayudarte.%C2%A0%F0%9F%98%8A"><?php echo $fila["celular_cliente"]; ?></a></td>
                                    <td><?php echo $fila["correo_cliente"]; ?></td>
                                    <td><?php echo $fila["departamento"]; ?></td>
                                    <td><?php echo $fila["ciudad"]; ?></td>
                                    <td><?php echo $fila["total_numeros"]; ?></td>
                                    <td><?php echo $fila["total_pagado"]; ?></td>
                                    <td><?php echo $fila["payment_id_mercadopago"]; ?></td>
                                    <td><?php echo $fila["external_reference_codigo_transaccion"]; ?></td>
                                    <td><?php echo $fila["vendido_por"]; ?></td>
                                    <td><?php echo $fila["fecha_venta"]; ?></td>
                                    <td>
                                        <!-- Botón para abrir el modal y pasar los datos -->
                                        <button class="btn btn-warning btn-detalles btn-sm" data-id="<?php echo $fila["id"]; ?>"><i class="fas fa-exclamation-triangle"></i></button>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan='13'>No se encontraron resultados.</td>
                            </tr>
                        <?php } ?>
                    </tbody>

                </table>

                <!-- Modal para mostrar los detalles -->
                <div class="modal fade" id="modal-detalles" tabindex="-1" aria-labelledby="modal-detalles-label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-detalles-label">Forzar Venta</h5>
                            </div>
                            <div class="modal-body">
                                <!-- Formulario con campos ocultos para enviar datos por POST -->
                                <form id="form-detalles" action="..//functions/forzar-venta.php" method="POST">
                                    <!-- Campos ocultos para los detalles de la fila -->
                                    <input type="hidden" id="detalle-id" name="id">
                                    <input type="hidden" id="detalle-nombre" name="nombre">
                                    <input type="hidden" id="detalle-celular" name="celular">
                                    <input type="hidden" id="detalle-correo" name="correo">
                                    <input type="hidden" id="detalle-departamento" name="departamento">
                                    <input type="hidden" id="detalle-ciudad" name="ciudad">
                                    <input type="hidden" id="detalle-total-numeros" name="total_numeros">
                                    <input type="hidden" id="detalle-total-pagado" name="total_pagado">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Ingresa el id de la pasarela de Pago</label>
                                        <input id="detalle-payment-id-mercadopago" name="payment_id_mercadopago" type="text" class="form-control" required>
                                    </div>
                                    <input type="hidden" id="detalle-external-reference-codigo-transaccion" name="external_reference_codigo_transaccion">
                                    <input type="hidden" id="detalle-vendido-por" name="vendido_por">
                                    <input type="hidden" id="detalle-fecha-venta" name="fecha_venta">

                                    <div class="alert alert-danger" role="alert">
                                        ¡Espera <i class="fa-regular fa-hand"></i> antes de forzar esta venta, verifica si el pago se acreditó!
                                    </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success" form="form-detalles">forzar</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        // Usar delegación de eventos para manejar clics en los botones dentro de la tabla
                        $('#respaldo').on('click', '.btn-detalles', function() {
                            var fila = $(this).closest('tr');
                            // Obtener los datos de la fila
                            var id = fila.find('td:eq(0)').text();
                            var nombre = fila.find('td:eq(1)').text();
                            var celular = fila.find('td:eq(2)').text();
                            var correo = fila.find('td:eq(3)').text();
                            var departamento = fila.find('td:eq(4)').text();
                            var ciudad = fila.find('td:eq(5)').text();
                            var total_numeros = fila.find('td:eq(6)').text();
                            var total_pagado = fila.find('td:eq(7)').text();
                            var payment_id_mercadopago = fila.find('td:eq(8)').text();
                            var external_reference_codigo_transaccion = fila.find('td:eq(9)').text();
                            var vendido_por = fila.find('td:eq(10)').text();
                            var fecha_venta = fila.find('td:eq(11)').text();

                            // Asignar valores a los campos ocultos del formulario
                            $('#detalle-id').val(id);
                            $('#detalle-nombre').val(nombre);
                            $('#detalle-celular').val(celular);
                            $('#detalle-correo').val(correo);
                            $('#detalle-departamento').val(departamento);
                            $('#detalle-ciudad').val(ciudad);
                            $('#detalle-total-numeros').val(total_numeros);
                            $('#detalle-total-pagado').val(total_pagado);
                            $('#detalle-payment-id-mercadopago').val(payment_id_mercadopago);
                            $('#detalle-external-reference-codigo-transaccion').val(external_reference_codigo_transaccion);
                            $('#detalle-vendido-por').val(vendido_por);
                            $('#detalle-fecha-venta').val(fecha_venta);

                            // Mostrar el modal
                            $('#modal-detalles').modal('show');
                        });

                        // Capturar el evento de submit del formulario
                        $('#form-detalles').submit(function(event) {
                            // Mostrar SweetAlert mientras se envía el formulario
                            Swal.fire({
                                title: "Espera.. <i class='fa-regular fa-hand'></i>",
                                html: '<div class="text-center"><i class="fas fa-circle-notch fa-spin fa-3x"></i><br><br><p>Estamos procesando la información...</p></div>',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                            });

                            setTimeout(function() {
                                $('#form-detalles')[0].submit(); // Esto envía el formulario realmente
                            }, 1000); // Cambia el tiempo según tus necesidades reales

                            // Evitar el comportamiento por defecto del envío de formulario
                            event.preventDefault();
                        });
                    });
                </script>


                <!-- termina contenido -->

                <!-- inicia footer -->
                <?php
                include '../include/footer.php';
                ?>
                <!-- termina footer -->
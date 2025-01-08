<?php
include '../include/header.php';
include '../../config/config_bd.php';

$conn = obtenerConexion();


// Verificar si se pudo obtener la conexión
if (!$conn) {
    die("Error al conectar con la base de datos.");
}

// Consulta SQL
$sql = "SELECT 
            v.id,
            v.id_cliente,
            c.id_cliente,
            c.nombre_cliente,
            c.celular_cliente,  
            c.correo_cliente,
            c.ciudad_cliente,          
            v.total_numeros, 
            v.total_pagado, 
            v.vendido_por, 
            v.fecha_venta
        FROM ventas_bot v
        JOIN clientes c ON v.id_cliente = c.id_cliente
        ORDER BY v.id DESC";

// Ejecutar la consulta
$resultado = $conn->query($sql);


include 'plantilla.php';
?>
<!-- termina plantilla -->


<!-- inicia contenido -->
<div id="layoutSidenav_content">
    <main>
        <h1 class="mt-4 mb-4 mx-4 d-flex justify-content-center">Ventas Bot</h1>
        <div class="container-fluid px-4">
            <div class="card-body">
                <table id="respaldo" class="table-striped">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Nombre Cliente</th>
                            <th>Celular Cliente</th>
                            <th>Correo Cliente</th>
                            <th>Ciudad</th>
                            <th>Total números</th>
                            <th>Total Pagado</th>
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
                                    <td><?php echo $fila["celular_cliente"]; ?></td>
                                    <td><?php echo $fila["correo_cliente"]; ?></td>
                                    <td><?php echo $fila["ciudad_cliente"]; ?></td>
                                    <td><?php echo $fila["total_numeros"]; ?></td>
                                    <td><?php echo $fila["total_pagado"]; ?></td>
                                    <td><?php echo $fila["vendido_por"]; ?></td>
                                    <td><?php echo date('d/m/Y h:i A', strtotime($fila["fecha_venta"])); ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-detalles btn-sm">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </button>
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
                                <h5 class="modal-title" id="modal-detalles-label">Finalizar Venta</h5>
                            </div>
                            <div class="modal-body">
                                <!-- Formulario con campos ocultos para enviar datos por POST -->
                                <form id="form-detalles" action="../functions/ventas-bot.php" method="POST">
                                    <!-- Campos ocultos para los detalles de la fila -->
                                    <input type="hidden" id="detalle-id" name="id">
                                    <input type="hidden" id="detalle-nombre-cliente" name="nombre">
                                    <input type="hidden" id="detalle-celular-cliente" name="celular">
                                    <input type="hidden" id="detalle-correo-cliente" name="correo">
                                    <input type="hidden" id="detalle-ciudad" name="ciudad">
                                    <input type="hidden" id="detalle-total-numeros" name="total_numeros">
                                    <input type="hidden" id="detalle-total-pagado" name="total_pagado">
                                    <input type="hidden" id="detalle-vendido-por" name="vendido_por">
                                    <input type="hidden" id="detalle-fecha-venta" name="fecha_venta">

                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label"># Comprobante de Pago</label>
                                        <input id="detalle-payment-id-mercadopago" name="payment_id_mercadopago" type="text" class="form-control" required>
                                    </div>
                                    <div class="alert alert-danger" role="alert">
                                        ¡Espera <i class="fa-regular fa-hand"></i> Antes de finalizar esta venta, verifica que el dinero sí esté en las cuentas bancarias!
                                    </div>

                            </div>
                            <div class="modal-footer">
                                <!-- El botón de enviar el formulario -->
                                <button type="submit" class="btn btn-success" form="form-detalles" id="btn-finalizar">
                                    Finalizar
                                </button>
                                <!-- Spinner oculto inicialmente -->
                                    <div id="spinner" style="display: none;" class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Espere...</span>
                                    </div>
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
                                var nombre_cliente = fila.find('td:eq(1)').text();
                                var celular_cliente = fila.find('td:eq(2)').text();
                                var correo_cliente = fila.find('td:eq(3)').text();
                                var ciudad_cliente = fila.find('td:eq(4)').text();
                                var total_numeros = fila.find('td:eq(5)').text();
                                var total_pagado = fila.find('td:eq(6)').text();
                                var vendido_por = fila.find('td:eq(7)').text();
                                var fecha_venta = fila.find('td:eq(8)').text();

                                // Asignar valores a los campos ocultos del formulario
                                $('#detalle-id').val(id);
                                $('#detalle-nombre-cliente').val(nombre_cliente); // Cambio aquí
                                $('#detalle-celular-cliente').val(celular_cliente); // Cambio aquí
                                $('#detalle-correo-cliente').val(correo_cliente); // Cambio aquí
                                $('#detalle-ciudad').val(ciudad_cliente);
                                $('#detalle-total-numeros').val(total_numeros);
                                $('#detalle-total-pagado').val(total_pagado);
                                $('#detalle-vendido-por').val(vendido_por);
                                $('#detalle-fecha-venta').val(fecha_venta);

                                // Mostrar el modal
                                $('#modal-detalles').modal('show');
                            });
                        });
                        
                        document.getElementById('form-detalles').addEventListener('submit', function() {
                            // Mostrar el spinner y desactivar el botón
                            document.getElementById('spinner').style.display = 'inline-block';
                            document.getElementById('btn-finalizar').disabled = true;
                        });

                    </script>


                <!-- termina contenido -->

                <!-- inicia footer -->
                <?php
                include '../include/footer.php';
                ?>
                <!-- termina footer -->
<?php
    include '../include/header.php';
    include '../../config/config_bd.php';

    $conn = obtenerConexion();


// Verificar si se pudo obtener la conexión
if (!$conn) {
    die("Error al conectar con la base de datos.");
}

// Consulta SQL
$sql = "SELECT v.id, c.nombre_cliente, c.correo_cliente, c.celular_cliente, c.departamento_cliente, c.ciudad_cliente, v.total_numeros, v.total_pagado, v.payment_id_mercadopago, v.external_reference_codigo_transaccion, v.vendido_por, v.fecha_venta, v.codigo_sorteo
FROM ventas v
JOIN clientes c ON v.id_cliente = c.id_cliente
ORDER BY v.id DESC;
";

// Ejecutar la consulta
$resultado = $conn->query($sql);


    include 'plantilla.php';
?>
<!-- inicia contenido -->
<div id="layoutSidenav_content">
                <main>
                    <div class="float-end d-flex mt-5 me-4 mb-3">
                    <button onclick="descargarEXCEL()" type="button" class="btn btn-outline-success"><i class="fa-solid fa-file-excel"></i> Exportar </button>
                    </div>
                    <h1 class="mt-4 mb-4 mx-4 d-flex justify-content-center">Ventas al Detalle</h1>
                    <div class="container-fluid px-4">                     
                    <div class="card-body">
                                <table id="ventasAldetalle" class="table-striped">                                
                                <thead>
                                        <tr>
                                            <th>Venta</th>
                                            <th>Nombre</th>
                                            <th>Celular</th>
                                            <th>Correo</th>
                                            <th>Depto</th>
                                            <th>Ciudad</th>
                                            <th>Total números</th>
                                            <th>Pagó</th>                                            
                                            <th>Id Transacción</th>
                                            <th>Fecha</th>                                            
                                            <th>Por</th>
                                            <th>Tipo</th>                                                                         
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
                                                    <td><?php echo $fila["departamento_cliente"]; ?></td>
                                                    <td><?php echo $fila["ciudad_cliente"]; ?></td>
                                                    <td><?php echo $fila["total_numeros"]; ?></td>
                                                    <td><?php echo '$' . number_format(floatval($fila["total_pagado"]), 0, ',', '.'); ?></td>
                                                    <td><?php echo $fila["external_reference_codigo_transaccion"]; ?></td>
                                                    <td><?php echo date("d/m/Y h:i A", strtotime($fila["fecha_venta"])); ?></td>
                                                    <td><?php echo $fila["vendido_por"]; ?></td>
                                                    <td><?php echo $fila["payment_id_mercadopago"]; ?></td>
                                                    <td>
                                                    <div class="d-flex">
                                                        <button type="button" onclick="devolucion(<?php echo $fila['id']; ?>, '<?php echo $fila['correo_cliente']; ?>')" class="btn btn-outline-danger btn-sm mx-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Anular venta">
                                                            <i class="fa-solid fa-rotate-left"></i>
                                                        </button>
                                                        
                                                        <button type="button" onclick="recordatorio(<?php echo $fila['id']; ?>, '<?php echo $fila['correo_cliente']; ?>')" class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Enviar recordatorio">
                                                            <i class="fa-solid fa-paper-plane"></i>
                                                        </button>
                                                    </div>

                                                    </td>

                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr><td colspan='13'>No se encontraron resultados.</td></tr>
                                        <?php } ?>
                                    </tbody>

                                </table>
                            </div>
                    </div>
                </main>
<!-- termina contenido -->

<!-- inicia footer -->
<?php
include '../include/footer.php';
?>
<!-- termina footer -->
<?php
    include '../include/header.php';
    include '../../config/config_bd.php';

    $conn = obtenerConexion();


// Verificar si se pudo obtener la conexión
if (!$conn) {
    die("Error al conectar con la base de datos.");
}

// Consulta SQL
$sql = "SELECT v.id, c.nombre_cliente, c.celular_cliente, nv.numero, v.external_reference_codigo_transaccion
FROM ventas v
JOIN numeros_vendidos nv ON v.id = nv.id_venta
JOIN clientes c ON v.id_cliente = c.id_cliente
ORDER BY v.id DESC;";

// Ejecutar la consulta
$resultado = $conn->query($sql);


    include 'plantilla.php';
?>
<!-- termina plantilla -->
 

<!-- inicia contenido -->
<div id="layoutSidenav_content">
    <main>
        <h1 class="mt-4 mb-4 mx-4 d-flex justify-content-center">Números Vendidos</h1>
        <div class="container-fluid px-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Números Vendidos
                </div>
                <div class="card-body">
                    <table id="numerosVendidos" class="table-striped">
                        <thead>
                            <tr>
                                <th>N° Venta</th>
                                <th>Cliente</th>
                                <th>Celular</th>
                                <th>Número</th>
                                <th>Código Transacción</th>
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
                                        <td><?php echo $fila["numero"]; ?></td>
                                        <td><?php echo $fila["external_reference_codigo_transaccion"]; ?></td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan='4'>No se encontraron resultados.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

         
<!-- termina contenido -->

<!-- inicia footer -->
<?php
include '../include/footer.php';
?>
<!-- termina footer -->
<?php
    include '../include/header.php';
    include '../../config/config_bd.php';

    include '../functions/query.php';

    $conn = obtenerConexion();

    // Obtiene los clientes
    $clientes = obtenerClientes($conn);



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
        <h1 class="mt-4 mb-4 mx-4 d-flex justify-content-center">Clientes</h1>
        <div class="container-fluid px-4">
            <div class="card mb-4">
            <div class="card-header">
                        <i class="fas fa-table"></i>
                        Clientes
                        <button onclick="descargarClientes()" type="button" class="btn btn-outline-success float-end"><i class="fa-solid fa-file-excel"></i> Exportar </button>
                    </div>
                <div class="card-body">
                <table id="clientes" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Celular</th>
                                    <th>Correo</th>
                                    <th>Depto</th>
                                    <th>Ciudad</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <tr>
                                        <td><?php echo $cliente['id_cliente']; ?></td>
                                        <td><?php echo $cliente['nombre_cliente']; ?></td>
                                        <td><?php echo $cliente['celular_cliente']; ?></td>
                                        <td><?php echo $cliente['correo_cliente']; ?></td>
                                        <td><?php echo $cliente['departamento_cliente']; ?></td>
                                        <td><?php echo $cliente['ciudad_cliente']; ?></td>
                                        <td>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#modalEditCliente" class="btnEditCliente btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen-to-square"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </main>

                    <!-- Modal editar clientes -->
                    <div class="modal fade" id="modalEditCliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Editar Cliente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="../functions/editar-cliente.php.php" id="formEditarCliente">
                                    <div class="mb-1">
                                        <label for="recipient-name" class="col-form-label">Celular:</label>
                                        <input readonly type="number" style="cursor: not-allowed; background: #a9a9a938" requiered class="form-control" id="celular" name="celular">
                                    </div>
                                    <div class="mb-1">
                                        <input type="hidden" name="id" id="id">
                                        <label for="recipient-name" class="col-form-label">Nombre:</label>
                                        <input type="text" requiered class="form-control" id="nombre" name="nombre" required>
                                    </div>


                                    <div class="mb-1">
                                        <label for="recipient-name" class="col-form-label">Correo:</label>
                                        <input type="email" requiered class="form-control" id="correo" name="correo" required>
                                    </div>


                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success" id="btnGuardar">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    Guardar
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>

         
<!-- termina contenido -->

<!-- inicia footer -->
<?php
include '../include/footer.php';
?>
<!-- termina footer -->
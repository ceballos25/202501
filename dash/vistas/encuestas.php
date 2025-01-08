<?php
    include '../include/header.php';
    include '../../config/config_bd.php';

    $conn = obtenerConexion();


// Verificar si se pudo obtener la conexión
if (!$conn) {
    die("Error al conectar con la base de datos.");
}

// Consulta SQL
$sql = "SELECT id, calificacion, observaciones, fecha FROM encuestas ORDER BY calificacion ASC";

// Ejecutar la consulta
$resultado = $conn->query($sql);


    include 'plantilla.php';
?>
<!-- termina plantilla -->
 

<!-- inicia contenido -->
<div id="layoutSidenav_content">
                <main>
                <h1 class="mt-4 mb-4 mx-4 d-flex justify-content-center">Calificaciones</h1>
                    <div class="container-fluid px-4">
                    <div class="card-body">
                                <table id="calificaciones" class="table-striped">
                                    <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Estrellas</th>
                                            <th>Observaciones</th>
                                            <th>Fecha</th>                                                        

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($resultado->num_rows > 0) {
                                            // Iterar sobre los resultados usando foreach
                                            foreach ($resultado as $fila) { ?>
                                                <tr>
                                                    <td><?php echo $fila["id"]; ?></td>
                                                    <td>
                                                    <?php
                                                    // Obtener el valor de la calificación de la fila
                                                    $calificacion = $fila["calificacion"];

                                                    // Imprimir las estrellas según el valor de la calificación
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        // Verificar si el índice actual es menor o igual a la calificación
                                                        if ($i <= $calificacion) {
                                                            // Mostrar una estrella llena y amarilla
                                                            echo '<i class="fas fa-star" style="color: #FFD700;"></i>';
                                                        }
                                                    }
                                                    ?>
                                                </td>

                                                <td><?php echo $fila["observaciones"]; ?></td>                                                  
                                                    <td><?php echo date('d/m/Y H:i:s A', strtotime($fila["fecha"])); ?></td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr><td colspan='13'>No se encontraron resultados.</td></tr>
                                        <?php } ?>
                                    </tbody>

                                </table>


<!-- termina contenido -->

<!-- inicia footer -->
<?php
include '../include/footer.php';
?>
<!-- termina footer -->
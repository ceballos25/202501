<?php
// Aquí incluyes el archivo de conexión a la base de datos
include 'config_bd.php';

// Verifica si se recibió el valor de totalNumeros desde la solicitud POST
if (isset($_POST['totalNumeros'])) {
    // Recupera y sanitiza el valor de totalNumeros
    $totalNumeros = filter_var($_POST['totalNumeros'], FILTER_SANITIZE_NUMBER_INT);

    // Intenta establecer la conexión con la base de datos
    $conexion = obtenerConexion();

    // Verifica si la conexión se estableció correctamente
    if ($conexion) {
        // Realiza la consulta para obtener la cantidad de números disponibles
        $consulta = $conexion->prepare("SELECT COUNT(*) AS total_disponibles FROM numeros");
        $consulta->execute();
        $resultado = $consulta->get_result();

        // Verifica si se obtuvo un resultado
        if ($fila = $resultado->fetch_assoc()) {
            $totalDisponibles = $fila['total_disponibles'];

            // Verifica si la cantidad solicitada es menor o igual a la cantidad disponible
            if ($totalNumeros <= $totalDisponibles) {
                // Si hay suficientes números disponibles, devuelve una respuesta de éxito
                $response = array('success' => true);
            } else {
                // Si no hay suficientes números disponibles, devuelve una respuesta de error
                $response = array('success' => false, 'message' => 'La cantidad solicitada supera la cantidad disponible, actualmente tenemos disponible: ' . $totalDisponibles .'  números.');
            }
        } else {
            // Si no se obtuvo un resultado, devuelve una respuesta de error
            $response = array('success' => false, 'message' => 'Hubo un error, vuelve a intentarlo.');
        }

        // Cierra la conexión a la base de datos
        $conexion->close();
    } else {
        // Si la conexión a la base de datos falla, devuelve una respuesta de error
        $response = array('success' => false, 'message' => 'Hubo un error, vuelve a intentarlo.');
    }
} else {
    // Si no se recibió el valor de totalNumeros, devuelve una respuesta de error
    $response = array('success' => false, 'message' => 'Hubo un error, vuelve a intentarlo.');
}

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

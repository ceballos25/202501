<?php
date_default_timezone_set('America/Bogota'); // Definimos la zona horaria de Colombia

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recupera y sanitiza los valores de calificacion y observaciones
    $calificacion = isset($_POST['rating']) ? intval($_POST['rating']) : null;
    $observaciones = isset($_POST['observaciones']) ? htmlspecialchars($_POST['observaciones']) : null;
    $fechaEncuesta = date('Y-m-d H:i:s');

    // Aquí deberías incluir el archivo de conexión a la base de datos
    require_once '../config/config_bd.php';

    // Intenta establecer la conexión con la base de datos
    $conexion = obtenerConexion();

    // Verifica si la conexión se estableció correctamente
    if ($conexion) {
        // Prepara la consulta SQL para insertar la calificación en la base de datos
        $consulta = $conexion->prepare("INSERT INTO encuestas (calificacion, observaciones, fecha) VALUES (?, ?, ?)");

        // Asigna los valores a los parámetros de la consulta
        $consulta->bind_param("iss", $calificacion, $observaciones, $fechaEncuesta);

        // Ejecuta la consulta
        if ($consulta->execute()) {
            // Si la consulta se ejecutó con éxito, devuelve una respuesta de éxito
            $response = array('success' => true);
        } else {
            // Si la consulta falló, devuelve una respuesta de error
            $response = array('success' => false, 'message' => 'Error al guardar la calificación. Por favor, intenta nuevamente.');
        }

        // Cierra la consulta y la conexión a la base de datos
        $consulta->close();
        $conexion->close();
    } else {
        // Si la conexión a la base de datos falla, devuelve una respuesta de error
        $response = array('success' => false, 'message' => 'Error al conectarse a la base de datos. Por favor, intenta nuevamente.');
    }
} else {
    // Si no se recibieron los valores de calificacion y observaciones, devuelve una respuesta de error
    $response = array('success' => false, 'message' => 'Faltan datos requeridos. Por favor, completa el formulario.');
}

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

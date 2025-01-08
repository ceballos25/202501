<?php
include '../../config/config_bd.php';

// Obtener una conexión a la base de datos
$conn = obtenerConexion();


// Verificar si se recibieron datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Recibir los datos del formulario
        $id = $_POST["id"];
        $nombre = $_POST["nombre"];
        $celular = $_POST["celular"];
        $correo = $_POST["correo"];

        // Aquí debes ejecutar la consulta SQL para actualizar los datos en la base de datos
        // Por ejemplo, supongamos que tienes una conexión a la base de datos llamada $conn

        // Preparar la consulta SQL
        $sql = "UPDATE clientes SET nombre_cliente=?, correo_cliente=? WHERE id_cliente=?";

        // Preparar la declaración
        $stmt = $conn->prepare($sql);

        // Vincular los parámetros
        $stmt->bind_param("ssi", $nombre, $correo, $id);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // La actualización fue exitosa
            echo json_encode(["success" => true, "message" => "¡La actualización fue exitosa!"]);
        } else {
            // Error al ejecutar la consulta
            echo json_encode(["success" => false, "message" => "Error al actualizar los datos: " . $stmt->error]);
        }

        // Cerrar la declaración
        $stmt->close();
        // Cerrar la conexión
        $conn->close();
    } catch (Exception $e) {
        // Manejar la excepción
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    // Si no se recibieron datos por POST, devolver un mensaje de error
    echo json_encode(["success" => false, "message" => "No se recibieron datos por POST"]);
}
?>

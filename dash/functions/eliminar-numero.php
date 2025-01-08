<?php

include '../../config/config_bd.php';

// Verificar si se recibió un ID de venta válido
if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no válido.']);
    exit;
}

// Obtener el ID de la venta a eliminar
$id = $_POST['id'];

// Obtener una conexión a la base de datos
$conn = obtenerConexion();

// Verificar si se pudo obtener la conexión
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos.']);
    exit;
}

// Iniciar una transacción
$conn->begin_transaction();

try {
    // 4. Eliminar el número de disponible
    $sql = "DELETE FROM numeros WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar el número: " . $stmt->error);
    }
    $stmt->close();

    // Confirmar la transacción
    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Cerrar la conexión
$conn->close();
?>

<?php

include '../../config/config_bd.php';

$conexion = obtenerConexion();

// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}


// Obtener el número a verificar (pasado como parámetro GET)
$numero = $_GET['numero'];

// Consultar si el número existe en la tabla de la base de datos
$sql = "SELECT COUNT(*) AS count FROM numeros_vendidos WHERE numero = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $numero);
$stmt->execute();
$result = $stmt->get_result();

// Obtener el resultado de la consulta
$row = $result->fetch_assoc();
$count = $row['count'];

// Enviar respuesta JSON indicando si el número existe
echo json_encode(['existe' => ($count > 0)]);

// Cerrar la conexión
$stmt->close();
$conexion->close();
?>

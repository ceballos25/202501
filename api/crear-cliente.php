<?php
// Establecer la zona horaria a Colombia
date_default_timezone_set('America/Bogota');

// Establecer la conexión a la base de datos (ajusta los valores según tu configuración)
require '../config/config_bd.php';

// Establecer el encabezado de respuesta para JSON
header('Content-Type: application/json');

// Obtener los datos JSON de la solicitud
$input = file_get_contents('php://input');

// Muestra el contenido JSON recibido para depuración
error_log("JSON recibido: $input");

// Decodificar el JSON en un array de PHP
$data = json_decode($input, true);

// Verificar si el JSON se decodificó correctamente
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Error al decodificar JSON.'));
    exit();
}

// Verificar si se recibieron los datos 'id_cliente' y 'total_numeros'
if (!isset($data['nombre']) ||
    !isset($data['celular']) ||
    !isset($data['correo']) ||
    !isset($data['ciudad'])   
    ) {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Faltan datos en la solicitud.'));
    exit();
}

    // Obtener los valores del JSON
    $nombre = $data['nombre'];
    $celular = $data['celular'];
    $correo = $data['correo'];
    $departamento = "N/A";
    $ciudad = $data['ciudad'];
    
    //quitar prefijo
    $celular = (substr($celular, 0, 2) === '57') ? substr($celular, 2) : $celular;



// Conectar a la base de datos
$conexion = obtenerConexion();

// Verificar la conexión
if (!$conexion) {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Error de conexión: ' . mysqli_connect_error()));
    exit();
}

// Preparar la consulta SQL para insertar los datos
$sql = "INSERT INTO clientes(nombre_cliente, celular_cliente, correo_cliente, departamento_cliente, ciudad_cliente) 
        VALUES (?, ?, ?, ?, ?)";

// Preparar la sentencia
$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Error en la preparación de la consulta: ' . $conexion->error));
    exit();
}

// Vincular los parámetros
$stmt->bind_param("sssss", $nombre, $celular, $correo, $departamento, $ciudad);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo json_encode(array('estado' => 'exito', 'mensaje' => 'Datos guardados exitosamente.'));
} else {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Error al guardar los datos: ' . $stmt->error));
}

// Cerrar la conexión
$stmt->close();
$conexion->close();
?>

<?php
// Establecer la conexión a la base de datos (ajusta los valores según tu configuración)
require '../config/config_bd.php';

// Establecer el encabezado de respuesta para JSON
header('Content-Type: application/json');

// Obtener datos JSON de la solicitud
$input = file_get_contents('php://input');

// Muestra el contenido JSON recibido para depuración
error_log("JSON recibido: $input");

// Decodifica el JSON en un array de PHP
$data = json_decode($input, true);

// Verificar si el JSON se decodificó correctamente
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Error al decodificar JSON.'));
    exit();
}

// Verificar si se recibió el dato 'celular'
if (!isset($data['celular'])) {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Falta el dato "celular" en la solicitud.'));
    exit();
}

// Obtener el celular del JSON
$celular = $data['celular'];

// Limpiar el número de celular: eliminar espacios y el prefijo "+57"
$celular = preg_replace("/^57/", "", $celular); // Elimina el prefijo "57" si está presente al inicio

// Conectar a la base de datos
$conexion = obtenerConexion();

// Verificar la conexión
if (!$conexion) {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Error de conexión: ' . mysqli_connect_error()));
    exit();
}

// Preparar la consulta SQL para evitar inyecciones SQL
$sql = "SELECT id_cliente, nombre_cliente, celular_cliente, correo_cliente, departamento_cliente, ciudad_cliente FROM clientes WHERE celular_cliente = ?";
$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Error en la preparación de la consulta: ' . $conexion->error));
    exit();
}

// Vincular el parámetro
$stmt->bind_param("s", $celular); // "s" indica que el parámetro es una cadena

// Ejecutar la consulta
$stmt->execute();
$result = $stmt->get_result();

// Verificar si la consulta tuvo éxito
if ($result) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Crear el array con los datos de la respuesta en el formato solicitado
        $respuesta = array(
            'estado' => 1,
            'id' => $row['id_cliente'],
            'nombre' => $row['nombre_cliente'],
            'correo' => $row['correo_cliente'],
            'celular' => $row['celular_cliente'],
            'departamento' => $row['departamento_cliente'],
            'ciudad' => $row['ciudad_cliente']
        );

        // Devolver los datos en formato JSON
        echo json_encode($respuesta);
    } else {
        // No se encontraron datos para el celular ingresado
        echo json_encode(array('estado' => 'error', 'mensaje' => 'Empleado no encontrado.'));
    }
} else {
    // Error en la consulta
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Error en la consulta: ' . $conexion->error));
}

// Cerrar la conexión a la base de datos
$stmt->close();
$conexion->close();
?>

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
if (!isset($data['id_cliente']) || !isset($data['total_numeros'])) {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Faltan datos en la solicitud.'));
    exit();
}

// Obtener los valores del JSON
$id_cliente = $data['id_cliente'];
$total_numeros = $data['total_numeros'];  // Valor como "5 x $15.500 🎟️"

// Usar explode para obtener el primer número antes del primer espacio
$valores = explode(' ', $total_numeros);  // Divide la cadena por los espacios
$primer_valor = $valores[0];  // El primer valor será el número antes del espacio

// Convertir a número entero (por si viene como cadena)
$total_numeros = (int)$primer_valor;

// Calcular el valor total a pagar según el precio unitario
if ($total_numeros >= 20) {
    $precio_unitario = 3000;  // Si son 20 o más, el precio es 3000
} else {
    $precio_unitario = 3500;  // Si son menos de 20, el precio es 3500
}
$total_pagado = $total_numeros * $precio_unitario;  // Multiplicamos el total de entradas por el precio unitario

// Generar los códigos únicos
$payment_id_mercadopago = substr(uniqid('', true), 0, 10);  // Generar un ID único de 10 caracteres
$external_reference_codigo_transaccion = substr(bin2hex(random_bytes(5)), 0, 10); // Generar un código único de 10 caracteres

$vendido_por = "Chat Bot"; // O asignar el valor de quien hizo la venta

// Obtener la fecha y hora actual en el formato solicitado (14/12/2024 07:00 PM)
$fecha_venta = date('Y-m-d H:i:s');  // Formato: día/mes/año hora:minutos AM/PM

// Conectar a la base de datos
$conexion = obtenerConexion();

// Verificar la conexión
if (!$conexion) {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Error de conexión: ' . mysqli_connect_error()));
    exit();
}

// Preparar la consulta SQL para insertar los datos
$sql = "INSERT INTO ventas_bot (id_cliente, total_numeros, total_pagado, payment_id_mercadopago, external_reference_codigo_transaccion, vendido_por, fecha_venta) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

// Preparar la sentencia
$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    echo json_encode(array('estado' => 'error', 'mensaje' => 'Error en la preparación de la consulta: ' . $conexion->error));
    exit();
}

// Vincular los parámetros
$stmt->bind_param("iiissss", $id_cliente, $total_numeros, $total_pagado, $payment_id_mercadopago, $external_reference_codigo_transaccion, $vendido_por, $fecha_venta);

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

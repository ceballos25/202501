<?php
// Establecer la conexión a la base de datos (ajusta los valores según tu configuración)
require '../config/config_bd.php';

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a la base de datos (reemplaza estos valores con los de tu conexión real)
    $conexion = obtenerConexion();

    // Verificar la conexión
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
} 

// Obtener la cédula del formulario
$celular = $_POST['celular'];

// Consulta SQL para buscar la cédula en la base de datos
$sql = "SELECT id_cliente, nombre_cliente, celular_cliente, correo_cliente, departamento_cliente, ciudad_cliente FROM clientes WHERE celular_cliente = $celular";
$result = $conexion->query($sql);

// Obtener los datos necesarios de la consulta SQL
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $datos = array(
        'nombre' => $row['nombre_cliente'],
        'correo' => $row['correo_cliente'],
        'celular' => $row['celular_cliente'],
        'departamento' => $row['departamento_cliente'], // Asegúrate de incluir el campo de departamento
        'ciudad' => $row['ciudad_cliente'] // Asegúrate de incluir el campo de ciudad
    );

    // Devolver los datos en formato JSON
    echo json_encode(array('success' => true, 'data' => $datos));
} else {
    // No se encontraron datos para la cédula ingresada
    echo json_encode(array('success' => false));
}


// Cerrar la conexión a la base de datos
$conexion->close();
?>

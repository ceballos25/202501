<?php
// Establecer la conexión a la base de datos (ajusta los valores según tu configuración)
require '../config/config_bd.php';

// Verificar si se envió el formulario y si el campo 'celular' está presente y no está vacío
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['celular'])) {
    // Conectar a la base de datos
    $conexion = obtenerConexion();

    // Verificar la conexión
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Obtener el valor del campo 'celular' del formulario y sanitizarlo (eliminar caracteres no deseados)
    $celular = filter_var($_POST['celular'], FILTER_SANITIZE_NUMBER_INT); // Sanitizar como número entero
    $celular = htmlspecialchars($celular); // Convertir caracteres especiales en entidades HTML
    
    // Consulta SQL utilizando una consulta preparada
    $sql = "SELECT numeros_vendidos.numero, clientes.correo_cliente
            FROM clientes
            JOIN ventas ON clientes.id_cliente = ventas.id_cliente
            JOIN numeros_vendidos ON ventas.id = numeros_vendidos.id_venta
            WHERE clientes.celular_cliente = ?"; // Usamos ? como marcador de posición

    // Preparar la consulta
    if ($stmt = $conexion->prepare($sql)) {
        // Vincular el parámetro usando bind_param
        $stmt->bind_param("s", $celular); // "s" indica que $celular es un string (puede ajustarse según el tipo de datos)

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener resultados de la consulta
        $result = $stmt->get_result();

        // Crear un array para almacenar los números vendidos y el correo electrónico
        $numeros = array();
        $correo = '';

        // Verificar si se encontraron resultados
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $numeros[] = $row['numero'];
                // Asignar el correo electrónico del cliente (será el mismo para todos los números)
                $correo = $row['correo_cliente'];
            }
        }

        // Devolver los datos en formato JSON
        if (!empty($numeros)) {
            $data = array(
                'numeros' => $numeros,
                'correo' => $correo
            );
            echo json_encode(array('success' => true, 'data' => $data));
        } else {
            echo json_encode(array('success' => false));
        }

        // Cerrar la sentencia
        $stmt->close();
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
}
?>

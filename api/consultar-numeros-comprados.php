<?php
// Establecer la conexión a la base de datos (ajusta los valores según tu configuración)
require '../config/config_bd.php';

// Configura el encabezado para que el navegador sepa que estamos enviando una imagen
header('Content-Type: image/png');

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el contenido de la solicitud JSON
    $json = file_get_contents('php://input');
    
    // Decodificar el JSON a un array PHP
    $data = json_decode($json, true);
    
    // Verificar si el campo 'celular' está presente en los datos
    if (isset($data['celular'])) {
        // Conectar a la base de datos
        $conexion = obtenerConexion();
        
        // Verificar la conexión
        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }
        
        // Obtener el valor del campo 'celular' y sanitizarlo
        $celular = filter_var($data['celular'], FILTER_SANITIZE_NUMBER_INT); // Sanitizar como número entero
        $celular = htmlspecialchars($celular); // Convertir caracteres especiales en entidades HTML
        
        // Consulta SQL utilizando una consulta preparada
        $sql = "SELECT numeros_vendidos.numero
                FROM clientes
                JOIN ventas ON clientes.id_cliente = ventas.id_cliente
                JOIN numeros_vendidos ON ventas.id = numeros_vendidos.id_venta
                WHERE clientes.celular_cliente = ?";
        
        // Preparar la consulta
        if ($stmt = $conexion->prepare($sql)) {
            // Vincular el parámetro usando bind_param
            $stmt->bind_param("s", $celular); // "s" indica que $celular es un string
            
            // Ejecutar la consulta
            $stmt->execute();
            
            // Obtener resultados de la consulta
            $result = $stmt->get_result();
            
            // Crear un array para almacenar los números
            $numeros = array();
            
            // Verificar si se encontraron resultados
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $numeros[] = $row['numero'];
                }
            }
            
            // Preparar para generar la imagen
            $ancho = 400;
            $alto = 200;
            $imagen = imagecreatetruecolor($ancho, $alto);
            
            // Definir colores
            $fondo = imagecolorallocate($imagen, 255, 255, 255); // Blanco
            $texto = imagecolorallocate($imagen, 0, 0, 0); // Negro
            
            // Rellenar el fondo con color blanco
            imagefill($imagen, 0, 0, $fondo);
            
            // Añadir texto a la imagen
            $y = 20;
            foreach ($numeros as $numero) {
                imagestring($imagen, 5, 10, $y, $numero, $texto);
                $y += 20; // Espacio entre números
            }
            
            // Enviar la imagen al navegador
            imagepng($imagen);
            
            // Liberar memoria
            imagedestroy($imagen);
            
            // Cerrar la sentencia
            $stmt->close();
        }
        
        // Cerrar la conexión a la base de datos
        $conexion->close();
    } else {
        // Si el campo 'celular' no está presente en el JSON
        header('HTTP/1.1 400 Bad Request');
        echo 'El campo "celular" es requerido.';
    }
} else {
    // Si no es una solicitud POST
    header('HTTP/1.1 405 Method Not Allowed');
    echo 'Método de solicitud no permitido.';
}
?>

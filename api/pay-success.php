<?php
// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurar la zona horaria de Colombia
date_default_timezone_set('America/Bogota');

// Requerir el autoload de PHPMailer
require '../../vendor/autoload.php';

// Llamamos a Openpay
use Openpay\Data\Openpay;

// Configuración de Openpay pruebas
$merchantId = 'mwjrpdj8qucqftsfv48r';  // ID del comerciante
$privateKey = 'sk_bdfd9449571d46249bd422c5a0b14a5d';  // Llave privada
$countryCode = 'CO';  // Código del país, en este caso Colombia

// Configuración del modo de producción (falso para pruebas)
Openpay::setProductionMode(false);

// Crear una instancia de Openpay con el ID de comerciante, llave privada y código de país
$openpay = Openpay::getInstance($merchantId, $privateKey, $countryCode);

// Función para validar y sanitizar los datos de entrada
function validar($dato)
{
    $dato = trim($dato); // Eliminar espacios en blanco al inicio y al final
    $dato = htmlspecialchars($dato); // Escapar caracteres especiales HTML
    return $dato; // Devolver el dato validado
}

// Verificar si se recibió el ID de la transacción
try {
    $codigoTransaccion = validar($_GET['id']);
    // Obtén la transacción
    $transaction = $openpay->charges->get($codigoTransaccion);

    // Verifica el estado de la transacción
    if ($transaction->status !== 'completed') {
        echo "<script>
        alert('La transacción no está completada.');
        window.location.href = 'https://eldiadetusuerte.com'; // Redirige a la página principal si la transacción no está completada
        </script>";
        exit();
    }
} catch (Exception $e) {
    // Manejo de errores
    echo "Error: " . $e->getMessage();
    exit();
}

// Verificar si se recibieron los datos necesarios a través de la URL
if (isset(
    $_GET['nombre'],
    $_GET['apellido'],
    $_GET['celular'],
    $_GET['correo'],
    $_GET['ciudad'],
    $_GET['totalPagar'],
    $_GET['cantidadNumeros'],
    $_GET['id']
)) {
    // Obtener y validar los datos de la URL
    $nombre = validar($_GET['nombre']);
    $apellido = validar($_GET['apellido']);
    $celular = validar($_GET['celular']);
    $correo = validar($_GET['correo']);
    $ciudad = validar($_GET['ciudad']);
    $totalPagar = validar($_GET['totalPagar']);
    $cantidadNumeros = validar($_GET['cantidadNumeros']);
    $codigoTransaccion = validar($_GET['id']);
    
    // Puedes realizar cualquier acción adicional aquí, como guardar en la base de datos o enviar una confirmación
    echo "Nombre: $nombre<br>";
    echo "Apellido: $apellido<br>";
    echo "Celular: $celular<br>";
    echo "Correo: $correo<br>";
    echo "Ciudad: $ciudad<br>";
    echo "Total a Pagar: $totalPagar<br>";
    echo "Cantidad de Números: $cantidadNumeros<br>";
    echo "Código de Transacción: $codigoTransaccion<br>";

} else {
    // Si no se encuentran los datos almacenados en la URL, mostramos un mensaje de error
    echo "Error: No se recibieron los datos del formulario.";
    exit();
}
?>

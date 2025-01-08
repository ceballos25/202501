<?php
// Redirigir a HTTPS si la solicitud no es segura
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $redirect_url");
    exit();
}

// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configura el encabezado para que el navegador sepa que estamos enviando JSON
header('Content-Type: application/json');

// Autoload de Composer
require_once '../vendor/autoload.php';

// Obtener datos JSON de la solicitud
$input = file_get_contents('php://input');

// Muestra el contenido JSON recibido para depuración
error_log("JSON recibido: $input");

// Decodifica el JSON en un array de PHP
$data = json_decode($input, true);

// Inicializa la respuesta
$response = array();

// Verifica si la decodificación fue exitosa
if (json_last_error() === JSON_ERROR_NONE) {
    // Obtiene la cantidad de números del JSON
    $cantidadNumeros = isset($data['cantidad_numeros']) ? intval($data['cantidad_numeros']) : null;

    if ($cantidadNumeros !== null && $cantidadNumeros >= 0) {
        // Determina la tarifa por unidad según la cantidad de números
        if ($cantidadNumeros < 3) {
            $tarifaPorNumero = 8000;
        } elseif ($cantidadNumeros < 10) {
            $tarifaPorNumero = 7000;
        } else {
            $tarifaPorNumero = 6000;
        }

        // Calcula el total a pagar
        $totalAPagar = $cantidadNumeros * $tarifaPorNumero;

        // Formatea el total a pagar como una cadena con formato monetario
        $totalAPagarFormateado = '$' . number_format($totalAPagar, 0, ',', '.');

        // Prepara la respuesta en caso de éxito
        $response = array(
            'status' => 'success',
            'total_a_pagar' => $totalAPagarFormateado
        );
    } else {
        // Respuesta en caso de que falten parámetros o la cantidad es inválida
        $response = array(
            'status' => 'error',
            'message' => 'Cantidad de números inválida o faltante'
        );
    }
} else {
    // Respuesta en caso de error en la decodificación JSON
    $response = array(
        'status' => 'error',
        'message' => 'Error en la decodificación del JSON: ' . json_last_error_msg()
    );
}

// Devuelve la respuesta en formato JSON
echo json_encode($response);
?>

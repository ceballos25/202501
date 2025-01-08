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

// Importa la clase Openpay
use Openpay\Data\Openpay;

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
    // Verifica si todos los parámetros necesarios están presentes
    $celular = isset($data['celular']) ? htmlspecialchars(strip_tags($data['celular'])) : null;
    $correo = isset($data['correo']) ? filter_var($data['correo'], FILTER_SANITIZE_EMAIL) : null;
    $nombre = isset($data['nombre']) ? htmlspecialchars(strip_tags($data['nombre'])) : null;
    $ciudad = isset($data['ciudad']) ? htmlspecialchars(strip_tags($data['ciudad'])) : null;
    $cantidadNumeros = isset($data['cantidad_numeros']) ? intval($data['cantidad_numeros']) : null;

    // Elimina los primeros dos dígitos
    $numeroSinPrefijo = substr($celular, 2);

    if ($numeroSinPrefijo && $nombre && $correo && $ciudad && $cantidadNumeros !== null) {
        // Generar un token único
        $token = bin2hex(random_bytes(16));

        // Configuración de Openpay pruebas
        $merchantId = 'mwjrpdj8qucqftsfv48r';  // ID del comerciante
        $privateKey = 'sk_bdfd9449571d46249bd422c5a0b14a5d';  // Llave privada
        $countryCode = 'CO';  // Código del país, en este caso Colombia

        // Configuración del modo de producción (falso para pruebas)
        Openpay::setProductionMode(false);

        // Crear una instancia de Openpay con el ID de comerciante, llave privada y código de país
        $openpay = Openpay::getInstance($merchantId, $privateKey, $countryCode);

        try {
            // Determina la tarifa por unidad según la cantidad de números
            if ($cantidadNumeros < 3) {
                $tarifaPorNumero = 8000;
            } elseif ($cantidadNumeros <= 10) {
                $tarifaPorNumero = 7000;
            } else {
                $tarifaPorNumero = 6000;
            }

            // Calcula el total a pagar usando la tarifa determinada
            $totalAPagar = $cantidadNumeros * $tarifaPorNumero;

            // Formatea el total a pagar como una cadena con formato monetario
            $totalAPagarFormateado = '$' . number_format($totalAPagar, 0, ',', '.');

            // Construir la URL de redirección después del pago
            $redirectUrl = 'https://eldiadetusuerte.com/functions/openpay/pay-success_api_pagar_bot.php?' . http_build_query(array(
                'nombre' => $nombre,
                'celular' => $numeroSinPrefijo,
                'correo' => $correo,
                'ciudad' => $ciudad,
                'totalPagar' => $totalAPagarFormateado,
                'cantidadNumeros' => $cantidadNumeros,
                'token' => $token
            ));

            // Crear array con la información del cliente
            $customer = array(
                'name' => $nombre,
                'email' => $correo,
                'phone_number' => $numeroSinPrefijo,
                'requires_account' => false,
                'customer_address' => array(
                    'city' => $ciudad
                ),
            );

            // Crear array con la información de la transacción PSE
            $pseRequest = array(
                'country' => 'COL',
                'amount' => $totalAPagar,
                'currency' => 'COP',
                'description' => 'Compra Online',
                'order_id' => uniqid(),
                'iva' => '0',
                'redirect_url' => $redirectUrl,
                'customer' => $customer
            );

            // Crear la transacción PSE en Openpay
            $pse = $openpay->pses->create($pseRequest);

            // Obtener la URL de pago de PSE
            $paymentUrl = $pse->redirect_url;

            // Preparar la respuesta en formato JSON
            $response = array(
                'status' => 'success',
                'payment_url' => $paymentUrl
            );

        } catch (Exception $e) {
            // Preparar la respuesta en caso de error
            $response = array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    } else {
        // Respuesta en caso de que falten parámetros
        $response = array(
            'status' => 'error',
            'message' => 'Faltan parámetros necesarios'
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

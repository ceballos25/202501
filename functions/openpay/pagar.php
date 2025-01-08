<?php
// Autoload de Composer
require_once '../../vendor/autoload.php';

session_start();

// Configurar la zona horaria de Colombia
date_default_timezone_set('America/Bogota');
include '../../config/config_bd.php';

$conexion = obtenerConexion();

// Generar un token único
$token = bin2hex(random_bytes(16));

// Almacenar el token y el id en la sesión
$_SESSION['transaction_token'] = $token;
$_SESSION['transaction_id'] = $id; // Suponiendo que $id es el valor de la transacción recibida

// Importa la clase Openpay
use Openpay\Data\Openpay;

// Configuración de Openpay pruebas
// $merchantId = 'mwjrpdj8qucqftsfv48r';  // ID del comerciante
// $privateKey = 'sk_bdfd9449571d46249bd422c5a0b14a5d';  // Llave privada
// $countryCode = 'CO';  // Código del país, en este caso Colombia

// Configuración de Openpay produccion
$merchantId = 'mqzp9plngpmkpgpbldvz';
$privateKey = 'sk_72eec587857e4e47afe602cf8af92a72';
$countryCode = 'CO';

// Configuración del modo de producción (verdadero para producción)
Openpay::setProductionMode(true);

// Crear una instancia de Openpay con el ID de comerciante, llave privada y código de país
$openpay = Openpay::getInstance($merchantId, $privateKey, $countryCode);

try {
    // Obtener datos del formulario enviados por POST y sanitizarlos con htmlspecialchars
    $celular = htmlspecialchars($_POST['celular']);
    $nombre = htmlspecialchars($_POST['nombre']);
    $correo = htmlspecialchars($_POST['correo']);
    $departamento = htmlspecialchars($_POST['departamento']);
    $ciudad = htmlspecialchars($_POST['ciudad']);
    $opciones_boletas = htmlspecialchars($_POST['opciones_boletas']);
    $otroInput = htmlspecialchars($_POST['otroInput']);
    $totalNumeros = htmlspecialchars($_POST['totalNumeros']);
    $totalAPagar = str_replace(['.', ','], ['', ''], htmlspecialchars($_POST['totalPagar']));

    // Verificar si el campo "correo" contiene "******"
    if (strpos($correo, '******') !== false) {
        $correoReal = htmlspecialchars($_POST['correoReal']);
        $correo = $correoReal;
    }

    $nombre = trim($nombre);

    // Separar el nombre completo en nombre y apellido
    $nombreCompleto = explode(" ", $nombre);
    $apellido = array_pop($nombreCompleto); // Obtiene el último elemento del array, que sería el apellido
    $nombre = implode(" ", $nombreCompleto);
    $nombreFinal = ucfirst($nombre) . ' ' . ucfirst($apellido);

    // Generar el código único de transacción
    $codigoTransaccion = substr($celular, -4) . mt_rand(00000, 99999);

    // Establecer el valor de vendido_por
    $vendido_por = "Página Web";

    // Obtener la fecha y hora actual en el formato MySQL
    $fecha_venta = date('Y-m-d H:i:s');

    // Preparar la consulta SQL para insertar en la tabla respaldo
    $consulta_respaldo = $conexion->prepare("INSERT INTO respaldo (nombre_cliente, celular_cliente, correo_cliente, departamento, ciudad, total_numeros, total_pagado, payment_id_mercadopago, external_reference_codigo_transaccion, vendido_por, fecha_venta) VALUES (?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?)");

    // Enlazar los parámetros con los valores
    $consulta_respaldo->bind_param("sssssdssss", $nombreFinal, $celular, $correo, $departamento, $ciudad, $totalNumeros, $totalAPagar, $codigoTransaccion, $vendido_por, $fecha_venta);

    // Ejecutar la consulta
    $consulta_respaldo->execute();

    // Cerrar la consulta
    $consulta_respaldo->close();

    // Construir la URL de redirección después del pago
    $redirectUrl = 'https://eldiadetusuerte.com/functions/openpay/pay-success.php?' . http_build_query(array(
        'nombre' => $nombreFinal,
        'celular' => $celular,
        'correo' => $correo,
        'departamento' => $departamento,
        'ciudad' => $ciudad,
        'totalNumeros' => $totalNumeros,
        'totalPagar' => $totalAPagar,
        'token' => $token
    ));

    // Crear array con la información del cliente
    $customer = array(
        'name' => $nombre,
        'last_name' => $apellido,
        'email' => $correo,
        'phone_number' => $celular, // Asegúrate de no incluir prefijos internacionales
        'requires_account' => false, // No requiere cuenta
        'customer_address' => array(
            'department' => $departamento, // Departamento (Estado)
            'city' => $ciudad // Ciudad
        ),
    );

    // Crear array con la información de la transacción PSE
    $pseRequest = array(
        'country' => 'COL', // País de la transacción
        'amount' => $totalAPagar, // Monto a pagar
        'currency' => 'COP', // Moneda de la transacción
        'description' => 'Compra Online', // Descripción de la transacción
        'order_id' => uniqid(), // ID único de la orden
        'iva' => '0', // IVA (impuesto)
        'redirect_url' => $redirectUrl, // URL a la que redireccionar después del pago
        'customer' => $customer // Información del cliente
    );

    // Crear la transacción PSE en Openpay
    $pse = $openpay->pses->create($pseRequest);

    // Obtener la URL de pago de PSE
    $paymentUrl = $pse->redirect_url;

    // Redireccionar al usuario a la URL de pago de PSE
    header("Location: $paymentUrl");
    exit;

} catch (Exception $e) {
    // Manejo de excepciones en caso de error
    // Redirigir a una página de error con el mensaje de la excepción
    header("Location: https://eldiadetusuerte.com/functions/openpay/pay-error.php?error=" . urlencode($e->getMessage()));
    exit;
}

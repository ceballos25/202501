<?php
// Autoload de Composer
require_once '../../vendor/autoload.php';

session_start();

include '../../config/config_bd.php';

$conexion = obtenerConexion();



// Generar un token único
$token = bin2hex(random_bytes(16));

// Almacenar el token y el id en la sesión
$_SESSION['transaction_token'] = $token;
$_SESSION['transaction_id'] = $id; // Suponiendo que $id es el valor de la transacción recibida

// Importa la clase Openpay
use Openpay\Data\Openpay;

// // Configuración de Openpay
// $merchantId = 'mwjrpdj8qucqftsfv48r';  // ID del comerciante
// $privateKey = 'sk_bdfd9449571d46249bd422c5a0b14a5d';  // Llave privada
// $countryCode = 'CO';  // Código del país, en este caso Colombia

// Configuración de produccion
$merchantId = 'mqzp9plngpmkpgpbldvz';  // ID del comerciante
$privateKey = 'sk_72eec587857e4e47afe602cf8af92a72';  // Llave privada
$countryCode = 'CO';  // Código del país, en este caso Colombia

// Configuración del modo de producción (falso para pruebas)
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

    //procedemos a guardar la la consulta respaldo
    // Genera el código único de transacción
    $codigoTransaccion = substr($celular, -4) . mt_rand(00000, 99999);

    // Establece el valor de vendido_por
    $vendido_por = "Página Web";

    // Obtiene la fecha y hora actual en el formato MySQL
    $fecha_venta = date('Y-m-d H:i:s');

    // Prepara la consulta SQL para insertar en la tabla respaldo
    $consulta_respaldo = $conexion->prepare("INSERT INTO respaldo (nombre_cliente, celular_cliente, correo_cliente, departamento, ciudad, total_numeros, total_pagado, payment_id_mercadopago, external_reference_codigo_transaccion, vendido_por, fecha_venta) VALUES (?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?)");

    // Enlaza los parámetros con los valores
    $consulta_respaldo->bind_param("sssssdssss", $nombreFinal, $celular, $correo, $departamento, $ciudad, $totalNumeros, $totalAPagar, $codigoTransaccion, $vendido_por, $fecha_venta);

    // Ejecuta la consulta
    $consulta_respaldo->execute();

    // Cierra la consulta
    $consulta_respaldo->close();

    // Datos del cliente y la transacción
    $customer = array(
    'name' => $nombre,
    'last_name' => $apellido,
    'phone_number' => $celular,
    'email' => $correo
);

    // Construir la URL de redireccionamiento con el token
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


    $chargeRequest = array(
        'method' => 'card', // Método de pago (en este caso, tarjeta)
        'amount' => $totalAPagar,
        'currency' => 'COP',
        'description' => 'Compra Online',
        'customer' => $customer,
        'order_id' => uniqid(),  // ID único de la orden
        'confirm' => false,
        'redirect_url' => $redirectUrl,  // URL a la que redireccionar después del pago
    );    

    // Realizar el cargo en Openpay con tarjeta
    $charge = $openpay->charges->create($chargeRequest);

    // Obtener la URL de pago de tarjeta (no es necesario en todos los casos)
    $paymentUrl = $charge->payment_method->url;

    // Redirigir al usuario a una página de éxito
    header("Location: " . $charge->payment_method->url);
    exit;

} catch (Exception $e) {
    // Manejo de excepciones en caso de error
    echo 'Error al procesar el pago: ' . $e->getMessage();
    //header("Location: https://test.eldiadetusuerte.com/");
    exit;
}

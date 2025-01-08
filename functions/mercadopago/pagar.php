<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// Incluye el archivo autoload.php del SDK de Mercado Pago
require_once '../../vendor/autoload.php';

include 'config_bd.php';

$conexion = obtenerConexion();


session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validación y obtención de los datos del formulario
    $nombre = isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '';
    $cedula = isset($_POST['cedula']) ? htmlspecialchars($_POST['cedula']) : '';
    $correo = isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : '';
    $celular = isset($_POST['celular']) ? htmlspecialchars($_POST['celular']) : '';
    $departamento = isset($_POST['departamento']) ? htmlspecialchars($_POST['departamento']) : '';
    $ciudad = isset($_POST['ciudad']) ? htmlspecialchars($_POST['ciudad']) : '';
    $opciones_boletas = isset($_POST['opciones_boletas']) ? htmlspecialchars($_POST['opciones_boletas']) : '';
    $otroInput = isset($_POST['otroInput']) ? htmlspecialchars($_POST['otroInput']) : '';
    $totalNumeros = isset($_POST['totalNumeros']) ? intval($_POST['totalNumeros']) : 0;

    // Verifica si los datos son válidos
    if (empty($nombre) || empty($cedula) || empty($correo) || empty($celular) || empty($departamento) || empty($ciudad) || empty($opciones_boletas) || empty($totalNumeros)) {
        echo "Error: Todos los campos del formulario son obligatorios.";
        header('Location: https://eldiadetusuerte.com');
        exit;
    }

    $nombre = trim($nombre);

    // Separar el nombre completo en nombre y apellido
    $nombreCompleto = explode(" ", $nombre);
    $apellido = array_pop($nombreCompleto); // Obtiene el último elemento del array, que sería el apellido
    $nombre = implode(" ", $nombreCompleto);
    $nombreFinal = ucfirst($nombre) . ' ' . ucfirst($apellido);
    
    // Definir el valor de la rifa ($valorRifa) basado en el valor de totalNumeros
    if ($totalNumeros === 3) {
        $totalAPagar = 15000; // Si totalNumeros es exactamente 20, el total a pagar es fijo en 99000
    }
    else if ($totalNumeros === 20) {
        $totalAPagar = 99000; // Si totalNumeros es exactamente 20, el total a pagar es fijo en 99000
    } else if ($totalNumeros < 4) {
        $valorRifa = 6000; // Si totalNumeros es menor que 4, la boleta vale 6000
    } else if ($totalNumeros >= 4 && $totalNumeros < 50) {
        $valorRifa = 5000; // Si totalNumeros está entre 4 (inclusive) y 49 (exclusivo), la boleta vale 5000
    } else if ($totalNumeros >= 50) {
        $valorRifa = 4600; // Si totalNumeros es 50 o mayor, la boleta vale 4600
    }

    // Si no es el caso especial de totalNumeros igual a 20, calcular el total a pagar por el usuario
    if (!isset($totalAPagar)) {
        $totalAPagar = $valorRifa * $totalNumeros;
    }

    // Genera el código único de transacción
    $codigoTransaccion = substr($cedula, -4) . mt_rand(00000, 99999);

    // Establece el valor de vendido_por
    $vendido_por = "Página Web";

    // Obtiene la fecha y hora actual en el formato MySQL
    $fecha_venta = date('Y-m-d H:i:s');

   // Prepara la consulta SQL para insertar en la tabla respaldo
    $consulta_respaldo = $conexion->prepare("INSERT INTO respaldo (nombre_cliente, cedula_cliente, correo_cliente, celular_cliente, departamento, ciudad, total_numeros, total_pagado, payment_id_mercadopago, external_reference_codigo_transaccion, vendido_por, fecha_venta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?)");

    // Enlaza los parámetros con los valores
    $consulta_respaldo->bind_param("ssssssdssss", $nombre, $cedula, $correo, $celular, $departamento, $ciudad, $totalNumeros, $totalAPagar, $codigoTransaccion, $vendido_por, $fecha_venta);

    // Ejecuta la consulta
    $consulta_respaldo->execute();

    // Cierra la consulta
    $consulta_respaldo->close();

    MercadoPago\SDK::setAccessToken("APP_USR-5629971001463341-032121-4eaf717f3023ca445a54d50d53f647c2-1736789621"); //la de contingencia                                
    // Crea una preferencia de pago
    $preference = new MercadoPago\Preference();
   // $preference->binary_mode = true;
    
    // Crea un artículo para la preferencia
    $item = new MercadoPago\Item();
    $item->title = 'Producto';
    $item->quantity = 1;
    $item->unit_price = $totalAPagar; // Utiliza el total a pagar recibido del formulario

    // Agrega el artículo a la preferencia
    $preference->items = array($item);

    // Agrega los datos del comprador
    $payer = new MercadoPago\Payer();
    $payer->first_name = $nombre; // Asigna el nombre
    $payer->last_name = $apellido; // Asigna el apellido
    $payer->email = $correo; // Asigna el correo electrónico    
    $payer->identification = $cedula;
    $payer->phone =  $celular;

    // Asigna el código único de transacción como external_reference
    $preference->external_reference = $codigoTransaccion;
    
    //FUNCIONA NO LO MUEVAS
    $preference->payment_methods = array(
    "excluded_payment_types" => array(
        array("id" => "ticket")
    ),
    "excluded_payment_methods" => array(
        array("id" => "cash"),
        array("id" => "atm"),
        array("id" => "bank_transfer")
    )
);



    
    $preference->notification_url = "https://eldiadetusuerte.com/functions/mercadopago/alertas-mercado-pago.php";
    

    // URL de respuestas deben coincidir con las del hosting
    $base_url = "https://localhost/rifas/functions/mercadopago/";
    $redirect_url_success = $base_url . "pay-success.php"; // URL de redirección de éxito

    // Agrega los datos del formulario a la URL de redirección de éxito
    $redirect_url_success .= '?nombre=' . urlencode($nombreFinal);
    $redirect_url_success .= '&cedula=' . urlencode($cedula);
    $redirect_url_success .= '&correo=' . urlencode($correo);
    $redirect_url_success .= '&celular=' . urlencode($celular);
    $redirect_url_success .= '&departamento=' . urlencode($departamento);
    $redirect_url_success .= '&ciudad=' . urlencode($ciudad);
    $redirect_url_success .= '&totalNumeros=' . urlencode($totalNumeros);    
    $redirect_url_success .= '&totalApagar=' . urlencode($totalAPagar);
    $redirect_url_success .= '&codigoTransaccion=' . urlencode($codigoTransaccion);
    $redirect_url_success .= '&external_reference_codigo_transaccion=' . urlencode($external_reference_codigo_transaccion);

    // Define las URL de redirección en el array back_urls
    $preference->back_urls = array(
        "success" => $redirect_url_success, // Página a la que se redirigirá si la transacción es exitosa
        "failure" => $base_url . "pay-error.php", // Página a la que se redirigirá si la transacción falla
        "pending" => $base_url . "pay-pending.php" // Página a la que se redirigirá si la transacción está pendiente
    );
    //aqui especificamos que lo redirecciones de manera automática
    $preference->auto_return = "approved";
    

    // Guarda la preferencia en Mercado Pago
    $preference->save();

    // Redirige al usuario a la página de pago de Mercado Pago
    header('Location: ' . $preference->init_point); // Utiliza sandbox_init_point si estás en modo de pruebas
    exit();
} else {
    // Si no se recibieron los datos por POST, redirige a una página de error o muestra un mensaje
    // header('Location: https://localhost/rifas' ); //se debe cambiar por la des servidor
    echo '<script>alert("Algo Salió mal, por vuelva a intentarlo, si el error persiste, contáctenos.")</script>;';
    header('Location: https://localhost/rifas/' ); //se debe cambiar por la des servidor
 }
?>

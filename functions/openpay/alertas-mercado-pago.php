<?php
// Requiere el autoload de PHPMailer
require '../../vendor/autoload.php';

require_once '../../config/config_bd.php';

// Incluye la librería PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Este script manejará las notificaciones de MercadoPago

// Verificar que el método de solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Leer el cuerpo de la solicitud
    $json = file_get_contents('php://input');
    // Convertir el JSON en un array asociativo
    $data = json_decode($json, true);

    // Verificar si la notificación es de un pago aprobado
    if ($data['action'] === 'payment.created' && $data['data']['status'] === 'approved') {
        // Obtener los detalles del pago
        $payment_id = $data['data']['id'];
        $payment_status = $data['data']['status'];
        $payment_amount = $data['data']['transaction_amount'];
        $payment_currency = $data['data']['currency_id'];



// Crear una nueva instancia de PHPMailer
$mail = new PHPMailer(true);

try {
    // Configurar el servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@eldiadetusuerte.com';
    $mail->Password = 'Colombia2024*';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Configurar el remitente y el destinatario
    $mail->setFrom('info@eldiadetusuerte.com', 'El día de Tu Suerte');
    $mail->addAddress("ceballosmarincristiancamilo@gmail.com");
    $mail->addBCC('info@eldiadetusuerte.com', 'Copia oculta');

    // Configurar el contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Confirmación: #' . $external_reference . '-' . $id_venta;
    $mail->Body    = 'Hola,<br><br>Este es un correo de confirmación.';

    // Enviar el correo
    $mail->send();
    echo 'El correo se ha enviado correctamente.';
} catch (Exception $e) {
    echo 'Hubo un error al enviar el correo: ', $mail->ErrorInfo;
}
    }
} else {
    // Si la solicitud no es POST, responder con un código de estado 405 (Método no permitido)
    http_response_code(405);
    echo "Método no permitido";
}
?>

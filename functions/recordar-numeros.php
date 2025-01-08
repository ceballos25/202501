<?php

// Requerir el autoload de PHPMailer
require '../vendor/autoload.php';

// Usar las clases de la librería PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Obtener los datos del formulario (correo y números vendidos)
$correo = isset($_POST['correo']) ? $_POST['correo'] : '';
$numeros_vendidos = isset($_POST['numeros']) ? $_POST['numeros'] : array();

// Función para enviar el correo electrónico con los números vendidos
function enviarCorreo($correo, $numeros_vendidos) {
    try {
        // Generamos el HTML para los números vendidos
        $numeros_html = '';
        $numeros_html .= '<div style="display: flex; flex-wrap: wrap;">';
        $contador_numeros = 1; // Inicializamos el contador de números

        foreach ($numeros_vendidos as $numero) {
            $numeros_html .= '<div style="display: flex; margin:5px">';
            $numeros_html .= '<span style="background-color: #EFB810; color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; margin-right: 5px; border-style: dotted;">' . $numero . '</span>';
            $numeros_html .= '</div>';

            // Agregamos un salto de línea después de cada conjunto de 5 números
            if ($contador_numeros % 5 == 0) {
                $numeros_html .= '</div><div style="display: flex; flex-wrap: wrap;">';
            }

            $contador_numeros++;
        }

        $numeros_html .= '</div>'; // Cierre del contenedor final

// Configuramos el servidor SMTP y creamos una instancia de PHPMailer
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->isHTML(true);
$mail->CharSet = 'UTF-8'; // Configurar la codificación de caracteres
$mail->Body = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        p {
            color: #555;
            line-height: 1.6;
        }
        .ticket {
            background-color: #ffc107;
            color: #333;
            border-radius: 5px;
            padding: 8px 12px;
            margin-bottom: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align: center"></h2>
        <img src="https://eldiadetusuerte.com/images/agradecimientov6.png" width="50%" alt="Imagen de agradecimiento" style="display: block; margin: 0 auto 20px; width: 100%;" >
        <p>Tienes: ' . count($numeros_vendidos) . ' oportunidades para ganar.</p>
        ' . $numeros_html . '
        <p style="margin:10px">Te deseamos mucha suerte.</p>

        <p>Únete a nuestro canal de WhatsApp para que no te pierdas los detalles del sorteo.
        <a href="https://chat.whatsapp.com/L8cLyUtv64GCdgb1GaxEm2" style="text-decoration: none;">
        <button style="padding: 5px 5px; background-color: #25d366; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer;">
            Ir al canal
        </button>
    </a>
    </p>
    </div>
</body>
</html>';

$mail->Host = 'smtp.hostinger.com'; // Ajusta el servidor SMTP
$mail->SMTPAuth = true;
$mail->Username = 'info@eldiadetusuerte.com'; // Ajusta el nombre de usuario SMTP
$mail->Password = 'Colombia2024*'; // Ajusta la contraseña SMTP
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Ajusta la seguridad SMTP (STARTTLS recomendado)
$mail->Port = 587; // Ajusta el puerto SMTP
$mail->Subject = 'Recordatorio';
$mail->setFrom('info@eldiadetusuerte.com', 'El día de Tu Suerte');
$mail->addAddress($correo);
$mail->addBCC('info@eldiadetusuerte.com', 'Copia oculta');

// Enviamos el correo electrónico
$mail->send();

        return true; // Retorna true si el correo se envió correctamente
    } catch (Exception $e) {
        echo "Error al enviar el correo electrónico: {$mail->ErrorInfo}";
        return false; // Retorna false si hubo un error al enviar el correo
    }
}

// Llama a la función para enviar el correo con los números vendidos
$enviado = enviarCorreo($correo, $numeros_vendidos);

// Devolver los datos en formato JSON
if ($enviado) {
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('success' => false));    
}

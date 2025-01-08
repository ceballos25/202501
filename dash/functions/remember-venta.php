<?php
date_default_timezone_set('America/Bogota');
include '../../config/config_bd.php';

require '../../vendor/autoload.php';

// // Incluye la librería PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Verificar si se recibió un ID de venta válido
if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de venta no válido.']);
    exit;
}

// Obtener el ID de la venta a eliminar
$id = $_POST['id'];
$correo_cliente = $_POST['correo_cliente'];

$fecha = date('d-m-Y H:i:s');

// Obtener una conexión a la base de datos
$conn = obtenerConexion();

// Verificar si se pudo obtener la conexión
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos.']);
    exit;
}

// Iniciar una transacción
$conn->begin_transaction();

try {
    // 1. Obtener los números vendidos relacionados con la venta
    $sql = "SELECT numero FROM numeros_vendidos WHERE id_venta = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $numeros = [];
    while ($row = $result->fetch_assoc()) {
        $numeros[] = $row['numero'];
    }
    $stmt->close();

    if (empty($numeros)) {
        throw new Exception("No se encontraron números vendidos para la venta con ID $id.");
    }


    // Confirmar la transacción
    $conn->commit();
    echo json_encode(['success' => true]);

    $numeros_html = '<div style="display: flex; flex-wrap: wrap;">';
    foreach ($numeros as $key => $numero) {
        if ($key > 0 && $key % 10 === 0) {
            $numeros_html .= '</div><div style="display: flex; flex-wrap: wrap;">';
        }
        $numeros_html .= '<span style="background-color: #EFB810; color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; margin-right: 5px; border-style: dotted;">' . $numero . '</span>';
    }
    $numeros_html .= '</div>';

    // Configura el servidor SMTP y crea una instancia de PHPMailer
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    // Contenido HTML del correo
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
        <span style="float: right"><b>Fecha: ' . $fecha . '</b></span>
        <h2 style="text-align: center; margin-top:40px;"></h2>
        <img src="https://eldiadetusuerte.com/images/agradecimientov5.png" alt="Imagen de agradecimiento" style="display: block; margin: 0 auto 20px; width: 100%;" >
            <p>Estimado cliente,</p>
            <p>A continuación encontrará los números que usted adquirió con nosotros: <br></p>
            <div class="badge-container">' . $numeros_html . '</div>
            <p>Atentamente, <br></p>
            <p>El día de Tu Suerte.<br></p>
        </div>
    </body>
    </html>';

    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@eldiadetusuerte.com';
    $mail->Password = 'Colombia2024*';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Subject = 'Recordatorio Venta N°: '.$id.'';
    $mail->Port = 587;
    $mail->setFrom('info@eldiadetusuerte.com', 'El dia de Tu Suerte');
    $mail->addAddress($correo_cliente);
    $mail->addBCC('info@eldiadetusuerte.com', 'Copia oculta');


    // Envía el correo electrónico
    $mail->send();

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Cerrar la conexión
$conn->close();
?>

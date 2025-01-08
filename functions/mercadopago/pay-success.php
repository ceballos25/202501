<?php

date_default_timezone_set('America/Bogota'); // Definimos la zona horaria de Colombia
session_start();

require_once '../../config/config_bd.php';

// Incluimos la librería PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Requerimos el autoload de PHPMailer
require '../../vendor/autoload.php';

// Intentamos establecer la conexión con la base de datos
$conexion = obtenerConexion();

$error = false;

if ($conexion) {
    // Si se recibieron los datos almacenados en la URL
    if(isset($_GET['nombre'], $_GET['cedula'], $_GET['correo'], $_GET['celular'], $_GET['departamento'], $_GET['ciudad'], $_GET['totalNumeros'], $_GET['totalApagar'], $_GET['codigoTransaccion'])) {
        // Obtén los datos de la URL y almacénalos en variables
        $nombre = validar($_GET['nombre']);
        $cedula = validar($_GET['cedula']);
        $correo = validar($_GET['correo']);
        $celular = validar($_GET['celular']);
        $departamento = validar($_GET['departamento']);
        $ciudad = validar($_GET['ciudad']);
        $totalNumeros = validar($_GET['totalNumeros']);
        $totalApagar = validar($_GET['totalApagar']);
        $codigoTransaccion = validar($_GET['codigoTransaccion']);
        
        $payment_id = isset($_GET['payment_id']) ? $_GET['payment_id'] : '';
        
        $rifa = 6000;

        // Continuar con el procesamiento de los datos
        $vendido_por = "Página Web";
        
        try {
            // Iniciamos una transacción
            $conexion->begin_transaction();

            // Preparamos la consulta SQL para seleccionar números disponibles de manera aleatoria
            $consulta_numeros = $conexion->prepare("SELECT id, numero FROM numeros ORDER BY RAND() LIMIT ?");
            $consulta_numeros->bind_param("i", $totalNumeros);
            $consulta_numeros->execute();
            $resultado_numeros = $consulta_numeros->get_result();

            // Verificamos si hay suficientes números disponibles
            if ($resultado_numeros->num_rows >= $totalNumeros) {
                // Calculamos el total pagado (simulado)

                //$total_pagado = $rifa * $totalNumeros;
                
                // Fecha de la venta (puedes usar la fecha actual)
                $fecha_venta = date('Y-m-d H:i:s');
                
                // Preparamos la consulta para insertar la venta
                $consulta_venta = $conexion->prepare("INSERT INTO ventas (nombre_cliente, cedula_cliente, correo_cliente, celular_cliente, departamento, ciudad, total_numeros, total_pagado, payment_id_mercadopago, external_reference_codigo_transaccion, vendido_por, fecha_venta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


                // Vincula los parámetros con los valores correspondientes
                $consulta_venta->bind_param("ssssssddssss", $nombre, $cedula, $correo, $celular, $departamento, $ciudad, $totalNumeros, $totalApagar, $payment_id, $codigoTransaccion, $vendido_por, $fecha_venta);
                $consulta_venta->execute();

                // Verificamos si la inserción de la venta fue exitosa
                if ($consulta_venta->affected_rows > 0) {
                    // Obtenemos el ID de la venta insertada
                    $id_venta = $conexion->insert_id;

                    // Preparamos la consulta SQL para insertar los números vendidos
                    $consulta_numeros_vendidos = $conexion->prepare("INSERT INTO numeros_vendidos (id_venta, numero) VALUES (?, ?)");

                    // Preparamos la consulta SQL para eliminar los números vendidos de la tabla numeros
                    $consulta_eliminar_numeros = $conexion->prepare("DELETE FROM numeros WHERE id = ?");
                             
                    // Array para almacenar los números vendidos
                    $numeros_vendidos = array();

                    // Iteramos sobre los números seleccionados y los insertamos en la tabla numeros_vendidos
                    while ($fila = $resultado_numeros->fetch_assoc()) {
                        $id_numero = $fila['id'];
                        $numero = $fila['numero'];

                        // Insertamos el número vendido en la tabla numeros_vendidos
                        $consulta_numeros_vendidos->bind_param("is", $id_venta, $numero);
                        $consulta_numeros_vendidos->execute();

                        // Almacenamos el número vendido en el array
                        $numeros_vendidos[] = $numero;

                        // Eliminamos el número vendido de la tabla numeros
                        $consulta_eliminar_numeros->bind_param("i", $id_numero);
                        $consulta_eliminar_numeros->execute();

                        // procedemos a eliminar de la sección de respaldo
                        $consulta_eliminar_respaldo = $conexion->prepare("DELETE FROM respaldo WHERE cedula_cliente = ?");
                        $consulta_eliminar_respaldo->bind_param("i", $cedula); // Enlaza la cedula
                        $consulta_eliminar_respaldo->execute(); // Ejecuta la consulta                           
                    }

                    // Confirmamos la transacción
                    $conexion->commit();

                    // Mostramos el modal con los números (esto debería manejarse en el frontend)
                    echo '<script>';
                    echo '$(document).ready(function() { ';
                    echo 'var numeros_vendidos = ' . json_encode($numeros_vendidos) . ';';
                    echo 'var numerosHTML = "";';
                    echo 'numeros_vendidos.forEach(function(numero) { ';
                    echo 'numerosHTML += "<span class=\"badge badge-warning p-2 mt-2\" style=\"margin-right: 10px;\">" + numero + "</span>";';
                    echo '});';
                    echo '$("#numeros_vendidos_container").html(numerosHTML);';
                    echo '$("#staticBackdrop").modal("show");';
                    echo '});';
                    echo '</script>';

                    // Enviamos el correo electrónico al cliente
                    enviarCorreo($correo, $codigoTransaccion, $id_venta, $nombre, $numeros_vendidos);
                } else {
                    // Si ocurre un error al insertar la venta, revertimos la transacción
                    $conexion->rollback();
                    echo "Error: No se pudieron insertar los datos de la venta en la base de datos.";
                }
            } else {
                // Si no hay suficientes números disponibles, revertimos la transacción
                $conexion->rollback();
                echo "Error: No hay suficientes números disponibles para realizar la venta.";
            }

            // Cerramos las consultas
            $consulta_numeros_vendidos->close();
            $consulta_eliminar_numeros->close();
            $consulta_numeros->close();
        } catch (Exception $e) {
            // Si se produce una excepción, revertimos la transacción y mostramos un mensaje de error
            $conexion->rollback();
            echo "Error al ejecutar la consulta: " . $e->getMessage();
        }
    } else {
        // Si no se encuentran los datos almacenados en la URL, mostramos un mensaje de error
        echo "Error: No se recibieron los datos del formulario.";
    }
} else {
    // Si la conexión a la base de datos no se estableció correctamente, mostramos un mensaje de error
    echo "Error de conexión a la base de datos.";
}

// Cerramos la conexión
$conexion->close();

// Función para validar y sanitizar los datos
function validar($dato) {
    // Aplicar la validación y sanitización necesaria
    $dato = trim($dato); // Eliminar espacios en blanco al inicio y al final
    $dato = htmlspecialchars($dato); // Escapar caracteres especiales HTML
    // Aquí puedes agregar más validaciones según tus necesidades
    return $dato;
}

// Función para enviar el correo electrónico al cliente
function enviarCorreo($correo, $codigoTransaccion, $id_venta, $nombre, $numeros_vendidos) {
    try {
        // Generamos el HTML para los números vendidos
        $numeros_html = '';
        $numeros_html .= '<div style="display: flex; flex-wrap: wrap;">';
        $contador_numeros = 1; // Inicializamos el contador de números

        foreach ($numeros_vendidos as $numero) {
            $numeros_html .= '<div style="display: flex; margin:5px">';
            $numeros_html .= '<span style="color: #fff; background-color: #000; padding: 5px; border-radius: 50%; font-weight: bold; margin-right: 5px;">' . $numero . '</span>';
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
                <h2 style="text-align: center">' . $nombre . '</h2>
                <img src="https://eldiadetusuerte.com/images/agradecimiento.png" width="50%" alt="Imagen de agradecimiento" style="display: block; margin: 0 auto 20px; width: 100%;" >
                <p>Queremos agradecerte por la compra, y esperamos que la suerte este de tu lado. A continuacion, los numeros generados por nuestro sistema.</p>
                <p><b>Recuerda:</b> ¡Juega este 31 de enero 🗓️ por la LOT de Medellin!.</p>
                <p>Hemos enviado una copia de esta informacion a tu correo electronico para tu referencia y conveniencia.</p>
                <p>Tienes: ' . count($numeros_vendidos) . ' oportunidades para ganar.</p>
                ' . $numeros_html . '
                <p style="margin:10px">Te deseamos mucha suerte.</p>

                <p>Unete a nuestro canal de WhatsApp para que no te pierdas la fecha del Sorteo.
                <a href="https://chat.whatsapp.com/L8cLyUtv64GCdgb1GaxEm2" style="text-decoration: none;">
                <button style="padding: 5px 5px; background-color: #25d366; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer;">
                    Ir al canal
                </button>
            </a>
            </p>
            </div>
        </body>
        </html>';

        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@eldiadetusuerte.com';
        $mail->Password = 'Colombia2024*';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Subject = 'Confirmacion: #' . $codigoTransaccion . '-' . $id_venta;
        $mail->Port = 587;
        $mail->setFrom('info@eldiadetusuerte.com', 'El dia de Tu Suerte');
        $mail->addAddress($correo);
        $mail->addBCC('info@eldiadetusuerte.com', 'Copia oculta');

        // Enviamos el correo electrónico
        $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar el correo electrónico: {$mail->ErrorInfo}";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación | El día de Tu Suerte</title>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link rel="stylesheet" href="https://kit.fontawesome.com/cf96aaa9b2.js" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@tsparticles/confetti@3.0.3/tsparticles.confetti.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="../../dash/css/agradecimiento.css">
    <script>
        const end = Date.now() + 15 * 100;

        // Go Buckeyes!
        const colors = ["#FFA500", "#0f0"];

        (function frame() {
            confetti({
                particleCount: 2,
                angle: 60,
                spread: 55,
                origin: { x: 0 },
                colors: colors,
            });

            confetti({
                particleCount: 2,
                angle: 120,
                spread: 55,
                origin: { x: 1 },
                colors: colors,
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        })();
    </script>
</head>
<body>
<div class="container mt-1" style="background-color: #fff;">
    <div class="mt-2">
        <div class="card-header text-center" style="background-color: #fff;"></div>
        <h4 class="d-flex justify-content-center my-4"><b><?php echo $nombre; ?></b></h4>
        <div class="">
            <div class="card mb-3">
                <img src="https://eldiadetusuerte.com/images/agradecimiento.png" class="card-img-top" alt="imagen-agradecimiento">
            </div>
            <ul class="list-group">
                <li class="list-group-item">Queremos agradecerte por la compra, y esperamos que la suerte este de tu lado. 🍀</li>
                <p class="list-group-item">A continuación los números generados por nuestro sistema:
                    <b>Recuerda:</b> ¡Juega este 31 de enero 🗓️ por la LOT de Medellin!. </p>
                <p style="margin-left:15px">Anunciaremos la fecha del sorteo en nuestro sitio web y redes sociales.</p>
                <p style="margin-left:15px">Hemos enviado una copia de esta información a tu correo electrónico para tu referencia y conveniencia.</p>
                <li class="list-group-item">
                    <i class="fas fa-ticket-alt me-3"></i> <strong>Estos son tus números:</strong>
                    <div id="numeros_vendidos_container">
                        <?php
                        // Generar badges para cada número vendido
                        foreach ($numeros_vendidos as $numero) {
                            echo '<span class="badge badge-warning p-2 mt-2 text-large" style="margin-right: 10px;">' . $numero . '</span>';
                        }
                        ?>
                    </div>
                    <!-- Aquí se mostrarán los badges -->
                </li>
            </ul>

            <p>Únete a nuestro canal de WhatsApp para que no te pierdas la fecha del Sorteo.
                <a href="https://chat.whatsapp.com/L8cLyUtv64GCdgb1GaxEm2" style="text-decoration: none;">
                <button style="padding: 5px 5px; background-color: #25d366; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer;">
                    Ir al canal
                </button>
            </a>
            </p>

            <div class="text-center mt-2">
                <button onclick="descargarComoPDF()" class="btn btn-sm btn-success">Descargar PDF</button>
                <a href="/" class="btn btn-sm btn-secondary">Regresar</a>
            </div>
            <?php
            // echo "Nombre: $nombre<br>";
            // echo "Cédula: $cedula<br>";
            // echo "Correo: $correo<br>";
            // echo "Celular: $celular<br>";
            // echo "Departamento: $departamento<br>";
            // echo "Ciudad: $ciudad<br>";
            // echo "Total de Números: $totalNumeros<br>";
            // echo "Total a Pagar: $totalApagar<br>";
            // echo "Código de Transacción: $codigoTransaccion<br>";
            ?>
        </div>
    </div>
</div>
<script>
    function descargarComoPDF() {
        const element = document.querySelector('.container');
        const options = {
            filename: 'agradecimiento.pdf', // Nombre del archivo PDF
            html2canvas: { // Configuración para html2canvas
                scale: 3, // Escala para mejorar la calidad de la imagen
            },
            jsPDF: { // Configuración para jsPDF
                format: 'letter', // Formato de página: 'letter', 'legal', 'tabloid', etc.
                orientation: 'portrait', // Orientación del documento: 'portrait' o 'landscape'
                unit: 'px', // Unidad de medida: 'mm', 'cm', 'in', 'px'
                hotfixes: ['px_scaling'], // Corrección para el escalado en píxeles
                format: [1000, 1000],
            }
        };
        html2pdf().set(options).from(element).save();
    }
</script>
<?php
// Eliminar todas las variables tipo POST
foreach ($_POST as $key => $value) {
    unset($_POST[$key]);
}
?>
</body>
</html>

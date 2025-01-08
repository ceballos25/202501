<?php
// Mostrar todos los errores
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Configurar la zona horaria de Colombia
date_default_timezone_set('America/Bogota');

// Iniciar la sesión
session_start();

// Requerir el autoload de PHPMailer
require '../../vendor/autoload.php';
// Incluir el archivo de configuración de la base de datos
require_once '../../config/config_bd.php';

// Usar las clases de la librería PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Llamamos aopenpay
use Openpay\Data\Openpay;

// Configuración de Openpay producción
$merchantId = 'mqzp9plngpmkpgpbldvz';
$privateKey = 'sk_72eec587857e4e47afe602cf8af92a72';
$countryCode = 'CO';

// Configuración de Openpay pruebas
// $merchantId = 'mwjrpdj8qucqftsfv48r';  // ID del comerciante
// $privateKey = 'sk_bdfd9449571d46249bd422c5a0b14a5d';  // Llave privada
// $countryCode = 'CO';  // Código del país, en este caso Colombia

// Configuración del modo de producción (verdadero para producción)
Openpay::setProductionMode(true);

// Crear una instancia de Openpay con el ID de comerciante, llave privada y código de país
$openpay = Openpay::getInstance($merchantId, $privateKey, $countryCode);

// Intentar establecer la conexión con la base de datos
$conexion = obtenerConexion();

// Variable para controlar errores
$error = false;

// Función para validar y sanitizar los datos de entrada
function validar($dato)
{
    $dato = trim($dato); // Eliminar espacios en blanco al inicio y al final
    $dato = htmlspecialchars($dato); // Escapar caracteres especiales HTML
    return $dato; // Devolver el dato validado
}

// validamos si está la transacciona probada
try {
    $codigoTransaccion = validar($_GET['id']);
    // Obtén la transacción
    $transaction = $openpay->charges->get($codigoTransaccion);

    // Verifica el estado de la transacción
    if ($transaction->status !== 'completed') {
        echo "<script>
        alert('La transacción no está completada.');
        window.location.href = 'https://eldiadetusuerte.com'; // Reemplaza 'index.php' con la URL de tu índice o la página a la que quieras redirigir
      </script>";
        exit();
    }
} catch (Exception $e) {
    // Manejo de errores
    echo "Error: " . $e->getMessage();
    exit();
}

// Verificar si la conexión a la base de datos fue exitosa
if ($conexion) {
    // Verificar si se recibieron los datos necesarios a través de la URL
    if (isset($_GET['nombre'], $_GET['celular'], $_GET['correo'], $_GET['departamento'], $_GET['ciudad'], $_GET['totalNumeros'], $_GET['totalPagar'], $_GET['id'])) {

        // Verificar si el token de la URL coincide con el token de la sesión
        if (isset($_GET['token']) && isset($_SESSION['transaction_token']) && $_GET['token'] === $_SESSION['transaction_token']) {
            // El token es válido, obtener el id de la sesión
            $id = $_SESSION['transaction_id'];

            // Limpiar el token de la sesión después de usarlo
            unset($_SESSION['transaction_token']);
            unset($_SESSION['transaction_id']);
        } else {
            // Token inválido o no encontrado
            echo '<div style="text-align: center; font-family: Arial, sans-serif; font-size: 20px; line-height: 1.6;">
            <p style="margin-bottom: 20px;">¿Tienes problemas con tu pago o tus números? ¡Estamos aquí para ayudarte!</p>
            <p style="margin-bottom: 20px;">Contacta con nuestro equipo especializado para recibir asistencia inmediata:</p>
            <p><a href="https://wa.link/iktc4e" target="_blank" style="color: #EFB810; text-decoration: underline; font-weight: bold;">Contáctanos en WhatsApp </a></p>
          </div> ';
            die('Error de validación: token inválido o no encontrado.');
        }

        // Obtener y validar los datos de la URL
        $nombre = validar($_GET['nombre']);
        $celular = validar($_GET['celular']);
        $correo = validar($_GET['correo']);
        $departamento = validar($_GET['departamento']);
        $ciudad = validar($_GET['ciudad']);
        $totalNumeros = validar($_GET['totalNumeros']);
        $totalPagar = validar($_GET['totalPagar']);
        $codigoTransaccion = validar($_GET['id']);
        $estadoTransaccion = $_GET['estadoTransaccion'] ?? '';


        // Obtener el ID de pago si está disponible
        $payment_id = "Página Web";

        // Continuar con el procesamiento de los datos
        $vendido_por = "Página Web";

        try {
            // Iniciar una transacción
            $conexion->begin_transaction();

            // Verificar si el cliente ya existe en la tabla de clientes
            $consulta_cliente = $conexion->prepare("SELECT id_cliente FROM clientes WHERE celular_cliente = ?");
            $consulta_cliente->bind_param("s", $celular);
            $consulta_cliente->execute();
            $resultado_cliente = $consulta_cliente->get_result();

            // Variable para almacenar el ID del cliente
            $id_cliente = null;

            // Verificar si el cliente ya existe en la tabla de clientes
            if ($resultado_cliente->num_rows > 0) {
                // Si el cliente ya existe, obtener su ID
                $fila_cliente = $resultado_cliente->fetch_assoc();
                $id_cliente = $fila_cliente['id_cliente'];
            } else {
                // Si el cliente no existe, insertar el cliente en la tabla de clientes
                $consulta_insertar_cliente = $conexion->prepare("INSERT INTO clientes (nombre_cliente, celular_cliente, correo_cliente, departamento_cliente, ciudad_cliente) VALUES (?, ?, ?, ?, ?)");
                $consulta_insertar_cliente->bind_param("sssss", $nombre, $celular, $correo, $departamento, $ciudad);
                $consulta_insertar_cliente->execute();

                // Verificar si se insertó correctamente el cliente
                if ($consulta_insertar_cliente->affected_rows > 0) {
                    // Si se insertó correctamente, obtener el ID del cliente insertado
                    $id_cliente = $conexion->insert_id;
                } else {
                    // Si hay algún error al insertar el cliente, revertir la transacción y mostrar un mensaje de error
                    $conexion->rollback();
                    echo "Error: No se pudo insertar el cliente en la base de datos.";
                    exit();
                }
            }

            // Preparar la consulta SQL para seleccionar números disponibles de manera aleatoria
            $consulta_numeros = $conexion->prepare("SELECT id, numero FROM numeros ORDER BY RAND() LIMIT ?");
            $consulta_numeros->bind_param("i", $totalNumeros);
            $consulta_numeros->execute();
            $resultado_numeros = $consulta_numeros->get_result();

            // Verificar si hay suficientes números disponibles
            if ($resultado_numeros->num_rows >= $totalNumeros) {
                // Fecha de la venta (usar la fecha actual)
                $fecha_venta = date('Y-m-d H:i:s');

                // Preparar la consulta para insertar la venta
                $consulta_venta = $conexion->prepare("INSERT INTO ventas
                                                        (id_cliente, total_numeros, total_pagado, payment_id_mercadopago, external_reference_codigo_transaccion, vendido_por, fecha_venta)
                                                        VALUES (?, ?, ?, ?, ?, ?, ?)");
                $consulta_venta->bind_param("iisssss", $id_cliente, $totalNumeros, $totalPagar, $payment_id, $codigoTransaccion, $vendido_por, $fecha_venta);
                $consulta_venta->execute();

                // Marcar el formulario como procesado en la sesión
                $_SESSION['formulario_procesado'] = true;

                // Verificar si la inserción de la venta fue exitosa
                if ($consulta_venta->affected_rows > 0) {
                    // Obtener el ID de la venta insertada
                    $id_venta = $conexion->insert_id;

                    // Preparar la consulta SQL para insertar los números vendidos
                    $consulta_numeros_vendidos = $conexion->prepare("INSERT INTO numeros_vendidos (id_venta, numero) VALUES (?, ?)");

                    // Preparar la consulta SQL para eliminar los números vendidos de la tabla numeros
                    $consulta_eliminar_numeros = $conexion->prepare("DELETE FROM numeros WHERE id = ?");

                    // Array para almacenar los números vendidos
                    $numeros_vendidos = array();

                    // Iterar sobre los números seleccionados y insertarlos en la tabla numeros_vendidos
                    while ($fila = $resultado_numeros->fetch_assoc()) {
                        $id_numero = $fila['id'];
                        $numero = $fila['numero'];

                        // Insertar el número vendido en la tabla numeros_vendidos
                        $consulta_numeros_vendidos->bind_param("is", $id_venta, $numero);
                        $consulta_numeros_vendidos->execute();

                        // Almacenar el número vendido en el array
                        $numeros_vendidos[] = $numero;

                        // Eliminar el número vendido de la tabla numeros
                        $consulta_eliminar_numeros->bind_param("i", $id_numero);
                        $consulta_eliminar_numeros->execute();

                        // Eliminar de la tabla de respaldo
                        $consulta_eliminar_respaldo = $conexion->prepare("DELETE FROM respaldo WHERE celular_cliente = ?");
                        $consulta_eliminar_respaldo->bind_param("i", $celular);
                        $consulta_eliminar_respaldo->execute();
                    }

                    // Confirmar la transacción
                    $conexion->commit();

                    // Mostrar el modal con los números (esto debería manejarse en el frontend)
                    echo '<script>';
                    echo '$(document).ready(function() { ';
                    echo 'var numeros_vendidos = ' . json_encode($numeros_vendidos) . ';';
                    echo 'var numerosHTML = "";';
                    echo 'numeros_vendidos.forEach(function(numero) { ';
                    echo 'numerosHTML += "<span class=\"\" style=\"margin-right: 10px;\">" + numero + "</span>";';
                    echo '});';
                    echo '$("#numeros_vendidos_container").html(numerosHTML);';
                    echo '$("#staticBackdrop").modal("show");';
                    echo '});';
                    echo '</script>';

                    // Enviar el correo electrónico al cliente
                    enviarCorreo($correo, $codigoTransaccion, $id_venta, $nombre, $numeros_vendidos);
                } else {
                    // Si ocurre un error al insertar la venta, revertir la transacción
                    $conexion->rollback();
                    echo "Error: No se pudieron insertar los datos de la venta en la base de datos.";
                }
            } else {
                // Si no hay suficientes números disponibles, revertir la transacción
                $conexion->rollback();
                echo "Error: No hay suficientes números disponibles para realizar la venta.";
            }

            // Cerrar las consultas
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
        exit();
    }
} else {
    // Si la conexión a la base de datos no se estableció correctamente, mostramos un mensaje de error
    echo "Error de conexión a la base de datos.";
    exit();
}

// Cerramos la conexión
$conexion->close();

// Función para enviar el correo electrónico al cliente
function enviarCorreo($correo, $codigoTransaccion, $id_venta, $nombre, $numeros_vendidos)
{
    try {
        // Definir los grupos de números con sus respectivos colores de fondo
        $grupo1 = ['1234', '1515', '1905', '0108', '1122', '9999', '7007', '6666'];
        $colorGrupo1 = '#007bff'; // Fondo azul 200 mil

        $grupo2 = ['4268', '8015'];
        $colorGrupo2 = '#dc3545'; // Fondo rojo 500

        $colorPredeterminado = '#efb810'; // Fondo predeterminado

        // Generamos el HTML para los números vendidos
        $numeros_html = '';
        $numeros_html .= '<div style="display: flex; flex-wrap: wrap;">';
        $contador_numeros = 1; // Inicializamos el contador de números

        foreach ($numeros_vendidos as $numero) {
            // Determinar el color de fondo basado en el número
            if (in_array($numero, $grupo1)) {
                $backgroundColor = $colorGrupo1;
            } elseif (in_array($numero, $grupo2)) {
                $backgroundColor = $colorGrupo2;
            } else {
                $backgroundColor = $colorPredeterminado;
            }

            $numeros_html .= '<div style="display: flex; margin:5px">';
            $numeros_html .= '<span style="background-color: ' . $backgroundColor . '; color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; margin-right: 5px; border-style: dotted;">' . $numero . '</span>';
            $numeros_html .= '</div>';

            // Agregamos un salto de línea después de cada conjunto de 5 números
            if ($contador_numeros % 5 == 0) {
                $numeros_html .= '</div><div style="display: flex; flex-wrap: wrap;">';
            }

            $contador_numeros++;
        }

        $numeros_html .= '</div>'; // Cierre del contenedor final

        // Cargamos la plantilla del correo electrónico
        $email_template = file_get_contents('../../email/email_template.html');

        // Reemplazamos las variables en la plantilla
        $email_template = str_replace('{nombre}', $nombre, $email_template);
        $email_template = str_replace('{count_numeros_vendidos}', count($numeros_vendidos), $email_template);
        $email_template = str_replace('{numeros_html}', $numeros_html, $email_template);

        // Configuramos el servidor SMTP y creamos una instancia de PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; // Configurar la codificación de caracteres
        $mail->Body = $email_template;

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
        echo "Error al enviar el correo electrónico:";
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
                origin: {
                    x: 0
                },
                colors: colors,
            });

            confetti({
                particleCount: 2,
                angle: 120,
                spread: 55,
                origin: {
                    x: 1
                },
                colors: colors,
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        })();
    </script>

        <!-- Facebook Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '2013325362420141'); 
        fbq('track', 'PageView');
        </script>
        <noscript>
        <img height="1" width="1" 
        src="https://www.facebook.com/tr?id=2013325362420141&ev=PageView
        &noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
</head>

<body>
    <script>
        fbq('track', 'Purchase', {
            currency: "COP",
            value: <?= $totalPagar ?>
        });
    </script>
    <div class="container mt-1" style="background-color: #fff;">
        <div class="mt-2">
            <div class="card-header text-center" style="background-color: #fff;"></div>
            <h4 class="d-flex justify-content-center my-4"><b><?php echo $nombre; ?></b></h4>
            <div class="">
                <div class="card mb-3">
                    <img src="http://eldiadetusuerte.com/images/agradecimientov6.png" class="card-img-top" alt="imagen-agradecimiento">
                </div>
                <ul class="list-group">
                    <li class="list-group-item" style="line-height:32px">
                        <i class="fas fa-ticket-alt me-3 mb-3"></i> <strong>Estos son tus números:</strong>
                        <div id="numeros_vendidos_container">
                            <?php
                                // Definir los grupos de números con sus respectivos colores de fondo
                                $grupo1 = ['1234', '1515', '1905', '0108', '1122', '9999', '7007', '6666'];
                                $colorGrupo1 = '#007bff'; // Fondo azul 200 mil

                                $grupo2 = ['4268', '8015'];
                                $colorGrupo2 = '#dc3545'; // Fondo rojo 500

                                $colorPredeterminado = '#efb810'; // Fondo predeterminado

                            echo '<div style="flex-wrap: wrap; margin-bottom: 10px;">'; // Contenedor principal con flexbox
                            foreach ($numeros_vendidos as $numero) {
                                // Determinar el color de fondo basado en el número
                                if (in_array($numero, $grupo1)) {
                                    $backgroundColor = $colorGrupo1;
                                } elseif (in_array($numero, $grupo2)) {
                                    $backgroundColor = $colorGrupo2;
                                } else {
                                    $backgroundColor = $colorPredeterminado;
                                }

                                echo '<span style="background-color: ' . $backgroundColor . '; color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted; margin-right:10px">' . $numero . '</span>';
                            }
                            echo '</div>'; // Cierre del contenedor principal

                            ?>
                        </div>
                        <!-- Aquí se mostrarán los badges -->
                    </li>
                        <p style="margin-left:15px">Hemos enviado una copia de esta información a tu correo electrónico.</p>
                </ul>

                <p>Únete a nuestro canal de WhatsApp para que no te pierdas los detalles del sorteo.
                    <a href="https://chat.whatsapp.com/GGKM68EuoOb0Dj9R1s4RFJ" style="text-decoration: none;">
                        <button style="padding: 5px 5px; background-color: #25d366; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer;">
                            Ir al canal
                        </button>
                    </a>
                </p>

                <div class="text-center mt-2">
                    <button onclick="descargarComoPDF()" class="btn btn-sm btn-success">Descargar PDF</button>
                    <a href="/" class="btn btn-sm btn-secondary">Regresar</a>
                </div>
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
<?php

date_default_timezone_set('America/Bogota');
// Función para obtener la conexión a la base de datos

// Incluir el archivo de encabezado
include '../include/header.php';

include '../../config/config_bd.php';

require '../../vendor/autoload.php';


// // Incluye la librería PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a la base de datos (reemplaza estos valores con los de tu conexión real)

    $conexion = obtenerConexion();

    // Verificar la conexión
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Escapar los valores recibidos del formulario
    $id_eliminar = mysqli_real_escape_string($conexion, $_POST["id"]);
    $nombre = mysqli_real_escape_string($conexion, $_POST["nombre"]);
    $celular = mysqli_real_escape_string($conexion, $_POST["celular"]);
    $correo = mysqli_real_escape_string($conexion, $_POST["correo"]);
    $departamento = mysqli_real_escape_string($conexion, $_POST["departamento"]);
    $ciudad = mysqli_real_escape_string($conexion, $_POST["ciudad"]);
    $totalNumeros = mysqli_real_escape_string($conexion, $_POST["total_numeros"]);
    $totalPagar = mysqli_real_escape_string($conexion, $_POST["total_pagado"]);
    $payment_id = "Venta Forzada";
    // Genera el código único de transacción
    $codigoTransaccion = substr($celular, -4) . mt_rand(1000, 9999);
    $vendido_por = "Página Web";
    $fecha_venta = mysqli_real_escape_string($conexion, $_POST["fecha_venta"]);

    //limpiamos el punto
    $totalPagar = str_replace('.', '', $totalPagar);

    try {
        // Inicia una transacción
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

        // Prepara la consulta SQL para seleccionar números disponibles de manera aleatoria
        $consulta_numeros = $conexion->prepare("SELECT id, numero FROM numeros ORDER BY RAND() LIMIT ?");
        $consulta_numeros->bind_param("i", $totalNumeros);
        $consulta_numeros->execute();
        $resultado_numeros = $consulta_numeros->get_result();

        // Verifica si hay suficientes números disponibles
        if ($resultado_numeros->num_rows >= $totalNumeros) {
            // Fecha de la venta (usar la fecha actual)                

            // Preparar la consulta para insertar la venta
            $consulta_venta = $conexion->prepare("INSERT INTO ventas
                                                        (id_cliente, total_numeros, total_pagado, payment_id_mercadopago, external_reference_codigo_transaccion, vendido_por, fecha_venta)
                                                        VALUES (?, ?, ?, ?, ?, ?, ?)");
            $consulta_venta->bind_param("iisssss", $id_cliente, $totalNumeros, $totalPagar, $payment_id, $codigoTransaccion, $vendido_por, $fecha_venta);
            $consulta_venta->execute();

            // Verifica si la inserción de la venta fue exitosa
            if ($consulta_venta->affected_rows > 0) {
                // Obtiene el ID de la venta insertada
                $id_venta = $conexion->insert_id;

                // Prepara la consulta SQL para insertar los números vendidos
                $consulta_numeros_vendidos = $conexion->prepare("INSERT INTO numeros_vendidos (id_venta, numero) VALUES (?, ?)");

                // Prepara la consulta SQL para eliminar los números vendidos de la tabla numeros
                $consulta_eliminar_numeros = $conexion->prepare("DELETE FROM numeros WHERE id = ?");

                // Array para almacenar los números vendidos
                $numeros_vendidos = array();

                // Itera sobre los números seleccionados y los inserta en la tabla numeros_vendidos
                while ($fila = $resultado_numeros->fetch_assoc()) {
                    $id_numero = $fila['id'];
                    $numero = $fila['numero'];

                    // Inserta el número vendido en la tabla numeros_vendidos
                    $consulta_numeros_vendidos->bind_param("is", $id_venta, $numero);
                    $consulta_numeros_vendidos->execute();

                    // Almacena el número vendido en el array
                    $numeros_vendidos[] = $numero;

                    // Elimina el número vendido de la tabla numeros
                    $consulta_eliminar_numeros->bind_param("i", $id_numero);
                    $consulta_eliminar_numeros->execute();
                    $fecha_final = date('d/m/Y h:i A', strtotime($fecha_venta));

                    // procedemos a eliminar de la sección de respaldo
                    $consulta_eliminar_respaldo = $conexion->prepare("DELETE FROM respaldo WHERE id = ?");
                    $consulta_eliminar_respaldo->bind_param("i", $id_eliminar); // Enlaza el parámetro ID
                    $consulta_eliminar_respaldo->execute(); // Ejecuta la consulta                        


                }

                    // Definir los grupos de números con sus respectivos colores de fondo
                    $grupo1 = ['1234', '1515', '1905', '0108', '1122', '9999', '7007', '6666'];
                    $colorGrupo1 = '#007bff'; // Fondo azul 200 mil

                    $grupo2 = ['4268', '8015'];
                    $colorGrupo2 = '#dc3545'; // Fondo rojo 500

                    $colorPredeterminado = '#efb810'; // Fondo predeterminado

                // Muestra el modal con los números
                echo '<script>';
                echo '$(document).ready(function() {';
                echo '    var numeros_vendidos = ' . json_encode($numeros_vendidos) . ';';
                echo '    var grupo1 = ' . json_encode($grupo1) . ';';
                echo '    var grupo2 = ' . json_encode($grupo2) . ';';
                echo '    var colorGrupo1 = "' . $colorGrupo1 . '";';
                echo '    var colorGrupo2 = "' . $colorGrupo2 . '";';
                echo '    var colorPredeterminado = "' . $colorPredeterminado . '";';
                echo '    var numerosHTML = "";';
                echo '    numeros_vendidos.forEach(function(numero, index) {';
                echo '        var backgroundColor;';
                echo '        if (grupo1.includes(numero)) {';
                echo '            backgroundColor = colorGrupo1;';
                echo '        } else if (grupo2.includes(numero)) {';
                echo '            backgroundColor = colorGrupo2;';
                echo '        }else {';
                echo '            backgroundColor = colorPredeterminado;';
                echo '        }';
                echo '        if (index > 0 && index % 5 === 0) {';
                echo '            numerosHTML += "</div><div style=\"flex-wrap: wrap; gap: 10px;\">";';
                echo '        }';
                echo '        numerosHTML += "<span style=\"background-color: " + backgroundColor + "; color: #000; padding: 5px 10px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted; text-align: center; width: 50px; height: 50px; line-height: 40px; margin: 5px;\">" + numero + "</span>";';
                echo '    });';
                echo '    $("#numeros_vendidos_container").html(numerosHTML);';
                echo '    $("#staticBackdrop").modal("show");';
                echo '});';
                echo '</script>';


                // Cierra las consultas
                $consulta_numeros_vendidos->close();
                $consulta_eliminar_numeros->close();

                // Confirma la transacción
                $conexion->commit();

                try {

                    // Definir los grupos de números con sus respectivos colores de fondo
                    $grupo1 = ['1234', '1515', '1905', '0108', '1122', '9999', '7007', '6666'];
                    $colorGrupo1 = '#007bff'; // Fondo azul 200 mil

                    $grupo2 = ['4268', '8015'];
                    $colorGrupo2 = '#dc3545'; // Fondo rojo 500

                    $colorPredeterminado = '#efb810'; // Fondo predeterminado

                    $numeros_html = '<div style="display: flex; flex-wrap: wrap;">';
                    foreach ($numeros_vendidos as $numero) {
                        // Determinar el color de fondo basado en el número
                        if (in_array($numero, $grupo1)) {
                            $backgroundColor = $colorGrupo1;
                        } elseif (in_array($numero, $grupo2)) {
                            $backgroundColor = $colorGrupo2;
                        } else {
                            $backgroundColor = $colorPredeterminado;
                        }

                        $numeros_html .= '<span style="background-color: ' . $backgroundColor . '; color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; margin: 5px; border-style: dotted;">' . $numero . '</span>';
                    }
                    $numeros_html .= '</div>';

                    // Configura el servidor SMTP y crea una instancia de PHPMailer
                    $mail = new PHPMailer(true);
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
                            <h2 style="text-align: center; margin-top:40px;">' . $nombre . '</h2>
                                <img src="https://eldiadetusuerte.com/images/agradecimientov6.png" alt="Imagen de agradecimiento" style="display: block; margin: 0 auto 20px; width: 100%;" >                                
                                <p>Tienes: ' . count($numeros_vendidos) . ' oportunidades para ganar.</p>
                                <div class="badge-container">' . $numeros_html . '</div>
                                <p>Te deseamos mucha suerte.</p>
                                <span style="float: left;"><b>id:</b> ' . $codigoTransaccion . ' </span><br>
                                <span style="float: right"><b>Fecha:</b> ' . $fecha_final . '</span><br>
                                <p>Únete a nuestro canal de WhatsApp para que no te pierdas la fecha del Sorteo.
                                <a href="https://chat.whatsapp.com/GGKM68EuoOb0Dj9R1s4RFJ" style="text-decoration: none;">
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
                    $mail->Subject = 'Confirmacion:' . '#' . $codigoTransaccion . '-' . $id_venta;
                    $mail->Port = 587;
                    $mail->setFrom('info@eldiadetusuerte.com', 'El dia de Tu Suerte');
                    $mail->addAddress($correo);
                    $mail->addBCC('info@eldiadetusuerte.com', 'Copia oculta');


                    // Envía el correo electrónico
                    $mail->send();
                } catch (Exception $e) {
                    echo "Error al enviar el correo electrónico:";
                }
            } else {
                // Si ocurre un error al insertar la venta, revierte la transacción
                $conexion->rollback();
                $conexion->rollback();
                echo <<<HTML
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert2 JS -->
                <script>
                    // Muestra la alerta
                    Swal.fire({
                        title: "Algo salió mal",
                        text: "Intenteo nuevamente.",
                        icon: "error",
                        confirmButtonText: "Aceptar",
                            confirmButtonColor: "#000"
                    }).then((result) => {
                        // Redirige la página al hacer clic en Aceptar
                        if (result.isConfirmed) {
                            window.location.href = "../vistas/generar-venta.php";
                        }
                    });
                </script>
                HTML;
            }
        } else {
            // Si no hay suficientes números disponibles, revierte la transacción
            $conexion->rollback();
            echo <<<HTML
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert2 JS -->
                <script>
                    // Muestra la alerta
                    Swal.fire({
                        title: "Algo salió mal",
                        text: "La cantidad ingresada es superior a la cantidad de números disponibles. Verifique la disponibilidad e inténtelo nuevamente.",
                        icon: "error",
                        confirmButtonText: "Aceptar",
                        confirmButtonColor: "#000"
                    }).then((result) => {
                        // Redirige la página al hacer clic en Aceptar
                        if (result.isConfirmed) {
                            window.location.href = "../vistas/generar-venta.php";
                        }
                    });
                </script>
                HTML;
        }

        // Cierra la consulta de números disponibles
        $consulta_numeros->close();
    } catch (Exception $e) {
        // Si se produce una excepción, revierte la transacción y muestra un mensaje de error
        $conexion->rollback();
        echo "Error al ejecutar la consulta: " . $e->getMessage();
    }
} else {
    // Si no se encuentran los datos almacenados en la sesión, muestra un mensaje de error
    echo "Error: No se recibieron los datos del formulario.";
}

// Cerrar la conexión
mysqli_close($conexion);

// Redireccionar o mostrar un mensaje de éxito
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por tu Compra!</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/agradecimiento.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

</head>

<body class="mb-5">

    <div class="container mt-1" style="background-color: #fff;">
        <div class="mt-2">
            <div class="card-header text-center" style="background-color: #fff;"></div>
            <div class="card mb-3">
                <div class="d-flex">
                    <span style="float: left; margin-top:8px; margin-left:10px"><b>Id:</b> <?php echo $codigoTransaccion ?></span>
                    <span style="float: right; margin-left:auto; margin-top:8px; margin-right:10px"><b>Fecha:</b> <?php echo $fecha_final ?></span>
                </div>
                <h4 class="text-center mt-3"> <b><?php echo $nombre ?></b></h4>
                <img src="https://eldiadetusuerte.com/images/agradecimientov6.png" class="card-img-top" alt="...">
            </div>
            <ul class="list-group" style="background-color: #fff;">
                <li class="list-group-item" style="line-height:32px">
                    <i class="fas fa-ticket-alt me-3" style="line-height:32px"></i> <strong>Estos son tus números:</strong>
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
                </li>
                <p style="margin-left:15px">Hemos enviado una copia de esta información a tu correo electrónico.</p>
            </ul>
            <p>Únete a nuestro canal de WhatsApp para que no te pierdas la fecha del Sorteo.
                <a href="https://chat.whatsapp.com/GGKM68EuoOb0Dj9R1s4RFJ" style="text-decoration: none;">
                    <button style="padding: 5px 5px; background-color: #25d366; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer;">
                        Ir al canal
                    </button>
                </a>
            </p>

            <div class="text-center mt-2 mx-5">
                <button onclick="descargarComoPDF()" class="btn btn-sm btn-success">Descargar PDF</button>
                <a href="../vistas/respaldo.php" class="btn btn-sm btn-secondary">Regresar</a>
            </div>
        </div>
    </div>
    </div>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
        function descargarComoPDF() {
            const element = document.querySelector('.container');
            const options = {
                filename: 'agradecimiento.pdf', // Nombre del archivo PDF
                html2canvas: { // Configuración para html2canvas
                    scale: 2, // Escala para mejorar la calidad de la imagen
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

</body>

</html>
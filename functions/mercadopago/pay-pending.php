<?php
// Inicia la sesión PHP para acceder a los datos almacenados
session_start();

// Verifica si se recibieron los datos almacenados en la sesión
if (isset($_SESSION['nombre'], $_SESSION['cedula'], $_SESSION['correo'], $_SESSION['celular'], $_SESSION['departamento'], $_SESSION['ciudad'], $_SESSION['totalNumeros'])) {
    // Recupera y valida los datos almacenados en las sesiones
    $nombre = validar($_SESSION['nombre']);
    $cedula = validar($_SESSION['cedula']);
    $correo = validar($_SESSION['correo']);
    $celular = validar($_SESSION['celular']);
    $departamento = validar($_SESSION['departamento']);
    $ciudad = validar($_SESSION['ciudad']);
    $totalNumeros = validar($_SESSION['totalNumeros']);

    // Precio unitario de la rifa
    $valorRifa = 6000;

    // Calcula el total a pagar por el usuario
    $totalPagado = $valorRifa * $totalNumeros; 

    // Muestra los datos recibidos del formulario
    echo "Nombre: " . $nombre . "<br>";
    echo "Cédula: " . $cedula . "<br>";
    echo "Correo: " . $correo . "<br>";
    echo "Celular: " . $celular . "<br>";
    echo "Departamento: " . $departamento . "<br>";
    echo "Ciudad: " . $ciudad . "<br>";
    echo "Total de números: " . $totalNumeros . "<br>";
    echo "Total pagado: " . $totalPagado . "<br>";

    // Limpia los datos de la sesión
    limpiar_sesion();

    $external_reference = $_GET['external_reference']; // Puede ser tu identificador único para la transacción
    $payment_id = $_GET['payment_id']; // El ID de pago asignado por Mercado Pago
    $status = $_GET['status'];

    echo "External Reference: " . $external_reference . "<br>";
    echo "Payment ID: " . $payment_id . "<br>";
    
    // Puedes seguir trabajando con los datos aquí, como guardarlos en una base de datos o enviar un correo electrónico de confirmación, según tus necesidades.
}else {
    // Si no se encuentran los datos almacenados en la sesión ni en la URL, muestra un mensaje de error
    echo "Error: No se recibieron los datos del formulario.";
}

// Función para validar los datos recibidos
function validar($dato) {
    // Filtra y escapa los datos para evitar inyección de HTML y JavaScript
    return htmlspecialchars(strip_tags(trim($dato)));
}

// Función para limpiar la sesión después de usar los datos
function limpiar_sesion() {
    // Elimina todos los datos de la sesión
    $_SESSION = array();

    // Si se utiliza un identificador de sesión en las cookies, también se debe eliminar la cookie de la sesión.
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }

    // Finalmente, destruye la sesión
    session_destroy();
}
?>

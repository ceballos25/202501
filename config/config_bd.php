<?php
// Función para obtener la conexión a la base de datos
function obtenerConexion() {
    $servername = "srv1311.hstgr.io";
    $username = "u794556006_sort_002";
    $password = "O5PP~XBF#]k!";
    $dbname = "u794556006_sort_002";
    
    // $servername = "localhost";
    // $username = "u794556006_sort_002";
    // $password = "O5PP~XBF#]k!";
    // $dbname = "u794556006_sort_002";
    // $sql = "SET time_zone = 'America/Bogota'";

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Establecer el conjunto de caracteres a utf8 (opcional)
    $conn->set_charset("utf8");

    return $conn;
}
?>

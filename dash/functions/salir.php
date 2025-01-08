<?php

session_start();


    // Elimina todas las variables de sesión
    session_unset();
    // La variable de sesión no está definida, redirige al usuario al índice
    header("Location: ../../dash/");
exit(); // Asegura que el script se detenga después de redirigir

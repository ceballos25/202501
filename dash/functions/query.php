<?php

// Función para obtener el total de números vendidos
function obtenerTotalNumerosVendidos($conn) {
    $sql = 'SELECT COUNT(*) as numeros_vendidos FROM numeros_vendidos';
    $resultado = $conn->query($sql);
    return ($resultado && $resultado->num_rows > 0) ? $resultado->fetch_assoc()['numeros_vendidos'] : 0;
}

// Función para obtener el total de dinero vendido
function obtenerTotalVendido($conn) {
    $sql = "SELECT SUM(total_pagado) as total_pagado FROM ventas";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return ($resultado && $resultado->num_rows > 0) ? $resultado->fetch_assoc()['total_pagado'] : 0;
}

// Función para obtener la cantidad de clientes, verificando que solo esté una vez la cédula
function obtenerTotalClientes($conn) {
    $sql = "SELECT COUNT(DISTINCT celular_cliente) AS total_clientes FROM clientes";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return ($resultado && $resultado->num_rows > 0) ? $resultado->fetch_assoc()['total_clientes'] : 0;
}


// Función para obtener los clientes con más numeros vendidos
function obtenerClientes($conn) {
    $sql = "SELECT id_cliente, nombre_cliente, celular_cliente, correo_cliente, departamento_cliente, ciudad_cliente 
    FROM clientes 
    GROUP BY celular_cliente 
    ORDER BY id_cliente DESC"; //limitamos los registros para no mostrar todos los clientes

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $clientes = array();
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $clientes[] = $fila;
        }
    }
    return $clientes;
}

// Función para obtener los clientes con más ventas
function obtenerClientesConMasVentas($conn, $limite = 10) {
    $sql = "SELECT c.id_cliente, c.celular_cliente, SUM(v.total_numeros) AS total_numeros
    FROM ventas v
    INNER JOIN clientes c ON v.id_cliente = c.id_cliente
    GROUP BY c.id_cliente, c.celular_cliente
    ORDER BY total_numeros DESC
    LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limite);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $clientes = array();
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $clientes[] = $fila;
        }
    }
    return $clientes;
}


    // Función para obtener las 10 ciudades con más total pagado por ventas
    function obtenerCiudadesMasTotalPagado($conn) {
        $sql = "SELECT c.ciudad_cliente AS ciudad, SUM(v.total_pagado) AS total_pagado_por_ciudad 
                FROM ventas v
                INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                GROUP BY c.ciudad_cliente
                ORDER BY total_pagado_por_ciudad DESC 
                LIMIT 10";
        $resultado = $conn->query($sql);
        $ciudades = array();
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $ciudades[] = $fila;
            }
        }
        return $ciudades;
    }
    



    function obtenerVentasPorTipo($conn) {
        // Define los nombres de personas para ventas manuales
        $ventasManual = ['Cristian Ceballos', 'Jorge Herrera', 'Enrique Pérez'];
        $ventasBot = 'Chat Bot';
        
        // Crea una expresión SQL CASE para agrupar por tipo de venta
        $sql = "SELECT
                    CASE
                        WHEN vendido_por IN ('" . implode("', '", $ventasManual) . "') THEN 'Venta Manual'
                        WHEN vendido_por = '$ventasBot' THEN 'Venta Chat Bot'
                        ELSE 'Página Web'
                    END AS tipo_venta,
                    COUNT(*) AS total_ventas
                FROM ventas
                GROUP BY tipo_venta
                ORDER BY total_ventas DESC";

    
        $resultado = $conn->query($sql);
        $ventasPorTipo = array(); // Inicializa el array para almacenar los datos de ventas por tipo
    
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $ventasPorTipo[] = $fila; // Almacena cada fila en el array
            }
        }
    
        return $ventasPorTipo;
    }
    


// Función para obtener los datos de ventas agrupados por vendedor
function obtenerVentasPorVendedor($conn) {
    $sql = "SELECT vendido_por, COUNT(*) AS cantidad_ventas 
            FROM ventas 
            GROUP BY vendido_por ORDER BY cantidad_ventas DESC";
    
    $resultado = $conn->query($sql);
    $ventasPorVendedor = array(); // Inicializa el array para almacenar los datos de ventas por vendedor

    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $ventasPorVendedor[] = $fila; // Almacena cada fila en el array
        }
    }

    return $ventasPorVendedor;
}
?>

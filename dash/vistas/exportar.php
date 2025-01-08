<?php
require '../../vendor/autoload.php';
include '../../config/config_bd.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Conectar a la base de datos
$conexion = obtenerConexion();

// Verificar la conexión
if (!$conexion) {
    die("Error al conectar con la base de datos.");
}

// Crear una nueva hoja de cálculo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Escribir la cabecera del archivo Excel (nombres de las columnas)
$sheet->setCellValue('A1', 'Id Venta');
$sheet->setCellValue('B1', 'Nombre');
$sheet->setCellValue('C1', 'Correo');
$sheet->setCellValue('D1', 'Celular');
$sheet->setCellValue('E1', 'Departamento');
$sheet->setCellValue('F1', 'Ciudad');
$sheet->setCellValue('G1', 'Total Números');
$sheet->setCellValue('H1', 'Total Pagado');
$sheet->setCellValue('I1', 'Id Pasarela de Pago');
$sheet->setCellValue('J1', 'Código Transacción');
$sheet->setCellValue('K1', 'Vendido Por');
$sheet->setCellValue('L1', 'Fecha Venta');


// Consulta SQL para obtener los datos
$sql = "SELECT v.id, c.nombre_cliente, c.correo_cliente, c.celular_cliente, c.departamento_cliente, c.ciudad_cliente, v.total_numeros, v.total_pagado, v.payment_id_mercadopago, v.external_reference_codigo_transaccion, v.vendido_por, v.fecha_venta, v.codigo_sorteo
        FROM ventas v
        JOIN clientes c ON v.id_cliente = c.id_cliente
        ORDER BY v.id DESC";
$resultado = $conexion->query($sql);

// Verificar si la consulta devolvió resultados
if ($resultado && $resultado->num_rows > 0) {
    $rowNum = 2; // Empezar en la fila 2 para los datos
    while ($row = $resultado->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNum, $row['id']);
        $sheet->setCellValue('B' . $rowNum, $row['nombre_cliente']);
        $sheet->setCellValue('C' . $rowNum, $row['correo_cliente']);
        $sheet->setCellValue('D' . $rowNum, $row['celular_cliente']);
        $sheet->setCellValue('E' . $rowNum, $row['departamento_cliente']);
        $sheet->setCellValue('F' . $rowNum, $row['ciudad_cliente']);
        $sheet->setCellValue('G' . $rowNum, $row['total_numeros']);
        $sheet->setCellValue('H' . $rowNum, $row['total_pagado']);
        $sheet->setCellValue('I' . $rowNum, $row['payment_id_mercadopago']);
        $sheet->setCellValue('J' . $rowNum, $row['external_reference_codigo_transaccion']);
        $sheet->setCellValue('K' . $rowNum, $row['vendido_por']);
        $sheet->setCellValue('L' . $rowNum, $row['fecha_venta']);
        $rowNum++;
    }
}

// Cerrar la conexión
$conexion->close();

// Crear un escritor para guardar el archivo Excel
$writer = new Xlsx($spreadsheet);

// Enviar las cabeceras correctas para forzar la descarga del archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="exported_table.xlsx"');
header('Cache-Control: max-age=0');

// Guardar el archivo Excel en la salida
$writer->save('php://output');
?>

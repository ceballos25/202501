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
$sheet->setCellValue('A1', 'Id Cliente');
$sheet->setCellValue('B1', 'Nombre');
$sheet->setCellValue('C1', 'Correo');
$sheet->setCellValue('D1', 'Celular');
$sheet->setCellValue('E1', 'Departamento');
$sheet->setCellValue('F1', 'Ciudad');

// Consulta SQL para obtener los datos
$sql = "SELECT id_cliente, nombre_cliente, celular_cliente, correo_cliente, departamento_cliente, ciudad_cliente
        FROM clientes ORDER BY id_cliente DESC";
$resultado = $conexion->query($sql);

// Verificar si la consulta devolvió resultados
if ($resultado && $resultado->num_rows > 0) {
    $rowNum = 2; // Empezar en la fila 2 para los datos
    while ($row = $resultado->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNum, $row['id_cliente']);
        $sheet->setCellValue('B' . $rowNum, $row['nombre_cliente']);
        $sheet->setCellValue('C' . $rowNum, $row['correo_cliente']);
        $sheet->setCellValue('D' . $rowNum, $row['celular_cliente']);
        $sheet->setCellValue('E' . $rowNum, $row['departamento_cliente']);
        $sheet->setCellValue('F' . $rowNum, $row['ciudad_cliente']);
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

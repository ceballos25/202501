// <?php
// require 'vendor/autoload.php';
// // ini_set('display_errors', 1);
// // ini_set('display_startup_errors', 1);
// // error_reporting(E_ALL);

// use PhpOffice\PhpSpreadsheet\IOFactory;

// // Datos de conexión a la base de datos
// $host = 'localhost'; // Cambia esto por tu host de base de datos
// $dbname = 'u794556006_sort_002'; // Cambia esto por tu nombre de base de datos
// $user = 'u794556006_sort_002'; // Cambia esto por tu usuario de base de datos
// $pass = 'O5PP~XBF#]k!'; // Cambia esto por tu contraseña de base de datos

// // Crear una conexión a la base de datos usando PDO
// try {
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("Error al conectar a la base de datos: " . $e->getMessage());
// }

// // Ruta al archivo Excel
// $archivoExcel = 'clientes.xlsx'; // Cambia esto a la ruta real del archivo Excel

// // Leer el archivo Excel
// $spreadsheet = IOFactory::load($archivoExcel);
// $sheet = $spreadsheet->getActiveSheet();
// $data = $sheet->toArray(null, true, true, true);

// // Preparar la consulta SQL
// $sql = "INSERT INTO clientes (nombre_cliente, celular_cliente, correo_cliente, departamento_cliente, ciudad_cliente) VALUES (?, ?, ?, ?, ?)";

// // Preparar el statement
// $stmt = $pdo->prepare($sql);

// // Insertar los datos
// $primeraFila = true; // La primera fila suele tener los encabezados
// $errores = []; // Opción para recopilar errores

// foreach ($data as $row) {
//     if ($primeraFila) {
//         $primeraFila = false; // Salta la primera fila (encabezados)
//         continue;
//     }

//     $nombre_cliente = $row['A'];
//     $celular_cliente = $row['B'];
//     $correo_cliente = $row['C'];
//     $departamento = $row['D'];
//     $ciudad = $row['E'];

//     try {
//         $stmt->execute([$nombre_cliente, $celular_cliente, $correo_cliente, $departamento, $ciudad]);
//     } catch (PDOException $e) {
//         // Recopila el error en un array para mostrarlo después
//         $errores[] = "Error al insertar cliente " . $nombre_cliente . ": " . $e->getMessage();
//     }
// }

// // Mostrar un mensaje de éxito
// echo "Clientes insertados con éxito.<br>";

// // Mostrar los errores si los hubo
// if (!empty($errores)) {
//     echo "Hubo errores al insertar algunos clientes:<br>";
//     foreach ($errores as $error) {
//         echo $error . "<br>";
//     }
// }

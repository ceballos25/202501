<?php
session_start();
// Al principio del script
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/config_bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a la base de datos (reemplaza estos valores con los de tu conexión real)
    $conexion = obtenerConexion();

    // Verificar la conexión
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Escapar variables de POST para evitar inyección de SQL
    $username = $conexion->real_escape_string($_POST['usuario']);
    $password = $conexion->real_escape_string($_POST['contrasena']);

    // Consulta SQL para obtener el usuario
    $sql = "SELECT id, usuario, nombre, contrasena FROM usuarios WHERE usuario = ?";
    $consulta = $conexion->prepare($sql);
    $consulta->bind_param("s", $username);
    $consulta->execute();
    $resultado = $consulta->get_result();

    if ($resultado->num_rows > 0) {
        // Existe el usuario, verificar la contraseña
        $fila = $resultado->fetch_assoc();
        if (password_verify($password, $fila['contrasena'])) {
            // Contraseña válida, iniciar sesión
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['usuario_nombre'] = $fila['nombre'];
            $_SESSION['usuario'] = $fila['usuario'];
            header("Location: vistas/principal.php"); // Redirigir al panel de control
            exit();
        } else {
            // Contraseña incorrecta
            echo '<script>alert("❌ Contraseña y/o usuario incorrecto, verifique e inténtelo nuevamente")</script>;';
        }
    } else {
        // Usuario no encontrado
        echo '<script>alert("❌ Contraseña y/o usuario incorrecto, verifique e inténtelo nuevamente")</script>;';
        }

    // Cerrar conexión
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>🍀 Login - El día de tu Suerte 🍀</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="">
<div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
        <main>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <div class="card shadow-lg border-0 rounded-lg mt-5">
                            <div class="card-header"><h3 class="text-center font-weight-light my-4">🍀El día de Tu Suerte🍀</h3></div>
                            <div class="card-body">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="inputEmail" type="text" name="usuario" required/>
                                        <label for="inputEmail">Usuario</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="inputPassword" type="password" name="contrasena" required/>
                                        <label for="inputPassword">Contraseña</label>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center mt-4 mb-0">
                                        <button type="submit" class="btn btn-success">Vamos</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div id="layoutAuthentication_footer">
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; El día de tuSuerte 🍀</div>
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
</body>
</html>

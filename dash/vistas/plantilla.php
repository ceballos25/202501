<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ms-3 pe-2" href="principal.php"><?php echo  $_SESSION['usuario_nombre']; ?>🍀</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="../functions/salir.php">Salir</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <!-- <div class="sb-sidenav-menu-heading mb-0">Admin</div>
                             <a class="nav-link" href="principal.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>  -->
                        <div class="sb-sidenav-menu-heading">Ventas</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Ventas
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="ventasAlDetalle.php">Ventas al detalle</a>
                                <a class="nav-link" href="numeros-vendidos.php">Números Vendidos</a>
                                <a class="nav-link" href="generar-venta.php">Generar Venta</a>
                                <a class="nav-link" href="escoger-numero.php" type="button" class="btn btn-primary position-relative">
                                    Escoger Número
                                </a>
                                
                            <a class="nav-link" href="clientes.php">
                            <div class="sb-nav-link-icon"><i class="fa-regular fa-address-card"></i></div>
                            Clentes
                        </a>
                        
                        <a class="nav-link" href="respaldo.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-database"></i></div>
                            Respaldo
                        </a>
                        
                        <a class="nav-link" href="ventas-bot.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-database"></i></div>
                            Ventas Bot🤖
                        </a>                        

                        <a class="nav-link" href="encuestas.php">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-square-poll-vertical"></i></div>
                            Calificaciones
                        </a>
                            </nav>
                        </div>
                        <div class="sb-sidenav-menu-heading">Sorteos</div>

                        <a class="nav-link" href="numeros-disponibles.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                            Números Disponibles
                        </a>


                    </div>
                    <hr>

                    <?php
                    // Conectar a la base de datos
                    $conexionResumen = obtenerConexion();

                    // Verificar la conexión
                    if (!$conexionResumen) {
                        die("Error de conexión: " . mysqli_connect_error());
                    }

                    // Definir la hora por defecto Bogotá
                    date_default_timezone_set('America/Bogota');
                    $day = date('Y-m-d');

                    // Función para obtener resultados de una consulta
                    function obtenerResultado($stmt)
                    {
                        $resultado = $stmt->get_result();
                        return $resultado && $resultado->num_rows > 0 ? $resultado->fetch_assoc() : [];
                    }

                    // Consultar el total de ventas por Página Web
                    $sqlPw = "SELECT COUNT(*) as PW FROM ventas WHERE vendido_por = 'Página Web' AND fecha_venta LIKE '%$day%'";
                    $stmt = $conexionResumen->prepare($sqlPw);
                    $stmt->execute();
                    $resultadoPW = obtenerResultado($stmt);
                    $ventasPW = isset($resultadoPW['PW']) ? (int)$resultadoPW['PW'] : 0;

                    // Consultar el total de ventas manuales
                    $sqlVm = "SELECT COUNT(*) as VM 
                                FROM ventas 
                                WHERE vendido_por IN ('Jorge Herrera', 'Enrique Pérez', 'Cristian Ceballos')
                                AND fecha_venta LIKE '%$day%'";
                    $stmt = $conexionResumen->prepare($sqlVm);
                    $stmt->execute();
                    $resultadoVm = obtenerResultado($stmt);
                    $ventasManual = isset($resultadoVm['VM']) ? (int)$resultadoVm['VM'] : 0;

                    // Total de ventas
                    $total = $ventasPW + $ventasManual;

                    // Sumar total vendido
                    $sqlTotalVendido = "SELECT SUM(total_pagado) as TV FROM ventas WHERE fecha_venta LIKE '%$day%'";
                    $stmt = $conexionResumen->prepare($sqlTotalVendido);
                    $stmt->execute();
                    $resultadoTotalVendido = obtenerResultado($stmt);
                    $TotalVendido = isset($resultadoTotalVendido['TV']) ? (float)$resultadoTotalVendido['TV'] : 0;

                    // Consultar el total de números vendidos por Página Web
                    $sqlNumerosPw = "SELECT SUM(total_numeros) as PW FROM ventas
                    WHERE vendido_por = 'Página Web'
                     AND fecha_venta LIKE '%$day%'";
                    $stmt = $conexionResumen->prepare($sqlNumerosPw);
                    $stmt->execute();
                    $resultadosqlNumerosPw = obtenerResultado($stmt);
                    $ventasNumerosPW = isset($resultadosqlNumerosPw['PW']) ? (int)$resultadosqlNumerosPw['PW'] : 0;

                    // Consultar el total de números vendidos por ventas manuales
                    $sqlNumerosVm = "SELECT SUM(total_numeros) as VM 
                                        FROM ventas 
                                        WHERE vendido_por IN ('Jorge Herrera', 'Enrique Pérez', 'Cristian Ceballos')
                                        AND fecha_venta LIKE '%$day%'";
                    $stmt = $conexionResumen->prepare($sqlNumerosVm);
                    $stmt->execute();
                    $resultadosqlNumerosVm = obtenerResultado($stmt);
                    $ventasNumerosVM = isset($resultadosqlNumerosVm['VM']) ? (int)$resultadosqlNumerosVm['VM'] : 0;
                    ?>

                    <div class="mt-0 ms-2" role="alert">
                        <p class="mt-1 ms-2 mb-1">Resumen Ventas:</p>
                        <ul>
                            <li><strong>Pw: </strong><?php echo $ventasPW; ?> </li>
                            <li><strong>Vm: </strong><?php echo $ventasManual; ?> </li>
                        </ul>
                        <p class="mb-0">Total: <?php echo $total; ?></p>
                        <p>Total Vendido: <?php echo '$' . number_format($TotalVendido, 0, ',', '.'); ?></p>
                        <hr>
                    </div>

                    <div class="mt-0 ms-2" role="alert">
                        <p class="mt-1 ms-2 mb-1">Resumen Números:</p>
                        <ul>
                            <li><strong>Pw: </strong><?php echo $ventasNumerosPW; ?> </li>
                            <li><strong>Vm: </strong><?php echo $ventasNumerosVM; ?> </li>
                        </ul>
                        <p class="mb-0">Total números: <?php echo $ventasNumerosPW + $ventasNumerosVM; ?></p>
                        <hr>
                    </div>


                </div>
            </nav>
        </div>
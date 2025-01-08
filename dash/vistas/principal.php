<?php
include '../include/header.php';
include '../../config/config_bd.php';
include '../functions/query.php';
$conn = obtenerConexion();

// Obtener la suma total de números vendidos
$numeros_vendidos = obtenerTotalNumerosVendidos($conn);

// Definir el objetivo de ventas
$objetivo_ventas = 10000; // Cambia esto al número deseado

// Calcular los números faltantes
$numeros_faltantes = $objetivo_ventas - $numeros_vendidos;

// Obtener el total vendido
$total_pagado = obtenerTotalVendido($conn);

// Obtener el total de clientes
$suma_total_clientes = obtenerTotalClientes($conn);


// Llama a la función para obtener los clientes con más compras
$clientesConMasCompras = obtenerClientesConMasVentas($conn);

// Prepara los datos para el gráfico
$nombresClientes = [];
$totalCompras = [];

foreach ($clientesConMasCompras as $cliente) {
    $nombresClientes[] = $cliente['celular_cliente'];
    $totalCompras[] = $cliente['total_numeros'];
}

// Llama a la función para obtener las 10 ciudades con más total pagado por ventas
$ciudadesMasTotalPagado = obtenerCiudadesMasTotalPagado($conn);

// Prepara los datos para el gráfico
$nombresCiudades = [];
$totalPagadoPorCiudad = [];

foreach ($ciudadesMasTotalPagado as $ciudad) {
    $nombresCiudades[] = $ciudad['ciudad'];
    $totalPagadoPorCiudad[] = $ciudad['total_pagado_por_ciudad'];
}


// Llama a la función para obtener el número de ventas por tipo
$ventasPorTipo = obtenerVentasPorTipo($conn);

// Prepara los datos para el gráfico
$nombresTipo = [];
$totalVentasTipo = [];

foreach ($ventasPorTipo as $venta) {
    $nombresTipo[] = $venta['tipo_venta'];
    $totalVentasTipo[] = $venta['total_ventas'];
}


// Llama a la función para obtener los datos de ventas por vendedor
$ventasPorVendedor = obtenerVentasPorVendedor($conn);

// Prepara los datos para el gráfico
$nombresVendedores = [];
$totalVentasVendedor = [];

foreach ($ventasPorVendedor as $venta) {
    $nombresVendedores[] = $venta['vendido_por'];
    $totalVentasVendedor[] = $venta['cantidad_ventas'];
}
?>

<!-- termina header -->

<!-- inicia plantilla -->
<?php

include 'plantilla.php';
?>
<!-- termina plantilla -->


<!-- inicia contenido -->
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-5">
            <div class="d-flex justify-content-center">
                <h4 class="mt-4 mb-4 titulo-subrayado">El día de tu Suerte 🍀</h4>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">Total números vendidos</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <span class="large text-white stretched-link"><?php echo $numeros_vendidos; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">Total Vendido</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <span class="large text-white stretched-link"><?php echo '$' . number_format($total_pagado, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">Total Clientes</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <span class="large text-white stretched-link"><?php echo $suma_total_clientes; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-body">Falta por vender</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <span class="large text-white stretched-link"><?php echo $numeros_faltantes; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Los 10 clientes con más números comprados
                        </div>
                        <div class="card-body"><canvas id="myBarChartMes" width="100%" height="40"></canvas></div>
                    </div>
                </div>


                <div class="d-flex justify-content-center m-3">
                    <h2 class="titulo-subrayado">Ventas</h2>
                </div>

                <div class="col-xl-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Ciudad donde más se vende
                        </div>
                        <div class="card-body"><canvas id="ciudadesMasTotalPagado" width="100%" height="50%"></canvas></div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Canal de ventas
                        </div>
                        <div class="card-body"><canvas id="ventasPorTipoPieChart" width="100%" height="222"></canvas></div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Ventas Por Vendedor
                        </div>
                        <div class="card-body"><canvas id="ventasPorVendedorBarChart" width="100%" height="222"></canvas></div>
                    </div>
                </div>



            </div>

        </div>
    </main>
    <!-- termina contenido -->

    <!-- inicia footer -->
    <?php
    include '../include/footer.php';
    ?>
    <!-- termina footer -->

    <script>
        // JavaScript para generar el gráfico de barras con Chart.js
        var ctx = document.getElementById('myBarChartMes').getContext('2d');
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($nombresClientes); ?>, // Nombres de los clientes
                datasets: [{
                    label: 'Números',
                    data: <?php echo json_encode($totalCompras); ?>, // Total de compras por cliente
                    backgroundColor: [ // Definir colores para el fondo de las barras
                        'rgba(255, 99, 132, 0.4)', // Rojo
                        'rgba(54, 162, 235, 0.4)', // Azul
                        'rgba(255, 206, 86, 0.4)', // Amarillo
                        'rgba(75, 192, 192, 0.4)', // Verde
                        'rgba(153, 102, 255, 0.4)', // Morado
                        'rgba(255, 159, 64, 0.4)', // Naranja
                        'rgba(255, 99, 132, 0.4)', // Rojo (repetido)
                        'rgba(54, 162, 235, 0.4)', // Azul (repetido)
                        'rgba(255, 206, 86, 0.4)', // Amarillo (repetido)
                        'rgba(75, 192, 192, 0.4)' // Verde (repetido)
                        // Puedes agregar más colores si lo deseas
                    ],
                    borderColor: [ // Definir colores para el borde de las barras
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                        // Puedes agregar más colores si lo deseas
                    ],
                    borderWidth: 1
                }]

            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });



        //ciudades donde más han pagado en dienero
        var ctx = document.getElementById('ciudadesMasTotalPagado').getContext('2d');
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($nombresCiudades); ?>,
                datasets: [{
                    label: 'Total Pagado',
                    data: <?php echo json_encode($totalPagadoPorCiudad); ?>,
                    backgroundColor: [
                        'rgba(0, 200, 150, 0.4)', // Azul oscuro
                        'rgba(128, 0, 128, 0.4)', // Púrpura oscuro
                        'rgba(0, 128, 0, 0.4)', // Verde oscuro
                        'rgba(255, 0, 0, 0.4)', // Rojo oscuro
                        'rgba(255, 159, 64, 0.4)', // Naranja claro
                        'rgba(153, 102, 255, 0.4)', // Morado claro
                        'rgba(75, 192, 192, 0.4)', // Verde claro
                        'rgba(255, 206, 86, 0.4)', // Amarillo claro
                        'rgba(54, 162, 235, 0.4)', // Azul claro
                        'rgba(255, 99, 132, 0.4)' // Rojo claro
                        // Puedes agregar más colores si lo deseas
                    ],
                    borderColor: [
                        'rgba(0, 200, 150, 1)', // Azul oscuro
                        'rgba(128, 0, 128, 1)', // Púrpura oscuro
                        'rgba(0, 128, 0, 1)', // Verde oscuro
                        'rgba(255, 0, 0, 1)', // Rojo oscuro
                        'rgba(255, 159, 64, 1)', // Naranja claro
                        'rgba(153, 102, 255, 1)', // Morado claro
                        'rgba(75, 192, 192, 1)', // Verde claro
                        'rgba(255, 206, 86, 1)', // Amarillo claro
                        'rgba(54, 162, 235, 1)', // Azul claro
                        'rgba(255, 99, 132, 1)' // Rojo claro
                        // Puedes agregar más colores si lo deseas
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            }
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var label = data.datasets[tooltipItem.datasetIndex].label || '';
                            label += ' $' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            return label;
                        }
                    }
                }
            }
        });



        var ctxPie = document.getElementById('ventasPorTipoPieChart').getContext('2d');
        var myPieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($nombresTipo); ?>, // Tipos de venta
                datasets: [{
                    label: 'Número de Ventas',
                    data: <?php echo json_encode($totalVentasTipo); ?>, // Total de ventas por tipo
                    backgroundColor: [ // Colores de fondo para cada sector
                        'rgba(255, 99, 132, 0.8)', // Rojo
                        'rgba(54, 162, 235, 0.8)', // Azul
                        'rgba(255, 205, 86, 0.8)', // Amarillo
                        'rgba(75, 192, 192, 0.8)', // Verde
                        'rgba(153, 102, 255, 0.8)', // Morado
                        'rgba(255, 159, 64, 0.8)', // Naranja
                        'rgba(255, 0, 255, 0.8)', // Magenta
                        'rgba(0, 255, 255, 0.8)', // Cian
                        'rgba(128, 128, 128, 0.8)', // Gris
                        'rgba(0, 0, 0, 0.8)' // Negro
                    ],
                    borderColor: 'rgba(255, 255, 255, 1)', // Color del borde de cada sector
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        });



        // Configuración y creación del gráfico de barras
        // Colores para las barras del gráfico
        var colores = [
            'rgba(90, 12, 135, 0.4)',
            'rgba(12, 162, 115, 0.4)',
            'rgba(255, 159, 64, 0.4)',
            'rgba(54, 162, 235, 0.4)',
            'rgba(255, 99, 132, 0.4)',
            'rgba(153, 102, 255, 0.4)',
            'rgba(255, 205, 86, 0.4)',
            'rgba(75, 192, 192, 0.4)',
            'rgba(255, 0, 255, 0.4)',
            'rgba(0, 255, 255, 0.4)'
        ];

        // Configuración y creación del gráfico de barras
        var ctxBar = document.getElementById('ventasPorVendedorBarChart').getContext('2d');
        var myBarChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($nombresVendedores); ?>,
                datasets: [{
                    label: 'Total Ventas',
                    data: <?php echo json_encode($totalVentasVendedor); ?>,
                    backgroundColor: colores,
                    borderColor: 'rgba(0, 0, 0, 1)', // Color del borde de las barras
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
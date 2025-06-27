<?php
require("conf.php");

// Parámetros de filtro (solo fechas)
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : date('Y-m-d', strtotime('-7 days'));
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : date('Y-m-d');

// Consultas estadísticas para el dashboard
try {
    // Total de movimientos
    $sql_movimientos = "SELECT COUNT(*) FROM camara WHERE DATE(cam_fecha_hora) BETWEEN :fecha_desde AND :fecha_hasta AND cam_emp_id != 13";
    $stmt = $pdo->prepare($sql_movimientos);
    $stmt->execute([':fecha_desde' => $filtro_fecha_desde, ':fecha_hasta' => $filtro_fecha_hasta]);
    $total_movimientos = $stmt->fetchColumn();

    // Total m3 mes actual
    $sql_m3_mes = "SELECT SUM(p.pat_m3) as total_m3_mes 
               FROM camara c
               JOIN patentes p ON c.cam_pat_id = p.pat_id
               WHERE c.cam_tipo = 'entrada' 
               AND c.cam_emp_id != 13
               AND MONTH(c.cam_fecha_hora) = MONTH(CURRENT_DATE())
               AND YEAR(c.cam_fecha_hora) = YEAR(CURRENT_DATE())";
    $stmt = $pdo->prepare($sql_m3_mes);
    $stmt->execute();
    $total_m3_mes = $stmt->fetchColumn();

    // Total m3 acumulados año actual
    $sql_m3_anual = "SELECT SUM(p.pat_m3) as total_m3_anual 
               FROM camara c
               JOIN patentes p ON c.cam_pat_id = p.pat_id
               WHERE c.cam_tipo = 'entrada'
               AND c.cam_emp_id != 13 
               AND YEAR(c.cam_fecha_hora) = YEAR(CURRENT_DATE())";
    $stmt = $pdo->prepare($sql_m3_anual);
    $stmt->execute();
    $total_m3_anual = $stmt->fetchColumn();

    // Movimientos por día
    $sql_por_dia = "SELECT DATE(cam_fecha_hora) as fecha, COUNT(*) as cantidad
                    FROM camara
                    WHERE DATE(cam_fecha_hora) BETWEEN :fecha_desde AND :fecha_hasta
                    AND cam_emp_id != 13
                    GROUP BY DATE(cam_fecha_hora)
                    ORDER BY fecha";
    $stmt = $pdo->prepare($sql_por_dia);
    $stmt->execute([':fecha_desde' => $filtro_fecha_desde, ':fecha_hasta' => $filtro_fecha_hasta]);
    $movimientos_por_dia = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Top 5 empresas con más movimientos
    $sql_empresas = "SELECT e.emp_nombre, COUNT(*) as cantidad
                     FROM camara c
                     JOIN empresas e ON c.cam_emp_id = e.emp_id
                     WHERE DATE(c.cam_fecha_hora) BETWEEN :fecha_desde AND :fecha_hasta
                     AND c.cam_emp_id != 13
                     GROUP BY e.emp_nombre
                     ORDER BY cantidad DESC
                     LIMIT 5";
    $stmt = $pdo->prepare($sql_empresas);
    $stmt->execute([':fecha_desde' => $filtro_fecha_desde, ':fecha_hasta' => $filtro_fecha_hasta]);
    $top_empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Últimos 10 registros
    $sql_ultimos = "SELECT c.cam_fecha_hora, p.pat_patente, e.emp_nombre, c.cam_tipo, p.pat_m3
                   FROM camara c
                   JOIN patentes p ON c.cam_pat_id = p.pat_id
                   JOIN empresas e ON c.cam_emp_id = e.emp_id
                   WHERE DATE(c.cam_fecha_hora) BETWEEN :fecha_desde AND :fecha_hasta
                   AND c.cam_emp_id != 13
                   ORDER BY c.cam_fecha_hora DESC
                   LIMIT 10";
    $stmt = $pdo->prepare($sql_ultimos);
    $stmt->execute([':fecha_desde' => $filtro_fecha_desde, ':fecha_hasta' => $filtro_fecha_hasta]);
    $ultimos_registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("<div class='ui error message'><strong>Error en consultas:</strong> ".htmlspecialchars($e->getMessage())."</div>");
}

// Preparar datos para gráficos
$chart_dias = [];
$chart_cantidades = [];
foreach ($movimientos_por_dia as $dia) {
    $chart_dias[] = "'" . date('d/m', strtotime($dia['fecha'])) . "'";
    $chart_cantidades[] = $dia['cantidad'];
}

$chart_empresas = [];
$chart_empresas_cant = [];
foreach ($top_empresas as $empresa) {
    $chart_empresas[] = "'" . $empresa['emp_nombre'] . "'";
    $chart_empresas_cant[] = $empresa['cantidad'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Resumen de Camiones</title>
    <?php require("librerias.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --success: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
            --light: #ecf0f1;
            --dark: #34495e;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--secondary), var(--dark));
            color: white;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        
        .dashboard-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
            padding: 0 10px;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.3s ease;
            min-height: 90px;
        }
        
        .dashboard-card:active {
            transform: scale(0.98);
        }
        
        .card-icon {
            font-size: 1.2rem;
            margin-bottom: 5px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }
        
        .card-icon.blue { background-color: var(--primary); }
        .card-icon.green { background-color: var(--success); }
        .card-icon.orange { background-color: var(--warning); }
        .card-icon.red { background-color: var(--danger); }
        
        .card-value {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 2px;
            text-align: center;
        }
        
        .card-label {
            color: #7f8c8d;
            font-size: 0.7rem;
            text-align: center;
        }
        
        .card-period {
            color: #95a5a6;
            font-size: 0.6rem;
            text-align: center;
            margin-top: 3px;
        }
        
        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 15px 10px;
            margin: 0 10px 15px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .chart-container h3 {
            margin: 0 0 10px 5px;
            color: var(--secondary);
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
        }
        
        .chart-container h3 i {
            margin-right: 8px;
            font-size: 0.9rem;
        }
        
        .chart-wrapper {
            position: relative;
            height: 0;
            padding-bottom: 75%;
            overflow: hidden;
        }
        
        .chart-wrapper canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%!important;
            height: 100%!important;
        }
        
        .chart-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
            font-size: 0.7rem;
            color: #666;
        }
        
        .chart-stat {
            text-align: center;
            padding: 0 5px;
        }
        
        .chart-stat .value {
            font-weight: bold;
            color: var(--primary);
            font-size: 0.8rem;
        }
        
        .data-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin: 0 10px 15px;
            width: calc(100% - 20px);
            overflow-x: auto;
        }
        
        .data-table table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }
        
        .data-table th {
            background-color: var(--secondary);
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .data-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #eee;
            font-size: 0.8rem;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            color: white;
            display: inline-block;
        }
        
        .badge-entry {
            background-color: var(--success);
        }
        
        .badge-exit {
            background-color: var(--danger);
        }
        
        .date-filter-container {
            background: white;
            padding: 15px;
            margin: 0 10px 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .date-filter-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .date-filter-row {
            display: flex;
            gap: 10px;
        }
        
        .date-filter-row .field {
            flex: 1;
        }
        
        .date-filter-row label {
            font-size: 0.8rem;
            margin-bottom: 5px;
            display: block;
            color: #555;
        }
        
        .date-filter-row .ui.input {
            width: 100%;
        }
        
        .date-filter-row .ui.input input {
            padding: 8px 10px;
            font-size: 0.8rem;
        }
        
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1000;
            background: rgba(44, 62, 80, 0.9);
            color: white;
            border: none;
            border-radius: 5px;
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        .mobile-menu-btn:active {
            transform: scale(0.95);
        }
        
        .main-content {
            padding: 15px 0;
            margin-left: 0;
            transition: margin-left 0.3s;
            padding-top: 60px;
        }
        
        .main-content.with-sidebar {
            margin-left: 250px;
        }

        #sidebar {
            position: fixed;
            width: 250px;
            height: 100vh;
            overflow-y: auto;
            z-index: 1001;
            background: #2c3e50;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        #sidebar.visible {
            transform: translateX(0);
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            display: none;
        }
        
        /* Efecto de ripple para botones */
        .ripple {
            position: relative;
            overflow: hidden;
        }
        
        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        /* Media queries para móviles */
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
                padding: 0 15px;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .main-content {
                margin-left: 0;
                padding-top: 70px;
            }
            
            #sidebar {
                transform: translateX(-100%);
            }
            
            #sidebar.visible {
                transform: translateX(0);
            }
            
            .chart-container {
                margin-left: 0;
                margin-right: 0;
                border-radius: 0;
                width: 100%;
            }
            
            .data-table {
                margin-left: 0;
                margin-right: 0;
                width: 100%;
                border-radius: 0;
            }
        }
        
        @media (min-width: 769px) {
            #sidebar {
                transform: translateX(0) !important;
            }
            
            .main-content {
                margin-left: 250px;
                padding-top: 15px;
            }
            
            .mobile-menu-btn {
                display: none !important;
            }
        }
        
        /* Estilos para pantallas muy pequeñas */
        @media (max-width: 350px) {
            .dashboard-header h1 {
                font-size: 1.3rem;
            }
            
            .dashboard-header p {
                font-size: 0.8rem;
            }
            
            .card-value {
                font-size: 0.9rem;
            }
            
            .chart-container h3 {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <?php require("menu_mobile.php"); ?>

    <div class="main-content" id="main-content">
        <div id="dashboard" class="active">
            <div class="dashboard-header">
                <h1>Resumen de Camiones</h1>
                <p>Estadísticas de movimientos vehiculares - Millancura</p>
            </div>
            
            <!-- Filtros por fecha -->
            <div class="date-filter-container">
                <form method="get" class="ui form date-filter-form">
                    <div class="date-filter-row">
                        <div class="field">
                            <label>Fecha Desde</label>
                            <div class="ui input">
                                <input type="date" name="fecha_desde" value="<?= $filtro_fecha_desde ?>">
                            </div>
                        </div>
                        <div class="field">
                            <label>Fecha Hasta</label>
                            <div class="ui input">
                                <input type="date" name="fecha_hasta" value="<?= $filtro_fecha_hasta ?>">
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="ui primary button ripple" style="width: 100%;">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Estadísticas principales -->
            <div class="dashboard-grid">
                <div class="dashboard-card ripple" onclick="focusChart('chartDias')">
                    <div class="card-icon blue">
                        <i class="fas fa-truck-moving"></i>
                    </div>
                    <div>
                        <div class="card-value"><?= number_format($total_movimientos) ?></div>
                        <div class="card-label">Movimientos totales</div>
                        <div class="card-period">Período seleccionado</div>
                    </div>
                </div>
                
                <div class="dashboard-card ripple" onclick="focusChart('chartEmpresas')">
                    <div class="card-icon green">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div>
                        <div class="card-value"><?= number_format($total_m3_mes, 2) ?></div>
                        <div class="card-label">m³ mes actual</div>
                        <div class="card-period"><?= date('F Y') ?></div>
                    </div>
                </div>
                
                <div class="dashboard-card ripple" onclick="focusChart('chartEmpresas')">
                    <div class="card-icon orange">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <div class="card-value"><?= number_format($total_m3_anual, 2) ?></div>
                        <div class="card-label">m³ año actual</div>
                        <div class="card-period"><?= date('Y') ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de movimientos por día -->
            <div class="chart-container">
                <h3><i class="fas fa-calendar-day"></i> Movimientos por día</h3>
                <div class="chart-wrapper">
                    <canvas id="chartDias"></canvas>
                </div>
                <div class="chart-stats">
                    <div class="chart-stat">
                        <div class="value"><?= array_sum($chart_cantidades) ?></div>
                        <div class="label">Total</div>
                    </div>
                    <div class="chart-stat">
                        <div class="value"><?= max($chart_cantidades) ?></div>
                        <div class="label">Máximo</div>
                    </div>
                    <div class="chart-stat">
                        <div class="value"><?= round(array_sum($chart_cantidades)/count($chart_cantidades), 1) ?></div>
                        <div class="label">Promedio</div>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de distribución por empresa -->
            <div class="chart-container">
                <h3><i class="fas fa-building"></i> Distribución por empresa</h3>
                <div class="chart-wrapper">
                    <canvas id="chartEmpresas"></canvas>
                </div>
                <div class="chart-stats">
                    <div class="chart-stat">
                        <div class="value"><?= count($top_empresas) ?></div>
                        <div class="label">Empresas</div>
                    </div>
                    <div class="chart-stat">
                        <div class="value"><?= array_sum($chart_empresas_cant) ?></div>
                        <div class="label">Movimientos</div>
                    </div>
                    <div class="chart-stat">
                        <div class="value"><?= round(array_sum($chart_empresas_cant)/count($chart_empresas_cant), 1) ?></div>
                        <div class="label">Promedio</div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de últimos registros -->
            <div class="data-table">
                <h3 style="padding: 10px 15px 5px; margin: 0; font-size: 1rem;"><i class="fas fa-list"></i> Últimos movimientos</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Patente</th>
                            <th>Empresa</th>
                            <th>Tipo</th>
                            <th>m³</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimos_registros as $registro): ?>
                            <?php
                            $fecha = new DateTime($registro["cam_fecha_hora"]);
                            $tipo_class = $registro["cam_tipo"] == 'entrada' ? 'badge-entry' : 'badge-exit';
                            ?>
                            <tr>
                                <td><?= $fecha->format('H:i') ?></td>
                                <td><?= htmlspecialchars($registro["pat_patente"]) ?></td>
                                <td><?= htmlspecialchars(substr($registro["emp_nombre"], 0, 15)) ?></td>
                                <td><span class="badge <?= $tipo_class ?>"><?= ucfirst($registro["cam_tipo"]) ?></span></td>
                                <td><?= ($registro["cam_tipo"] == 'entrada' ? number_format($registro["pat_m3"], 2) : '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Control del menú móvil
            $('#mobileMenuBtn').on('click', function(e) {
                e.stopPropagation();
                $('#sidebar').toggleClass('visible');
                $('.sidebar-overlay').toggle();
                createRippleEffect(e, this);
            });
            
            // Cerrar menú al hacer clic en el overlay
            $('.sidebar-overlay').on('click', function() {
                $('#sidebar').removeClass('visible');
                $(this).hide();
            });
                    
            // Ajustar menú al cambiar tamaño de pantalla
            $(window).on('resize', function() {
                if ($(window).width() >= 769) {
                    $('#sidebar').addClass('visible');
                    $('.sidebar-overlay').hide();
                }
            }).trigger('resize');
            
            // Navegación del menú
            $('.menu-item, #sidebar a').on('click', function(e) {
                if ($(window).width() < 769) {
                    $('#sidebar').removeClass('visible');
                    $('.sidebar-overlay').hide();
                }
                
                $('.menu-item').removeClass('active');
                $(this).addClass('active');
                $('#dashboard, #database').removeClass('active');
                $('#' + $(this).data('section')).addClass('active');
                
                createRippleEffect(e, this);
            });

            // Gráfico de movimientos por día
            const ctxDias = document.getElementById('chartDias').getContext('2d');
            new Chart(ctxDias, {
                type: 'bar',
                data: {
                    labels: [<?= implode(',', $chart_dias) ?>],
                    datasets: [{
                        label: 'Movimientos',
                        data: [<?= implode(',', $chart_cantidades) ?>],
                        backgroundColor: '#36a2eb',
                        borderColor: '#2980b9',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            titleFont: { size: 10 },
                            bodyFont: { size: 10 },
                            padding: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' movimientos';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { size: 9 },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { 
                                font: { size: 9 },
                                stepSize: 1,
                                precision: 0
                            },
                            grid: { 
                                color: 'rgba(0,0,0,0.05)',
                                drawBorder: false
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });

            // Gráfico de distribución por empresa
            const ctxEmpresas = document.getElementById('chartEmpresas').getContext('2d');
            new Chart(ctxEmpresas, {
                type: 'doughnut',
                data: {
                    labels: [<?= implode(',', $chart_empresas) ?>],
                    datasets: [{
                        data: [<?= implode(',', $chart_empresas_cant) ?>],
                        backgroundColor: [
                            '#e74c3c', '#3498db', '#f39c12', '#2ecc71', '#9b59b6'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                font: {
                                    size: 9
                                },
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            titleFont: { size: 10 },
                            bodyFont: { size: 10 },
                            padding: 8,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '60%',
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });

            // Efecto ripple para todos los elementos con clase .ripple
            $('.ripple').on('click', function(e) {
                createRippleEffect(e, this);
            });
            
            // Función para crear efecto ripple
            function createRippleEffect(event, element) {
                const btn = $(element);
                const x = event.pageX - btn.offset().left;
                const y = event.pageY - btn.offset().top;
                
                const ripple = document.createElement('span');
                ripple.className = 'ripple-effect';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                
                element.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            }
            
            // Función para animar al hacer clic en cards
            function focusChart(chartId) {
                const chartElement = document.getElementById(chartId);
                const chartContainer = chartElement.closest('.chart-container');
                
                // Scroll suave al gráfico
                chartContainer.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                // Efecto de destello
                chartContainer.style.transition = 'box-shadow 0.5s';
                chartContainer.style.boxShadow = '0 0 15px rgba(52, 152, 219, 0.5)';
                setTimeout(() => {
                    chartContainer.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.1)';
                }, 1000);
            }
        });
    </script>
    <div class="sidebar-overlay"></div>
</body>
</html>
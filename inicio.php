<?php
// Debug: Mostrar errores (quitar en producción)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require("conf.php");

// Verificar conexión a la base de datos
try {
    $pdo->query("SELECT 1")->fetch();
} catch (Exception $e) {
    die("<div class='ui error message'><strong>Error de conexión:</strong> ".htmlspecialchars($e->getMessage())."</div>");
}

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

    // Movimientos por tipo (entrada/salida)
    $sql_tipos = "SELECT cam_tipo, COUNT(*) as cantidad 
                 FROM camara 
                 WHERE DATE(cam_fecha_hora) BETWEEN :fecha_desde AND :fecha_hasta  AND cam_emp_id != 13
                 GROUP BY cam_tipo";
    $stmt = $pdo->prepare($sql_tipos);
    $stmt->execute([':fecha_desde' => $filtro_fecha_desde, ':fecha_hasta' => $filtro_fecha_hasta]);
    $movimientos_por_tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    // Total m3 Integral SEMANAL
    $sql_m3_integral = "SELECT SUM(p.pat_m3) as total_m3_integral 
               FROM camara c
               JOIN patentes p ON c.cam_pat_id = p.pat_id
               JOIN empresas e ON c.cam_emp_id = e.emp_id
               WHERE c.cam_tipo = 'entrada' 
               AND c.cam_emp_id != 13
               AND e.emp_tipo = 'Integral'
               AND DATE(c.cam_fecha_hora) BETWEEN :fecha_desde AND :fecha_hasta";
    $stmt = $pdo->prepare($sql_m3_integral);
    $stmt->execute([':fecha_desde' => $filtro_fecha_desde, ':fecha_hasta' => $filtro_fecha_hasta]);
    $total_m3_integral = $stmt->fetchColumn();

    // Total m3 Base SEMANAL
    $sql_m3_base = "SELECT SUM(p.pat_m3) as total_m3_base 
               FROM camara c
               JOIN patentes p ON c.cam_pat_id = p.pat_id
               JOIN empresas e ON c.cam_emp_id = e.emp_id
               WHERE c.cam_tipo = 'entrada' 
               AND c.cam_emp_id != 13
               AND e.emp_tipo = 'Base'
               AND DATE(c.cam_fecha_hora) BETWEEN :fecha_desde AND :fecha_hasta";
    $stmt = $pdo->prepare($sql_m3_base);
    $stmt->execute([':fecha_desde' => $filtro_fecha_desde, ':fecha_hasta' => $filtro_fecha_hasta]);
    $total_m3_base = $stmt->fetchColumn();

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

// Debug: Verificar si $url_base está definida
if(!isset($url_base)) {
    $url_base = ''; // Asignar un valor por defecto
    error_log("Advertencia: \$url_base no está definida, usando valor vacío");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Registros</title>
    <?php require("librerias.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }
        #main-content {
            width: 100%;
            max-width: 100%;
            padding: 0 !important;
        }
        .ui.grid {
            margin: 0 !important;
        }
        .ui.container.main-content {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
            padding: 0 15px !important;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid #2185d0;
        }
        .stat-card h3 {
            margin-top: 0;
            color: #555;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
        }
        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .filtros-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .tipo-entrada {
            color: #21ba45;
            font-weight: bold;
        }
        .tipo-salida {
            color: #db2828;
            font-weight: bold;
        }
        .stat-card.integral {
            border-left-color: #21ba45;
        }
        .stat-card.base {
            border-left-color: #f2711c;
        }
    </style>
</head>
<body>
<div class="ui container" style="padding: 0; width: 100%;">
    <div class="ui grid stackable" style="margin: 0; width: 100%; overflow-x: hidden;">
        <!-- Menú lateral -->
        <div class="four wide column" style="padding: 0;">
            <?php require("menu.php"); ?>
        </div>
        
        <!-- Contenido principal -->
        <div class="twelve column" id="main-content" style="padding: 0;">
            <div class="ui container main-content">
                <div class="dashboard-header">
                    <h1><i class="fas fa-tachometer-alt"></i> Resumen de Registros</h1>
                    <p>Estadísticas de movimientos vehiculares - Millancura</p>
                </div>

                <!-- Filtros por fecha -->
                <div class="filtros-container">
                    <form method="get" class="ui form">
                        <div class="two fields">
                            <div class="field">
                                <label>Fecha Desde</label>
                                <input type="date" name="fecha_desde" value="<?= $filtro_fecha_desde ?>" class="ui input">
                            </div>
                            <div class="field">
                                <label>Fecha Hasta</label>
                                <input type="date" name="fecha_hasta" value="<?= $filtro_fecha_hasta ?>" class="ui input">
                            </div>
                        </div>
                        <button type="submit" class="ui primary button"><i class="fas fa-filter"></i> Filtrar</button>
                    </form>
                </div>

                <!-- Estadísticas principales -->
                <div class="ui four column stackable grid">
                    <div class="column">
                        <div class="stat-card">
                            <h3><i class="fas fa-truck-moving"></i> Total Movimientos</h3>
                            <div class="stat-value"><?= number_format($total_movimientos) ?></div>
                            <p>Período seleccionado</p>
                        </div>
                    </div>
                    <div class="column">
                        <div class="stat-card">
                            <h3><i class="fas fa-cubes"></i> Total m³ Mes</h3>
                            <div class="stat-value"><?= number_format($total_m3_mes, 2) ?></div>
                            <p>Mes actual: <?= date('F Y') ?></p>
                        </div>
                    </div>
                    <div class="column">
                        <div class="stat-card integral">
                            <h3><i class="fas fa-cube"></i> m³ Integral</h3>
                            <div class="stat-value"><?= number_format($total_m3_integral, 2) ?></div>
                            <p>Período seleccionado</p>
                        </div>
                    </div>
                    <div class="column">
                        <div class="stat-card base">
                            <h3><i class="fas fa-cube"></i> m³ Base</h3>
                            <div class="stat-value"><?= number_format($total_m3_base, 2) ?></div>
                            <p>Período seleccionado</p>
                        </div>
                    </div>
                </div>

                <!-- Segunda fila de estadísticas -->
                <div class="ui one column stackable grid">
                    <div class="column">
                        <div class="stat-card">
                            <h3><i class="fas fa-calendar-alt"></i> Total m³ Año</h3>
                            <div class="stat-value"><?= number_format($total_m3_anual, 2) ?></div>
                            <p>Año actual: <?= date('Y') ?></p>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="ui two column stackable grid">
                    <div class="column">
                        <div class="chart-container">
                            <h3>Movimientos por Día</h3>
                            <canvas id="chartDias" height="300"></canvas>
                        </div>
                    </div>
                    <div class="column">
                        <div class="chart-container">
                            <h3>Top 5 Empresas</h3>
                            <canvas id="chartEmpresas" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tabla de últimos registros -->
                <div class="chart-container">
                    <h3>Últimos Registros</h3>
                    <?php
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
                    
                    if (count($ultimos_registros) > 0) {
                        echo '<table class="ui celled table">';
                        echo '<thead><tr>
                                <th>Fecha/Hora</th>
                                <th>Patente</th>
                                <th>Empresa</th>
                                <th>Tipo</th>
                                <th>m³</th>
                              </tr></thead>';
                        echo '<tbody>';
                        
                        foreach ($ultimos_registros as $registro) {
                            $fecha = new DateTime($registro["cam_fecha_hora"]);
                            $tipo_class = $registro["cam_tipo"] == 'entrada' ? 'tipo-entrada' : 'tipo-salida';
                            
                            echo '<tr>';
                            echo '<td>' . $fecha->format('d/m/Y H:i:s') . '</td>';
                            echo '<td>' . htmlspecialchars($registro["pat_patente"]) . '</td>';
                            echo '<td>' . htmlspecialchars($registro["emp_nombre"]) . '</td>';
                            echo '<td><span class="' . $tipo_class . '">' . ucfirst($registro["cam_tipo"]) . '</span></td>';
                            echo '<td>' . ($registro["cam_tipo"] == 'entrada' ? number_format($registro["pat_m3"], 2) : '-') . '</td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody></table>';
                    } else {
                        echo '<div class="ui message">No hay registros en el período seleccionado</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Gráfico de movimientos por día
    const ctxDias = document.getElementById('chartDias').getContext('2d');
    new Chart(ctxDias, {
        type: 'bar',
        data: {
            labels: [<?= implode(',', $chart_dias) ?>],
            datasets: [{
                label: 'Movimientos por día',
                data: [<?= implode(',', $chart_cantidades) ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de top empresas
    const ctxEmpresas = document.getElementById('chartEmpresas').getContext('2d');
    new Chart(ctxEmpresas, {
        type: 'doughnut',
        data: {
            labels: [<?= implode(',', $chart_empresas) ?>],
            datasets: [{
                data: [<?= implode(',', $chart_empresas_cant) ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>
</body>
</html>
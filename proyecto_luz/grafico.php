<?php
require("../conf.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Analytics - Vista Cliente</title>
    <?php require("../librerias.php"); ?>
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        
        .card-title {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .metric-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0.5rem 0;
        }
        
        .metric-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .progress-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            margin-top: 1rem;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--accent);
            border-radius: 4px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1.5rem;
        }
        
        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.75rem;
        }
        
        .badge-primary {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }
        
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>

<?php 
// require("../menu.php"); 
?>

<div class="dashboard-container">
    <!-- Header -->
    <div class="header">
        <div>
            <h1 style="margin: 0; color: var(--dark);">Panel de Analytics</h1>
            <p style="margin: 0.5rem 0 0; color: #6c757d;">Resumen completo de métricas para tu negocio</p>
        </div>
        <div>
            <span class="ui blue label">Actualizado: <?php echo date('d/m/Y H:i'); ?></span>
        </div>
    </div>
    
    <!-- Row 1: Metricas rápidas -->
    <div class="ui four column stackable grid">
        <div class="column">
            <div class="card metric-card">
                <i class="chart line icon" style="color: var(--accent); font-size: 1.5rem;"></i>
                <div class="metric-value">12,548</div>
                <div class="metric-label">Visitas</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 78%"></div>
                </div>
            </div>
        </div>
        <div class="column">
            <div class="card metric-card">
                <i class="shopping cart icon" style="color: var(--accent); font-size: 1.5rem;"></i>
                <div class="metric-value">1,254</div>
                <div class="metric-label">Conversiones</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 65%"></div>
                </div>
            </div>
        </div>
        <div class="column">
            <div class="card metric-card">
                <i class="money bill wave icon" style="color: var(--accent); font-size: 1.5rem;"></i>
                <div class="metric-value">$3.2M</div>
                <div class="metric-label">Ingresos</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 82%"></div>
                </div>
            </div>
        </div>
        <div class="column">
            <div class="card metric-card">
                <i class="users icon" style="color: var(--accent); font-size: 1.5rem;"></i>
                <div class="metric-value">8,742</div>
                <div class="metric-label">Usuarios</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 58%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Row 2: Gráficos principales -->
    <div class="ui two column stackable grid">
        <div class="column">
            <div class="card">
                <div class="card-title">
                    <i class="chart bar icon"></i> Tráfico por Canal
                    <span class="badge badge-primary">Últimos 30 días</span>
                </div>
                <div class="chart-container">
                    <canvas id="trafficChart"></canvas>
                </div>
            </div>
        </div>
        <div class="column">
            <div class="card">
                <div class="card-title">
                    <i class="chart pie icon"></i> Conversiones por Dispositivo
                    <span class="badge badge-primary">Tasa de conversión</span>
                </div>
                <div class="chart-container">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Row 3: Tabla y gráfico secundario -->
    <div class="ui two column stackable grid">
        <div class="column">
            <div class="card">
                <div class="card-title">
                    <i class="table icon"></i> Top Páginas
                    <span class="badge badge-primary">Por visitas</span>
                </div>
                <div class="table-responsive">
                    <table class="ui striped table">
                        <thead>
                            <tr>
                                <th>Página</th>
                                <th>Visitas</th>
                                <th>Tasa Rebote</th>
                                <th>Duración</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>/inicio</td>
                                <td>4,582</td>
                                <td>32%</td>
                                <td>2:45</td>
                            </tr>
                            <tr>
                                <td>/productos</td>
                                <td>3,124</td>
                                <td>45%</td>
                                <td>3:12</td>
                            </tr>
                            <tr>
                                <td>/contacto</td>
                                <td>2,845</td>
                                <td>28%</td>
                                <td>1:56</td>
                            </tr>
                            <tr>
                                <td>/blog</td>
                                <td>1,956</td>
                                <td>51%</td>
                                <td>4:23</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="column">
            <div class="card">
                <div class="card-title">
                    <i class="chart line icon"></i> Tendencias Mensuales
                    <span class="badge badge-primary">2025</span>
                </div>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico de tráfico por canal
const trafficCtx = document.getElementById('trafficChart').getContext('2d');
const trafficChart = new Chart(trafficCtx, {
    type: 'bar',
    data: {
        labels: ['Directo', 'Orgánico', 'Social', 'Email', 'Referido'],
        datasets: [{
            label: 'Visitas',
            data: [3520, 5840, 2150, 980, 1058],
            backgroundColor: [
                '#4361ee',
                '#4895ef',
                '#3f37c9',
                '#4cc9f0',
                '#560bad'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Gráfico de dispositivos
const deviceCtx = document.getElementById('deviceChart').getContext('2d');
const deviceChart = new Chart(deviceCtx, {
    type: 'doughnut',
    data: {
        labels: ['Mobile', 'Desktop', 'Tablet'],
        datasets: [{
            data: [65, 30, 5],
            backgroundColor: [
                '#4361ee',
                '#4895ef',
                '#3f37c9'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});

// Gráfico de tendencias
const trendCtx = document.getElementById('trendChart').getContext('2d');
const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        datasets: [
            {
                label: 'Visitas',
                data: [8500, 9200, 10200, 11500, 12500, 13400],
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                fill: true,
                tension: 0.3
            },
            {
                label: 'Conversiones',
                data: [750, 920, 1050, 1200, 1250, 1400],
                borderColor: '#4895ef',
                backgroundColor: 'rgba(72, 149, 239, 0.1)',
                fill: true,
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

</body>
</html>
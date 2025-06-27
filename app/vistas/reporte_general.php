<?php
session_start();

if (!isset($_SESSION["loggedin_neonled"]) || $_SESSION["loggedin_neonled"] !== true) {
    header("location: ../../index.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
    <head>    
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="../libs/bootstrap.css">
        <link rel="stylesheet" href="../libs/all.css">
        <link rel="stylesheet" href="../libs/datatables.css">
        <link rel="stylesheet" href="../css/neonled.css">
        <title>NeonLed | Reporte</title>
    </head>
    <body>

        <!-- Navbar -->
        <?php include 'templates/nav.php'?>        
        <!-- Navbar -->

        <div class="container mt-3">

            <!-- Título y botón agregar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 id="titulo">Reporte General</h1>
                </div>  
                <!-- <div>
                    <button class="btn btn-sm btn-primary" title="Agregar nuevo cliente" data-toggle="modal" data-target="#agregar_cliente"><i class="fa fa-plus"></i></button>
                </div> -->
            </div>

            <!-- Total proyectos ultimos 12 meses -->
            <div class="row mt-3">
                <div class="col-12 ">                    
                    <h5>Total proyectos últimos 12 meses</h5>                                            
                    <div class="chart-container mt-3">
                        <canvas id="g_proyectos_ultimos_12" style="height:250px"></canvas>
                    </div>
                </div>                
            </div>

            <!-- Total proyectos ultimos 12 meses -->
            <div class="row mt-5">
                <div class="col-12">
                    <h5>Total ventas últimos 12 meses (valores sin IVA)</h5>
                    <div class="chart-container mt-3">
                        <canvas id="g_ventas_ultimos_12" style="height:250px"></canvas>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <h5>Total ventas últimos 12 meses (valores con IVA)</h5>
                    <div class="chart-container mt-3">
                        <canvas id="g_ventas_ultimos_12_iva" style="height:250px"></canvas>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <h5>Promedio ventas últimos 12 meses (valores sin IVA)</h5>                    
                    <div class="chart-container mt-3">
                        <canvas id="g_promedio_ventas_ultimos_12" style="height:250px"></canvas>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <h5>Promedio ventas últimos 12 meses (valores con IVA)</h5>                    
                    <div class="chart-container mt-3">
                        <canvas id="g_promedio_ventas_ultimos_12_iva" style="height:250px"></canvas>
                    </div>
                </div>
            </div>
            
        </div>        
        
        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="../libs/datatables.js"></script>
        <script src="../libs/chart.js"></script>
        <script src="../libs/datalabels.js"></script>
        <script type="module" src="../js/reporte_general.js?<?php echo time();?>"></script>
    </body>
</html>
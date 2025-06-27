<?php
session_start();

if (!isset($_SESSION["loggedin_neonled"]) || $_SESSION["loggedin_neonled"] !== true) {
    header("location: ../../index.php");
    exit;
}

?>
<!doctype html>
<html lang="es-CL">
    <head>    
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="../libs/bootstrap.css">
        <link rel="stylesheet" href="../libs/all.css">
        <!-- <link rel="stylesheet" href="../libs/datatables.css"> -->
        <link rel="stylesheet" href="//cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">
        <link rel="stylesheet" href="../css/neonled.css">
        <title>NeonLed | Operacion</title>
    </head>
    <body>

        <!-- Navbar -->
        <?php include 'templates/nav.php'?>        
        <!-- Navbar -->

        <div class="container-fluid mt-3">

            <!-- Título y botón agregar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 id="titulo">Pedidos</h1>
                </div>  
                <div>
                    <a href="proyectos_nuevo.php" class="btn btn-sm btn-primary" title="Nuevo Proyecto"><i class="fa fa-plus"></i></a>
                </div>
            </div>
            <div id="lista_proyectos"></div>
        </div>        
        
        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="//cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
        <script type="module" src="../js/proyectos.js?<?php echo time();?>"></script>
    </body>
</html>
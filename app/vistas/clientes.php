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
        <!-- <link rel="stylesheet" href="../libs/datatables.css">-->
        <link rel="stylesheet" href="//cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">
        <link rel="stylesheet" href="../css/neonled.css">
        <title>NeonLed | Clientes</title>
    </head>
    <body>

        <!-- Navbar -->
        <?php include 'templates/nav.php'?>        
        <!-- Navbar -->

        <div class="container mt-3">

            <!-- Título y botón agregar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 id="titulo">Clientes</h1>
                </div>  
                <div>
                    <a href="clientes_nuevo.php" class="btn btn-sm btn-primary" title="Nuevo Cliente"><i class="fa fa-plus"></i></a>
                </div>
            </div>
            
            <!-- Lista -->
            <table id="tabla_proyectos" class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Rut</th>
                        <th>Teléfono</th>
                        <th>Mail</th>
                        <th>Proyectos</th>
                        <th width="20px">Detalle</th>
                    </tr>
                </thead>
                <tbody id="lista_proyectos">                                                          
                </tbody>
            </table>
        </div>        
        
        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="//cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
        <script src="../js/clientes.js?<?php echo time();?>"></script>
    </body>
</html>
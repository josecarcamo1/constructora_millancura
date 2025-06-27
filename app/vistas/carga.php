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
        <title>NeonLed | Nuevo Cliente</title>
    </head>
    <body>

        <!-- Navbar -->
        <?php include 'templates/nav.php'?>        
        <!-- Navbar -->

        <div class="container mt-3">

            <!-- Título y botón agregar -->
            <!-- <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 id="titulo">Cargas CSV</h1>
                </div>                  
            </div>

            <div class="mt-3">
                <h5>Agregar Proyectos</h5>
                <form id="agregar_proyectos" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="col">
                            <label for="lista_proyectos">Lista proyectos (csv)</label>
                            <input type="file" name="files[]" class="form-control" id="lista_proyectos" aria-describedby="inputGroupFileAddon04" aria-label="Upload" multiple>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3" type="submit">Subir</button>
                </form>
            </div> -->

            <button id="act" class="btn btn-primary">Actualizar pagos</button>
        </div>        
        
        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="../libs/datatables.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.2.0/papaparse.min.js"></script>
        <script type="module" src="../js/carga.js?<?php echo time();?>"></script>
    </body>
</html>
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
        <title>NeonLed | Detalle Cliente</title>
    </head>
    <body>

        <!-- Navbar -->
        <?php include 'templates/nav.php'?>        
        <!-- Navbar -->

        <div class="container mt-3">

            <!-- Título y botón agregar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 id="titulo">Detalle Vendedor</h1>
                </div>
            </div>

            <form id="info_vendedor">
                <div class="row">
                    <div class="form-group col-auto">
                        <label for="validationTooltip05">Color</label>
                        <input type="color" class="form-control form-control-color" id="color">
                    </div>
                    <div class="form-group col-12 col-md">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre">
                    </div>
                    <div class="form-group col-12 col-md-3">
                        <label for="selector_estados">Estado</label>
                        <select class="form-select" aria-label="Estado" id="selector_estados">                            
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>                                
                        </select>
                    </div>                    
                </div>
                
                <div id="botones" class="py-3" style="background-color:white;">                    
                    <button id="editar" type="submit" class="btn btn-warning text-white btn-edit">Editar</button>                    
                </div>
            </form>                        
        </div>        
        
        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="../libs/datatables.js"></script>
        <script type="module" src="../js/vendedor_detalle.js?<?php echo time();?>"></script>
    </body>
</html>
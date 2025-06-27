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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 id="titulo">Nuevo Cliente</h1>
                </div>                  
            </div>

            <form id="nuevo_cliente">
                <div class="row">
                    <div class="form-group col-12 col-md">
                        <label for="validationTooltip03">Nombre</label>
                        <input type="text" class="form-control" id="nombre">
                    </div>                                    
                    <div class="form-group col-12 col-md-3">
                        <label for="validationTooltip05">Rut</label>
                        <input type="text" class="form-control" id="rut">
                    </div>                                    
                    <div class="form-group col-12 col-md-3">
                        <label for="validationTooltip04">Teléfono</label>
                        <input type="text" class="form-control" id="telefono">
                    </div>                    
                </div>
                <div class="row mt-2">
                    <div class="form-group col-12 col-md">
                        <label for="correo">Correo</label>
                        <input type="text" class="form-control" id="correo">
                    </div> 
                    <div class="form-group col-12 col-md">
                        <label for="direccion">Dirección</label>
                        <input type="text" class="form-control" id="direccion">
                    </div>                                   
                </div>
                <div class="row mt-2">                                   
                    <div class="form-group col-12 col-md">
                        <label for="comuna">Comuna</label>
                        <input id="com" type="text" class="form-control" list="comunas" autocomplete="off">
                            <datalist id="comunas" name="comunas_datalist">
                        </datalist>                        
                    </div>
                    <div class="form-group col-12 col-md">
                        <label for="provincia">Provincia</label>
                        <input type="text" class="form-control" id="provincia" disabled>
                    </div>
                    <div class="form-group col-12 col-md">
                        <label for="region">Región</label>
                        <input type="text" class="form-control" id="region" disabled>
                    </div>                                    
                </div>
                
                <button type="submit" class="btn btn-primary emitir mt-2">
                    Guardar
                    <div id="spinner" class="spinner-border text-light spinner-border-sm spinner" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </button>
            </form>                        
        </div>        
        
        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="../libs/datatables.js"></script>
        <script type="module" src="../js/clientes_nuevo.js?<?php echo time();?>"></script>
    </body>
</html>
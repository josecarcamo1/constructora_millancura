<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin_neonled"]) || $_SESSION["loggedin_neonled"] !== true) {
    header("location: ../../");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Nuevo Usuario</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../libs/bootstrap.css">
        <link rel="stylesheet" href="../css/neonled.css">
        <link rel="stylesheet" href="../libs/all.css">
    </head>
    <body>
        <!-- Navbar -->
        <?php include 'templates/nav.php'?>
        <!-- Navbar -->

        <div class="container my-3">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3>Cambiar Contraseña</h3>
                </div>                              
            </div>

            <form id="editar_contr" enctype="multipart/form-data" class="my-3" autocomplete="off">
                
                <div class="row">
                    <div class="col-12 col-md">
                        <label for="correo">Contraseña actual</label>
                        <input type="password" id="actual" class="form-control">
                    </div>
                    <div class="col-12 col-md">
                        <label for="nombre">Contraseña nueva</label>
                        <input type="password" id="nueva" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">                    
                    Cambiar
                    <div id="spinner" class="spinner-border text-light spinner-border-sm" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </button>
            </form>
        </div>        

        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="../js/nav.js?<?php echo time();?>"></script>
        <script type="module" src="../js/cambiar_contr.js?<?php echo time();?>"></script>
    </body>
</html>

<?php
session_start();

if (!isset($_SESSION["loggedin_neonled"]) || $_SESSION["loggedin_neonled"] !== true) {
    header("location: ../../");
    exit;
}

if ($_SESSION["acceso"] != "Administrador") {
    header("location: ../../");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Lista Usuarios</title>        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../libs/bootstrap.css">        
        <link rel="stylesheet" href="../libs/datatables.css">
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
                    <h3>Lista Usuarios</h3>
                </div>
                <div class="ms-auto">
                    <a href="usuarios_nuevo.php" class="btn btn-xl text-white btn-primary" title="Agregar nuevo usuario"><i class="fa fa-plus"></i></a>
                </div>
                <!-- <div class="ms-2">
                    <button id="correo_prueba" type="button" class="btn btn-xl text-white btn-warning" title="Correo Prueba"><i class="fa fa-envelope"></i></button>
                </div> -->
            </div>

            <div id="lista" class="mt-3">                
                <table id="lista_dt" class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th class="text-left" width="20px">#</th>
                            <th class="text-left">Nombre</th>
                            <th class="text-left">Correo</th>                            
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acceso</th>
                            <th class="text-center" width="20px">Editar</th>
                        </tr>
                    </thead>
                    <tbody id="lista_items">                        
                    </tbody>
                </table>
            </div>
        </div>        

        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="../libs/datatables.js"></script>
        <script src="../js/nav.js?<?php echo time();?>"></script>
        <script type="module" src="../js/usuarios_lista.js?<?php echo time();?>"></script>
    </body>
</html>

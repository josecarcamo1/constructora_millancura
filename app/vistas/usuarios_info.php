<?php
session_start();
// Check if the user is logged in, if not then redirect him to login page
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

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Información Usuarios</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../libs/bootstrap.css">
        <link rel="stylesheet" href="../libs/datatables.css">
        <link rel="stylesheet" href="../css/neonled.css">
        <link rel="stylesheet" href="../libs/all.css">
    </head>

    <body>
        <!-- Navbar -->
        <?php include 'templates/nav.php' ?>
        <!-- Navbar -->

        <div class="container my-3">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3>Información del usuario</h3>
                </div>
                <div>
                    <button id="borrar_usuario" class="btn btn-xl btn-danger" title="Borrar usuario"><i class="fa fa-times"></i></button>
                </div>
            </div>

            <form id="info_usuario" enctype="multipart/form-data" class="my-3">

                <div class="row">
                    <div class="col-12 col-md">
                        <label for="correo">Correo:</label>
                        <input type="text" id="correo" class="form-control" disabled>
                    </div>
                    <div class="col-12 col-md">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" class="form-control">
                    </div>
                    <div class="col-12 col-md">
                        <label for="selector_estado">Estado:</label>
                        <select id="selector_estado" class="form-select">
                            <option></option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-12 col-md">
                        <label for="selector_tipo_usuario">Tipo Usuario:</label>
                        <select id="selector_tipo_usuario" class="form-select">
                            <option></option>
                            <option value="Administrador">Administrador</option>
                            <option value="Vendedor">Vendedor</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    Guardar
                    <div id="spinner" class="spinner-border text-light spinner-border-sm" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </button>
            </form>

            <div id="accesos_vendedores" style="display:none">
                <h3>Agregar accesos a vendedor</h3>
                <div class="d-flex justify-content-between">                
                    <select id="selector_vendedores" class="form-select">
                        <option></option>                            
                    </select>
                    <button id="agregar_acceso" type="button" class="btn btn-primary mx-2" title="Agregar Acceso"><i class="fa fa-plus"></i></a></button>
                </div>

                <div id="lista" class="mt-3">
                    <table id="lista_dt" class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th class="text-left" width="20px">#</th>
                                <th class="text-left">Nombre</th>                                
                                <th class="text-center" width="20px">Borrar</th>
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
            <script type="module" src="../js/usuarios_info.js?<?php echo time();?>"></script>
        </div>
    </body>
</html>

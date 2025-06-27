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
                    <h1 id="titulo">Detalle Cliente</h1>
                </div>
                <div>
                    <button type="button" id="borrar" class="btn btn-sm btn-danger" title="Borrar Cliente"><i class="fa fa-times"></i></a>
                </div>
            </div>

            <form id="info_cliente" class="lectura">
                <div class="row">
                    <div class="form-group col-12 col-md">
                        <label for="validationTooltip03">Nombre</label>
                        <input type="text" class="form-control" id="nombre" disabled>
                    </div>                                    
                    <div class="form-group col-12 col-md-3">
                        <label for="validationTooltip05">Rut</label>
                        <input type="text" class="form-control" id="rut" disabled>
                    </div>                                    
                    <div class="form-group col-12 col-md-3">
                        <label for="validationTooltip04">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" disabled>
                    </div>                    
                </div>
                <div class="row mt-2">
                    <div class="form-group col-12 col-md">
                        <label for="correo">Correo</label>
                        <input type="text" class="form-control" id="correo" disabled>
                    </div> 
                    <div class="form-group col-12 col-md">
                        <label for="direccion">Dirección</label>
                        <input type="text" class="form-control" id="direccion" disabled>
                    </div>                                   
                </div>
                <div class="row mt-2">                                   
                    <div class="form-group col-12 col-md">
                        <label for="comuna">Comuna</label>
                        <input id="com" type="text" class="form-control" list="comunas" autocomplete="off" disabled>
                            <datalist id="comunas" name="comunas_datalist">
                        </datalist>                        
                    </div>
                    <div class="form-group col-12 col-md">
                        <label for="provincia">Provincia</label>
                        <input type="text" class="form-control fijo" id="provincia" disabled>
                    </div>
                    <div class="form-group col-12 col-md">
                        <label for="region">Región</label>
                        <input type="text" class="form-control fijo" id="region" disabled>
                    </div>                                    
                </div>
                
                <div id="botones" class="py-3" style="background-color:white;">                    
                    <button id="editar" type="button" class="btn btn-warning text-white btn-edit">Editar</button>
                    <button id="guardar" type="submit" class="btn btn-success btn-save mr-2 emitir">Guardar
                        <div id="spinner" class="spinner-border text-light spinner-border-sm ml-2 spinner" role="status">
                        </div>
                    </button>
                    <button id="cancelar" type="button" class="btn btn-primary btn-save emitir">Cancelar</button>
                </div>
            </form>                        
        </div>        
        
        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="../libs/datatables.js"></script>
        <script type="module" src="../js/clientes_detalle.js?<?php echo time();?>"></script>
    </body>
</html>
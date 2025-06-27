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
        <link rel="stylesheet" href="../libs/datatables.css">
        <link rel="stylesheet" href="../css/neonled.css">
        <title>NeonLed | Operacion</title>
    </head>
    <body>

        <!-- Navbar -->
        <?php include 'templates/nav.php'?>        
        <!-- Navbar -->

        <div class="container mt-3">

            <!-- Título y botón agregar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 id="titulo">Nuevo operacion</h1>
                </div>  
                <div>
                    <button class="btn btn-sm btn-primary" title="Agregar nuevo cliente" data-toggle="modal" data-target="#agregar_cliente"><i class="fa fa-plus"></i></button>
                </div>
            </div>

            <!-- Modal agregar operacion -->            
            <div id="agregar_cliente" class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Agregar nuevo cliente</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>                                
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="validationTooltip03">Nombre</label>
                                        <input type="text" class="form-control" id="nombre">
                                    </div>                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="validationTooltip05">Rut</label>
                                        <input type="text" class="form-control" id="rut">
                                    </div>                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="validationTooltip04">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono">
                                    </div>                                    
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="validationTooltip03">Mail</label>
                                        <input type="text" class="form-control" id="nombre">
                                    </div>                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="validationTooltip05">Dirección</label>
                                        <input type="text" class="form-control" id="rut">
                                    </div>                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="validationTooltip04">Ciudad</label>
                                        <input type="text" class="form-control" id="telefono">
                                    </div>                                    
                                </div>                                
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary">Agregar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lista -->
            <table id="tabla_operacions" class="table">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Mail</th>                        
                        <th scope="col">Detalle</th>
                    </tr>
                </thead>
                <tbody id="lista_operacions">                                                          
                </tbody>
            </table>
        </div>        
        
        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="../libs/datatables.js"></script>
        <script src="../js/operacions.js?<?php echo time();?>"></script>
    </body>
</html>
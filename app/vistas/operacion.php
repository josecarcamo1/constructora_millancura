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
                    <h1 id="titulo">Pedidos Activos</h1>
                </div>  
                <div class="d-flex justify-content-between align-items-center ms-3">
                    <div>
                        <a href="proyectos_nuevo.php" class="btn btn-sm btn-primary" title="Nuevo Proyecto"><i class="fa fa-plus"></i></a>
                    </div>
                    <div >                        
                        <button type="button" class="btn btn-sm btn-success ms-1" data-bs-toggle="modal" data-bs-target="#reporte_excel"><i class="fa fa-file-excel"></i></button>
                        <div class="modal fade" id="reporte_excel" tabindex="-1" aria-labelledby="reporte_excel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Reporte Excel</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col">
                                                <input id="desde" type="date" class="form-control" placeholder="Desde" aria-label="Desde">
                                            </div>
                                            <div class="col">
                                                <input id="hasta" type="date" class="form-control" placeholder="Hasta" aria-label="Hasta">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        <button id="descargar" type="button" class="btn btn-primary">Descargar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            <div id="lista_operaciones"></div>
        </div>        
        
        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="//cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
        <script src="../js/nav.js?<?php echo time();?>"></script>
        <script type="module" src="../js/operacion.js?<?php echo time();?>"></script>
    </body>
</html>
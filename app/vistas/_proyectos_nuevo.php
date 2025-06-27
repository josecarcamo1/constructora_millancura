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
        <link rel="stylesheet" href="../libs/combobox.css">
        <link rel="stylesheet" href="../css/neonled.css">
        <title>NeonLed | Proyectos Nuevo</title>
    </head>
    <body>

        <!-- Navbar -->
        <?php include 'templates/nav.php'?>
        <!-- Navbar -->

        <div class="container mt-3">

            <!-- Título y botón agregar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 id="titulo">Nuevo Pedido</h1>
                </div>
            </div>
            
            <!-- Lista -->
            <form id="nuevo_proyecto">
                <div class="row">
                    <div class="form-group col-4 col-md-1">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" id="cantidad" class="form-control" min="1" value="1">
                    </div>                    
                    <div class="form-group col-8 col-md">
                        <label for="cliente">Cliente</label>
                        <input id="tr" type="text" class="form-control" list="clientes" autocomplete="off">
                        <datalist id="clientes" name="clientes_datalist">
                        </datalist>
                    </div>
                    <div class="form-group col-12 col-md">
                        <label for="nombre">Nombre Proyecto</label>
                        <input type="text" id="nombre" class="form-control">
                    </div>
                    <div class="form-group col-6 col-md">
                        <label for="nombre">Vendedor</label>
                        <select class="form-select" id="lista_vendedores">
                            <option></option>
                        </select>
                    </div>
                    <div class="form-group col-6 col-md">
                        <label for="nombre">Con Iva?</label>
                        <select class="form-select" id="estado_iva">
                            <option></option>
                            <option value=1>Con</option>
                            <option value=0>Sin</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-6 col-md">
                        <label for="fecha_creacion">Fecha Creación</label>
                        <input type="date" id="fecha_creacion" class="form-control" max="0">
                    </div>
                    <div class="form-group col-6 col-md">
                        <label for="valor">Valor sin IVA</label>
                        <input type="number" id="valor" min="1" class="form-control">
                    </div>
                    <div class="form-group col-6 col-md">
                        <label for="valor">Valor + IVA</label>
                        <input type="number" id="valor_iva" class="form-control" disabled>
                    </div>
                    <div class="form-group col-6 col-md">
                        <label for="estado">Estado</label>
                        <input type="text" id="estado" class="form-control" disabled value="ACTIVO">
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
        <script src="../libs/combobox.js"></script>
        <script src="../libs/typehead.js"></script>
        <script type="module" src="../js/proyectos_nuevo.js?<?php echo time();?>"></script>
    </body>
</html>
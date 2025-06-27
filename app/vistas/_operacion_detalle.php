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
        <title>NeonLed | Proyecto</title>
    </head>
    <body>

        <!-- Navbar -->
        <?php include 'templates/nav.php'?>        
        <!-- Navbar -->

        <div class="container mt-3">

            <!-- Título y botón agregar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 id="titulo">Proyecto</h1>
                </div>
            </div>

            <!-- Info General -->
            <form id="proyecto" class="lectura">
                <div class="row">
                    <div class="form-group col-4 col-md-2">
                        <label for="cliente">Numero</label>
                        <input class="form-control fijo" id="numero" disabled>                            
                    </div>
                    <div class="form-group col-8 col-md">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" class="form-control" disabled>
                    </div>
                    <div class="form-group col-12 col-md-3">
                        <label for="clientes">Cliente</label>
                        <!-- <input class="form-control" id="clientes" disabled> -->
                        <input id="selector_clientes" type="text" class="form-control" list="clientes" autocomplete="off" disabled>
                        <datalist id="clientes" name="clientes_datalist">
                    </div>
                    <div class="form-group col-12 col-md-3">
                        <label for="lista_vendedores">Vendedor</label>
                        <select class="form-select" id="lista_vendedores" disabled>
                        </select>                            
                    </div>                                       
                </div>
                <div class="row">
                    <div class="form-group col-4 col-md-2">
                        <label for="lista_vendedores">IVA</label>
                        <select class="form-select" id="iva" disabled>
                            <option val="0">SIN</option>
                            <option val="1">CON</option>
                        </select>                            
                    </div> 
                    <div class="form-group col-12 col-md">
                        <label for="estado">Estado</label>
                        <input type="text" id="estado" class="form-control fijo" disabled>
                    </div>
                    <div class="form-group col-6 col-md">
                        <label for="fecha_creacion">Fecha Creación</label>
                        <input type="date" id="fecha_creacion" class="form-control" max="0" disabled>
                    </div>
                    <div class="form-group col-6 col-md">
                        <label for="dias_activo">Días Activo</label>
                        <input type="text" id="dias_activo" class="form-control fijo" disabled>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-6 col-md">
                        <label for="valor">Valor sin IVA</label>
                        <input type="text" id="valor" class="form-control" disabled>
                    </div>
                    <div class="form-group col-6 col-md">
                        <label for="valor">Valor + IVA</label>
                        <input type="text" id="valor_iva" class="form-control fijo" disabled>
                    </div>
                    <div class="form-group col-6 col-md">
                        <label for="total_pagado">Total Pagado</label>
                        <input type="text" id="total_pagado" class="form-control fijo" disabled>
                    </div>
                    <div class="form-group col-6 col-md">
                        <label for="por_pagar">Por pagar</label>
                        <input type="text" id="por_pagar" class="form-control fijo" disabled>
                    </div>                    
                </div>
                <div class="row mt-2">
                    <div class="form-group col-12 col-md">
                        <label for="estado_pago">Estado Pago</label>
                        <input type="text" id="estado_pago" class="form-control fijo" disabled>
                    </div>
                    <div class="form-group col-12 col-md">
                        <label for="estado_factura">Estado Factura</label>
                        <input type="text" id="estado_factura" class="form-control fijo" disabled>
                    </div>
                    <div class="form-group col-12 col-md">
                        <label for="estado_entregado">Estado Entregado</label>                        
                        <select class="form-select" id="estado_entregado" disabled>
                            <option value="0">PENDIENTE</option>
                            <option value="1">ENTREGADO</option>
                        </select>
                    </div>                  
                </div>
                <div class="mt-2">
                    <button id="editar" type="button" class="btn btn-warning text-white btn-edit">Editar</button>
                    <button id="guardar" type="submit" class="btn btn-success btn-save mr-2 emitir">Guardar
                        <div id="spinner" class="spinner-border text-light spinner-border-sm ml-2" role="status">
                        </div>
                    </button>
                    <button id="cancelar" type="button" class="btn btn-primary btn-save emitir">Cancelar</button>
                </div>
            </form>
            
            <!-- Pagos -->            
            <div class="d-flex justify-content-between align-items-center mt-5">
                <div>
                    <h2>Pagos</h2>
                </div>
                <button type="button" class="btn btn-primary" title="Agregar nuevo pago" data-bs-toggle="modal" data-bs-target="#modal_pagos">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
            <table id="tabla_pagos" class="table mt-3">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Valor</th>
                        <th class="hide-mobile">Comentario</th>
                        <th width="10px" class="text-center">Borrar</th>
                    </tr>
                </thead>
                <tbody id="lista_pagos">
                </tbody>
            </table>

            <!-- Modal Pagos -->
            <div id="modal_pagos" class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Nuevo Pago</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <!-- <div class="form-group col-6">
                                    <label for="ref_valor_iva">Valor + IVA</label>
                                    <input type="text" id="ref_valor_iva" class="form-control" disabled>
                                </div> -->
                                <div class="form-group">
                                    <label for="ref_por_pagar">Por pagar</label>
                                    <input type="text" id="ref_por_pagar" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="fecha_creacion_pago">Fecha</label>
                                    <input type="date" id="fecha_creacion_pago" class="form-control" max="0">
                                </div>
                                <div class="form-group col-6">
                                    <label for="nuevo_valor">Valor</label>
                                    <input type="text" id="nuevo_valor" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="comentario">Comentario</label>
                                    <textarea type="text" id="comentario" class="form-control"></textarea>
                                </div>                    
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button id="nuevo_pago" type="button" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Facturas -->
            <div id="facturas" style="display:none">
                <div class="d-flex justify-content-between align-items-center mt-5">
                    <div>
                        <h2>Facturas</h2>
                    </div>
                    <button type="button" class="btn btn-primary" title="Agregar nuevo pago" data-bs-toggle="modal" data-bs-target="#modal_facturas">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
                <table id="tabla_facturas" class="table mt-3">
                    <thead>
                        <tr>
                            <th>Numero</th>
                            <th>Valor</th>
                            <th class="hide-mobile">Comentario</th>
                            <th width="10px" class="text-center">Borrar</th>
                        </tr>
                    </thead>
                    <tbody id="lista_facturas">
                    </tbody>
                </table>
            </div>

            <!-- Modal Facturas -->
            <div id="modal_facturas" class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Nueva Factura</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <!-- <div class="form-group col-6">
                                    <label for="ref_valor_iva">Valor + IVA</label>
                                    <input type="text" id="ref_fact_valor_iva" class="form-control" disabled>
                                </div> -->
                                <div class="form-group">
                                    <label for="ref_por_pagar">Por facturar</label>
                                    <input type="text" id="ref_fact_por_pagar" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <label for="fecha_creacion_factura">Fecha</label>
                                    <input type="date" id="fecha_creacion_factura" class="form-control" max="0">
                                </div>
                                <div class="form-group col-6 col-md">
                                    <label for="nuevo_valor">Numero</label>
                                    <input type="text" id="nueva_factura_numero" class="form-control">
                                </div>
                                <div class="form-group col-6 col-md">
                                    <label for="nuevo_valor">Valor</label>
                                    <input type="text" id="nueva_factura_valor" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="comentario">Comentario</label>
                                    <textarea type="text" id="factura_comentario" class="form-control"></textarea>
                                </div>                    
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button id="nueva_factura" type="button" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
        
        <script src="../libs/jquery.js"></script>
        <script src="../libs/bootstrap.js"></script>
        <script src="../libs/moment.js"></script>
        <script src="../libs/datatables.js"></script>
        <script src="../libs/combobox.js"></script>
        <script src="../libs/typehead.js"></script>
        <script type="module" src="../js/operacion_detalle.js?<?php echo time();?>"></script>
    </body>
</html>
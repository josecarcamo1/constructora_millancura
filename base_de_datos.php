<?php
// Debug: Mostrar errores (quitar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require("conf.php");

// Debug: Verificar conexión a la base de datos
try {
    if(!isset($pdo)) {
        throw new Exception("La conexión PDO no está inicializada");
    }
    // Test simple de conexión
    $pdo->query("SELECT 1")->fetch();
} catch (Exception $e) {
    die("<div class='ui error message'><strong>Error de conexión:</strong> ".htmlspecialchars($e->getMessage())."</div>");
}

// Configuración de rutas - CORREGIDA
$basePathImagenes = realpath(__DIR__ . '/../../../imagenes') . '/'; // Ruta absoluta para verificación de archivos
$baseUrlImagenes = 'imagenes/'; // Ruta relativa para el navegador

// Parámetros de filtro
$filtro_empresa = isset($_GET['empresa']) ? $_GET['empresa'] : '';
$filtro_patente = isset($_GET['patente']) ? $_GET['patente'] : '';
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

// Configuración de paginación
$registros_por_pagina = 50;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta base con filtros (ahora incluyendo las imágenes)
$sql = "SELECT SQL_CALC_FOUND_ROWS c.cam_id, p.pat_patente, p.pat_m3, 
        c.cam_foto_general, c.cam_foto_patente,
        CASE WHEN e.emp_id = 12 THEN 'Otros' ELSE e.emp_nombre END as emp_nombre,
        e.emp_id, -- AÑADIDO
        c.cam_fecha_hora, c.cam_tipo
        FROM camara c
        JOIN patentes p ON c.cam_pat_id = p.pat_id
        JOIN empresas e ON c.cam_emp_id = e.emp_id
        WHERE 1=1";

$params = array();

if (!empty($filtro_empresa)) {
    $sql .= " AND (CASE WHEN p.pat_emp_id = 12 THEN 'Otros' ELSE e.emp_nombre END) LIKE :empresa";
    $params[':empresa'] = "%$filtro_empresa%";
}

if (!empty($filtro_patente)) {
    $sql .= " AND p.pat_patente LIKE :patente";
    $params[':patente'] = "%$filtro_patente%";
}

if (!empty($filtro_fecha_desde)) {
    $sql .= " AND DATE(c.cam_fecha_hora) >= :fecha_desde";
    $params[':fecha_desde'] = $filtro_fecha_desde;
}

if (!empty($filtro_fecha_hasta)) {
    $sql .= " AND DATE(c.cam_fecha_hora) <= :fecha_hasta";
    $params[':fecha_hasta'] = $filtro_fecha_hasta;
}

$sql .= " ORDER BY c.cam_fecha_hora DESC 
          LIMIT $offset, $registros_por_pagina";

// Consulta para calcular total de m3 por rango de fechas (solo entradas)
$total_m3 = 0;
if (!empty($filtro_fecha_desde)) {
    $sql_m3 = "SELECT SUM(p.pat_m3) as total_m3 
               FROM camara c
               JOIN patentes p ON c.cam_pat_id = p.pat_id
               WHERE c.cam_tipo = 'entrada' AND DATE(c.cam_fecha_hora) >= :fecha_desde";
    
    if (!empty($filtro_fecha_hasta)) {
        $sql_m3 .= " AND DATE(c.cam_fecha_hora) <= :fecha_hasta";
    }
    
    if (!empty($filtro_empresa)) {
        $sql_m3 .= " AND c.cam_emp_id IN (SELECT emp_id FROM empresas WHERE emp_nombre LIKE :empresa)";
    }
    
    if (!empty($filtro_patente)) {
        $sql_m3 .= " AND p.pat_patente LIKE :patente";
    }
    
    $stmt_m3 = $pdo->prepare($sql_m3);
    $stmt_m3->bindValue(':fecha_desde', $filtro_fecha_desde);
    
    if (!empty($filtro_fecha_hasta)) {
        $stmt_m3->bindValue(':fecha_hasta', $filtro_fecha_hasta);
    }
    
    if (!empty($filtro_empresa)) {
        $stmt_m3->bindValue(':empresa', "%$filtro_empresa%");
    }
    
    if (!empty($filtro_patente)) {
        $stmt_m3->bindValue(':patente', "%$filtro_patente%");
    }
    
    $stmt_m3->execute();
    $total_m3 = $stmt_m3->fetchColumn();
}

// Obtener empresas para el filtro
try {
    $empresas = $pdo->query("SELECT emp_id, emp_nombre FROM empresas ORDER BY emp_nombre")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div class='ui error message'><strong>Error al obtener empresas:</strong> ".htmlspecialchars($e->getMessage())."</div>");
}

// Debug: Verificar si $url_base está definida
if(!isset($url_base)) {
    $url_base = ''; // Asignar un valor por defecto
    error_log("Advertencia: \$url_base no está definida, usando valor vacío");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Cámara</title>
    <?php require("librerias.php"); ?>
    <!-- Agregar Fancybox para el visor de imágenes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }
        #main-content {
            width: 100%;
            max-width: 100%;
            padding: 0 !important;
        }
        .ui.grid {
            margin: 0 !important;
        }
        .ui.container.main-content {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
            padding: 0 15px !important;
        }
        .ui.table {
            font-size: 14px;
            margin: 10px 0 !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .ui.button {
            padding: 8px 12px;
            font-size: 13px;
            transition: all 0.3s ease;
        }
        .ui.button.excel-style {
            background-color: #217346 !important;
            color: white !important;
            font-weight: bold;
            border-radius: 4px;
            box-shadow: 0 2px 0 #1a5a37;
            border: 1px solid #1a5a37;
            padding: 0.78571429em 1.5em 0.78571429em !important;
            margin-bottom: 10px;
        }
        .ui.button.excel-style:hover {
            background-color: #1a5a37 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 0 #1a5a37;
        }
        .filtros-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        .header-container {
            background: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%);
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .header-container h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            position: relative;
            z-index: 2;
        }
        .header-container p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 16px;
            position: relative;
            z-index: 2;
        }
        .header-container::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .header-container::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        .paginacion {
            margin-top: 25px;
            display: flex;
            justify-content: center;
        }
        .ui.pagination.menu .active.item {
            background-color: #2c3e50;
            color: white;
        }
        .ui.dropdown {
            border-radius: 4px !important;
        }
        .ui.input input {
            border-radius: 4px !important;
        }
        .ui.form .field>label {
            font-weight: 500;
            color: #555;
        }
        .m3-badge {
            background-color: #2185d0;
            color: white;
            padding: 5px 10px;
            border-radius: 10px;
            font-weight: bold;
            margin-left: 10px;
            display: inline-block;
        }
        .tipo-entrada {
            color: #21ba45;
            font-weight: bold;
        }
        .tipo-salida {
            color: #db2828;
            font-weight: bold;
        }
        /* Nuevos estilos para las imágenes */
        .imagenes-container {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }
        .mini-imagen {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.3s;
            border: 1px solid #ddd;
        }
        .mini-imagen:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .sin-imagen {
            width: 60px;
            height: 40px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            color: #999;
            font-size: 10px;
            border: 1px dashed #ddd;
        }
        .edit-icon {
            color: #2185d0;
            cursor: pointer;
            margin-left: 5px;
            transition: all 0.3s;
        }
        .edit-icon:hover {
            color: #0d71bb;
            transform: scale(1.2);
        }
        .modal-edit-patente {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-modal:hover {
            color: black;
        }
        .modal-actions {
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>

<!-- Agregar este modal antes del cierre del body -->
<div id="modalEditPatente" class="modal-edit-patente">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h3>Editar Registro</h3>
        <form id="formEditPatente" class="ui form">
            <input type="hidden" id="editCamId" name="cam_id">
            <div class="field">
                <label>Patente actual</label>
                <input type="text" id="currentPatente" readonly>
            </div>
            <div class="field">
                <label>Nueva patente</label>
                <input type="text" id="newPatente" name="new_patente" required>
            </div>
            <!-- AÑADIDO: Selector de empresa -->
            <div class="field">
                <label>Empresa</label>
                <select id="newEmpresa" name="new_empresa" class="ui dropdown">
                    <option value="">Seleccionar empresa</option>
                    <?php foreach ($empresas as $empresa): ?>
                        <option value="<?= $empresa['emp_id'] ?>"><?= htmlspecialchars($empresa['emp_nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="ui button" id="cancelEdit">Cancelar</button>
                <button type="submit" class="ui primary button">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div class="ui container" style="padding: 0; width: 100%;">
    <div class="ui grid stackable" style="margin: 0; width: 100%; overflow-x: hidden;">
        <div class="four wide column" style="padding: 0;">
            <?php require("menu.php"); ?>
        </div>
        <div class="twelve column" id="main-content" style="padding: 0;">
            <div class="ui container main-content">
                <div class="header-container">
                    <h1><i class="fas fa-camera-retro"></i> Gestión de Registros de Cámara</h1>
                    <p>Sistema de seguimiento vehicular - Millancura</p>
                    <?php if (!empty($filtro_fecha_desde)): ?>
                        <div style="margin-top: 10px;">
                            <span class="m3-badge">
                                <i class="fas fa-ruler-combined"></i> Total m³ del período: <?= number_format($total_m3, 2) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Filtros con rango de fechas -->
                <div class="filtros-container">
                    <form method="get" class="ui form">
                        <div class="three fields">
                            <div class="field">
                                <label>Empresa</label>
                                <select name="empresa" class="ui dropdown">
                                    <option value="">Todas las empresas</option>
                                    <?php foreach($empresas as $empresa): ?>
                                        <option value="<?= $empresa['emp_nombre'] ?>" <?= $filtro_empresa == $empresa['emp_nombre'] ? 'selected' : '' ?>>
                                            <?= $empresa['emp_nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="field">
                                <label>Patente</label>
                                <div class="ui input">
                                    <input type="text" name="patente" placeholder="Buscar por patente" value="<?= htmlspecialchars($filtro_patente) ?>">
                                </div>
                            </div>
                            <div class="field">
                                <label>Fecha Desde</label>
                                <div class="ui input">
                                    <input type="date" name="fecha_desde" value="<?= $filtro_fecha_desde ?>">
                                </div>
                            </div>
                            <div class="field">
                                <label>Fecha Hasta</label>
                                <div class="ui input">
                                    <input type="date" name="fecha_hasta" value="<?= $filtro_fecha_hasta ?>">
                                </div>
                            </div>
                        </div>
                        <div class="field" style="text-align: right;">
                            <button type="submit" class="ui primary button"><i class="fas fa-filter"></i> Filtrar</button>
                            <a href="?" class="ui button"><i class="fas fa-broom"></i> Limpiar</a>
                        </div>
                    </form>
                </div>

                <!-- Botón de exportación a Excel -->
                <div style="margin-bottom: 15px;">
                    <form method="post" action="base_de_datos_excel.php" style="display: inline;">
                        <input type="hidden" name="empresa" value="<?= htmlspecialchars($filtro_empresa) ?>">
                        <input type="hidden" name="patente" value="<?= htmlspecialchars($filtro_patente) ?>">
                        <input type="hidden" name="fecha_desde" value="<?= htmlspecialchars($filtro_fecha_desde) ?>">
                        <input type="hidden" name="fecha_hasta" value="<?= htmlspecialchars($filtro_fecha_hasta) ?>">
                        <button type="submit" class="ui button excel-style">
                            <i class="fas fa-file-excel"></i> Exportar a Excel
                        </button>
                    </form>
                </div>

                <!-- Tabla de Registros -->
                <div>
                    <?php
                    try {
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $total_registros = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();

                        if ($stmt->rowCount() > 0) {
                            echo "<table class='ui celled striped table'>";
                            echo "<thead><tr>
                                    <th>Patente</th>
                                    <th>Empresa</th>
                                    <th>Fecha/Hora</th>
                                    <th>m³</th>
                                    <th>Imágenes</th>
                                </tr></thead>";
                            echo "<tbody>";

                            // Configuración de rutas según tu estructura
                            $imagenesPath = realpath(__DIR__ . '../../../imagenes') . '/';
                            $baseUrlImagenes = 'imagenes/'; // Ruta relativa para el navegador

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $fecha = new DateTime($row["cam_fecha_hora"]);
                                $tipo_class = $row["cam_tipo"] == 'entrada' ? 'tipo-entrada' : 'tipo-salida';
                                
                                echo "<tr>";
                                echo "<td>
                                    <strong>" . htmlspecialchars($row["pat_patente"]) . "</strong>
                                    <i class='fas fa-pencil-alt edit-icon' 
                                    onclick='openEditModal(\"" . $row["cam_id"] . "\", \"" . htmlspecialchars($row["pat_patente"]) . "\", \"" . $row["emp_id"] . "\")'></i>
                                </td>";
                                echo "<td>" . htmlspecialchars($row["emp_nombre"]) . "</td>";
                                echo "<td>" . $fecha->format('d/m/Y H:i:s') . "</td>";
                                // echo "<td><span class='$tipo_class'>" . ucfirst($row["cam_tipo"]) . "</span></td>";
                                echo "<td>" . ($row["cam_tipo"] == 'entrada' ? number_format($row["pat_m3"], 2) : '-') . "</td>";
                                
                                // Columna de imágenes
                                echo "<td>";
                                echo "<div class='imagenes-container'>";
                                
                                
                                // Configuración de rutas ABSOLUTAS (ajusta esto según tu servidor)
                                $rutaAbsolutaImagenes = '/home/customer/www/imagenes/'; // Ruta física real en el servidor
                                $rutaRelativaImagenes = 'imagenes/'; // Ruta relativa desde tu página actual
                                
                                // Foto general
                                if (!empty($row["cam_foto_general"])) {
                                    $nombreArchivo = $row["cam_foto_general"];
                                    $rutaCompleta = $rutaAbsolutaImagenes . $nombreArchivo;
                                    $rutaMostrar = $rutaRelativaImagenes . rawurlencode($nombreArchivo);
                                    
                                    if (file_exists($rutaCompleta) && is_readable($rutaCompleta)) {
                                        // Solución 1: Usar Data URI (para imágenes pequeñas)
                                        $imagenData = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($rutaCompleta));
                                        echo "<a href='$imagenData' data-fancybox='gallery-{$row["cam_id"]}'>";
                                        echo "<img src='$imagenData' class='mini-imagen' title='Foto general'>";
                                        echo "</a>";
                                        
                                        /* Solución 2: Si las imágenes son grandes, usa esto:
                                        echo "<a href='$rutaMostrar' data-fancybox='gallery-{$row["cam_id"]}'>";
                                        echo "<img src='$rutaMostrar' class='mini-imagen' title='Foto general' 
                                              onerror='this.parentNode.innerHTML=\"<div class=error-imagen>Error al cargar</div>\";'>";
                                        echo "</a>";
                                        */
                                    } else {
                                        echo "<div class='sin-imagen'>Imagen no disponible</div>";
                                    }
                                } else {
                                    echo "<div class='sin-imagen'>N/A</div>";
                                }
                                
                                // Foto patente (misma estructura)
                                if (!empty($row["cam_foto_patente"])) {
                                    $nombreArchivo = $row["cam_foto_patente"];
                                    $rutaCompleta = $rutaAbsolutaImagenes . $nombreArchivo;
                                    $rutaMostrar = $rutaRelativaImagenes . rawurlencode($nombreArchivo);
                                    
                                    if (file_exists($rutaCompleta) && is_readable($rutaCompleta)) {
                                        $imagenData = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($rutaCompleta));
                                        echo "<a href='$imagenData' data-fancybox='gallery-{$row["cam_id"]}'>";
                                        echo "<img src='$imagenData' class='mini-imagen' title='Foto patente'>";
                                        echo "</a>";
                                    } else {
                                        echo "<div class='sin-imagen'>Imagen no disponible</div>";
                                    }
                                } else {
                                    echo "<div class='sin-imagen'>N/A</div>";
                                }
                                

                                
                                echo "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }

                            echo "</tbody>";
                            echo "</table>";

                            // Paginación
                            $total_paginas = ceil($total_registros / $registros_por_pagina);
                            if ($total_paginas > 1) {
                                echo '<div class="paginacion">';
                                echo '<div class="ui pagination menu">';
                                
                                // Botón Anterior
                                if ($pagina_actual > 1) {
                                    echo '<a class="item" href="?'.http_build_query(array_merge($_GET, ['pagina' => $pagina_actual - 1])).'"><i class="fas fa-chevron-left"></i> Anterior</a>';
                                }
                                
                                // Números de página
                                $inicio = max(1, $pagina_actual - 2);
                                $fin = min($total_paginas, $pagina_actual + 2);
                                
                                if ($inicio > 1) {
                                    echo '<a class="item" href="?'.http_build_query(array_merge($_GET, ['pagina' => 1])).'">1</a>';
                                    if ($inicio > 2) echo '<div class="disabled item">...</div>';
                                }
                                
                                for ($i = $inicio; $i <= $fin; $i++) {
                                    $active = $i == $pagina_actual ? 'active' : '';
                                    echo '<a class="item '.$active.'" href="?'.http_build_query(array_merge($_GET, ['pagina' => $i])).'">'.$i.'</a>';
                                }
                                
                                if ($fin < $total_paginas) {
                                    if ($fin < $total_paginas - 1) echo '<div class="disabled item">...</div>';
                                    echo '<a class="item" href="?'.http_build_query(array_merge($_GET, ['pagina' => $total_paginas])).'">'.$total_paginas.'</a>';
                                }
                                
                                // Botón Siguiente
                                if ($pagina_actual < $total_paginas) {
                                    echo '<a class="item" href="?'.http_build_query(array_merge($_GET, ['pagina' => $pagina_actual + 1])).'">Siguiente <i class="fas fa-chevron-right"></i></a>';
                                }
                                
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo "<div class='ui icon message'>
                                    <i class='fas fa-search-minus icon'></i>
                                    <div class='content'>
                                        <div class='header'>No se encontraron registros</div>
                                        <p>No hay resultados que coincidan con tus criterios de búsqueda.</p>
                                    </div>
                                </div>";
                        }
                    } catch (PDOException $e) {
                        echo "<div class='ui negative icon message'>
                                <i class='fas fa-exclamation-circle icon'></i>
                                <div class='content'>
                                    <div class='header'>Error en la consulta</div>
                                    <p>" . htmlspecialchars($e->getMessage()) . "</p>
                                </div>
                            </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.ui.dropdown').dropdown();
    
    // Efecto hover para filas de la tabla
    $('table tbody tr').hover(
        function() {
            $(this).css('transform', 'translateX(2px)');
            $(this).css('box-shadow', '0 2px 5px rgba(0,0,0,0.1)');
        },
        function() {
            $(this).css('transform', '');
            $(this).css('box-shadow', '');
        }
    );
    
    // Configurar Fancybox
    $('[data-fancybox]').fancybox({
        buttons: [
            "zoom",
            "slideShow",
            "fullScreen",
            "download",
            "thumbs",
            "close"
        ],
        animationEffect: "zoom-in-out",
        transitionEffect: "circular"
    });

    
    
    
});

// Variables para el modal
const modal = document.getElementById("modalEditPatente");
const span = document.getElementsByClassName("close-modal")[0];
const cancelBtn = document.getElementById("cancelEdit");

// Función para abrir el modal
function openEditModal(camId, currentPatente, currentEmpId) {
    document.getElementById("editCamId").value = camId;
    document.getElementById("currentPatente").value = currentPatente;
    document.getElementById("newPatente").value = currentPatente;
    document.getElementById("newEmpresa").value = currentEmpId;
    $('#newEmpresa').dropdown('refresh');
    modal.style.display = "block";
}

// Cerrar modal al hacer clic en la X
span.onclick = function() {
    modal.style.display = "none";
}

// Cerrar modal al hacer clic en Cancelar
cancelBtn.onclick = function() {
    modal.style.display = "none";
}

// Cerrar modal al hacer clic fuera del contenido
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Manejar el envío del formulario
$("#formEditPatente").submit(function(e) {
    e.preventDefault();
    
    const camId = $("#editCamId").val();
    const newPatente = $("#newPatente").val().trim().toUpperCase();
    const newEmpresa = $("#newEmpresa").val();
    
    if (!newPatente) {
        alert("Por favor ingrese una patente válida");
        return;
    }
    
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    
    $.ajax({
        url: "actualizar_patente.php",
        method: "POST",
        data: {
            cam_id: camId,
            new_patente: newPatente,
            new_empresa: newEmpresa
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                alert("Registro actualizado correctamente");
                modal.style.display = "none";
                location.reload();
            } else {
                alert("Error: " + (response.message || 'Error desconocido'));
            }
        },
        error: function(xhr, status, error) {
            try {
                const response = JSON.parse(xhr.responseText);
                alert("Error: " + (response.message || error));
            } catch (e) {
                alert("Error al actualizar: " + error);
            }
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('Guardar');
        }
    });
});
</script>
</body>
</html>
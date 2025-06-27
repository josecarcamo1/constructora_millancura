<?php
require("conf.php");

// Parámetros de filtro actualizados
$filtro_empresa = isset($_GET['empresa']) ? $_GET['empresa'] : '';
$filtro_patente = isset($_GET['patente']) ? $_GET['patente'] : '';
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

// Configuración de paginación
$registros_por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta base con filtros actualizada
$sql = "SELECT SQL_CALC_FOUND_ROWS c.cam_id, p.pat_patente, 
        CASE WHEN p.pat_emp_id = 12 THEN 'Otros' ELSE e.emp_nombre END as emp_nombre,
        c.cam_fecha_hora, c.cam_tipo, p.pat_m3
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

// Obtener empresas para el filtro
$empresas = $pdo->query("SELECT emp_id, emp_nombre FROM empresas ORDER BY emp_nombre")->fetchAll(PDO::FETCH_ASSOC);

// Calcular total de m3 para el rango (solo entradas)
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
        $sql_m3 .= " AND (CASE WHEN p.pat_emp_id = 12 THEN 'Otros' ELSE e.emp_nombre END) LIKE :empresa";
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Registros de Cámara</title>
    <?php require("librerias.php"); ?>
    <style>
        /* Estilos móviles específicos */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .mobile-header {
            background: linear-gradient(135deg, #2c3e50, #4ca1af);
            color: white;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .mobile-header h1 {
            margin: 0;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
        }
        
        .mobile-header h1 i {
            margin-right: 10px;
        }
        
        .mobile-header p {
            margin: 5px 0 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .m3-badge {
            background-color: #2185d0;
            color: white;
            padding: 5px 10px;
            border-radius: 10px;
            font-weight: bold;
            margin-top: 10px;
            display: inline-block;
            font-size: 0.8rem;
        }
        
        .filters-container {
            background: white;
            padding: 15px;
            margin: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .filter-group {
            margin-bottom: 15px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .filter-group select, 
        .filter-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95rem;
        }
        
        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .filter-actions button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .btn-secondary {
            background: white;
            color: #555;
            border: 1px solid #ddd;
        }
        
        .export-btn {
            background: linear-gradient(135deg, #217346, #1a5a37);
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            margin: 0 15px 20px;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            text-decoration: none;
        }
        
        /* Vista de tarjetas para móviles */
        .cards-view {
            margin: 15px;
        }
        
        .camion-card {
            background: white;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .card-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .card-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .card-label {
            font-weight: 600;
            color: #2c3e50;
            flex: 1;
            font-size: 0.9rem;
        }
        
        .card-value {
            flex: 2;
            text-align: right;
            font-size: 0.95rem;
        }
        
        .tipo-entrada {
            color: #2ecc71;
            font-weight: bold;
        }
        
        .tipo-salida {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .no-results {
            background: white;
            padding: 30px 20px;
            margin: 15px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            padding: 20px 15px;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .pagination a {
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            background-color: white;
            color: #2c3e50;
            border: 1px solid #e0e0e0;
        }
        
        .pagination a.active {
            background: #2c3e50;
            color: white;
        }
    </style>
</head>
<body>
    <?php
    require("menu_mobile.php");
    ?>
    
    <div class="main-content" id="main-content">
        <div class="mobile-header">
            <h1><i class="fas fa-camera-retro"></i> Registros de Cámara</h1>
            <p>Sistema de seguimiento vehicular</p>
            <?php if (!empty($filtro_fecha_desde)): ?>
                <div class="m3-badge">
                    <i class="fas fa-ruler-combined"></i> Total m³: <?= number_format($total_m3, 2) ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Filtros -->
        <div class="filters-container">
            <form method="get" id="filterForm">
                <div class="filter-group">
                    <label><i class="fas fa-building"></i> Empresa</label>
                    <select name="empresa" class="ui dropdown">
                        <option value="">Todas las empresas</option>
                        <?php foreach($empresas as $empresa): ?>
                            <option value="<?= $empresa['emp_nombre'] ?>" <?= $filtro_empresa == $empresa['emp_nombre'] ? 'selected' : '' ?>>
                                <?= $empresa['emp_nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-car-alt"></i> Patente</label>
                    <input type="text" name="patente" placeholder="Buscar por patente" value="<?= htmlspecialchars($filtro_patente) ?>">
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <div class="filter-group" style="flex: 1;">
                        <label><i class="far fa-calendar-alt"></i> Desde</label>
                        <input type="date" name="fecha_desde" value="<?= $filtro_fecha_desde ?>">
                    </div>
                    
                    <div class="filter-group" style="flex: 1;">
                        <label><i class="far fa-calendar-alt"></i> Hasta</label>
                        <input type="date" name="fecha_hasta" value="<?= $filtro_fecha_hasta ?>">
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <button type="button" id="clearFilters" class="btn-secondary">
                        <i class="fas fa-broom"></i> Limpiar
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Botón Exportar -->
        <form method="post" action="base_de_datos_excel.php">
            <input type="hidden" name="empresa" value="<?= htmlspecialchars($filtro_empresa) ?>">
            <input type="hidden" name="patente" value="<?= htmlspecialchars($filtro_patente) ?>">
            <input type="hidden" name="fecha_desde" value="<?= htmlspecialchars($filtro_fecha_desde) ?>">
            <input type="hidden" name="fecha_hasta" value="<?= htmlspecialchars($filtro_fecha_hasta) ?>">
            <button type="submit" class="export-btn">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </button>
        </form>
        
        <!-- Vista de tarjetas (para móvil) -->
        <div class="cards-view">
            <?php
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $total_registros = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();

                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fecha = new DateTime($row["cam_fecha_hora"]);
                        $tipo_class = $row["cam_tipo"] == 'entrada' ? 'tipo-entrada' : 'tipo-salida';
                        
                        echo '<div class="camion-card">';
                        echo '<div class="card-row">';
                        echo '<span class="card-label">Patente:</span>';
                        echo '<span class="card-value"><strong>' . htmlspecialchars($row["pat_patente"]) . '</strong></span>';
                        echo '</div>';
                        
                        echo '<div class="card-row">';
                        echo '<span class="card-label">Empresa:</span>';
                        echo '<span class="card-value">' . htmlspecialchars($row["emp_nombre"]) . '</span>';
                        echo '</div>';
                        
                        echo '<div class="card-row">';
                        echo '<span class="card-label">Fecha/Hora:</span>';
                        echo '<span class="card-value">' . $fecha->format('d/m/Y H:i:s') . '</span>';
                        echo '</div>';
                        
                        // echo '<div class="card-row">';
                        // echo '<span class="card-label">Tipo:</span>';
                        // echo '<span class="card-value"><span class="'.$tipo_class.'">' . ucfirst($row["cam_tipo"]) . '</span></span>';
                        // echo '</div>';
                        
                        echo '<div class="card-row">';
                        echo '<span class="card-label">m³:</span>';
                        echo '<span class="card-value">' . ($row["cam_tipo"] == 'entrada' ? number_format($row["pat_m3"], 2) : '-') . '</span>';
                        echo '</div>';
                        
                        echo '</div>';
                    }
                } else {
                    echo '<div class="no-results">
                            <i class="fas fa-search-minus"></i>
                            <h3>No se encontraron registros</h3>
                            <p>No hay resultados con los filtros actuales</p>
                          </div>';
                }
            } catch (PDOException $e) {
                echo '<div class="no-results">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Error en la consulta</h3>
                        <p>'.htmlspecialchars($e->getMessage()).'</p>
                      </div>';
            }
            ?>
        </div>
        
        <!-- Paginación -->
        <?php if (isset($total_registros) && $total_registros > $registros_por_pagina): ?>
        <div class="pagination">
            <?php
            $total_paginas = ceil($total_registros / $registros_por_pagina);
            
            if ($pagina_actual > 1) {
                echo '<a href="?'.http_build_query(array_merge($_GET, ['pagina' => $pagina_actual - 1])).'"><i class="fas fa-chevron-left"></i></a>';
            }
            
            $inicio = max(1, $pagina_actual - 2);
            $fin = min($total_paginas, $pagina_actual + 2);
            
            if ($inicio > 1) {
                echo '<a href="?'.http_build_query(array_merge($_GET, ['pagina' => 1])).'">1</a>';
                if ($inicio > 2) echo '<span>...</span>';
            }
            
            for ($i = $inicio; $i <= $fin; $i++) {
                $active = $i == $pagina_actual ? 'active' : '';
                echo '<a class="'.$active.'" href="?'.http_build_query(array_merge($_GET, ['pagina' => $i])).'">'.$i.'</a>';
            }
            
            if ($fin < $total_paginas) {
                if ($fin < $total_paginas - 1) echo '<span>...</span>';
                echo '<a href="?'.http_build_query(array_merge($_GET, ['pagina' => $total_paginas])).'">'.$total_paginas.'</a>';
            }
            
            if ($pagina_actual < $total_paginas) {
                echo '<a href="?'.http_build_query(array_merge($_GET, ['pagina' => $pagina_actual + 1])).'"><i class="fas fa-chevron-right"></i></a>';
            }
            ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
    $(document).ready(function() {
        // Inicializar dropdowns
        $('.ui.dropdown').dropdown();
        
        // Botón Limpiar Filtros
        $('#clearFilters').on('click', function() {
            $('#filterForm')[0].reset();
            window.location.href = window.location.pathname;
        });
    });
    </script>
</body>
</html>
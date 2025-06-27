<?php
require("conf.php");

// Recibir parámetros de filtro actualizados
$filtro_empresa = isset($_POST['empresa']) ? $_POST['empresa'] : '';
$filtro_patente = isset($_POST['patente']) ? $_POST['patente'] : '';
$filtro_fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
$filtro_fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';

// Consulta SQL actualizada
$sql = "SELECT p.pat_patente, 
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

$sql .= " ORDER BY c.cam_fecha_hora DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Configurar headers para descarga
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="registros_camara_'.date('Y-m-d').'.xls"');
    
    // Crear contenido Excel
    echo "<table border='1'>";
    echo "<tr>
            <th>Patente</th>
            <th>Empresa</th>
            <th>Fecha/Hora</th>
            <th>m³</th>
          </tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $fecha = new DateTime($row["cam_fecha_hora"]);
        
        echo "<tr>";
        echo "<td>".htmlspecialchars($row["pat_patente"])."</td>";
        echo "<td>".htmlspecialchars($row["emp_nombre"])."</td>";
        echo "<td>".$fecha->format('d/m/Y H:i:s')."</td>";
        // echo "<td>".ucfirst($row["cam_tipo"])."</td>";
        echo "<td>".($row["cam_tipo"] == 'entrada' ? number_format($row["pat_m3"], 2) : '-')."</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Calcular total de m3 para el rango (solo entradas)
    if (!empty($filtro_fecha_desde)) {
        $sql_total = "SELECT SUM(p.pat_m3) as total_m3 
                     FROM camara c
                     JOIN patentes p ON c.cam_pat_id = p.pat_id
                     WHERE c.cam_tipo = 'entrada' 
                     AND DATE(c.cam_fecha_hora) >= :fecha_desde";
        
        if (!empty($filtro_fecha_hasta)) {
            $sql_total .= " AND DATE(c.cam_fecha_hora) <= :fecha_hasta";
        }
        
        $stmt_total = $pdo->prepare($sql_total);
        $stmt_total->bindValue(':fecha_desde', $filtro_fecha_desde);
        
        if (!empty($filtro_fecha_hasta)) {
            $stmt_total->bindValue(':fecha_hasta', $filtro_fecha_hasta);
        }
        
        $stmt_total->execute();
        $total_m3 = $stmt_total->fetchColumn();
        
        echo "<br><br>";
        echo "<table border='1'>";
        echo "<tr><th colspan='2'>Resumen</th></tr>";
        echo "<tr><td><strong>Total m³ (entradas)</strong></td><td>".number_format($total_m3, 2)."</td></tr>";
        
        if (!empty($filtro_fecha_desde) && !empty($filtro_fecha_hasta)) {
            echo "<tr><td><strong>Período</strong></td><td>".date('d/m/Y', strtotime($filtro_fecha_desde))." - ".date('d/m/Y', strtotime($filtro_fecha_hasta))."</td></tr>";
        } elseif (!empty($filtro_fecha_desde)) {
            echo "<tr><td><strong>Desde</strong></td><td>".date('d/m/Y', strtotime($filtro_fecha_desde))."</td></tr>";
        }
        
        echo "</table>";
    }
    
    exit;
    
} catch (PDOException $e) {
    die("Error al generar Excel: ".$e->getMessage());
}
?>
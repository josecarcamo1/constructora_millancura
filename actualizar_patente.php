<?php
require("conf.php");

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false, 
        'message' => 'Método no permitido'
    ]);
    exit;
}

$cam_id = isset($_POST['cam_id']) ? (int)$_POST['cam_id'] : 0;
$new_patente = isset($_POST['new_patente']) ? trim($_POST['new_patente']) : '';
$new_empresa = isset($_POST['new_empresa']) ? (int)$_POST['new_empresa'] : 0;

// Validaciones
if ($cam_id <= 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'ID de registro inválido'
    ]);
    exit;
}

if (empty($new_patente) || strlen($new_patente) < 4) {
    echo json_encode([
        'success' => false, 
        'message' => 'La patente debe tener al menos 4 caracteres'
    ]);
    exit;
}

if ($new_empresa <= 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Seleccione una empresa válida'
    ]);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // 1. Manejo de la patente (lógica existente)
    $stmt = $pdo->prepare("SELECT pat_id FROM patentes WHERE pat_patente = ?");
    $stmt->execute([$new_patente]);
    $existing_patente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT cam_pat_id FROM camara WHERE cam_id = ?");
    $stmt->execute([$cam_id]);
    $current_pat = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$current_pat) throw new Exception("Registro no encontrado");
    
    $current_pat_id = $current_pat['cam_pat_id'];
    
    if ($existing_patente) {
        $new_pat_id = $existing_patente['pat_id'];
    } else {
        $stmt = $pdo->prepare("SELECT pat_m3, pat_emp_id FROM patentes WHERE pat_id = ?");
        $stmt->execute([$current_pat_id]);
        $current_pat_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current_pat_data) throw new Exception("Error obteniendo datos de patente");
        
        $stmt = $pdo->prepare("INSERT INTO patentes (pat_patente, pat_m3, pat_emp_id) VALUES (?, ?, ?)");
        $stmt->execute([$new_patente, $current_pat_data['pat_m3'], $current_pat_data['pat_emp_id']]);
        $new_pat_id = $pdo->lastInsertId();
    }
    
    // 2. Actualizar ambos campos en camara (modificación principal)
    $stmt = $pdo->prepare("UPDATE camara SET cam_pat_id = ?, cam_emp_id = ? WHERE cam_id = ?");
    $stmt->execute([$new_pat_id, $new_empresa, $cam_id]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Actualización exitosa',
        'new_patente' => $new_patente,
        'new_empresa_id' => $new_empresa
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false, 
        'message' => 'Error BD: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
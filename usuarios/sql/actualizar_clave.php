<?php
require("../../conf.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_POST['id'] ?? '';
    $nueva_clave = $_POST['nueva_clave'] ?? '';
    
    if (empty($id_usuario) || empty($nueva_clave)) {
        die("Datos incompletos");
    }
    
    try {
        $clave_hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE personas SET per_contrasena = ? WHERE per_id = ?");
        $stmt->execute([$clave_hash, $id_usuario]);
        
        header("Location: ../usuarios.php?exito=4");
        exit();
    } catch (PDOException $e) {
        die("Error al actualizar la contraseña: " . $e->getMessage());
    }
} else {
    header("Location: ../usuarios/index.php");
    exit();
}
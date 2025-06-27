<?php
require("../../conf.php"); // Conexión a la base de datos

// Verificamos si se recibió un ID por GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // Sanear el ID por seguridad

    try {
        // Preparar la consulta DELETE
        $stmt = $pdo->prepare("DELETE FROM personas WHERE per_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar
        $stmt->execute();

        // Redirigir con mensaje
        header("Location: ".$url_base."usuarios/usuarios.php?exito=3");
        exit();

    } catch (PDOException $e) {
        echo "Error al eliminar: " . $e->getMessage();
    }
} else {
    echo "ID no válido.";
}
?>

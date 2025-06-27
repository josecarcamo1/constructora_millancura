<?php
require("../../conf.php"); // Conexión a la base de datos

// Verificamos si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitar los datos recibidos
    $nombre = htmlspecialchars($_POST['nombre']);
    $segundo_nombre = htmlspecialchars($_POST['segundo_nombre']);
    $apellido = htmlspecialchars($_POST['apellido']);
    $segundo_apellido = htmlspecialchars($_POST['segundo_apellido']);
    $correo = htmlspecialchars($_POST['correo']);
    $contraseña = htmlspecialchars($_POST['contrasena']);

    // Encriptar la contraseña
    $contraseña_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);

    // Realizar el INSERT utilizando PDO
    try {
        // Query con los parámetros correctos
        $query = "INSERT INTO personas (per_nombre, per_segundo_nombre, per_apellido, per_segundo_apellido, per_correo, per_contrasena)
                  VALUES (:nombre, :segundo_nombre, :apellido, :segundo_apellido, :correo, :contrasena)";
        
        // Preparar la consulta
        $stmt = $pdo->prepare($query);
        
        // Bind de parámetros con los valores correctos
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':segundo_nombre', $segundo_nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':segundo_apellido', $segundo_apellido);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':contrasena', $contraseña_encriptada);
        
        // Ejecutar la consulta
        $stmt->execute();

        // Redirigir con un mensaje de éxito
        header("Location: ".$url_base."usuarios/usuarios.php?exito=1");
        exit();

    } catch (PDOException $e) {
        // Si ocurre un error, mostrarlo
        echo "Error: " . $e->getMessage();
    }
} else {
    // Si no se envió el formulario
    echo "Acceso no autorizado.";
}
?>

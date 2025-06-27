<?php
require("../conf.php");

if (isset($_GET['id'])) {
    // Obtener el ID del usuario desde la URL
    $userId = $_GET['id'];

    // Obtener los datos del usuario desde la base de datos
    $stmt = $pdo->prepare("SELECT * FROM personas WHERE per_id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $nombre = htmlspecialchars($_POST['nombre']);
    $segundo_nombre = htmlspecialchars($_POST['segundo_nombre']);
    $apellido = htmlspecialchars($_POST['apellido']);
    $segundo_apellido = htmlspecialchars($_POST['segundo_apellido']);
    $correo = htmlspecialchars($_POST['correo']);
    $contrasena = htmlspecialchars($_POST['contrasena']);
    $contrasena_encriptada = password_hash($contrasena, PASSWORD_DEFAULT);

    // Asegúrate de que el ID esté correctamente recibido desde la URL
    if (isset($_GET['id'])) {
        $userId = $_GET['id'];
    } else {
        echo "❌ Error: ID no proporcionado.";
        exit();
    }

    try {

        // Consulta SQL para actualizar
        $sql = "UPDATE personas SET 
            per_nombre = :nombre,
            per_segundo_nombre = :segundo_nombre,
            per_apellido = :apellido,
            per_segundo_apellido = :segundo_apellido,
            per_correo = :correo,
            per_contrasena = :contrasena
            WHERE per_id = :id";

        $stmt = $pdo->prepare($sql);

        // Vincular los parámetros (¡sin "ñ" en :contrasena!)
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':segundo_nombre', $segundo_nombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindParam(':segundo_apellido', $segundo_apellido, PDO::PARAM_STR);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->bindParam(':contrasena', $contrasena_encriptada, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo '<script type="text/javascript">
                window.location.href = "' . $url_base . 'usuarios/usuarios.php?exito=2";
            </script>';
            exit();
        } else {
            echo "❌ Error al ejecutar la consulta.";
        }

    } catch (PDOException $e) {
        echo "🚨 Error al ejecutar la consulta: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <?php require("../librerias.php"); ?>
</head>
<body>

<!-- Sidebar (menú) -->
<?php 
require("../menu.php"); 
?>

<!-- Contenido principal bien alineado -->
<div >
    <div class="ui grid" style="height: 100%; margin: 0;">
        <div class="twelve column" id="main-content" >
            <div class="ui raised very padded text container segment" 
                style="max-width: 700px; width: 100%; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 10px;">
                <h2 class="ui teal center aligned header">Editar Usuario</h2>
                <form action="" method="POST" class="ui form">
                    <div class="ui two column stackable grid">
                        <div class="column field">
                            <label>Nombre</label>
                            <input type="text" name="nombre" value="<?= $user['per_nombre'] ?>" required placeholder="Ej. Juan">
                        </div>
                        <div class="column field">
                            <label>Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" value="<?= $user['per_segundo_nombre'] ?>" placeholder="Opcional">
                        </div>
                        <div class="column field">
                            <label>Apellido</label>
                            <input type="text" name="apellido" value="<?= $user['per_apellido'] ?>" required placeholder="Ej. Pérez">
                        </div>
                        <div class="column field">
                            <label>Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" value="<?= $user['per_segundo_apellido'] ?>" placeholder="Opcional">
                        </div>
                        <div class="column field">
                            <label>Correo Electrónico</label>
                            <input type="email" name="correo" value="<?= $user['per_correo'] ?>" required placeholder="correo@ejemplo.com">
                        </div>
                        <div class="column field">
                            <label>Contraseña</label>
                            <input type="password" name="contrasena" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="ui stackable two column grid">
                        <div class="column">
                            <button type="submit" class="ui green button fluid">Actualizar Usuario</button>
                        </div>
                        <div class="column">
                            <a href="<?php echo $url_base; ?>usuarios/usuarios.php" class="ui red button fluid">Volver</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>

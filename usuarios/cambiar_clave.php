<?php
require("../conf.php");

// Verificar si se recibió el ID del usuario
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_GET['id'];

// Obtener información del usuario
try {
    $stmt = $pdo->prepare("SELECT per_nombre, per_apellido, per_contrasena FROM personas WHERE per_id = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    $error = "Error al obtener información del usuario: " . $e->getMessage();
}

// Procesar el formulario de cambio de clave
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $clave_actual = $_POST['clave_actual'] ?? '';
    $nueva_clave = $_POST['nueva_clave'] ?? '';
    $confirmar_clave = $_POST['confirmar_clave'] ?? '';
    
    // Validaciones
    if (empty($clave_actual) || empty($nueva_clave) || empty($confirmar_clave)) {
        $error = "Todos los campos son obligatorios";
    } elseif (!password_verify($clave_actual, $usuario['per_contrasena'])) {
        $error = "La contraseña actual no es correcta";
    } elseif ($nueva_clave !== $confirmar_clave) {
        $error = "Las nuevas contraseñas no coinciden";
    } else {
        // Hash de la nueva contraseña
        $clave_hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("UPDATE personas SET per_contrasena = ? WHERE per_id = ?");
            $stmt->execute([$clave_hash, $id_usuario]);
            
            // Redirigir con mensaje de éxito
            header("Location: usuarios.php?exito=4");
            exit();
        } catch (PDOException $e) {
            $error = "Error al actualizar la contraseña: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <?php require("../librerias.php"); ?>
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
        .header-container {
            background: linear-gradient(135deg, #2185d0 0%, #1678c2 100%);
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
        .ui.form {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .ui.blue.button {
            background-color: #2185d0 !important;
            color: white !important;
            font-weight: bold;
            border-radius: 4px;
            box-shadow: 0 2px 0 #1678c2;
            border: 1px solid #1678c2;
            padding: 0.78571429em 1.5em 0.78571429em !important;
        }
        .ui.blue.button:hover {
            background-color: #1678c2 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 0 #1678c2;
        }
        .ui.orange.button {
            background-color: #f2711c !important;
            color: white !important;
        }
    </style>
</head>
<body>
<div class="ui container" style="padding: 0; width: 100%;">
    <div class="ui grid stackable" style="margin: 0; width: 100%; overflow-x: hidden;">
        <div class="four wide column" style="padding: 0;">
            <?php require("../menu.php"); ?>
        </div>
        <div class="twelve column" id="main-content" style="padding: 0;">
            <div class="ui container main-content">
                <!-- Título mejorado -->
                <div class="header-container">
                    <h1><i class="fas fa-key"></i> Cambiar Contraseña</h1>
                    <p>Administración del sistema - Millancura</p>
                </div>
                
                <!-- Formulario para cambiar clave -->
                <div class="ui form">
                    <h3 class="ui dividing header">Cambiar contraseña para <?php echo htmlspecialchars($usuario['per_nombre']) . ' ' . htmlspecialchars($usuario['per_apellido']); ?></h3>
                    
                    <?php if (isset($error)): ?>
                        <div class="ui negative message">
                            <i class="close icon"></i>
                            <div class="header">Error</div>
                            <p><?php echo $error; ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="field">
                            <label>Contraseña Actual</label>
                            <input type="password" name="clave_actual" placeholder="Ingrese su contraseña actual" required>
                        </div>
                        <div class="field">
                            <label>Nueva Contraseña</label>
                            <input type="password" name="nueva_clave" placeholder="Ingrese nueva contraseña" required>
                            <small>La contraseña puede tener cualquier longitud y caracteres</small>
                        </div>
                        <div class="field">
                            <label>Confirmar Contraseña</label>
                            <input type="password" name="confirmar_clave" placeholder="Confirme la nueva contraseña" required>
                        </div>
                        
                        <div class="field" style="margin-top: 20px;">
                            <button type="submit" class="ui blue button">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="usuarios.php" class="ui button">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Cerrar mensajes de error al hacer clic en la X
    $('.message .close').on('click', function() {
        $(this).closest('.message').transition('fade');
    });
});
</script>
</body>
</html>
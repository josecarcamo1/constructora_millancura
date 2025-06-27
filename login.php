<?php
require("conf.php");

// Función para detectar dispositivos móviles
function esMovil() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    $dispositivosMoviles = [
        'mobile', 'android', 'iphone', 'ipad', 'ipod', 
        'blackberry', 'webos', 'windows phone', 'iemobile'
    ];
    
    foreach ($dispositivosMoviles as $dispositivo) {
        if (strpos($userAgent, $dispositivo) !== false) {
            return true;
        }
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $errorMessage = "";

    try {
        if (!isset($pdo)) {
            $errorMessage = "Error: No se pudo conectar a la base de datos.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM personas WHERE per_correo = :email");
            $stmt->bindParam(':email', $user);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($pass, $usuario['per_contrasena'])) {
                    session_start();
                   
                    $_SESSION['nombre_usuario'] = $usuario['per_nombre'];
                    $_SESSION['usuario_id'] = $usuario['per_id'];
                    $_SESSION['test'] = 'valor';

                    
                    // Determinar a qué página redirigir
                    $pagina_redireccion = esMovil() ? 'inicio_mobile.php' : 'inicio.php';
                    
                    echo '<script type="text/javascript">
                        window.location.href = "' . $url_base . $pagina_redireccion . '";
                    </script>';
                    exit();
                } else {
                    $errorMessage = "Credenciales incorrectas";
                }
            } else {
                $errorMessage = "Usuario no encontrado";
            }
        }
    } catch (PDOException $e) {
        $errorMessage = "Error en la base de datos: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        error_log("Error de conexión: " . $e->getMessage());
    }
}else{
    // Destruir la sesión
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constructora Millancura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2c3e50, #4ca1af);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            text-align: center;
            width: 350px;
            box-sizing: border-box;
        }
        .login-container img {
            width: 220px;
            height: auto;
            margin-bottom: 0;
            object-fit: contain;
        }
        .input-group {
            position: relative;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
        }
        .input-group input {
            width: 100%;
            padding: 12px 10px 12px 40px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .input-group input:focus {
            border-color: #4ca1af;
            box-shadow: 0 0 8px rgba(76, 161, 175, 0.5);
        }
        .input-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
        }
        .login-button {
            background-color: #4ca1af;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            box-sizing: border-box;
        }
        .login-button:hover {
            background-color: #357a80;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="imagenes/logo.jpeg" alt="Logo">
        <?php if (!empty($errorMessage)): ?>
            <div class="ui negative message">
                <i class="close icon"></i>
                <div class="header">Error</div>
                <p><?php echo $errorMessage; ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="text" name="username" placeholder="Correo electrónico" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="ui button primary login-button">Ingresar</button>
        </form>
    </div>
    <script>
        // Script para cerrar el mensaje de error
        $('.message .close').on('click', function() {
            $(this).closest('.message').fadeOut();
        });
    </script>
</body>
</html>
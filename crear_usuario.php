<?php
require("conf.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <?php
    require("librerias.php");
    ?>
    <!-- <link href="css.css" rel="stylesheet" type="text/css"> -->
</head>
<body>
    <div class="ui grid" style="height: 100vh;">
        <?php require("menu.php"); ?>

    <!-- Main content -->
    <div class="twelve wide column" style="padding-left: 260px; padding-right: 20px;">
        <div class="ui segment">
            <h2 class="ui centered header">Crear Usuario</h2>
            <form action="guardar_usuario.php" method="POST" class="ui form">
                <div class="field">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="field">
                    <label for="segundo_nombre">Segundo Nombre:</label>
                    <input type="text" id="segundo_nombre" name="segundo_nombre">
                </div>
                <div class="field">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                <div class="field">
                    <label for="segundo_apellido">Segundo Apellido:</label>
                    <input type="text" id="segundo_apellido" name="segundo_apellido">
                </div>
                <div class="field">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                <div class="field">
                    <label for="contraseña">Contraseña:</label>
                    <input type="password" id="contraseña" name="contraseña" required>
                </div>
                <div class="ui two buttons">
                    <button type="submit" class="ui green button">Guardar Usuario</button>
                    <a href="index.php" class="ui red button">Volver</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>

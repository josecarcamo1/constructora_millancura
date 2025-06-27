<?php
require("../conf.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <?php require("../librerias.php"); ?>
</head>
<body>

<!-- Sidebar (menú) -->
<?php require("../menu.php"); ?>

<!-- Contenido principal bien alineado -->
<div >
    <div class="ui grid" style="height: 100%; margin: 0;">
        <div class="twelve column" id="main-content" >
            <div class="ui raised very padded text container segment" 
                style="max-width: 700px; width: 100%; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 10px;">
                <h2 class="ui teal center aligned header">Crear Usuario</h2>
                <form action="<?php echo $url_base; ?>usuarios/sql/crear_guardar.php" method="POST" class="ui form">
                    <div class="ui two column stackable grid">
                        <div class="column field">
                            <label>Nombre</label>
                            <input type="text" name="nombre" required placeholder="Ej. Juan">
                        </div>
                        <div class="column field">
                            <label>Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" placeholder="Opcional">
                        </div>
                        <div class="column field">
                            <label>Apellido</label>
                            <input type="text" name="apellido" required placeholder="Ej. Pérez">
                        </div>
                        <div class="column field">
                            <label>Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" placeholder="Opcional">
                        </div>
                        <div class="column field">
                            <label>Correo Electrónico</label>
                            <input type="email" name="correo" required placeholder="correo@ejemplo.com">
                        </div>
                        <div class="column field">
                            <label>Contraseña</label>
                            <input type="password" name="contrasena" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="ui stackable two column grid">
                        <div class="column">
                            <button type="submit" class="ui green button fluid">Guardar Usuario</button>
                        </div>
                        <div class="column">
                            <a href="usuarios.php" class="ui red button fluid">Volver</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>

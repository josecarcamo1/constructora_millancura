<?php
require("../conf.php");

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    // header("Location: ".$url_base."login.php");
    // exit();
}

// Obtener información del usuario actual
$usuario_actual_id = $_SESSION['usuario_id'];
echo $usuario_actual_id;
$perfil_usuario = 0; // Valor por defecto para usuarios normales
$usuario_actual_data = [];

try {
    $stmt = $pdo->prepare("SELECT per_pfl_id, per_nombre, per_apellido FROM personas WHERE per_id = ?");
    $stmt->execute([$usuario_actual_id]);
    $usuario_actual_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario_actual_data) {
        $perfil_usuario = $usuario_actual_data['per_pfl_id'];
    }
} catch (PDOException $e) {
    die("Error al obtener información del usuario: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
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
        .ui.table {
            font-size: 14px;
            margin: 10px 0 !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .ui.button {
            padding: 8px 12px;
            font-size: 13px;
            transition: all 0.3s ease;
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
        .ui.blue.button {
            background-color: #2185d0 !important;
            color: white !important;
            font-weight: bold;
            border-radius: 4px;
            box-shadow: 0 2px 0 #1678c2;
            border: 1px solid #1678c2;
            padding: 0.78571429em 1.5em 0.78571429em !important;
            margin-bottom: 10px;
        }
        .ui.blue.button:hover {
            background-color: #1678c2 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 0 #1678c2;
        }
        .ui.green.button:hover, .ui.red.button:hover, .ui.orange.button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 0 rgba(0,0,0,0.2);
        }
        table tbody tr:hover {
            background-color: #f8f8f8 !important;
            transform: translateX(2px);
            transition: all 0.2s ease;
        }
        .ui.orange.button {
            background-color: #f2711c !important;
            color: white !important;
        }
        .access-denied {
            text-align: center;
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .access-denied i {
            font-size: 50px;
            color: #db2828;
            margin-bottom: 20px;
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
                    <h1><i class="fas fa-users-cog"></i> Gestión de Usuarios</h1>
                    <p>Administración del sistema - <?php echo htmlspecialchars($usuario_actual_data['per_nombre'] ?? '').' '.htmlspecialchars($usuario_actual_data['per_apellido'] ?? ''); ?></p>
                </div>
                
                <?php if ($perfil_usuario == 1): ?>
                    <!-- Botón Crear Usuario (solo para administradores) -->
                    <div style="margin-bottom: 15px;">
                        <a href="<?php echo $url_base; ?>usuarios/crear_usuario.php" class="ui blue button">
                            <i class="fas fa-user-plus"></i> Crear Nuevo Usuario
                        </a>
                    </div>

                    <!-- Tabla de Usuarios (completa para administradores) -->
                    <div>
                        <?php
                        try {
                            $stmt = $pdo->prepare("SELECT * FROM personas");
                            $stmt->execute();

                            if ($stmt->rowCount() > 0) {
                                echo "<table class='ui celled striped table'>";
                                echo "<thead><tr>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Email</th>
                                        <th>Perfil</th>
                                        <th width='400'>Acciones</th>
                                    </tr></thead>";
                                echo "<tbody>";

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["per_nombre"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["per_apellido"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["per_correo"]) . "</td>";
                                    echo "<td>" . ($row["per_pfl_id"] == 1 ? 'Administrador' : 'Usuario') . "</td>";
                                    echo "<td>
                                        <a href='".$url_base."usuarios/editar_usuario.php?id=" . $row["per_id"] . "' class='ui green button'><i class='fas fa-edit'></i> Editar</a>
                                        <a href='".$url_base."usuarios/cambiar_clave.php?id=" . $row["per_id"] . "' class='ui orange button'><i class='fas fa-key'></i> Cambiar Clave</a>";
                                    
                                    // Solo permitir eliminar usuarios que no sean el propio administrador
                                    if ($row["per_id"] != $usuario_actual_id) {
                                        echo "<a href='".$url_base."usuarios/sql/eliminar_usuario.php?id=" . $row["per_id"] . "' class='ui red button' onclick='return confirm(\"¿Está seguro que desea eliminar este usuario?\")'><i class='fas fa-trash-alt'></i> Eliminar</a>";
                                    }
                                    
                                    echo "</td>";
                                    echo "</tr>";
                                }

                                echo "</tbody>";
                                echo "</table>";
                            } else {
                                echo "<div class='ui icon message'>
                                        <i class='fas fa-user-slash icon'></i>
                                        <div class='content'>
                                            <div class='header'>No se encontraron usuarios</div>
                                            <p>No hay usuarios registrados en el sistema.</p>
                                        </div>
                                    </div>";
                            }
                        } catch (PDOException $e) {
                            echo "<div class='ui negative icon message'>
                                    <i class='fas fa-exclamation-circle icon'></i>
                                    <div class='content'>
                                        <div class='header'>Error en la consulta</div>
                                        <p>" . htmlspecialchars($e->getMessage()) . "</p>
                                    </div>
                                </div>";
                        }
                        ?>
                    </div>
                <?php else: ?>
                    <!-- Vista para usuarios normales (solo su propio perfil) -->
                    <div>
                        <?php
                        try {
                            $stmt = $pdo->prepare("SELECT * FROM personas WHERE per_id = ?");
                            $stmt->execute([$usuario_actual_id]);
                            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($usuario) {
                                echo "<table class='ui celled striped table'>";
                                echo "<thead><tr>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Email</th>
                                        <th width='300'>Acciones</th>
                                    </tr></thead>";
                                echo "<tbody>";
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($usuario["per_nombre"]) . "</td>";
                                echo "<td>" . htmlspecialchars($usuario["per_apellido"]) . "</td>";
                                echo "<td>" . htmlspecialchars($usuario["per_correo"]) . "</td>";
                                echo "<td>
                                    <a href='".$url_base."usuarios/editar_usuario.php?id=" . $usuario["per_id"] . "' class='ui green button'><i class='fas fa-edit'></i> Editar</a>
                                    <a href='".$url_base."usuarios/cambiar_clave.php?id=" . $usuario["per_id"] . "' class='ui orange button'><i class='fas fa-key'></i> Cambiar Clave</a>
                                </td>";
                                echo "</tr>";
                                echo "</tbody>";
                                echo "</table>";
                            } else {
                                echo "<div class='access-denied'>
                                        <i class='fas fa-exclamation-triangle'></i>
                                        <h2>Usuario no encontrado</h2>
                                        <p>No se pudo encontrar la información de su usuario.</p>
                                    </div>";
                            }
                        } catch (PDOException $e) {
                            echo "<div class='ui negative icon message'>
                                    <i class='fas fa-exclamation-circle icon'></i>
                                    <div class='content'>
                                        <div class='header'>Error en la consulta</div>
                                        <p>" . htmlspecialchars($e->getMessage()) . "</p>
                                    </div>
                                </div>";
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
  <div id="alerta-exito" class="ui success message" style="display:none; position: fixed; top: 20px; right: 20px; z-index: 1000;">
    <i class="close icon"></i>
    <div class="header">¡Usuario creado exitosamente!</div>
  </div>
  <script>
    $(document).ready(function () {
      $('#alerta-exito')
        .transition('fade in');

      setTimeout(function () {
        $('#alerta-exito').transition('fade');
      }, 4000);

      $('#alerta-exito .close')
        .on('click', function () {
          $(this).closest('.message').transition('fade');
        });
    });
  </script>
<?php endif; ?>

<?php if (isset($_GET['exito']) && $_GET['exito'] == 2): ?>
  <div id="alerta-editar" class="ui info message" style="display:none; position: fixed; top: 20px; right: 20px; z-index: 1000;">
    <i class="close icon"></i>
    <div class="header">¡Usuario editado exitosamente!</div>
  </div>
  <script>
    $(document).ready(function () {
      $('#alerta-editar')
        .transition('fade in');

      setTimeout(function () {
        $('#alerta-editar').transition('fade');
      }, 4000);

      $('#alerta-editar .close')
        .on('click', function () {
          $(this).closest('.message').transition('fade');
        });
    });
  </script>
<?php endif; ?>

<?php if (isset($_GET['exito']) && $_GET['exito'] == 3): ?>
  <div id="alerta-eliminar" class="ui red message" style="display:none; position: fixed; top: 20px; right: 20px; z-index: 1000;">
    <i class="close icon"></i>
    <div class="header">¡Usuario eliminado exitosamente!</div>
  </div>
  <script>
    $(document).ready(function () {
      $('#alerta-eliminar')
        .transition('fade in');

      setTimeout(function () {
        $('#alerta-eliminar').transition('fade');
      }, 4000);

      $('#alerta-eliminar .close')
        .on('click', function () {
          $(this).closest('.message').transition('fade');
        });
    });
  </script>
<?php endif; ?>

<script>
$(document).ready(function() {
    // Efecto hover para filas de la tabla
    $('table tbody tr').hover(
        function() {
            $(this).css('transform', 'translateX(2px)');
            $(this).css('box-shadow', '0 2px 5px rgba(0,0,0,0.1)');
        },
        function() {
            $(this).css('transform', '');
            $(this).css('box-shadow', '');
        }
    );
    
    // Confirmación para acciones importantes
    $('.ui.red.button').on('click', function(e) {
        return confirm('¿Está seguro que desea realizar esta acción?');
    });
});
</script>
</body>
</html>
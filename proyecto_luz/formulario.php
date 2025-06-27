<?php
require("../conf.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Empresa</title>
    <?php require("../librerias.php"); ?>
    <style>
        .main-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            margin: 0 auto;
            max-width: 800px;
            border-top: 5px solid #00b5ad;
        }
        
        .form-header {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 1rem;
        }
        
        .form-header:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 25%;
            width: 50%;
            height: 3px;
            background: linear-gradient(90deg, #00b5ad 0%, #00827a 100%);
            border-radius: 3px;
        }
        
        .section-title {
            color: #00b5ad;
            margin: 1.5rem 0 1rem;
            font-weight: 500;
            border-left: 4px solid #00b5ad;
            padding-left: 10px;
        }
        
        .ui.checkbox label:before {
            border: 2px solid #00b5ad;
        }
        
        .ui.checkbox input:checked~label:after {
            color: #00b5ad;
        }
        
        .save-btn {
            background: linear-gradient(135deg, #00b5ad 0%, #00827a 100%) !important;
            color: white !important;
            transition: all 0.3s !important;
        }
        
        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 181, 173, 0.4) !important;
        }
        
        .cancel-btn {
            transition: all 0.3s !important;
        }
        
        .cancel-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(221, 81, 76, 0.4) !important;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar (menú) -->
<?php 
// require("../menu.php"); ?>

<!-- Contenido principal -->
<div class="main-container">
    <div class="ui container">
        <div class="form-container">
            <h2 class="form-header">Modificar Empresa</h2>
            <form action="<?php echo $url_base; ?>empresa/sql/modificar_guardar.php" method="POST" class="ui form">
                
                <!-- Sección de datos básicos -->
                <h4 class="section-title">Información Básica</h4>
                <div class="field">
                    <label>Tipo de Negocio</label>
                    <select name="tipo_negocio" class="ui dropdown" required>
                        <option value="">Seleccione...</option>
                        <option value="Almacén / Minimalket" selected>Almacén / Minimalket</option>
                        <option value="Restaurante">Restaurante</option>
                        <option value="Tienda de Ropa">Tienda de Ropa</option>
                        <option value="Supermercado">Supermercado</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                
                <div class="ui stackable two column grid">
                    <div class="column field">
                        <label>RUT</label>
                        <input type="text" name="rut" required placeholder="Ej: 12.345.678-9" value="93.993.999">
                    </div>
                    <div class="column field">
                        <label>Nombre o Razón Social</label>
                        <input type="text" name="nombre_razon" required value="Minimalket del Vecino">
                    </div>
                </div>
                
                <div class="field">
                    <label>Giro</label>
                    <input type="text" name="giro" required value="Minimalket">
                </div>
                
                <div class="ui stackable three column grid">
                    <div class="column field">
                        <label>Dirección</label>
                        <input type="text" name="direccion" required value="Las Flores 1656, Local 3">
                    </div>
                    <div class="column field">
                        <label>Comuna</label>
                        <input type="text" name="comuna" required value="Pudahuel">
                    </div>
                    <div class="column field">
                        <label>Ciudad</label>
                        <input type="text" name="ciudad" required value="Santiago">
                    </div>
                </div>
                
                <div class="ui stackable two column grid">
                    <div class="column field">
                        <label>Teléfono</label>
                        <input type="tel" name="telefono" required value="+569 37138616">
                    </div>
                    <div class="column field">
                        <label>Correo o Redes Sociales</label>
                        <input type="text" name="contacto" required value="minimalketdelvecino@gmail.com">
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="ui stackable two column grid" style="margin-top: 2rem;">
                    <div class="column">
                        <button type="submit" class="ui save-btn button fluid">
                            <i class="save icon"></i> Guardar Cambios
                        </button>
                    </div>
                    <div class="column">
                        <a href="empresas.php" class="ui red cancel-btn button fluid">
                            <i class="times icon"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.ui.dropdown').dropdown();
    $('.ui.checkbox').checkbox();
    
    // Efecto hover para los inputs
    $('.ui.form input').focus(function(){
        $(this).parent().addClass('focused');
    }).blur(function(){
        $(this).parent().removeClass('focused');
    });
});
</script>

</body>
</html>
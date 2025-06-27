<!-- Sidebar Fijo con Indicador de Página Activa -->
<div class="modern-sidebar" id="sidebar">
    <!-- Encabezado con avatar -->
    <div class="sidebar-header">
        <div class="avatar-container">
            <div class="avatar-circle">
                <img src="<?php echo $url_base; ?>logo_m.png" alt="Logo M" class="avatar-image">
            </div>
            <h2>Gestor de Camiones</h2>
            <p class="welcome-message">
                <i class="fas fa-user-circle"></i> Bienvenido, <?php echo isset($_SESSION['nombre_usuario']) ? htmlspecialchars($_SESSION['nombre_usuario']) : 'Usuario'; ?>
            </p>
        </div>
    </div>
    
    <!-- Menú principal -->
    <div class="sidebar-menu">
        <a href="<?php echo $url_base; ?>inicio.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'inicio.php' ? 'active' : ''; ?>" data-section="dashboard">
            <div class="menu-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <span class="menu-text">Resumen</span>
            <div class="active-indicator"></div>
        </a>
        
        <a href="<?php echo $url_base; ?>base_de_datos.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'base_de_datos.php' ? 'active' : ''; ?>" data-section="database">
            <div class="menu-icon">
                <i class="fas fa-database"></i>
            </div>
            <span class="menu-text">Detalle Camiones</span>
            <div class="active-indicator"></div>
        </a>
        
        <a href="<?php echo $url_base; ?>usuarios/usuarios.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : ''; ?>" data-section="users">
            <div class="menu-icon">
                <i class="fas fa-users"></i>
            </div>
            <span class="menu-text">Gestión de Usuarios</span>
            <div class="active-indicator"></div>
        </a>
    </div>
    
    <!-- Footer con logout -->
    <div class="sidebar-footer">
        <div class="divider"></div>
        <a href="<?php echo $url_base; ?>logout.php" class="logout-btn">
            <div class="logout-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <span>Cerrar Sesión</span>
        </a>
        <div class="version-info">
            Versión 1.0.0
        </div>
    </div>
</div>

<!-- Script para efectos interactivos -->
<script>
    $(document).ready(function() {
        // Efecto hover en items del menú
        $('.menu-item').hover(
            function() {
                $(this).addClass('hover');
            },
            function() {
                $(this).removeClass('hover');
            }
        );
        
        // Efecto al hacer clic
        $('.menu-item').click(function() {
            $('.menu-item').removeClass('active');
            $(this).addClass('active');
            
            // Efecto ripple
            const posX = $(this).offset().left;
            const posY = $(this).offset().top;
            const buttonWidth = $(this).width();
            const buttonHeight = $(this).height();
            
            $(this).prepend('<span class="ripple-effect"></span>');
            
            $('.ripple-effect').css({
                'width': buttonWidth,
                'height': buttonHeight,
                'top': 0,
                'left': 0
            }).addClass('ripple');
            
            setTimeout(function() {
                $('.ripple-effect').remove();
            }, 600);
        });
        
        // Efecto hover para logout
        $('.logout-btn').hover(
            function() {
                $(this).addClass('hover');
            },
            function() {
                $(this).removeClass('hover');
            }
        );
    });
    // Función para resaltar el ítem activo basado en la URL actual
    function highlightActiveMenuItem() {
        const currentPage = window.location.pathname.split('/').pop().toLowerCase();
        document.querySelectorAll('.menu-item').forEach(item => {
            const itemHref = item.getAttribute('href').toLowerCase();
            if (itemHref.includes(currentPage)) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    // Ejecutar al cargar la página y cuando cambia la URL (para SPA)
    document.addEventListener('DOMContentLoaded', highlightActiveMenuItem);
    window.addEventListener('popstate', highlightActiveMenuItem);
</script>

<style>
    /* Sidebar fijo moderno */
    .modern-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 280px;
        height: 100vh;
        background: linear-gradient(135deg, #2c3e50, #1a2634);
        color: white;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        box-shadow: 5px 0 25px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    /* Encabezado del sidebar */
    .sidebar-header {
        padding: 30px 20px;
        background: rgba(0, 0, 0, 0.15);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .avatar-container {
        text-align: center;
    }
    
    .avatar-circle {
        width: 90px;
        height: 90px;
        margin: 0 auto 15px;
        background: linear-gradient(45deg, #3498db, #2980b9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    
    .avatar-circle:hover {
        transform: scale(1.05);
    }
    
    .sidebar-header h2 {
        margin: 10px 0 5px;
        font-size: 1.4rem;
        font-weight: 600;
    }
    
    .welcome-message {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-top: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .welcome-message i {
        margin-right: 8px;
        font-size: 1rem;
    }
    
    /* Menú principal */
    .sidebar-menu {
        flex: 1;
        padding: 20px 0;
        overflow-y: auto;
    }
    
    .menu-item {
        display: flex;
        align-items: center;
        padding: 16px 25px;
        color: #ecf0f1;
        text-decoration: none;
        position: relative;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .menu-item.hover {
        background: rgba(255, 255, 255, 0.05);
    }
    
    .menu-item.active {
        background: rgba(52, 152, 219, 0.2) !important;
        border-left: 4px solid #3498db;
    }
    
    .menu-item.active .menu-text {
        font-weight: 600;
        color: white !important;
    }
    
    .menu-item.active .menu-icon {
        color: #3498db !important;
        transform: scale(1.1);
    }

    .menu-item.active .active-indicator {
        display: block;
        background: #3498db;
        box-shadow: 0 0 10px rgba(52, 152, 219, 0.7);
    }
    
    .menu-icon {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }
    
    .menu-text {
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .active-indicator {
        display: none;
        position: absolute;
        right: 15px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    /* Footer del sidebar */
    .sidebar-footer {
        padding: 15px 20px;
        background: rgba(0, 0, 0, 0.15);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.1);
        margin: 10px 0 15px;
    }
    
    .logout-btn {
        display: flex;
        align-items: center;
        color: #e74c3c;
        text-decoration: none;
        padding: 10px 15px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    
    .logout-btn.hover {
        background: rgba(231, 76, 60, 0.1);
    }
    
    .logout-btn .logout-icon {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 1.1rem;
    }
    
    .version-info {
        font-size: 0.75rem;
        text-align: center;
        margin-top: 15px;
        opacity: 0.6;
    }
    
    /* Efecto ripple */
    .ripple-effect {
        position: absolute;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    /* Ajuste para el contenido principal */
    #main-content {
        margin-left: 280px;
        padding: 20px;
        min-height: 100vh;
        transition: margin-left 0.3s ease;
    }
    .avatar-circle {
    /* Mantén tus estilos existentes para el círculo */
    width: 100px; /* Ajusta según necesites */
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Esto asegura que la imagen cubra todo el círculo */
}
</style>
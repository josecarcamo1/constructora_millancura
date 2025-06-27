<button class="mobile-menu-btn" id="mobileMenuBtn">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Mobile -->
<div class="custom-sidebar" id="sidebar">
<!-- Encabezado con efecto de acordeón -->
<div class="sidebar-header">
    <div class="user-profile">
        <div class="avatar">
            <i class="fas fa-truck"></i>
        </div>
        <div class="user-info">
            <h3>Gestor de Camiones</h3>
            <p class="welcome-msg">
                <i class="fas fa-user-circle"></i> Bienvenido, <?php echo isset($_SESSION['nombre_usuario']) ? htmlspecialchars($_SESSION['nombre_usuario']) : 'Usuario'; ?>
            </p>
        </div>
    </div>
</div>

<!-- Menú principal con hover effects -->
<div class="sidebar-menu">
        <a href="<?php echo $url_base; ?>inicio_mobile.php" class="menu-item" data-section="dashboard">
            <div class="menu-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <span class="menu-text">Resumen</span>
            <div class="hover-effect"></div>
        </a>
        
        <a href="<?php echo $url_base; ?>base_de_datos_mobile.php" class="menu-item" data-section="database">
            <div class="menu-icon">
                <i class="fas fa-database"></i>
            </div>
            <span class="menu-text">Detalle Camiones</span>
            <div class="hover-effect"></div>
        </a>
    </div>
    
    <!-- Sección inferior con logout -->
    <div class="sidebar-footer">
        <div class="divider"></div>
        <a href="<?php echo $url_base; ?>logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Cerrar Sesión</span>
        </a>
        <div class="version-info">
            Versión 1.0.0
        </div>
    </div>
</div>

<style>
    .mobile-menu-btn {
        display: none;
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1000; /* Reducido de 10000 a 1000 */
        background: rgba(44, 62, 80, 0.9); /* Fondo semitransparente */
        color: white;
        border: none;
        border-radius: 5px; /* Cambiado de 50% a 5px para forma rectangular */
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
    }

    .mobile-menu-btn:hover {
        background: rgba(44, 62, 80, 1);
        transform: scale(1.05);
    }
    .main-content {
        padding: 15px;
        margin-left: 0;
        transition: margin-left 0.3s;
        padding-top: 70px; /* Añadir espacio para el botón */
    }
    
    .main-content.with-sidebar {
        margin-left: 250px;
    }

    #sidebar {
        position: fixed;
        width: 250px;
        height: 100vh;
        overflow-y: auto;
        z-index: 1001;
        background: #2c3e50;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }

    #sidebar.visible {
        transform: translateX(0);
    }

    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        display: none;
    }
    
    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        
        .mobile-menu-btn {
            display: block;
        }
        
        .main-content {
            margin-left: 0;
        }
        
        .main-content.with-sidebar {
            margin-left: 0;
        }
        
        #sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s;
        }
        
        #sidebar.visible {
            transform: translateX(0);
        }
    }
    
    @media (min-width: 769px) {
        #sidebar {
            transform: translateX(0) !important;
        }
        
        .main-content {
            margin-left: 250px;
            padding-top: 15px; /* Restaurar padding en desktop */
        }
        
        .mobile-menu-btn {
            display: none !important;
        }
    }
    /* Estilos para el sidebar moderno */
    .custom-sidebar {
        position: fixed;
        width: 280px;
        height: 100vh;
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
        transform: translateX(-100%);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .custom-sidebar.visible {
        transform: translateX(0);
    }

    .sidebar-header {
        padding: 25px 20px;
        background: rgba(0, 0, 0, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .user-profile {
        display: flex;
        align-items: center;
    }

    .avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(45deg, #3498db, #2980b9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.4rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .user-info h3 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .welcome-msg {
        margin: 5px 0 0;
        font-size: 0.85rem;
        opacity: 0.8;
        display: flex;
        align-items: center;
    }

    .welcome-msg i {
        margin-right: 5px;
    }

    .sidebar-menu {
        flex: 1;
        padding: 20px 0;
        overflow-y: auto;
    }

    .menu-item {
        display: flex;
        align-items: center;
        padding: 15px 25px;
        color: white;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .menu-item:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .menu-item.active {
        background: rgba(52, 152, 219, 0.2);
    }

    .menu-item.active .menu-text {
        font-weight: 600;
    }

    .menu-item.active .menu-icon {
        transform: scale(1.1);
    }

    .menu-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        transition: all 0.3s ease;
    }

    .menu-text {
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .hover-effect {
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: #3498db;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .menu-item:hover .hover-effect {
        transform: translateX(0);
    }

    .sidebar-footer {
        padding: 15px 20px;
        background: rgba(0, 0, 0, 0.1);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.1);
        margin-bottom: 15px;
    }

    .logout-btn {
        display: flex;
        align-items: center;
        color: #e74c3c;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        background: rgba(231, 76, 60, 0.1);
    }

    .logout-btn i {
        margin-right: 10px;
    }

    .version-info {
        font-size: 0.7rem;
        text-align: center;
        margin-top: 15px;
        opacity: 0.6;
    }

    /* Efecto de onda al hacer clic */
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }
</style>
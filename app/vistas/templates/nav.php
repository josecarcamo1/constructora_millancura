<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm" id="navbar_principal">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../img/logo.png" height="30" alt="Logo Neon Led">
        </a>        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a id="nav_reportes" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Reportes
                    </a>
                    <ul class="dropdown-menu">                       
                        <?php
                            if($_SESSION["acceso"] == "Administrador"){
                                echo '<li><a class="dropdown-item" href="reporte_general.php">General</a></li>';
                            }
                        ?>
                        <li><a class="dropdown-item" href="reporte_vendedor.php">Vendedor</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a id="nav_activos" class="nav-link" href="operacion.php">Pedidos Activos</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Administración
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="clientes.php">Clientes</a></li>
                        <li><a class="dropdown-item" href="proyectos.php">Pedidos</a></li>
                        <?php
                            if($_SESSION["acceso"] == "Administrador"){
                                echo '<li><a class="dropdown-item" href="vendedores.php">Vendedores</a></li>';
                            }
                        ?>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto d-flex align-items-center text-white">
                <li><?php echo $_SESSION["nombre"];?></li>
                <li id="admin" class="nav-item">
                    <a class="btn text-white" href="cambiar_contr.php" title="Cambiar Contraseña"><i class="fas fa-user"></i></a>
                </li>
                <li class="nav-item">
                    <a class="btn text-white" href="../../api/salir.php" title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
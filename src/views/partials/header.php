    <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
            <img src="/restaurant/public/assets/brand/restaurant-logo.png" title="<?= APP_NAME ?>" width="40" height="24">
            </a>
            <!-- <a class="navbar-brand" href="/restaurant/public/home"><?php echo APP_NAME; ?></a> -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/restaurant/public/home">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/restaurant/public/products">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/restaurant/public/about">Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/restaurant/public/legal">Legal</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/restaurant/public/cart">
                                <span id="cart-count" class="badge bg-primary">0</span> Carrito
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <?php echo htmlspecialchars($_SESSION['name'] ?? 'Usuario'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/restaurant/public/account/profile">Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="/restaurant/public/account/orders">Mis Pedidos</a></li>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/restaurant/public/admin">Panel Admin</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/restaurant/public/logout">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/restaurant/public/login">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/restaurant/public/register">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>



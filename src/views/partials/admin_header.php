    <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/restaurant/public/admin">Admin Panel</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-light" href="/restaurant/public/home">Volver al sitio</a>
                <a class="nav-link text-light" href="/restaurant/public/logout">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    <div class="container-fluid mt-4">
        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button">Usuarios</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button">Pedidos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="promotions-tab" data-bs-toggle="tab" data-bs-target="#promotions" type="button">Promociones</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button">Categorías</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button">Productos</button>
            </li>
        </ul>
    </div>


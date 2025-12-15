<?php $title = 'Panel de Administración'; ?>
<div class="tab-pane fade px-3 show active" id="users" role="tabpanel">
    <h3>Usuarios</h3>
    <div id="users-content">Cargando usuarios...</div>
</div>

<div class="tab-pane fade px-3" id="orders" role="tabpanel">
    <h3>Pedidos</h3>
    <div class="mb-3">
        <input type="text" class="form-control d-inline-block w-auto" id="filter-client" placeholder="Filtrar por cliente">
        <input type="date" class="form-control d-inline-block w-auto" id="filter-date-from" placeholder="Desde">
        <input type="date" class="form-control d-inline-block w-auto" id="filter-date-to" placeholder="Hasta">
        <button class="btn btn-primary" id="apply-filters">Aplicar Filtros</button>
    </div>
    <div id="orders-content">Cargando pedidos...</div>
</div>

<div class="tab-pane fade px-3" id="promotions" role="tabpanel">
    <h3>Promociones</h3>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#promotionModal">Nueva Promoción</button>
    <div id="promotions-content">Cargando promociones...</div>
</div>

<div class="tab-pane fade px-3" id="categories" role="tabpanel">
    <h3>Categorías</h3>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#categoryModal">Nueva Categoría</button>
    <div id="categories-content">Cargando categorías...</div>
</div>

<div class="tab-pane fade px-3" id="products" role="tabpanel">
    <h3>Productos</h3>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#productModal">Nuevo Producto</button>
    <div id="products-content">Cargando productos...</div>
</div>

<!-- Modals will be added via JavaScript -->


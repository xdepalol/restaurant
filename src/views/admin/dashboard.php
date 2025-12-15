<?php $title = 'Panel de Administración'; ?>

<!-- USUARIOS -->
<div class="tab-pane fade px-3 show active" id="users" role="tabpanel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Usuarios</h3>
        <button
            type="button"
            class="btn btn-primary"
            id="btn-new-user"
            data-entity="user"
            data-action="new"
        >
            Nuevo Usuario
        </button>
    </div>

    <!-- Listado -->
    <div id="users-list">
        <div id="users-content">Cargando usuarios...</div>
    </div>

    <!-- Formulario CRUD oculto -->
    <div id="users-form-wrapper" class="card d-none mt-3">
        <div class="card-body">
            <h4 id="users-form-title" class="card-title mb-3"></h4>
            <form id="user-form" data-entity="user">
                <input type="hidden" name="user_id" id="user_id">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="user_name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="user_name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="user_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="user_email" name="email" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="user_address" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="user_address" name="address" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="user_phone" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="user_phone" name="phone" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="user_role" class="form-label">Rol</label>
                        <select class="form-select" id="user_role" name="role" required>
                            <option value="client">Cliente</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="user_login" class="form-label">Login</label>
                        <input type="text" class="form-control" id="user_login" name="login" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="user_password" class="form-label">Contraseña (solo alta / cambio)</label>
                        <input type="password" class="form-control" id="user_password" name="password">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-entity="user"
                        data-action="cancel-form"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        data-entity="user"
                        data-action="submit-form"
                    >
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PEDIDOS -->
<div class="tab-pane fade px-3" id="orders" role="tabpanel">
    <h3>Pedidos</h3>
    <div class="mb-3">
        <input type="text" class="form-control d-inline-block w-auto" id="filter-client" placeholder="Filtrar por cliente (ID)">
        <input type="date" class="form-control d-inline-block w-auto" id="filter-date-from" placeholder="Desde">
        <input type="date" class="form-control d-inline-block w-auto" id="filter-date-to" placeholder="Hasta">
        <button class="btn btn-primary" id="apply-filters">Aplicar Filtros</button>
    </div>
    <div id="orders-content">Cargando pedidos...</div>
</div>

<!-- PROMOCIONES -->
<div class="tab-pane fade px-3" id="promotions" role="tabpanel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Promociones</h3>
        <button
            class="btn btn-primary"
            type="button"
            id="btn-new-promotion"
            data-entity="promotion"
            data-action="new"
        >
            Nueva Promoción
        </button>
    </div>

    <div id="promotions-list">
        <div id="promotions-content">Cargando promociones...</div>
    </div>

    <div id="promotions-form-wrapper" class="card d-none mt-3">
        <div class="card-body">
            <h4 id="promotions-form-title" class="card-title mb-3"></h4>
            <form id="promotion-form" data-entity="promotion">
                <input type="hidden" name="promotion_id" id="promotion_id">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="promo_code" class="form-label">Código</label>
                        <input type="text" class="form-control" id="promo_code" name="promo_code" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="promo_discount" class="form-label">Descuento (%)</label>
                        <input type="number" step="0.01" class="form-control" id="promo_discount" name="discount" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="promo_status" class="form-label">Estado</label>
                        <select class="form-select" id="promo_status" name="status">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="promo_description" class="form-label">Descripción</label>
                    <textarea class="form-control" id="promo_description" name="description" rows="3" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="promo_starts_at" class="form-label">Inicio</label>
                        <input type="datetime-local" class="form-control" id="promo_starts_at" name="starts_at">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="promo_ends_at" class="form-label">Fin</label>
                        <input type="datetime-local" class="form-control" id="promo_ends_at" name="ends_at">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-entity="promotion"
                        data-action="cancel-form"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        data-entity="promotion"
                        data-action="submit-form"
                    >
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CATEGORÍAS -->
<div class="tab-pane fade px-3" id="categories" role="tabpanel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Categorías</h3>
        <button
            class="btn btn-primary"
            type="button"
            id="btn-new-category"
            data-entity="category"
            data-action="new"
        >
            Nueva Categoría
        </button>
    </div>

    <div id="categories-list">
        <div id="categories-content">Cargando categorías...</div>
    </div>

    <div id="categories-form-wrapper" class="card d-none mt-3">
        <div class="card-body">
            <h4 id="categories-form-title" class="card-title mb-3"></h4>
            <form id="category-form" data-entity="category">
                <input type="hidden" name="category_id" id="category_id">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category_name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="category_name" name="nombre" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="category_order" class="form-label">Orden</label>
                        <input type="number" class="form-control" id="category_order" name="order" value="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="category_status" class="form-label">Estado</label>
                        <select class="form-select" id="category_status" name="status">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="category_image" class="form-label">URL Imagen</label>
                    <input type="text" class="form-control" id="category_image" name="image">
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-entity="category"
                        data-action="cancel-form"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        data-entity="category"
                        data-action="submit-form"
                    >
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PRODUCTOS -->
<div class="tab-pane fade px-3" id="products" role="tabpanel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Productos</h3>
        <button
            class="btn btn-primary"
            type="button"
            id="btn-new-product"
            data-entity="product"
            data-action="new"
        >
            Nuevo Producto
        </button>
    </div>

    <div id="products-list">
        <div id="products-content">Cargando productos...</div>
    </div>

    <div id="products-form-wrapper" class="card d-none mt-3">
        <div class="card-body">
            <h4 id="products-form-title" class="card-title mb-3"></h4>
            <form id="product-form" data-entity="product">
                <input type="hidden" name="product_id" id="product_id">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="product_name" name="nombre" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="product_price" class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" id="product_price" name="price" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="product_status" class="form-label">Estado</label>
                        <select class="form-select" id="product_status" name="status">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_category" class="form-label">Categoría (ID)</label>
                        <input type="number" class="form-control" id="product_category" name="category_id" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="product_image" class="form-label">URL Imagen</label>
                        <input type="text" class="form-control" id="product_image" name="image" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="product_order" class="form-label">Orden</label>
                    <input type="number" class="form-control" id="product_order" name="order" value="0">
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-entity="product"
                        data-action="cancel-form"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        data-entity="product"
                        data-action="submit-form"
                    >
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


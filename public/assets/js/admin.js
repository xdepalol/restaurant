// Admin Panel JavaScript
const API_BASE = '/restaurant/public/api';
let apiToken = '';

// Get API token from session
async function getApiToken() {
    if (apiToken) {
        return apiToken;
    }
    
    // Try to get token from session
    try {
        const response = await fetch(API_BASE + '/auth/token');
        const data = await response.json();
        if (data.success) {
            setApiToken(data.data.token);
            return apiToken;
        }
    } catch (error) {
        console.error('Error getting API token:', error);
    }
    
    return '';
}

function setApiToken(token) {
    apiToken = token;
    localStorage.setItem('api_token', token);
}

// Initialize token on load
document.addEventListener('DOMContentLoaded', async function() {
    await getApiToken();
});

// API Helper Functions (always return Promises)
async function apiCall(endpoint, method = 'GET', data = null) {
    // Ensure we have a token
    if (!apiToken) {
        await getApiToken();
    }

    const options = {
        method,
        headers: {
            'Content-Type': 'application/json'
        }
    };

    if (apiToken) {
        options.headers.Authorization = 'Bearer ' + apiToken;
    }

    if (data) {
        options.body = JSON.stringify(data);
    }

    const response = await fetch(API_BASE + endpoint, options);
    const json = await response.json().catch(() => ({}));

    if (!response.ok) {
        const message = json.message || `Error HTTP ${response.status}`;
        return Promise.reject(new Error(message));
    }

    return json;
}

// Generic table elements
function createCell(content, className = '') {
    const td = document.createElement('td');

    // Permet text, números o nodes DOM
    if (content instanceof Node) {
        td.appendChild(content);
    } else {
        td.textContent = content ?? '';
    }

    if (className) {
        td.className = className;
    }

    return td;
}

// actions: [{ label, className, action }]
function createActionsCell(actions, entityId) {
  const td = document.createElement('td');

  actions.forEach(({ label, className, action }) => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = className;
    btn.textContent = label;

    // data-* per delegació
    btn.dataset.action = action;
    btn.dataset.id = String(entityId);

    td.appendChild(btn);
  });

  return td;
}

// Users Management
async function loadUsers() {
    const container = document.getElementById('users-content');
    if (!container) return;

    container.textContent = 'Cargando usuarios...';

    try {
        const data = await apiCall('/user/');
        if (data.success) {
            renderUsers(data.data);
        } else {
            container.textContent = data.message || 'Error al cargar usuarios';
            container.classList.add('text-danger');
        }
    } catch (err) {
        container.textContent = err.message || 'Error al cargar usuarios';
        container.classList.add('text-danger');
    }
}

function renderUsers(users) {
  const container = document.getElementById('users-content');
  container.innerHTML = '';

  const table = document.createElement('table');
  table.className = 'table table-striped';
  table.id = 'users-table'; // útil per delegació

  // THEAD
  const thead = document.createElement('thead');
  const headerRow = document.createElement('tr');
  ['ID', 'Nombre', 'Email', 'Rol', 'Acciones'].forEach(text => {
    const th = document.createElement('th');
    th.textContent = text;
    headerRow.appendChild(th);
  });
  thead.appendChild(headerRow);
  table.appendChild(thead);

  // TBODY
  const tbody = document.createElement('tbody');

  users.forEach(user => {
    const tr = document.createElement('tr');

    tr.appendChild(createCell(user.user_id));
    tr.appendChild(createCell(user.name));
    tr.appendChild(createCell(user.email));
    tr.appendChild(createCell(user.role, 'text-uppercase'));

    tr.appendChild(
      createActionsCell(
        [
          { label: 'Editar',  className: 'btn btn-sm btn-info me-2', action: 'edit-user' },
          { label: 'Eliminar', className: 'btn btn-sm btn-danger',   action: 'delete-user' }
        ],
        user.user_id
      )
    );

    tbody.appendChild(tr);
  });

  table.appendChild(tbody);
  container.appendChild(table);

  // Delegació (evita duplicar listeners si tornes a renderitzar)
  if (!container.dataset.bound) {
    container.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-action][data-id]');
      if (!btn || !container.contains(btn)) return;

      const { action, id } = btn.dataset;
      const userId = Number(id);

      switch (action) {
        case 'edit-user':
          editUser(userId);
          break;
        case 'delete-user':
          deleteUser(userId);
          break;
      }
    });

    container.dataset.bound = '1';
  }
}

// Orders Management
async function loadOrders(filters = {}) {
    const container = document.getElementById('orders-content');
    if (!container) return;

    container.textContent = 'Cargando pedidos...';

    let endpoint = '/purchase_order/';
    if (Object.keys(filters).length > 0) {
        const params = new URLSearchParams(filters);
        endpoint += '?' + params.toString();
    }

    try {
        const data = await apiCall(endpoint);
        if (data.success) {
            renderOrders(data.data);
        } else {
            container.textContent = data.message || 'Error al cargar pedidos';
            container.classList.add('text-danger');
        }
    } catch (err) {
        container.textContent = err.message || 'Error al cargar pedidos';
        container.classList.add('text-danger');
    }
}

function renderOrders(orders) {
    const container = document.getElementById('orders-content');
    if (!container) return;

    container.innerHTML = '';

    const table = document.createElement('table');
    table.className = 'table table-striped';
    table.id = 'orders-table';

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    ['ID', 'Cliente', 'Fecha', 'Total', 'Acciones'].forEach(text => {
        const th = document.createElement('th');
        th.textContent = text;
        headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    orders.forEach(order => {
        const tr = document.createElement('tr');

        tr.appendChild(createCell('#' + order.order_id));
        tr.appendChild(createCell(order.client_name));
        tr.appendChild(createCell(order.order_date));
        tr.appendChild(createCell(order.total_amount.toFixed(2)));

        tr.appendChild(
            createActionsCell(
                [{ label: 'Ver', className: 'btn btn-sm btn-info', action: 'view-order' }],
                order.order_id
            )
        );

        tbody.appendChild(tr);
    });

    table.appendChild(tbody);
    container.appendChild(table);

    if (!container.dataset.bound) {
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-action][data-id]');
            if (!btn || !container.contains(btn)) return;

            const { action, id } = btn.dataset;
            const orderId = Number(id);

            if (action === 'view-order') {
                viewOrder(orderId);
            }
        });

        container.dataset.bound = '1';
    }
}

// Promotions Management
async function loadPromotions() {
    const container = document.getElementById('promotions-content');
    if (!container) return;

    container.textContent = 'Cargando promociones...';

    try {
        const data = await apiCall('/promotion/?include_inactive=1');
        if (data.success) {
            renderPromotions(data.data);
        } else {
            container.textContent = data.message || 'Error al cargar promociones';
            container.classList.add('text-danger');
        }
    } catch (err) {
        container.textContent = err.message || 'Error al cargar promociones';
        container.classList.add('text-danger');
    }
}

function renderPromotions(promotions) {
    const container = document.getElementById('promotions-content');
    if (!container) return;

    container.innerHTML = '';

    const table = document.createElement('table');
    table.className = 'table table-striped';
    table.id = 'promotions-table';

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    ['ID', 'Código', 'Descuento', 'Estado', 'Acciones'].forEach(text => {
        const th = document.createElement('th');
        th.textContent = text;
        headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    promotions.forEach(promo => {
        const tr = document.createElement('tr');

        tr.appendChild(createCell(promo.promotion_id));
        tr.appendChild(createCell(promo.promo_code));
        tr.appendChild(createCell(promo.discount + '%'));
        tr.appendChild(createCell(promo.status ? 'Activo' : 'Inactivo'));

        tr.appendChild(
            createActionsCell(
                [
                    { label: 'Editar', className: 'btn btn-sm btn-info me-2', action: 'edit-promotion' },
                    { label: 'Eliminar', className: 'btn btn-sm btn-danger', action: 'delete-promotion' }
                ],
                promo.promotion_id
            )
        );

        tbody.appendChild(tr);
    });

    table.appendChild(tbody);
    container.appendChild(table);

    if (!container.dataset.bound) {
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-action][data-id]');
            if (!btn || !container.contains(btn)) return;

            const { action, id } = btn.dataset;
            const promotionId = Number(id);

            switch (action) {
                case 'edit-promotion':
                    editPromotion(promotionId);
                    break;
                case 'delete-promotion':
                    deletePromotion(promotionId);
                    break;
            }
        });

        container.dataset.bound = '1';
    }
}

// Categories Management
async function loadCategories() {
    const container = document.getElementById('categories-content');
    if (!container) return;

    container.textContent = 'Cargando categorías...';

    try {
        const data = await apiCall('/category/?include_inactive=1');
        if (data.success) {
            renderCategories(data.data);
        } else {
            container.textContent = data.message || 'Error al cargar categorías';
            container.classList.add('text-danger');
        }
    } catch (err) {
        container.textContent = err.message || 'Error al cargar categorías';
        container.classList.add('text-danger');
    }
}

function renderCategories(categories) {
    const container = document.getElementById('categories-content');
    if (!container) return;

    container.innerHTML = '';

    const table = document.createElement('table');
    table.className = 'table table-striped';
    table.id = 'categories-table';

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    ['ID', 'Nombre', 'Orden', 'Estado', 'Acciones'].forEach(text => {
        const th = document.createElement('th');
        th.textContent = text;
        headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    categories.forEach(cat => {
        const tr = document.createElement('tr');

        tr.appendChild(createCell(cat.category_id));
        tr.appendChild(createCell(cat.nombre));
        tr.appendChild(createCell(cat.order));
        tr.appendChild(createCell(cat.status ? 'Activo' : 'Inactivo'));

        tr.appendChild(
            createActionsCell(
                [
                    { label: 'Editar', className: 'btn btn-sm btn-info me-2', action: 'edit-category' },
                    { label: 'Eliminar', className: 'btn btn-sm btn-danger', action: 'delete-category' }
                ],
                cat.category_id
            )
        );

        tbody.appendChild(tr);
    });

    table.appendChild(tbody);
    container.appendChild(table);

    if (!container.dataset.bound) {
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-action][data-id]');
            if (!btn || !container.contains(btn)) return;

            const { action, id } = btn.dataset;
            const categoryId = Number(id);

            switch (action) {
                case 'edit-category':
                    editCategory(categoryId);
                    break;
                case 'delete-category':
                    deleteCategory(categoryId);
                    break;
            }
        });

        container.dataset.bound = '1';
    }
}

// Products Management
async function loadProducts() {
    const container = document.getElementById('products-content');
    if (!container) return;

    container.textContent = 'Cargando productos...';

    try {
        const data = await apiCall('/product/?include_inactive=1');
        if (data.success) {
            renderProducts(data.data);
        } else {
            container.textContent = data.message || 'Error al cargar productos';
            container.classList.add('text-danger');
        }
    } catch (err) {
        container.textContent = err.message || 'Error al cargar productos';
        container.classList.add('text-danger');
    }
}

function renderProducts(products) {
    const container = document.getElementById('products-content');
    if (!container) return;

    container.innerHTML = '';

    const table = document.createElement('table');
    table.className = 'table table-striped';
    table.id = 'products-table';

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    ['ID', 'Nombre', 'Categoría', 'Precio', 'Acciones'].forEach(text => {
        const th = document.createElement('th');
        th.textContent = text;
        headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    products.forEach(product => {
        const tr = document.createElement('tr');

        tr.appendChild(createCell(product.product_id));
        tr.appendChild(createCell(product.nombre));
        tr.appendChild(createCell(product.category_name || ''));
        tr.appendChild(createCell(product.price.toFixed(2)));

        tr.appendChild(
            createActionsCell(
                [
                    { label: 'Editar', className: 'btn btn-sm btn-info me-2', action: 'edit-product' },
                    { label: 'Eliminar', className: 'btn btn-sm btn-danger', action: 'delete-product' }
                ],
                product.product_id
            )
        );

        tbody.appendChild(tr);
    });

    table.appendChild(tbody);
    container.appendChild(table);

    if (!container.dataset.bound) {
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-action][data-id]');
            if (!btn || !container.contains(btn)) return;

            const { action, id } = btn.dataset;
            const productId = Number(id);

            switch (action) {
                case 'edit-product':
                    editProduct(productId);
                    break;
                case 'delete-product':
                    deleteProduct(productId);
                    break;
            }
        });

        container.dataset.bound = '1';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data for active tab
    const activeTab = document.querySelector('.nav-link.active');
    if (activeTab) {
        const targetId = activeTab.dataset.bsTarget.replace('#', '');
        switch(targetId) {
            case 'users':
                loadUsers();
                break;
            case 'orders':
                loadOrders();
                break;
            case 'promotions':
                loadPromotions();
                break;
            case 'categories':
                loadCategories();
                break;
            case 'products':
                loadProducts();
                break;
        }
    }
    
    // Tab change event
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            const targetId = e.target.dataset.bsTarget.replace('#', '');
            switch(targetId) {
                case 'users':
                    loadUsers();
                    break;
                case 'orders':
                    loadOrders();
                    break;
                case 'promotions':
                    loadPromotions();
                    break;
                case 'categories':
                    loadCategories();
                    break;
                case 'products':
                    loadProducts();
                    break;
            }
        });
    });
    
    // Apply filters for orders
    document.getElementById('apply-filters')?.addEventListener('click', function() {
        const filters = {};
        const clientFilter = document.getElementById('filter-client').value;
        const dateFrom = document.getElementById('filter-date-from').value;
        const dateTo = document.getElementById('filter-date-to').value;
        
        if (clientFilter) filters.client_id = clientFilter;
        if (dateFrom) filters.date_from = dateFrom;
        if (dateTo) filters.date_to = dateTo;
        
        loadOrders(filters);
    });

    // Botones "Nuevo" (delegación simple por id + data-*)
    document.getElementById('btn-new-user')?.addEventListener('click', () => openUserFormForCreate());
    document.getElementById('btn-new-promotion')?.addEventListener('click', () => openPromotionFormForCreate());
    document.getElementById('btn-new-category')?.addEventListener('click', () => openCategoryFormForCreate());
    document.getElementById('btn-new-product')?.addEventListener('click', () => openProductFormForCreate());

    // Delegación de envío/cancelación de formularios por data-entity/data-action
    document.body.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-entity][data-action]');
        if (!btn) return;

        const { entity, action } = btn.dataset;

        switch (entity) {
            case 'user':
                if (action === 'cancel-form') hideUserForm();
                break;
            case 'promotion':
                if (action === 'cancel-form') hidePromotionForm();
                break;
            case 'category':
                if (action === 'cancel-form') hideCategoryForm();
                break;
            case 'product':
                if (action === 'cancel-form') hideProductForm();
                break;
        }
    });

    // Submit handlers
    document.getElementById('user-form')?.addEventListener('submit', handleUserFormSubmit);
    document.getElementById('promotion-form')?.addEventListener('submit', handlePromotionFormSubmit);
    document.getElementById('category-form')?.addEventListener('submit', handleCategoryFormSubmit);
    document.getElementById('product-form')?.addEventListener('submit', handleProductFormSubmit);
});

// CRUD helpers - USERS
function toggleUserSections(showForm) {
    const list = document.getElementById('users-list');
    const form = document.getElementById('users-form-wrapper');
    if (!list || !form) return;
    if (showForm) {
        list.classList.add('d-none');
        form.classList.remove('d-none');
    } else {
        form.classList.add('d-none');
        list.classList.remove('d-none');
    }
}

function openUserFormForCreate() {
    const form = document.getElementById('user-form');
    if (!form) return;
    form.reset();
    form.dataset.mode = 'create';
    document.getElementById('user_id').value = '';
    const title = document.getElementById('users-form-title');
    if (title) title.textContent = 'Nuevo Usuario';
    toggleUserSections(true);
}

async function editUser(id) {
    try {
        const data = await apiCall('/user/' + id);
        if (!data.success) return;
        const user = data.data;
        const form = document.getElementById('user-form');
        if (!form) return;

        form.dataset.mode = 'edit';
        document.getElementById('user_id').value = user.user_id;
        document.getElementById('user_name').value = user.name || '';
        document.getElementById('user_email').value = user.email || '';
        document.getElementById('user_address').value = user.address || '';
        document.getElementById('user_phone').value = user.phone || '';
        document.getElementById('user_login').value = user.login || '';
        document.getElementById('user_role').value = user.role || 'client';
        document.getElementById('user_password').value = '';

        const title = document.getElementById('users-form-title');
        if (title) title.textContent = 'Editar Usuario #' + user.user_id;
        toggleUserSections(true);
    } catch (err) {
        alert(err.message || 'No se pudo cargar el usuario');
    }
}

function hideUserForm() {
    toggleUserSections(false);
}

async function handleUserFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const mode = form.dataset.mode || 'create';

    const payload = {
        name: form.name.value,
        email: form.email.value,
        address: form.address.value,
        phone: form.phone.value,
        login: form.login.value,
        role: form.role.value
    };

    if (form.password.value) {
        payload.password = form.password.value;
    }

    try {
        if (mode === 'create') {
            await apiCall('/user/', 'POST', payload);
        } else {
            const id = form.user_id.value;
            await apiCall('/user/' + id, 'PUT', payload);
        }
        hideUserForm();
        loadUsers();
    } catch (err) {
        alert(err.message || 'Error al guardar usuario');
    }
}

async function deleteUser(id) {
    if (!confirm('¿Eliminar usuario?')) return;
    try {
        const data = await apiCall('/user/' + id, 'DELETE');
        if (data.success) {
            loadUsers();
        } else {
            alert(data.message || 'No se pudo eliminar el usuario');
        }
    } catch (err) {
        alert(err.message || 'No se pudo eliminar el usuario');
    }
}

// CRUD helpers - PROMOTIONS
function togglePromotionSections(showForm) {
    const list = document.getElementById('promotions-list');
    const form = document.getElementById('promotions-form-wrapper');
    if (!list || !form) return;
    if (showForm) {
        list.classList.add('d-none');
        form.classList.remove('d-none');
    } else {
        form.classList.add('d-none');
        list.classList.remove('d-none');
    }
}

function openPromotionFormForCreate() {
    const form = document.getElementById('promotion-form');
    if (!form) return;
    form.reset();
    form.dataset.mode = 'create';
    document.getElementById('promotion_id').value = '';
    const title = document.getElementById('promotions-form-title');
    if (title) title.textContent = 'Nueva Promoción';
    togglePromotionSections(true);
}

async function editPromotion(id) {
    try {
        const data = await apiCall('/promotion/' + id);
        if (!data.success) return;
        const promo = data.data;
        const form = document.getElementById('promotion-form');
        if (!form) return;

        form.dataset.mode = 'edit';
        document.getElementById('promotion_id').value = promo.promotion_id;
        document.getElementById('promo_code').value = promo.promo_code || '';
        document.getElementById('promo_discount').value = promo.discount || 0;
        document.getElementById('promo_status').value = promo.status || 1;
        document.getElementById('promo_description').value = promo.description || '';

        // starts_at / ends_at -> datetime-local requires "YYYY-MM-DDTHH:MM"
        const starts = document.getElementById('promo_starts_at');
        const ends = document.getElementById('promo_ends_at');
        if (starts) {
            starts.value = promo.starts_at ? promo.starts_at.replace(' ', 'T').slice(0, 16) : '';
        }
        if (ends) {
            ends.value = promo.ends_at ? promo.ends_at.replace(' ', 'T').slice(0, 16) : '';
        }

        const title = document.getElementById('promotions-form-title');
        if (title) title.textContent = 'Editar Promoción #' + promo.promotion_id;
        togglePromotionSections(true);
    } catch (err) {
        alert(err.message || 'No se pudo cargar la promoción');
    }
}

function hidePromotionForm() {
    togglePromotionSections(false);
}

async function handlePromotionFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const mode = form.dataset.mode || 'create';

    const payload = {
        promo_code: form.promo_code.value,
        discount: parseFloat(form.discount.value),
        description: form.description.value,
        status: parseInt(form.status.value, 10)
    };

    if (form.starts_at.value) {
        payload.starts_at = form.starts_at.value.replace('T', ' ');
    }
    if (form.ends_at.value) {
        payload.ends_at = form.ends_at.value.replace('T', ' ');
    }

    try {
        if (mode === 'create') {
            await apiCall('/promotion/', 'POST', payload);
        } else {
            const id = form.promotion_id.value;
            await apiCall('/promotion/' + id, 'PUT', payload);
        }
        hidePromotionForm();
        loadPromotions();
    } catch (err) {
        alert(err.message || 'Error al guardar promoción');
    }
}

async function deletePromotion(id) {
    if (!confirm('¿Eliminar promoción?')) return;
    try {
        const data = await apiCall('/promotion/' + id, 'DELETE');
        if (data.success) {
            loadPromotions();
        } else {
            alert(data.message || 'No se pudo eliminar la promoción');
        }
    } catch (err) {
        alert(err.message || 'No se pudo eliminar la promoción');
    }
}

// CRUD helpers - CATEGORIES
function toggleCategorySections(showForm) {
    const list = document.getElementById('categories-list');
    const form = document.getElementById('categories-form-wrapper');
    if (!list || !form) return;
    if (showForm) {
        list.classList.add('d-none');
        form.classList.remove('d-none');
    } else {
        form.classList.add('d-none');
        list.classList.remove('d-none');
    }
}

function openCategoryFormForCreate() {
    const form = document.getElementById('category-form');
    if (!form) return;
    form.reset();
    form.dataset.mode = 'create';
    document.getElementById('category_id').value = '';
    const title = document.getElementById('categories-form-title');
    if (title) title.textContent = 'Nueva Categoría';
    toggleCategorySections(true);
}

async function editCategory(id) {
    try {
        const data = await apiCall('/category/' + id);
        if (!data.success) return;
        const cat = data.data;
        const form = document.getElementById('category-form');
        if (!form) return;

        form.dataset.mode = 'edit';
        document.getElementById('category_id').value = cat.category_id;
        document.getElementById('category_name').value = cat.nombre || '';
        document.getElementById('category_order').value = cat.order || 0;
        document.getElementById('category_status').value = cat.status || 1;
        document.getElementById('category_image').value = cat.image || '';

        const title = document.getElementById('categories-form-title');
        if (title) title.textContent = 'Editar Categoría #' + cat.category_id;
        toggleCategorySections(true);
    } catch (err) {
        alert(err.message || 'No se pudo cargar la categoría');
    }
}

function hideCategoryForm() {
    toggleCategorySections(false);
}

async function handleCategoryFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const mode = form.dataset.mode || 'create';

    const payload = {
        nombre: form.nombre.value,
        order: parseInt(form.order.value, 10) || 0,
        status: parseInt(form.status.value, 10) || 1,
        image: form.image.value || null
    };

    try {
        if (mode === 'create') {
            await apiCall('/category/', 'POST', payload);
        } else {
            const id = form.category_id.value;
            await apiCall('/category/' + id, 'PUT', payload);
        }
        hideCategoryForm();
        loadCategories();
    } catch (err) {
        alert(err.message || 'Error al guardar categoría');
    }
}

async function deleteCategory(id) {
    if (!confirm('¿Eliminar categoría?')) return;
    try {
        const data = await apiCall('/category/' + id, 'DELETE');
        if (data.success) {
            loadCategories();
        } else {
            alert(data.message || 'No se pudo eliminar la categoría');
        }
    } catch (err) {
        alert(err.message || 'No se pudo eliminar la categoría');
    }
}

// CRUD helpers - PRODUCTS
function toggleProductSections(showForm) {
    const list = document.getElementById('products-list');
    const form = document.getElementById('products-form-wrapper');
    if (!list || !form) return;
    if (showForm) {
        list.classList.add('d-none');
        form.classList.remove('d-none');
    } else {
        form.classList.add('d-none');
        list.classList.remove('d-none');
    }
}

function openProductFormForCreate() {
    const form = document.getElementById('product-form');
    if (!form) return;
    form.reset();
    form.dataset.mode = 'create';
    document.getElementById('product_id').value = '';
    const title = document.getElementById('products-form-title');
    if (title) title.textContent = 'Nuevo Producto';
    toggleProductSections(true);
}

async function editProduct(id) {
    try {
        const data = await apiCall('/product/' + id);
        if (!data.success) return;
        const p = data.data;
        const form = document.getElementById('product-form');
        if (!form) return;

        form.dataset.mode = 'edit';
        document.getElementById('product_id').value = p.product_id;
        document.getElementById('product_name').value = p.nombre || '';
        document.getElementById('product_price').value = p.price || 0;
        document.getElementById('product_status').value = p.status || 1;
        document.getElementById('product_category').value = p.category_id || '';
        document.getElementById('product_image').value = p.image || '';
        document.getElementById('product_order').value = p.order || 0;

        const title = document.getElementById('products-form-title');
        if (title) title.textContent = 'Editar Producto #' + p.product_id;
        toggleProductSections(true);
    } catch (err) {
        alert(err.message || 'No se pudo cargar el producto');
    }
}

function hideProductForm() {
    toggleProductSections(false);
}

async function handleProductFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const mode = form.dataset.mode || 'create';

    const payload = {
        nombre: form.nombre.value,
        price: parseFloat(form.price.value),
        status: parseInt(form.status.value, 10) || 1,
        category_id: parseInt(form.category_id.value, 10),
        image: form.image.value,
        order: parseInt(form.order.value, 10) || 0
    };

    try {
        if (mode === 'create') {
            await apiCall('/product/', 'POST', payload);
        } else {
            const id = form.product_id.value;
            await apiCall('/product/' + id, 'PUT', payload);
        }
        hideProductForm();
        loadProducts();
    } catch (err) {
        alert(err.message || 'Error al guardar producto');
    }
}

async function deleteProduct(id) {
    if (!confirm('¿Eliminar producto?')) return;
    try {
        const data = await apiCall('/product/' + id, 'DELETE');
        if (data.success) {
            loadProducts();
        } else {
            alert(data.message || 'No se pudo eliminar el producto');
        }
    } catch (err) {
        alert(err.message || 'No se pudo eliminar el producto');
    }
}

// View order details using Promise-based API + simple alert (could be modal)
async function viewOrder(id) {
    try {
        const data = await apiCall('/purchase_order/' + id);
        if (data.success) {
            alert('Detalles del pedido:\n' + JSON.stringify(data.data, null, 2));
        } else {
            alert(data.message || 'No se pudo cargar el pedido');
        }
    } catch (err) {
        alert(err.message || 'No se pudo cargar el pedido');
    }
}


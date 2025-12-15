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

// API Helper Functions
async function apiCall(endpoint, method = 'GET', data = null) {
    // Ensure we have a token
    if (!apiToken) {
        await getApiToken();
    }
    
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (apiToken) {
        options.headers['Authorization'] = 'Bearer ' + apiToken;
    }
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    const response = await fetch(API_BASE + endpoint, options);
    return await response.json();
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
    const data = await apiCall('/user/');
    if (data.success) {
        renderUsers(data.data);
    } else {
        document.getElementById('users-content').innerHTML = '<p class="text-danger">Error al cargar usuarios</p>';
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
    let endpoint = '/purchase_order/';
    if (Object.keys(filters).length > 0) {
        const params = new URLSearchParams(filters);
        endpoint += '?' + params.toString();
    }
    
    const data = await apiCall(endpoint);
    if (data.success) {
        renderOrders(data.data);
    } else {
        document.getElementById('orders-content').innerHTML = '<p class="text-danger">Error al cargar pedidos</p>';
    }
}

function renderOrders(orders) {
    let html = '<table class="table table-striped"><thead><tr><th>ID</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Acciones</th></tr></thead><tbody>';
    
    orders.forEach(order => {
        html += `
            <tr>
                <td>#${order.order_id}</td>
                <td>${order.client_name}</td>
                <td>${order.order_date}</td>
                <td>$${order.total_amount.toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewOrder(${order.order_id})">Ver</button>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    document.getElementById('orders-content').innerHTML = html;
}

// Promotions Management
async function loadPromotions() {
    const data = await apiCall('/promotion/?include_inactive=1');
    if (data.success) {
        renderPromotions(data.data);
    }
}

function renderPromotions(promotions) {
    let html = '<table class="table table-striped"><thead><tr><th>ID</th><th>Código</th><th>Descuento</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>';
    
    promotions.forEach(promo => {
        html += `
            <tr>
                <td>${promo.promotion_id}</td>
                <td>${promo.promo_code}</td>
                <td>${promo.discount}%</td>
                <td>${promo.status ? 'Activo' : 'Inactivo'}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editPromotion(${promo.promotion_id})">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="deletePromotion(${promo.promotion_id})">Eliminar</button>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    document.getElementById('promotions-content').innerHTML = html;
}

// Categories Management
async function loadCategories() {
    const data = await apiCall('/category/?include_inactive=1');
    if (data.success) {
        renderCategories(data.data);
    }
}

function renderCategories(categories) {
    let html = '<table class="table table-striped"><thead><tr><th>ID</th><th>Nombre</th><th>Orden</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>';
    
    categories.forEach(cat => {
        html += `
            <tr>
                <td>${cat.category_id}</td>
                <td>${cat.nombre}</td>
                <td>${cat.order}</td>
                <td>${cat.status ? 'Activo' : 'Inactivo'}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editCategory(${cat.category_id})">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCategory(${cat.category_id})">Eliminar</button>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    document.getElementById('categories-content').innerHTML = html;
}

// Products Management
async function loadProducts() {
    const data = await apiCall('/product/?include_inactive=1');
    if (data.success) {
        renderProducts(data.data);
    }
}

function renderProducts(products) {
    let html = '<table class="table table-striped"><thead><tr><th>ID</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Acciones</th></tr></thead><tbody>';
    
    products.forEach(product => {
        html += `
            <tr>
                <td>${product.product_id}</td>
                <td>${product.nombre}</td>
                <td>${product.category_name || ''}</td>
                <td>$${product.price.toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editProduct(${product.product_id})">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.product_id})">Eliminar</button>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    document.getElementById('products-content').innerHTML = html;
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
});

// CRUD functions (simplified - would need modals for full implementation)
function editUser(id) {
    alert('Edit user ' + id + ' - Modal implementation needed');
}

function deleteUser(id) {
    if (confirm('¿Eliminar usuario?')) {
        apiCall('/user/' + id, 'DELETE').then(data => {
            if (data.success) {
                loadUsers();
            }
        });
    }
}

function editPromotion(id) {
    alert('Edit promotion ' + id + ' - Modal implementation needed');
}

function deletePromotion(id) {
    if (confirm('¿Eliminar promoción?')) {
        apiCall('/promotion/' + id, 'DELETE').then(data => {
            if (data.success) {
                loadPromotions();
            }
        });
    }
}

function editCategory(id) {
    alert('Edit category ' + id + ' - Modal implementation needed');
}

function deleteCategory(id) {
    if (confirm('¿Eliminar categoría?')) {
        apiCall('/category/' + id, 'DELETE').then(data => {
            if (data.success) {
                loadCategories();
            }
        });
    }
}

function editProduct(id) {
    alert('Edit product ' + id + ' - Modal implementation needed');
}

function deleteProduct(id) {
    if (confirm('¿Eliminar producto?')) {
        apiCall('/product/' + id, 'DELETE').then(data => {
            if (data.success) {
                loadProducts();
            }
        });
    }
}

function viewOrder(id) {
    apiCall('/purchase_order/' + id).then(data => {
        if (data.success) {
            alert('Order details:\n' + JSON.stringify(data.data, null, 2));
        }
    });
}


# JavaScript Architecture

Este proyecto utiliza **ES6 Modules** para organizar el código JavaScript de manera profesional y modular.

## Estructura de Archivos

```
assets/js/
├── utils/
│   ├── api.js              # Funciones de API (promesas)
│   └── dom-helpers.js      # Helpers para crear elementos DOM
├── shopping/
│   └── cart.js            # Gestión del carrito de compras
├── admin.js                # Panel de administración
├── main.js                 # Páginas públicas
└── app.js                  # Entry point (placeholder)
```

## Módulos

### `utils/api.js`
Funciones para realizar llamadas a la API usando Promesas:
- `apiCall(endpoint, method, data, requireAuth)` - Llamada genérica a la API
- `getApiToken()` - Obtener token de autenticación
- `setApiToken(token)` - Establecer token
- `verifyCart(items)` - Verificar carrito de compras

### `utils/dom-helpers.js`
Funciones helper para crear elementos DOM:
- `createCell(content, className)` - Crear celda de tabla
- `createHeader(text, className)` - Crear encabezado de tabla
- `createActionsCell(actions, entityId)` - Crear celda con botones de acción
- `createTable(headers, className, id)` - Crear tabla completa
- `createButton(label, className, dataAttrs)` - Crear botón
- `createInput(type, name, value, options)` - Crear input
- `addRow(label, value, options)` - Crear fila con label y valor
- `formatMoney(amount, currency)` - Formatear dinero
- `createLoadingSpinner(text)` - Crear spinner de carga
- `createErrorMessage(message)` - Crear mensaje de error
- `createSuccessMessage(message)` - Crear mensaje de éxito

### `shopping/cart.js`
Clase `ShoppingCart` que gestiona el carrito:
- Almacenamiento en localStorage
- Verificación vía API (`/api/cart/verify`)
- Renderizado usando DOM (no HTML strings)
- Event delegation para acciones

### `admin.js`
Panel de administración:
- Carga de datos usando Promesas
- Renderizado con DOM helpers
- Event delegation con `data-*` attributes
- CRUD completo para todas las entidades

### `main.js`
Páginas públicas:
- Integración con carrito
- Validación de códigos promocionales
- Visualización de detalles de pedidos

## Características Principales

### 1. Promesas para todas las llamadas API
```javascript
try {
    const data = await apiCall('/user/');
    if (data.success) {
        renderUsers(data.data);
    }
} catch (err) {
    container.appendChild(createErrorMessage(err.message));
}
```

### 2. Renderizado con DOM (no HTML strings)
```javascript
// ❌ Antes (HTML strings)
container.innerHTML = '<table>...</table>';

// ✅ Ahora (DOM elements)
const { table, tbody } = createTable(['ID', 'Nombre'], 'table table-striped');
container.appendChild(table);
```

### 3. Event Delegation con data-* attributes
```javascript
// Botones con data-action y data-id
btn.dataset.action = 'edit-user';
btn.dataset.id = userId;

// Delegación en el contenedor
container.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-action][data-id]');
    if (!btn) return;
    
    const { action, id } = btn.dataset;
    // Manejar acción...
});
```

### 4. Verificación del Carrito
El carrito se verifica automáticamente vía API cuando el usuario visita la página:
- Verifica que los productos existan
- Verifica que estén disponibles
- Actualiza precios
- Elimina productos no disponibles

## Uso

### En HTML
```html
<script type="module" src="/restaurant/public/assets/js/main.js"></script>
```

### Importar módulos
```javascript
import { apiCall } from './utils/api.js';
import { createTable, formatMoney } from './utils/dom-helpers.js';
```

## Compatibilidad

Los módulos ES6 son compatibles con:
- Chrome 61+
- Firefox 60+
- Safari 10.1+
- Edge 16+

Para navegadores más antiguos, se recomienda usar un bundler como:
- Webpack
- Rollup
- Parcel
- Vite

## Mejores Prácticas

1. **Siempre usar Promesas** para llamadas asíncronas
2. **Nunca usar innerHTML** con strings HTML - usar DOM helpers
3. **Event delegation** en lugar de listeners individuales
4. **data-* attributes** para identificar elementos
5. **Módulos ES6** para organización del código

## Ejemplo Completo

```javascript
import { apiCall } from './utils/api.js';
import { createTable, createCell, createActionsCell, createLoadingSpinner } from './utils/dom-helpers.js';

async function loadUsers() {
    const container = document.getElementById('users-content');
    container.innerHTML = '';
    container.appendChild(createLoadingSpinner('Cargando...'));
    
    try {
        const data = await apiCall('/user/');
        if (data.success) {
            renderUsers(data.data);
        }
    } catch (err) {
        container.innerHTML = '';
        container.appendChild(createErrorMessage(err.message));
    }
}

function renderUsers(users) {
    const container = document.getElementById('users-content');
    container.innerHTML = '';
    
    const { table, tbody } = createTable(['ID', 'Nombre', 'Email', 'Acciones']);
    
    users.forEach(user => {
        const tr = document.createElement('tr');
        tr.appendChild(createCell(user.user_id));
        tr.appendChild(createCell(user.name));
        tr.appendChild(createCell(user.email));
        tr.appendChild(createActionsCell([
            { label: 'Editar', className: 'btn btn-sm btn-info', action: 'edit' },
            { label: 'Eliminar', className: 'btn btn-sm btn-danger', action: 'delete' }
        ], user.user_id));
        tbody.appendChild(tr);
    });
    
    container.appendChild(table);
}
```


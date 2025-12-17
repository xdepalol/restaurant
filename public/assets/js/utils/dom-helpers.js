/**
 * DOM Helper Functions
 * Utility functions for creating DOM elements
 */

/**
 * Creates a table cell (td) element
 * @param {string|number|Node} content - Content for the cell
 * @param {string} className - Optional CSS class
 * @returns {HTMLTableCellElement}
 */
export function createCell(content, className = '') {
    const td = document.createElement('td');

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

/**
 * Creates a table header cell (th) element
 * @param {string} text - Header text
 * @param {string} className - Optional CSS class
 * @returns {HTMLTableCellElement}
 */
export function createHeader(text, className = '') {
    const th = document.createElement('th');
    th.textContent = text;
    if (className) {
        th.className = className;
    }
    return th;
}

/**
 * Creates an actions cell with buttons
 * @param {Array} actions - Array of action objects: [{ label, className, action }]
 * @param {string|number} entityId - ID of the entity
 * @returns {HTMLTableCellElement}
 */
export function createActionsCell(actions, entityId) {
    const td = document.createElement('td');

    actions.forEach(({ label, className, action }) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = className;
        btn.textContent = label;

        // data-* for event delegation
        btn.dataset.action = action;
        btn.dataset.id = String(entityId);

        td.appendChild(btn);
    });

    return td;
}

/**
 * Creates a row with label and value
 * @param {string} label - Label text
 * @param {string|number} value - Value text
 * @param {Object} options - Options: { className }
 * @returns {HTMLParagraphElement}
 */
export function addRow(label, value, options = {}) {
    const p = document.createElement('p');
    p.className = options.className || 'mb-1';

    const strong = document.createElement('strong');
    strong.className = 'me-1';
    strong.textContent = label;

    p.appendChild(strong);
    p.appendChild(document.createTextNode(value ?? ''));

    return p;
}

/**
 * Formats money value
 * @param {number} amount - Amount to format
 * @param {string} currency - Currency symbol (default: '$')
 * @returns {string}
 */
export function formatMoney(amount, currency = '$') {
    return `${currency}${Number(amount).toFixed(2)}`;
}

/**
 * Creates a table element
 * @param {Array} headers - Array of header strings
 * @param {string} className - Optional CSS class
 * @param {string} id - Optional table ID
 * @returns {HTMLTableElement}
 */
export function createTable(headers, className = 'table table-striped', id = '') {
    const table = document.createElement('table');
    table.className = className;
    if (id) {
        table.id = id;
    }

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    
    headers.forEach(text => {
        headerRow.appendChild(createHeader(text));
    });
    
    thead.appendChild(headerRow);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    table.appendChild(tbody);

    return { table, tbody };
}

/**
 * Creates a button element
 * @param {string} label - Button label
 * @param {string} className - CSS classes
 * @param {Object} dataAttrs - Data attributes: { action: 'edit', id: 123 }
 * @returns {HTMLButtonElement}
 */
export function createButton(label, className = 'btn btn-primary', dataAttrs = {}) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = className;
    btn.textContent = label;

    Object.entries(dataAttrs).forEach(([key, value]) => {
        btn.dataset[key] = String(value);
    });

    return btn;
}

/**
 * Creates an input element
 * @param {string} type - Input type
 * @param {string} name - Input name
 * @param {string|number} value - Input value
 * @param {Object} options - Options: { className, placeholder, min, max, required }
 * @returns {HTMLInputElement}
 */
export function createInput(type, name, value = '', options = {}) {
    const input = document.createElement('input');
    input.type = type;
    input.name = name;
    input.value = value;

    if (options.className) {
        input.className = options.className;
    }
    if (options.placeholder) {
        input.placeholder = options.placeholder;
    }
    if (options.min !== undefined) {
        input.min = options.min;
    }
    if (options.max !== undefined) {
        input.max = options.max;
    }
    if (options.required) {
        input.required = true;
    }

    return input;
}

/**
 * Creates a loading spinner
 * @param {string} text - Loading text
 * @returns {HTMLDivElement}
 */
export function createLoadingSpinner(text = 'Cargando...') {
    const div = document.createElement('div');
    div.className = 'text-center py-4';
    div.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">${text}</span>
        </div>
        <p class="mt-2">${text}</p>
    `;
    return div;
}

/**
 * Creates an error message element
 * @param {string} message - Error message
 * @returns {HTMLDivElement}
 */
export function createErrorMessage(message) {
    const div = document.createElement('div');
    div.className = 'alert alert-danger';
    div.textContent = message;
    return div;
}

/**
 * Creates a success message element
 * @param {string} message - Success message
 * @returns {HTMLDivElement}
 */
export function createSuccessMessage(message) {
    const div = document.createElement('div');
    div.className = 'alert alert-success';
    div.textContent = message;
    return div;
}


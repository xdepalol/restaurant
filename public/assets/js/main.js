/**
 * Main JavaScript
 * Entry point for public-facing pages
 */

import { cart } from './shopping/cart.js';
import { apiCall } from './utils/api.js';
import { createLoadingSpinner, createErrorMessage, addRow, formatMoney } from './utils/dom-helpers.js';

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Render cart if on cart page
    if (document.getElementById('cart-content')) {
        cart.verifyAndRenderCart();
    }
    
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = parseInt(this.dataset.productId);
            const productName = this.dataset.productName;
            const productPrice = parseFloat(this.dataset.productPrice);
            
            cart.addItem(productId, productName, productPrice, 1);
        });
    });
    
    // Checkout form
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            document.getElementById('cart-data').value = JSON.stringify(cart.cart);
        });
        
        // Load order summary
        if (cart.cart.length > 0) {
            renderOrderSummary();
        }
    }
    
    // Validate promo code
    const validatePromoBtn = document.getElementById('validate-promo');
    if (validatePromoBtn) {
        validatePromoBtn.addEventListener('click', async function() {
            const promoCode = document.getElementById('promo_code').value;
            const messageEl = document.getElementById('promo-message');
            
            if (!promoCode) {
                messageEl.innerHTML = '';
                const errorSpan = document.createElement('span');
                errorSpan.className = 'text-danger';
                errorSpan.textContent = 'Ingresa un código';
                messageEl.appendChild(errorSpan);
                return;
            }
            
            try {
                const data = await apiCall('/promotion/validate', 'POST', { promo_code: promoCode }, false);
                
                messageEl.innerHTML = '';
                if (data.success) {
                    const successSpan = document.createElement('span');
                    successSpan.className = 'text-success';
                    successSpan.textContent = `Código válido: ${data.data.discount}% de descuento`;
                    messageEl.appendChild(successSpan);
                    renderOrderSummary(promoCode);
                } else {
                    const errorSpan = document.createElement('span');
                    errorSpan.className = 'text-danger';
                    errorSpan.textContent = data.message;
                    messageEl.appendChild(errorSpan);
                }
            } catch (error) {
                messageEl.innerHTML = '';
                const errorSpan = document.createElement('span');
                errorSpan.className = 'text-danger';
                errorSpan.textContent = 'Error al validar código';
                messageEl.appendChild(errorSpan);
            }
        });
    }
    
    // Order details modal
    setupOrderDetailsModal();
});

/**
 * Render order summary using DOM
 */
function renderOrderSummary(promoCode = '') {
    const summaryEl = document.getElementById('order-summary');
    if (!summaryEl) return;
    
    summaryEl.innerHTML = '';
    
    const listGroup = document.createElement('ul');
    listGroup.className = 'list-group mb-3';
    
    let subtotal = 0;
    
    cart.cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        
        const listItem = document.createElement('li');
        listItem.className = 'list-group-item d-flex justify-content-between';
        
        const nameSpan = document.createElement('span');
        nameSpan.textContent = `${item.product_name} x ${item.quantity}`;
        
        const priceSpan = document.createElement('span');
        priceSpan.textContent = formatMoney(itemTotal);
        
        listItem.appendChild(nameSpan);
        listItem.appendChild(priceSpan);
        listGroup.appendChild(listItem);
    });
    
    summaryEl.appendChild(listGroup);
    
    const subtotalDiv = document.createElement('div');
    subtotalDiv.className = 'd-flex justify-content-between mb-2';
    
    const subtotalLabel = document.createElement('strong');
    subtotalLabel.textContent = 'Subtotal:';
    
    const subtotalValue = document.createElement('strong');
    subtotalValue.textContent = formatMoney(subtotal);
    
    subtotalDiv.appendChild(subtotalLabel);
    subtotalDiv.appendChild(subtotalValue);
    summaryEl.appendChild(subtotalDiv);
    
    if (promoCode) {
        const promoDiv = document.createElement('div');
        promoDiv.className = 'text-success mb-2';
        
        const small = document.createElement('small');
        small.textContent = `Código aplicado: ${promoCode}`;
        promoDiv.appendChild(small);
        
        summaryEl.appendChild(promoDiv);
    }
}

/**
 * Setup order details modal with event delegation
 */
function setupOrderDetailsModal() {
    document.body.addEventListener('click', async (e) => {
        const btn = e.target.closest('button[data-action="view-order"][data-order-id]');
        if (!btn) return;
        
        const orderId = btn.dataset.orderId;
        
        try {
            const data = await apiCall(`/purchase_order/${orderId}`, 'GET', null, true);
            
            if (data.success) {
                renderOrderDetails(data.data);
            }
        } catch (error) {
            console.error('Error loading order:', error);
        }
    });
}

/**
 * Render order details using DOM
 */
function renderOrderDetails(order) {
    const content = document.getElementById('orderDetailsContent');
    if (!content) return;
    
    content.innerHTML = '';
    
    content.appendChild(addRow('ID:', `#${order.order_id}`));
    content.appendChild(addRow('Fecha:', order.order_date));
    content.appendChild(addRow('Total:', formatMoney(order.total_amount), {
        className: 'fw-semibold'
    }));
    
    const productsTitle = document.createElement('h6');
    productsTitle.className = 'mt-3';
    productsTitle.textContent = 'Productos:';
    content.appendChild(productsTitle);
    
    const productsList = document.createElement('ul');
    if (order.lines && order.lines.length > 0) {
        order.lines.forEach(line => {
            const li = document.createElement('li');
            li.textContent = `${line.product_name || 'Producto'} - ${formatMoney(line.price)} x ${line.quantity}`;
            productsList.appendChild(li);
        });
    }
    content.appendChild(productsList);
    
    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    modal.show();
}

// Make cart available globally for backward compatibility
window.cart = cart;

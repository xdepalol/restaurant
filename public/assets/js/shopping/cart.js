/**
 * Shopping Cart Management
 * Uses localStorage and verifies cart via API
 */

import { apiCall, verifyCart } from '../utils/api.js';
import { createTable, createCell, createButton, createInput, createLoadingSpinner, createErrorMessage, formatMoney } from '../utils/dom-helpers.js';

class ShoppingCart {
    constructor() {
        this.cart = this.loadCart();
        this.verifiedCart = null;
        this.updateCartCount();
    }
    
    loadCart() {
        const cart = localStorage.getItem('shoppingCart');
        return cart ? JSON.parse(cart) : [];
    }
    
    saveCart() {
        localStorage.setItem('shoppingCart', JSON.stringify(this.cart));
        this.updateCartCount();
    }
    
    addItem(productId, productName, productPrice, quantity = 1) {
        const existingItem = this.cart.find(item => item.product_id === productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.cart.push({
                product_id: productId,
                product_name: productName,
                price: productPrice,
                quantity: quantity
            });
        }
        
        this.saveCart();
        this.showCartNotification();
    }
    
    removeItem(productId) {
        this.cart = this.cart.filter(item => item.product_id !== productId);
        this.saveCart();
        if (document.getElementById('cart-content')) {
            this.verifyAndRenderCart();
        }
    }
    
    updateQuantity(productId, quantity) {
        const item = this.cart.find(item => item.product_id === productId);
        if (item) {
            if (quantity <= 0) {
                this.removeItem(productId);
            } else {
                item.quantity = quantity;
                this.saveCart();
                if (document.getElementById('cart-content')) {
                    this.verifyAndRenderCart();
                }
            }
        }
    }
    
    clearCart() {
        this.cart = [];
        this.verifiedCart = null;
        this.saveCart();
    }
    
    getTotal() {
        if (this.verifiedCart) {
            return this.verifiedCart.subtotal;
        }
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }
    
    updateCartCount() {
        const count = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        const cartCountEl = document.getElementById('cart-count');
        if (cartCountEl) {
            cartCountEl.textContent = count;
        }
    }
    
    /**
     * Verify cart via API and render
     */
    async verifyAndRenderCart() {
        const cartContent = document.getElementById('cart-content');
        if (!cartContent) return;
        
        // Show loading
        cartContent.innerHTML = '';
        cartContent.appendChild(createLoadingSpinner('Verificando carrito...'));
        
        try {
            const response = await verifyCart(this.cart.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity
            })));
            
            if (response.success) {
                this.verifiedCart = response.data;
                // Update localStorage with verified prices
                this.cart.forEach(item => {
                    const verified = response.data.items.find(v => v.product_id === item.product_id);
                    if (verified) {
                        item.price = verified.price;
                        item.product_name = verified.product_name;
                    }
                });
                this.saveCart();
                this.renderCart();
            } else {
                cartContent.innerHTML = '';
                cartContent.appendChild(createErrorMessage('Error al verificar el carrito'));
            }
        } catch (error) {
            console.error('Error verifying cart:', error);
            cartContent.innerHTML = '';
            cartContent.appendChild(createErrorMessage('Error al verificar el carrito. Por favor, intenta de nuevo.'));
        }
    }
    
    /**
     * Render cart using DOM elements
     */
    renderCart() {
        const cartContent = document.getElementById('cart-content');
        if (!cartContent) return;
        
        cartContent.innerHTML = '';
        
        if (!this.verifiedCart || this.verifiedCart.items.length === 0) {
            const emptyMsg = document.createElement('p');
            emptyMsg.className = 'text-muted';
            emptyMsg.textContent = 'Tu carrito está vacío.';
            cartContent.appendChild(emptyMsg);
            
            const checkoutBtn = document.getElementById('checkout-btn');
            if (checkoutBtn) {
                checkoutBtn.style.display = 'none';
            }
            return;
        }
        
        // Create table
        const { table, tbody } = createTable(
            ['Producto', 'Precio', 'Cantidad', 'Total', 'Acciones'],
            'table table-striped',
            'cart-table'
        );
        
        // Add rows
        this.verifiedCart.items.forEach(item => {
            const tr = document.createElement('tr');
            
            // Product name
            tr.appendChild(createCell(item.product_name));
            
            // Price
            tr.appendChild(createCell(formatMoney(item.price)));
            
            // Quantity input
            const quantityCell = document.createElement('td');
            const quantityInput = createInput('number', `quantity_${item.product_id}`, item.quantity, {
                className: 'form-control form-control-sm d-inline-block w-auto',
                min: 1
            });
            quantityInput.dataset.productId = item.product_id;
            quantityInput.addEventListener('change', (e) => {
                this.updateQuantity(item.product_id, parseInt(e.target.value));
            });
            quantityCell.appendChild(quantityInput);
            tr.appendChild(quantityCell);
            
            // Item total
            tr.appendChild(createCell(formatMoney(item.item_total)));
            
            // Actions
            const actionsCell = document.createElement('td');
            const removeBtn = createButton('Eliminar', 'btn btn-sm btn-danger', {
                action: 'remove-item',
                productId: item.product_id
            });
            actionsCell.appendChild(removeBtn);
            tr.appendChild(actionsCell);
            
            tbody.appendChild(tr);
        });
        
        // Add footer with total
        const tfoot = document.createElement('tfoot');
        const footerRow = document.createElement('tr');
        footerRow.appendChild(createCell('Total', '', 3)); // colspan will be set via style
        footerRow.children[0].colSpan = 3;
        footerRow.children[0].className = 'text-end fw-bold';
        footerRow.appendChild(createCell(formatMoney(this.verifiedCart.subtotal), 'fw-bold'));
        footerRow.appendChild(createCell(''));
        tfoot.appendChild(footerRow);
        table.appendChild(tfoot);
        
        cartContent.appendChild(table);
        
        // Show checkout button
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.style.display = 'block';
        }
        
        // Setup event delegation for remove buttons
        this.setupCartEventDelegation(cartContent);
    }
    
    /**
     * Setup event delegation for cart actions
     */
    setupCartEventDelegation(container) {
        if (container.dataset.bound) return;
        
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-action][data-product-id]');
            if (!btn || !container.contains(btn)) return;
            
            const { action, productId } = btn.dataset;
            
            if (action === 'remove-item') {
                this.removeItem(parseInt(productId));
            }
        });
        
        container.dataset.bound = '1';
    }
    
    showCartNotification() {
        const notification = document.createElement('div');
        notification.className = 'alert alert-success position-fixed top-0 end-0 m-3';
        notification.style.zIndex = '9999';
        notification.textContent = 'Producto agregado al carrito';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Export singleton instance
export const cart = new ShoppingCart();


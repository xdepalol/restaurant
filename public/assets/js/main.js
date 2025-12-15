// Shopping Cart Management using localStorage
class ShoppingCart {
    constructor() {
        this.cart = this.loadCart();
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
            this.renderCart();
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
                    this.renderCart();
                }
            }
        }
    }
    
    clearCart() {
        this.cart = [];
        this.saveCart();
    }
    
    getTotal() {
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }
    
    updateCartCount() {
        const count = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        const cartCountEl = document.getElementById('cart-count');
        if (cartCountEl) {
            cartCountEl.textContent = count;
        }
    }
    
    renderCart() {
        const cartContent = document.getElementById('cart-content');
        if (!cartContent) return;
        
        if (this.cart.length === 0) {
            cartContent.innerHTML = '<p>Tu carrito está vacío.</p>';
            document.getElementById('checkout-btn').style.display = 'none';
            return;
        }
        
        let html = '<table class="table"><thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Total</th><th>Acciones</th></tr></thead><tbody>';
        
        this.cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            html += `
                <tr>
                    <td>${item.product_name}</td>
                    <td>$${item.price.toFixed(2)}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm d-inline-block w-auto" 
                               value="${item.quantity}" min="1" 
                               onchange="cart.updateQuantity(${item.product_id}, parseInt(this.value))">
                    </td>
                    <td>$${itemTotal.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="cart.removeItem(${item.product_id})">Eliminar</button>
                    </td>
                </tr>
            `;
        });
        
        html += `</tbody><tfoot><tr><th colspan="3">Total</th><th>$${this.getTotal().toFixed(2)}</th><th></th></tr></tfoot></table>`;
        
        cartContent.innerHTML = html;
        document.getElementById('checkout-btn').style.display = 'block';
    }
    
    showCartNotification() {
        // Simple notification
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

// Initialize cart
const cart = new ShoppingCart();

// Add to cart buttons
document.addEventListener('DOMContentLoaded', function() {
    // Render cart if on cart page
    if (document.getElementById('cart-content')) {
        cart.renderCart();
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
        validatePromoBtn.addEventListener('click', function() {
            const promoCode = document.getElementById('promo_code').value;
            if (!promoCode) {
                document.getElementById('promo-message').innerHTML = '<span class="text-danger">Ingresa un código</span>';
                return;
            }
            
            fetch('/restaurant/public/api/promotion/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ promo_code: promoCode })
            })
            .then(response => response.json())
            .then(data => {
                const messageEl = document.getElementById('promo-message');
                if (data.success) {
                    messageEl.innerHTML = '<span class="text-success">Código válido: ' + data.data.discount + '% de descuento</span>';
                    renderOrderSummary(promoCode);
                } else {
                    messageEl.innerHTML = '<span class="text-danger">' + data.message + '</span>';
                }
            })
            .catch(error => {
                document.getElementById('promo-message').innerHTML = '<span class="text-danger">Error al validar código</span>';
            });
        });
    }
});

function renderOrderSummary(promoCode = '') {
    const summaryEl = document.getElementById('order-summary');
    if (!summaryEl) return;
    
    let html = '<ul class="list-group mb-3">';
    let subtotal = 0;
    
    cart.cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        html += `<li class="list-group-item d-flex justify-content-between">
            <span>${item.product_name} x ${item.quantity}</span>
            <span>$${itemTotal.toFixed(2)}</span>
        </li>`;
    });
    
    html += `</ul><div class="d-flex justify-content-between mb-2">
        <strong>Subtotal:</strong>
        <strong>$${subtotal.toFixed(2)}</strong>
    </div>`;
    
    if (promoCode) {
        // Calculate discount (would need to fetch promotion details)
        html += `<div class="text-success mb-2">
            <small>Código aplicado: ${promoCode}</small>
        </div>`;
    }
    
    summaryEl.innerHTML = html;
}

// Order details modal
document.addEventListener('DOMContentLoaded', function() {
    const viewOrderBtns = document.querySelectorAll('.view-order-details');
    viewOrderBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            // Fetch order details via API
            fetch(`/restaurant/public/api/purchase_order/${orderId}`, {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderOrderDetails(data.data);
                }
            });
        });
    });
});

function renderOrderDetails(order) {
    const content = document.getElementById('orderDetailsContent');
    let html = `<p><strong>ID:</strong> #${order.order_id}</p>`;
    html += `<p><strong>Fecha:</strong> ${order.order_date}</p>`;
    html += `<p><strong>Total:</strong> $${order.total_amount}</p>`;
    html += '<h6>Productos:</h6><ul>';
    order.lines.forEach(line => {
        html += `<li>${line.product_name} - $${line.price} x ${line.quantity}</li>`;
    });
    html += '</ul>';
    content.innerHTML = html;
    
    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    modal.show();
}



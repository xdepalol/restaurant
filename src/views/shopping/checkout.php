<?php $title = 'Checkout'; ?>
<h2>Checkout</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" id="checkout-form">
    <input type="hidden" name="cart" id="cart-data">
    
    <div class="row">
        <div class="col-md-6">
            <h4>Información de Entrega</h4>
            <div class="mb-3">
                <label for="client_name" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="client_name" name="client_name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="client_address" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="client_address" name="client_address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="client_phone" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="client_phone" name="client_phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <h4>Resumen del Pedido</h4>
            <div id="order-summary">
                <p>Cargando resumen...</p>
            </div>
            
            <div class="mb-3 mt-3">
                <label for="promo_code" class="form-label">Código de Promoción (opcional)</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="promo_code" name="promo_code">
                    <button type="button" class="btn btn-outline-secondary" id="validate-promo">Validar</button>
                </div>
                <div id="promo-message" class="mt-2"></div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Notas (opcional)</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg w-100">Confirmar Pedido</button>
        </div>
    </div>
</form>



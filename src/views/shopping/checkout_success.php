<?php $title = 'Pedido Confirmado'; ?>
<div class="alert alert-success">
    <h2>¡Pedido Confirmado!</h2>
    <p>Tu pedido #<?php echo $orderId; ?> ha sido creado exitosamente.</p>
    <p>Recibirás un email de confirmación pronto.</p>
</div>

<div class="card">
    <div class="card-header">
        <h5>Resumen del Pedido</h5>
    </div>
    <div class="card-body">
        <p><strong>ID del Pedido:</strong> #<?php echo $order['order_id']; ?></p>
        <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
        <p><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
    </div>
</div>

<a href="/restaurant/public/account/orders" class="btn btn-primary mt-3">Ver Mis Pedidos</a>
<a href="/restaurant/public/products" class="btn btn-secondary mt-3">Seguir Comprando</a>


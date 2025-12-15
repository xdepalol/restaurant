<?php $title = 'Mis Pedidos'; ?>
<h2>Mis Pedidos</h2>

<?php if (empty($orders)): ?>
    <p>No tienes pedidos aún.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['order_id']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>Completado</td>
                        <td>
                            <button class="btn btn-sm btn-info view-order-details" data-order-id="<?php echo $order['order_id']; ?>">Ver Detalles</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Content loaded via JavaScript -->
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>



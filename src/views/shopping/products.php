<?php $title = 'Productos'; ?>
<h2>Productos</h2>

<div class="row">
    <div class="col-md-3">
        <h4>Filtrar por Categoría</h4>
        <ul class="list-group">
            <li class="list-group-item">
                <a href="/restaurant/public/products">Todas las categorías</a>
            </li>
            <?php foreach ($categories as $category): ?>
                <li class="list-group-item <?php echo ($selectedCategory == $category['category_id']) ? 'active' : ''; ?>">
                    <a href="/restaurant/public/products?category=<?php echo $category['category_id']; ?>">
                        <?php echo htmlspecialchars($category['nombre']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-md-9">
        <div class="row">
            <?php if (empty($products)): ?>
                <p>No hay productos disponibles en esta categoría.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['nombre']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['nombre']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></p>
                                <p class="card-text"><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                                <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $product['product_id']; ?>" data-product-name="<?php echo htmlspecialchars($product['nombre']); ?>" data-product-price="<?php echo $product['price']; ?>">
                                    Agregar al Carrito
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>


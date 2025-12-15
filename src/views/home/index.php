<?php $title = 'Inicio'; ?>
<div class="hero-section bg-primary text-white text-center py-5 mb-5 rounded">
    <h1 class="display-4">Bienvenido a <?php echo APP_NAME; ?></h1>
    <p class="lead">Deliciosa comida a tu alcance</p>
    <a href="/restaurant/public/products" class="btn btn-light btn-lg">Ver Productos</a>
</div>

<div class="row mb-5">
    <div class="col-12">
        <h2 class="mb-4">Categorías</h2>
        <div class="row">
            <?php foreach ($categories as $category): ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <?php if ($category['image']): ?>
                            <img src="<?php echo htmlspecialchars($category['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($category['nombre']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($category['nombre']); ?></h5>
                            <a href="/restaurant/public/products?category=<?php echo $category['category_id']; ?>" class="btn btn-primary">Ver Productos</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Productos Destacados</h2>
        <div class="row">
            <?php foreach ($featuredProducts as $product): ?>
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
        </div>
    </div>
</div>



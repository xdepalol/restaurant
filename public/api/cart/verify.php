<?php
/**
 * @api {post} /api/cart/verify Verificar carrito de compras
 * @apiName VerifyCart
 * @apiGroup Cart
 * @apiParam {Array} items Array de items del carrito con product_id y quantity
 * @apiSuccess {Boolean} success Estado de la operación
 * @apiSuccess {Array} data.items Items verificados con información actualizada
 * @apiSuccess {Number} data.subtotal Subtotal calculado
 * @apiError (400) BadRequest Carrito inválido o productos no disponibles
 */
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/models/ProductModel.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['items']) || !is_array($data['items'])) {
    Response::error('Invalid cart data', 400);
}

$productModel = new ProductModel();
$verifiedItems = [];
$subtotal = 0;
$errors = [];

foreach ($data['items'] as $item) {
    if (!isset($item['product_id']) || !isset($item['quantity'])) {
        $errors[] = 'Invalid item format';
        continue;
    }
    
    $product = $productModel->getById($item['product_id']);
    
    if (!$product) {
        $errors[] = "Product {$item['product_id']} not found";
        continue;
    }
    
    if ($product['status'] != 1) {
        $errors[] = "Product {$product['nombre']} is not available";
        continue;
    }
    
    $quantity = max(1, (int)$item['quantity']);
    $price = (float)$product['price'];
    $itemTotal = $price * $quantity;
    $subtotal += $itemTotal;
    
    $verifiedItems[] = [
        'product_id' => $product['product_id'],
        'product_name' => $product['nombre'],
        'price' => $price,
        'quantity' => $quantity,
        'item_total' => $itemTotal,
        'image' => $product['image'] ?? null
    ];
}

if (!empty($errors)) {
    Response::error('Cart verification failed', 400, ['errors' => $errors, 'verified_items' => $verifiedItems]);
}

Response::success([
    'items' => $verifiedItems,
    'subtotal' => $subtotal
], 'Cart verified successfully');


<?php
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/PurchaseOrderModel.php';
require_once BASE_PATH . '/src/models/PromotionModel.php';
require_once BASE_PATH . '/src/models/ProductModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$model = new PurchaseOrderModel();

switch ($method) {
    case 'GET':
        /**
         * @api {get} /api/purchase_order/ Listar órdenes de compra
         * @apiName GetPurchaseOrders
         * @apiGroup PurchaseOrder
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} [client_id] Filtrar por cliente
         * @apiParam {Number} [promotion_id] Filtrar por promoción
         * @apiParam {String} [date_from] Filtrar desde fecha (YYYY-MM-DD)
         * @apiParam {String} [date_to] Filtrar hasta fecha (YYYY-MM-DD)
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Array} data Lista de órdenes
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         */
        $authUser = AuthMiddleware::validateToken();
        
        $filters = [];
        
        // Non-admins can only see their own orders
        if ($authUser['role'] !== 'admin') {
            $filters['client_id'] = $authUser['user_id'];
        } else {
            if (isset($_GET['client_id'])) {
                $filters['client_id'] = $_GET['client_id'];
            }
            if (isset($_GET['promotion_id'])) {
                $filters['promotion_id'] = $_GET['promotion_id'];
            }
            if (isset($_GET['date_from'])) {
                $filters['date_from'] = $_GET['date_from'];
            }
            if (isset($_GET['date_to'])) {
                $filters['date_to'] = $_GET['date_to'];
            }
        }
        
        $orders = $model->getAll($filters);
        
        // Include order lines for each order
        foreach ($orders as &$order) {
            $order['lines'] = $model->getOrderLines($order['order_id']);
        }
        
        Response::success($orders, 'Purchase orders retrieved successfully');
        break;
        
    case 'POST':
        /**
         * @api {post} /api/purchase_order/ Crear orden de compra
         * @apiName CreatePurchaseOrder
         * @apiGroup PurchaseOrder
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} client_id ID del cliente
         * @apiParam {String} client_name Nombre del cliente
         * @apiParam {String} client_address Dirección del cliente
         * @apiParam {String} client_phone Teléfono del cliente
         * @apiParam {String} [promo_code] Código de promoción
         * @apiParam {Array} lines Líneas de la orden
         * @apiParam {String} [notes] Notas
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Number} data ID de la orden creada
         * @apiError (400) BadRequest Datos inválidos
         * @apiError (401) Unauthorized Token no válido
         */
        $authUser = AuthMiddleware::validateToken();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $rules = [
            'client_id' => 'required|integer',
            'client_name' => 'required|min:2|max:255',
            'client_address' => 'required|min:5|max:255',
            'client_phone' => 'required|min:5|max:50',
            'lines' => 'required'
        ];
        
        $errors = Validator::validate($data, $rules);
        if (Validator::hasErrors($errors)) {
            Response::error('Validation failed', 400, $errors);
        }
        
        // Validate that client_id matches authenticated user (unless admin)
        if ($authUser['role'] !== 'admin' && $authUser['user_id'] != $data['client_id']) {
            Response::forbidden('You can only create orders for yourself');
        }
        
        // Validate products and calculate totals
        $productModel = new ProductModel();
        $subtotal = 0;
        $validatedLines = [];
        
        foreach ($data['lines'] as $line) {
            $product = $productModel->getById($line['product_id']);
            if (!$product || $product['status'] != 1) {
                Response::error("Product {$line['product_id']} not found or inactive", 400);
            }
            
            $quantity = (int)($line['quantity'] ?? 1);
            $price = (float)$product['price'];
            $lineTotal = $price * $quantity;
            $subtotal += $lineTotal;
            
            $validatedLines[] = [
                'product_id' => $product['product_id'],
                'price' => $price,
                'quantity' => $quantity
            ];
        }
        
        // Validate and apply promotion if provided
        $discountPercent = 0;
        $promotionId = null;
        
        if (!empty($data['promo_code'])) {
            $promotionModel = new PromotionModel();
            $promoResult = $promotionModel->validatePromoCode($data['promo_code']);
            
            if ($promoResult['valid']) {
                $promotion = $promoResult['promotion'];
                $discountPercent = (float)$promotion['discount'];
                $promotionId = $promotion['promotion_id'];
            } else {
                Response::error($promoResult['message'], 400);
            }
        }
        
        $discountAmount = $subtotal * ($discountPercent / 100);
        $totalAmount = $subtotal - $discountAmount;
        
        $orderData = [
            'client_id' => $data['client_id'],
            'client_name' => $data['client_name'],
            'client_address' => $data['client_address'],
            'client_phone' => $data['client_phone'],
            'promo_code' => $data['promo_code'] ?? null,
            'promotion_id' => $promotionId,
            'order_date' => date('Y-m-d H:i:s'),
            'subtotal' => $subtotal,
            'discount_percent' => $discountPercent,
            'total_amount' => $totalAmount,
            'notes' => $data['notes'] ?? null
        ];
        
        try {
            $orderId = $model->create($orderData, $validatedLines);
            Response::success(['order_id' => $orderId], 'Purchase order created successfully', 201);
        } catch (Exception $e) {
            Response::error('Failed to create order: ' . $e->getMessage(), 500);
        }
        break;
        
    default:
        Response::error('Method not allowed', 405);
}



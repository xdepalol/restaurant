<?php
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/PurchaseOrderModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$model = new PurchaseOrderModel();

// Extract ID from path or router variable
$id = $_SERVER['API_ENTITY_ID'] ?? $_GET['id'] ?? null;
if (!$id) {
    $path = $_SERVER['REQUEST_URI'];
    preg_match('/\/api\/purchase_order\/(\d+)/', $path, $matches);
    $id = $matches[1] ?? null;
}

if (!$id) {
    Response::error('Invalid order ID', 400);
}

switch ($method) {
    case 'GET':
        /**
         * @api {get} /api/purchase_order/:id Obtener orden de compra
         * @apiName GetPurchaseOrder
         * @apiGroup PurchaseOrder
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID de la orden
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Object} data Datos de la orden con líneas
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         * @apiError (404) NotFound Orden no encontrada
         */
        $authUser = AuthMiddleware::validateToken();
        
        $order = $model->getById($id);
        if (!$order) {
            Response::notFound('Order not found');
        }
        
        // Check permissions
        if ($authUser['role'] !== 'admin' && $authUser['user_id'] != $order['client_id']) {
            Response::forbidden('You can only view your own orders');
        }
        
        $order['lines'] = $model->getOrderLines($id);
        Response::success($order, 'Purchase order retrieved successfully');
        break;
        
    case 'PUT':
    case 'PATCH':
        /**
         * @api {put} /api/purchase_order/:id Actualizar orden de compra
         * @apiName UpdatePurchaseOrder
         * @apiGroup PurchaseOrder
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID de la orden
         * @apiParam {String} [client_name] Nombre del cliente
         * @apiParam {String} [client_address] Dirección
         * @apiParam {String} [client_phone] Teléfono
         * @apiParam {Array} [lines] Líneas de la orden
         * @apiParam {String} [notes] Notas
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiError (400) BadRequest Datos inválidos
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         * @apiError (404) NotFound Orden no encontrada
         */
        AuthMiddleware::requireRole('admin');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $order = $model->getById($id);
        if (!$order) {
            Response::notFound('Order not found');
        }
        
        $orderLines = null;
        if (isset($data['lines'])) {
            $orderLines = $data['lines'];
            unset($data['lines']);
        }
        
        try {
            $model->update($id, $data, $orderLines);
            Response::success(null, 'Purchase order updated successfully');
        } catch (Exception $e) {
            Response::error('Failed to update order: ' . $e->getMessage(), 500);
        }
        break;
        
    case 'DELETE':
        /**
         * @api {delete} /api/purchase_order/:id Eliminar orden de compra
         * @apiName DeletePurchaseOrder
         * @apiGroup PurchaseOrder
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID de la orden
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         * @apiError (404) NotFound Orden no encontrada
         */
        AuthMiddleware::requireRole('admin');
        
        $order = $model->getById($id);
        if (!$order) {
            Response::notFound('Order not found');
        }
        
        try {
            $model->delete($id);
            Response::success(null, 'Purchase order deleted successfully');
        } catch (Exception $e) {
            Response::error('Failed to delete order: ' . $e->getMessage(), 500);
        }
        break;
        
    default:
        Response::error('Method not allowed', 405);
}


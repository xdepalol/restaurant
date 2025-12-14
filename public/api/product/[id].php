<?php
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/ProductModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$model = new ProductModel();

// Extract ID from path or router variable
$id = $_SERVER['API_ENTITY_ID'] ?? $_GET['id'] ?? null;
if (!$id) {
    $path = $_SERVER['REQUEST_URI'];
    preg_match('/\/api\/product\/(\d+)/', $path, $matches);
    $id = $matches[1] ?? null;
}

if (!$id) {
    Response::error('Invalid product ID', 400);
}

switch ($method) {
    case 'GET':
        /**
         * @api {get} /api/product/:id Obtener producto
         * @apiName GetProduct
         * @apiGroup Product
         * @apiParam {Number} id ID del producto
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Object} data Datos del producto
         * @apiError (404) NotFound Producto no encontrado
         */
        $product = $model->getById($id);
        if (!$product) {
            Response::notFound('Product not found');
        }
        
        Response::success($product, 'Product retrieved successfully');
        break;
        
    case 'PUT':
    case 'PATCH':
        /**
         * @api {put} /api/product/:id Actualizar producto
         * @apiName UpdateProduct
         * @apiGroup Product
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID del producto
         * @apiParam {String} [nombre] Nombre del producto
         * @apiParam {String} [image] URL de la imagen
         * @apiParam {Number} [category_id] ID de la categoría
         * @apiParam {Number} [price] Precio
         * @apiParam {Number} [status] Estado
         * @apiParam {Number} [order] Orden de visualización
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiError (400) BadRequest Datos inválidos
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         * @apiError (404) NotFound Producto no encontrado
         */
        AuthMiddleware::requireRole('admin');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $product = $model->getById($id);
        if (!$product) {
            Response::notFound('Product not found');
        }
        
        $model->update($id, $data);
        Response::success(null, 'Product updated successfully');
        break;
        
    case 'DELETE':
        /**
         * @api {delete} /api/product/:id Eliminar producto
         * @apiName DeleteProduct
         * @apiGroup Product
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID del producto
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         * @apiError (404) NotFound Producto no encontrado
         */
        AuthMiddleware::requireRole('admin');
        
        $product = $model->getById($id);
        if (!$product) {
            Response::notFound('Product not found');
        }
        
        $model->delete($id);
        Response::success(null, 'Product deleted successfully');
        break;
        
    default:
        Response::error('Method not allowed', 405);
}


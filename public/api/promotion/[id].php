<?php
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/PromotionModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$model = new PromotionModel();

// Extract ID from path or router variable
$id = $_SERVER['API_ENTITY_ID'] ?? $_GET['id'] ?? null;
if (!$id) {
    $path = $_SERVER['REQUEST_URI'];
    preg_match('/\/api\/promotion\/(\d+)/', $path, $matches);
    $id = $matches[1] ?? null;
}

if (!$id) {
    Response::error('Invalid promotion ID', 400);
}

switch ($method) {
    case 'GET':
        /**
         * @api {get} /api/promotion/:id Obtener promoción
         * @apiName GetPromotion
         * @apiGroup Promotion
         * @apiParam {Number} id ID de la promoción
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Object} data Datos de la promoción
         * @apiError (404) NotFound Promoción no encontrada
         */
        $promotion = $model->getById($id);
        if (!$promotion) {
            Response::notFound('Promotion not found');
        }
        
        Response::success($promotion, 'Promotion retrieved successfully');
        break;
        
    case 'PUT':
    case 'PATCH':
        /**
         * @api {put} /api/promotion/:id Actualizar promoción
         * @apiName UpdatePromotion
         * @apiGroup Promotion
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID de la promoción
         * @apiParam {String} [promo_code] Código de promoción
         * @apiParam {Number} [discount] Descuento
         * @apiParam {String} [description] Descripción
         * @apiParam {String} [starts_at] Fecha de inicio
         * @apiParam {String} [ends_at] Fecha de fin
         * @apiParam {Number} [status] Estado
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiError (400) BadRequest Datos inválidos
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         * @apiError (404) NotFound Promoción no encontrada
         */
        AuthMiddleware::requireRole('admin');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $promotion = $model->getById($id);
        if (!$promotion) {
            Response::notFound('Promotion not found');
        }
        
        $model->update($id, $data);
        Response::success(null, 'Promotion updated successfully');
        break;
        
    case 'DELETE':
        /**
         * @api {delete} /api/promotion/:id Eliminar promoción
         * @apiName DeletePromotion
         * @apiGroup Promotion
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID de la promoción
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         * @apiError (404) NotFound Promoción no encontrada
         */
        AuthMiddleware::requireRole('admin');
        
        $promotion = $model->getById($id);
        if (!$promotion) {
            Response::notFound('Promotion not found');
        }
        
        $model->delete($id);
        Response::success(null, 'Promotion deleted successfully');
        break;
        
    default:
        Response::error('Method not allowed', 405);
}


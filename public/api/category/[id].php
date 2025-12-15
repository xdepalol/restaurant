<?php
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/CategoryModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$model = new CategoryModel();

// Extract ID from path or router variable
$id = $_SERVER['API_ENTITY_ID'] ?? $_GET['id'] ?? null;
if (!$id) {
    $path = $_SERVER['REQUEST_URI'];
    preg_match('/\/api\/category\/(\d+)/', $path, $matches);
    $id = $matches[1] ?? null;
}

if (!$id) {
    Response::error('Invalid category ID', 400);
}

switch ($method) {
    case 'GET':
        /**
         * @api {get} /api/category/:id Obtener categoría
         * @apiName GetCategory
         * @apiGroup Category
         * @apiParam {Number} id ID de la categoría
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Object} data Datos de la categoría
         * @apiError (404) NotFound Categoría no encontrada
         */
        $category = $model->getById($id);
        if (!$category) {
            Response::notFound('Category not found');
        }
        
        Response::success($category, 'Category retrieved successfully');
        break;
        
    case 'PUT':
    case 'PATCH':
        /**
         * @api {put} /api/category/:id Actualizar categoría
         * @apiName UpdateCategory
         * @apiGroup Category
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID de la categoría
         * @apiParam {String} [nombre] Nombre de la categoría
         * @apiParam {String} [image] URL de la imagen
         * @apiParam {Number} [order] Orden de visualización
         * @apiParam {Number} [status] Estado
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiError (400) BadRequest Datos inválidos
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         * @apiError (404) NotFound Categoría no encontrada
         */
        AuthMiddleware::requireRole('admin');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $category = $model->getById($id);
        if (!$category) {
            Response::notFound('Category not found');
        }
        
        $model->update($id, $data);
        Response::success(null, 'Category updated successfully');
        break;
        
    case 'DELETE':
        /**
         * @api {delete} /api/category/:id Eliminar categoría
         * @apiName DeleteCategory
         * @apiGroup Category
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID de la categoría
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         * @apiError (404) NotFound Categoría no encontrada
         */
        AuthMiddleware::requireRole('admin');
        
        $category = $model->getById($id);
        if (!$category) {
            Response::notFound('Category not found');
        }
        
        $model->delete($id);
        Response::success(null, 'Category deleted successfully');
        break;
        
    default:
        Response::error('Method not allowed', 405);
}


<?php
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/CategoryModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$model = new CategoryModel();

switch ($method) {
    case 'GET':
        /**
         * @api {get} /api/category/ Listar categorías
         * @apiName GetCategories
         * @apiGroup Category
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Array} data Lista de categorías
         */
        $includeInactive = isset($_GET['include_inactive']) && $_GET['include_inactive'] === '1';
        $authUser = null;
        
        // Only authenticated users can see inactive categories
        if ($includeInactive) {
            $authUser = AuthMiddleware::requireRole('admin');
        }
        
        $categories = $model->getAll(!$includeInactive);
        Response::success($categories, 'Categories retrieved successfully');
        break;
        
    case 'POST':
        /**
         * @api {post} /api/category/ Crear categoría
         * @apiName CreateCategory
         * @apiGroup Category
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {String} nombre Nombre de la categoría
         * @apiParam {String} [image] URL de la imagen
         * @apiParam {Number} [order] Orden de visualización
         * @apiParam {Number} [status] Estado (1=activo, 0=inactivo)
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Number} data ID de la categoría creada
         * @apiError (400) BadRequest Datos inválidos
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         */
        AuthMiddleware::requireRole('admin');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $rules = [
            'nombre' => 'required|min:2|max:255',
            'order' => 'integer',
            'status' => 'integer'
        ];
        
        $errors = Validator::validate($data, $rules);
        if (Validator::hasErrors($errors)) {
            Response::error('Validation failed', 400, $errors);
        }
        
        $id = $model->create($data);
        Response::success(['category_id' => $id], 'Category created successfully', 201);
        break;
        
    default:
        Response::error('Method not allowed', 405);
}



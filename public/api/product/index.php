<?php
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/ProductModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$model = new ProductModel();

switch ($method) {
    case 'GET':
        /**
         * @api {get} /api/product/ Listar productos
         * @apiName GetProducts
         * @apiGroup Product
         * @apiParam {Number} [category_id] Filtrar por categoría
         * @apiParam {Number} [include_inactive] Incluir productos inactivos (requiere auth)
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Array} data Lista de productos
         */
        $includeInactive = isset($_GET['include_inactive']) && $_GET['include_inactive'] === '1';
        
        if ($includeInactive) {
            AuthMiddleware::requireRole('admin');
        }
        
        if (isset($_GET['category_id'])) {
            $products = $model->getByCategory($_GET['category_id'], !$includeInactive);
        } else {
            $products = $model->getAll(!$includeInactive);
        }
        
        Response::success($products, 'Products retrieved successfully');
        break;
        
    case 'POST':
        /**
         * @api {post} /api/product/ Crear producto
         * @apiName CreateProduct
         * @apiGroup Product
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {String} nombre Nombre del producto
         * @apiParam {String} image URL de la imagen
         * @apiParam {Number} category_id ID de la categoría
         * @apiParam {Number} price Precio
         * @apiParam {Number} [status] Estado (1=activo, 0=inactivo)
         * @apiParam {Number} [order] Orden de visualización
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Number} data ID del producto creado
         * @apiError (400) BadRequest Datos inválidos
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         */
        AuthMiddleware::requireRole('admin');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $rules = [
            'nombre' => 'required|min:2|max:255',
            'image' => 'required|max:200',
            'category_id' => 'required|integer',
            'price' => 'required|numeric',
            'status' => 'integer',
            'order' => 'integer'
        ];
        
        $errors = Validator::validate($data, $rules);
        if (Validator::hasErrors($errors)) {
            Response::error('Validation failed', 400, $errors);
        }
        
        $id = $model->create($data);
        Response::success(['product_id' => $id], 'Product created successfully', 201);
        break;
        
    default:
        Response::error('Method not allowed', 405);
}



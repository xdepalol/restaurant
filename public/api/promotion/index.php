<?php
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/PromotionModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$model = new PromotionModel();

switch ($method) {
    case 'GET':
        /**
         * @api {get} /api/promotion/ Listar promociones
         * @apiName GetPromotions
         * @apiGroup Promotion
         * @apiParam {Number} [include_inactive] Incluir promociones inactivas (requiere auth)
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Array} data Lista de promociones
         */
        $includeInactive = isset($_GET['include_inactive']) && $_GET['include_inactive'] === '1';
        
        if ($includeInactive) {
            AuthMiddleware::requireRole('admin');
        }
        
        $promotions = $model->getAll(!$includeInactive);
        Response::success($promotions, 'Promotions retrieved successfully');
        break;
        
    case 'POST':
        /**
         * @api {post} /api/promotion/ Crear promoción
         * @apiName CreatePromotion
         * @apiGroup Promotion
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {String} promo_code Código de promoción
         * @apiParam {Number} discount Descuento (porcentaje)
         * @apiParam {String} description Descripción
         * @apiParam {String} [starts_at] Fecha de inicio (datetime)
         * @apiParam {String} [ends_at] Fecha de fin (datetime)
         * @apiParam {Number} [status] Estado (1=activo, 0=inactivo)
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Number} data ID de la promoción creada
         * @apiError (400) BadRequest Datos inválidos
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         */
        AuthMiddleware::requireRole('admin');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $rules = [
            'promo_code' => 'required|min:2|max:50',
            'discount' => 'required|numeric',
            'description' => 'required',
            'status' => 'integer'
        ];
        
        $errors = Validator::validate($data, $rules);
        if (Validator::hasErrors($errors)) {
            Response::error('Validation failed', 400, $errors);
        }
        
        $id = $model->create($data);
        Response::success(['promotion_id' => $id], 'Promotion created successfully', 201);
        break;
        
    default:
        Response::error('Method not allowed', 405);
}




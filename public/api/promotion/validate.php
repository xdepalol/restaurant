<?php
/**
 * @api {post} /api/promotion/validate Validar código de promoción
 * @apiName ValidatePromotion
 * @apiGroup Promotion
 * @apiParam {String} promo_code Código de promoción
 * @apiSuccess {Boolean} valid Si el código es válido
 * @apiSuccess {Object} promotion Datos de la promoción
 * @apiError (400) BadRequest Código inválido o expirado
 */
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/models/PromotionModel.php';

$data = json_decode(file_get_contents('php://input'), true);

$rules = [
    'promo_code' => 'required'
];

$errors = Validator::validate($data, $rules);
if (Validator::hasErrors($errors)) {
    Response::error('Validation failed', 400, $errors);
}

$model = new PromotionModel();
$result = $model->validatePromoCode($data['promo_code']);

if (!$result['valid']) {
    Response::error($result['message'], 400);
}

Response::success($result['promotion'], 'Promotion code is valid');




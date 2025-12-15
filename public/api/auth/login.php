<?php
/**
 * @api {post} /api/auth/login Iniciar sesión
 * @apiName Login
 * @apiGroup Auth
 * @apiParam {String} email Email del usuario
 * @apiParam {String} password Contraseña
 * @apiSuccess {Boolean} success Estado de la operación
 * @apiSuccess {String} data.token Token de autenticación
 * @apiSuccess {Object} data.user Datos del usuario
 * @apiError (400) BadRequest Credenciales inválidas
 * @apiError (401) Unauthorized Email o contraseña incorrectos
 */
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/UserModel.php';

$data = json_decode(file_get_contents('php://input'), true);

$rules = [
    'email' => 'required|email',
    'password' => 'required'
];

$errors = Validator::validate($data, $rules);
if (Validator::hasErrors($errors)) {
    Response::error('Validation failed', 400, $errors);
}

$model = new UserModel();
$user = $model->verifyPassword($data['email'], $data['password']);

if (!$user) {
    Response::unauthorized('Invalid email or password');
}

$token = AuthMiddleware::generateToken($user);
unset($user['password']);

Response::success([
    'token' => $token,
    'user' => $user
], 'Login successful');



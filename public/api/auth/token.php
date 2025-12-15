<?php
/**
 * @api {get} /api/auth/token Obtener token desde sesión
 * @apiName GetTokenFromSession
 * @apiGroup Auth
 * @apiDescription Genera un token API basado en la sesión actual del usuario
 * @apiSuccess {Boolean} success Estado de la operación
 * @apiSuccess {String} data.token Token de autenticación
 * @apiError (401) Unauthorized Usuario no autenticado
 */
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/UserModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    Response::unauthorized('User not authenticated');
}

$userModel = new UserModel();
$user = $userModel->getById($_SESSION['user_id']);

if (!$user) {
    Response::unauthorized('User not found');
}

$token = AuthMiddleware::generateToken([
    'user_id' => $user['user_id'],
    'email' => $user['email'],
    'role' => $user['role']
]);

Response::success(['token' => $token], 'Token generated successfully');



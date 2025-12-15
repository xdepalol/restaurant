<?php
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/UserModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$model = new UserModel();

// Extract ID from path or router variable
$id = $_SERVER['API_ENTITY_ID'] ?? $_GET['id'] ?? null;
if (!$id) {
    $path = $_SERVER['REQUEST_URI'];
    preg_match('/\/api\/user\/(\d+)/', $path, $matches);
    $id = $matches[1] ?? null;
}

if (!$id) {
    Response::error('Invalid user ID', 400);
}

switch ($method) {
    case 'GET':
        /**
         * @api {get} /api/user/:id Obtener usuario
         * @apiName GetUser
         * @apiGroup User
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID del usuario
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Object} data Datos del usuario
         * @apiError (401) Unauthorized Token no válido
         * @apiError (404) NotFound Usuario no encontrado
         */
        $authUser = AuthMiddleware::validateToken();
        
        // Users can only view their own profile unless they're admin
        if ($authUser['role'] !== 'admin' && $authUser['user_id'] != $id) {
            Response::forbidden('You can only view your own profile');
        }
        
        $user = $model->getById($id);
        if (!$user) {
            Response::notFound('User not found');
        }
        
        Response::success($user, 'User retrieved successfully');
        break;
        
    case 'PUT':
    case 'PATCH':
        /**
         * @api {put} /api/user/:id Actualizar usuario
         * @apiName UpdateUser
         * @apiGroup User
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID del usuario
         * @apiParam {String} [name] Nombre del usuario
         * @apiParam {String} [address] Dirección
         * @apiParam {String} [phone] Teléfono
         * @apiParam {String} [email] Email
         * @apiParam {String} [password] Nueva contraseña
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiError (400) BadRequest Datos inválidos
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         * @apiError (404) NotFound Usuario no encontrado
         */
        $authUser = AuthMiddleware::validateToken();
        
        // Users can only update their own profile unless they're admin
        if ($authUser['role'] !== 'admin' && $authUser['user_id'] != $id) {
            Response::forbidden('You can only update your own profile');
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Non-admins cannot change role
        if ($authUser['role'] !== 'admin' && isset($data['role'])) {
            unset($data['role']);
        }
        
        $user = $model->getById($id);
        if (!$user) {
            Response::notFound('User not found');
        }
        
        $model->update($id, $data);
        Response::success(null, 'User updated successfully');
        break;
        
    case 'DELETE':
        /**
         * @api {delete} /api/user/:id Eliminar usuario
         * @apiName DeleteUser
         * @apiGroup User
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {Number} id ID del usuario
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         * @apiError (404) NotFound Usuario no encontrado
         */
        AuthMiddleware::requireRole('admin');
        
        $user = $model->getById($id);
        if (!$user) {
            Response::notFound('User not found');
        }
        
        $model->delete($id);
        Response::success(null, 'User deleted successfully');
        break;
        
    default:
        Response::error('Method not allowed', 405);
}


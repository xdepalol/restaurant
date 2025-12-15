<?php
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/src/utils/Response.php';
require_once BASE_PATH . '/src/utils/Validator.php';
require_once BASE_PATH . '/src/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/src/models/UserModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$model = new UserModel();

switch ($method) {
    case 'GET':
        /**
         * @api {get} /api/user/ Listar usuarios
         * @apiName GetUsers
         * @apiGroup User
         * @apiHeader {String} Authorization Token de autenticación
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Array} data Lista de usuarios
         * @apiError (401) Unauthorized Token no válido o faltante
         * @apiError (403) Forbidden Permisos insuficientes
         */
        $user = AuthMiddleware::requireRole('admin');
        
        $users = $model->getAll();
        Response::success($users, 'Users retrieved successfully');
        break;
        
    case 'POST':
        /**
         * @api {post} /api/user/ Crear usuario
         * @apiName CreateUser
         * @apiGroup User
         * @apiHeader {String} Authorization Token de autenticación
         * @apiParam {String} name Nombre del usuario
         * @apiParam {String} address Dirección
         * @apiParam {String} phone Teléfono
         * @apiParam {String} email Email (único)
         * @apiParam {String} login Nombre de usuario
         * @apiParam {String} password Contraseña
         * @apiParam {String} role Rol (client/admin)
         * @apiSuccess {Boolean} success Estado de la operación
         * @apiSuccess {Number} data ID del usuario creado
         * @apiError (400) BadRequest Datos inválidos
         * @apiError (401) Unauthorized Token no válido
         * @apiError (403) Forbidden Permisos insuficientes
         */
        $user = AuthMiddleware::requireRole('admin');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $rules = [
            'name' => 'required|min:2|max:255',
            'address' => 'required|min:5|max:255',
            'phone' => 'required|min:5|max:50',
            'email' => 'required|email|max:100',
            'login' => 'required|min:3|max:100',
            'password' => 'required|min:6',
            'role' => 'required'
        ];
        
        $errors = Validator::validate($data, $rules);
        if (Validator::hasErrors($errors)) {
            Response::error('Validation failed', 400, $errors);
        }
        
        // Check if email already exists
        $existing = $model->getByEmail($data['email']);
        if ($existing) {
            Response::error('Email already exists', 400);
        }
        
        $id = $model->create($data);
        Response::success(['user_id' => $id], 'User created successfully', 201);
        break;
        
    default:
        Response::error('Method not allowed', 405);
}


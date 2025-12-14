<?php
/**
 * Main Router - Simple MVC Router
 */
require_once dirname(__DIR__) . '/config/config.php';

// Start session
session_start();

// Simple routing
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string
$requestUri = strtok($requestUri, '?');

// Remove base path if needed
$basePath = '/restaurant/public';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Remove leading/trailing slashes
$requestUri = trim($requestUri, '/');

// Route to API if it's an API request
if (strpos($requestUri, 'api/') === 0) {
    require dirname(__DIR__) . '/public/api/router.php';
    exit;
}

// Website routing
$routes = [
    '' => ['controller' => 'HomeController', 'action' => 'index'],
    'home' => ['controller' => 'HomeController', 'action' => 'index'],
    'about' => ['controller' => 'HomeController', 'action' => 'about'],
    'legal' => ['controller' => 'HomeController', 'action' => 'legal'],
    'login' => ['controller' => 'AuthController', 'action' => 'login'],
    'register' => ['controller' => 'AuthController', 'action' => 'register'],
    'logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    'account' => ['controller' => 'AccountController', 'action' => 'index'],
    'account/profile' => ['controller' => 'AccountController', 'action' => 'profile'],
    'account/orders' => ['controller' => 'AccountController', 'action' => 'orders'],
    'products' => ['controller' => 'ShoppingController', 'action' => 'products'],
    'cart' => ['controller' => 'ShoppingController', 'action' => 'cart'],
    'checkout' => ['controller' => 'ShoppingController', 'action' => 'checkout'],
    'admin' => ['controller' => 'AdminController', 'action' => 'index']
];

// Find matching route
$route = null;
if (isset($routes[$requestUri])) {
    $route = $routes[$requestUri];
} else {
    // 404
    http_response_code(404);
    require VIEWS_PATH . '/errors/404.php';
    exit;
}

// Load controller
$controllerName = $route['controller'];
$controllerFile = SRC_PATH . '/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(500);
    die("Controller file not found: {$controllerFile}");
}

require_once $controllerFile;

if (!class_exists($controllerName)) {
    http_response_code(500);
    die("Controller class not found: {$controllerName}");
}

$controller = new $controllerName();
$action = $route['action'];

if (!method_exists($controller, $action)) {
    http_response_code(500);
    die("Action not found: {$action}");
}

// Execute action
$controller->$action();


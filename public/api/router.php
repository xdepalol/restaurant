<?php
/**
 * API Router
 */
require_once dirname(__DIR__, 2) . '/config/config.php';

// Get request URI and method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string
$requestUri = strtok($requestUri, '?');

// Remove base path
$basePath = '/restaurant/public/api';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Remove leading/trailing slashes
$requestUri = trim($requestUri, '/');

// Route mapping
$routes = [
    'helloworld' => 'helloworld.php',
    'auth/login' => 'auth/login.php',
    'auth/token' => 'auth/token.php',
    'promotion/validate' => 'promotion/validate.php'
];

// Check if it's a direct route
if (isset($routes[$requestUri])) {
    require __DIR__ . '/' . $routes[$requestUri];
    exit;
}

// Handle entity routes with ID
if (preg_match('/^([^\/]+)\/(\d+)$/', $requestUri, $matches)) {
    $entity = $matches[1];
    $id = $matches[2];
    $idFile = __DIR__ . '/' . $entity . '/[id].php';
    if (file_exists($idFile)) {
        // Set ID in a way that the API file can access it
        $_GET['id'] = $id;
        $_SERVER['API_ENTITY_ID'] = $id;
        require $idFile;
        exit;
    }
}

// Handle entity index routes
if (preg_match('/^([^\/]+)\/?$/', $requestUri, $matches)) {
    $entity = $matches[1];
    $indexFile = __DIR__ . '/' . $entity . '/index.php';
    if (file_exists($indexFile)) {
        require $indexFile;
        exit;
    }
}

// 404
http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
exit;


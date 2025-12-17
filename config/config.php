<?php
/**
 * Application Configuration
 */
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('SRC_PATH', BASE_PATH . '/src');
define('VIEWS_PATH', SRC_PATH . '/views');
define('API_PATH', PUBLIC_PATH . '/api');

// API Configuration
define('API_TOKEN_SECRET', 'hP2BCzu3t+J+jcQD8uSxyb22B@lCbX#^TCz%d5Xe@T^='); // Change this in production!
define('API_TOKEN_EXPIRY', 3600); // 1 hour

// Application Settings
define('APP_NAME', 'Restaurant Management System');
define('APP_URL', 'http://localhost/restaurant/public');

// Email Configuration (for order confirmations)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM', 'noreply@restaurant.com');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');




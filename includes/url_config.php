<?php
require_once __DIR__ . '/config.php';

// Kiểm tra môi trường từ biến ENVIRONMENT
$environment = getenv('ENVIRONMENT') ?: 'local';
$isProduction = $environment === 'host';

// Base URL cho website
if ($isProduction) {
    // Trên môi trường production (Heroku)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    define('BASE_URL', '');
} else {
    // Trên môi trường local
    define('BASE_URL', '/snackhaven');
}

// Hàm helper để tạo URL
function url($path = '') {
    return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
} 
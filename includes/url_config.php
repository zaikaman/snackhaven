<?php
require_once __DIR__ . '/config.php';

// Kiểm tra môi trường từ biến ENVIRONMENT
$environment = getenv('ENVIRONMENT') ?: 'local';
$isProduction = $environment === 'host';

// Base URL cho website
define('BASE_URL', $isProduction ? '' : '/snackhaven');

// Hàm helper để tạo URL
function url($path = '') {
    return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
} 
<?php
// Kiểm tra môi trường
$isProduction = isset($_SERVER['HEROKU_APP_DIR']) || 
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');

// Base URL cho website
define('BASE_URL', $isProduction ? '' : '/snackhaven');

// Hàm helper để tạo URL
function url($path = '') {
    return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
} 
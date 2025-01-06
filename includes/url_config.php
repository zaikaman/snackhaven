<?php
// Kiểm tra môi trường
$isProduction = strpos($_SERVER['HTTP_HOST'], 'herokuapp.com') !== false;

// Base URL cho website
define('BASE_URL', $isProduction ? '' : '/snackhaven');

// Hàm helper để tạo URL
function url($path = '') {
    return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
} 
<?php
require_once __DIR__ . '/config.php';

// Base URL cho website
define('BASE_URL', '/snackhaven');

// Hàm helper để tạo URL
function url($path = '') {
    return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
} 
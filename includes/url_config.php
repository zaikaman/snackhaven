<?php
require_once __DIR__ . '/config.php';

// Base URL cho website
define('BASE_URL', '/snackhaven');

// Hàm helper để tạo URL
function url($path = '') {
    return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
}

// Hàm kiểm tra đường dẫn hiện tại
function is_current_path($path) {
    $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $current_path = str_replace('/snackhaven/', '', $current_path);
    return $current_path === ltrim($path, '/');
}

// Hàm thêm class 'active' cho menu item
function active_menu($path) {
    return is_current_path($path) ? 'active' : '';
}

// Hàm redirect
function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

// Hàm kiểm tra đăng nhập
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        redirect('auth/login.php');
    }
}

// Hàm lấy thông tin user đang đăng nhập
function get_logged_user() {
    global $pdo;
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}
?> 
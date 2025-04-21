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

    // Kiểm tra trạng thái active của tài khoản
    global $pdo;
    $stmt = $pdo->prepare("SELECT active FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !$user['active']) {
        // Xóa session và chuyển về trang đăng nhập
        session_destroy();
        redirect('auth/login.php?status=error&message=' . urlencode('Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'));
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
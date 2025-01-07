<?php
require_once '../includes/url_config.php';
session_start();

// Kiểm tra nếu là Ajax request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Xóa tất cả các biến session
    $_SESSION = array();

    // Xóa cookie session
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }

    // Hủy session
    session_destroy();

    // Trả về response dạng JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Đăng xuất thành công',
        'redirect' => url()
    ]);
    exit;
} else {
    // Nếu không phải Ajax request, xử lý như bình thường
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    session_destroy();
    header('Location: ' . url());
    exit;
} 
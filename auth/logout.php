<?php
session_start();

// Xóa tất cả các biến session
$_SESSION = array();

// Xóa cookie session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Hủy session
session_destroy();

// Chuyển hướng về trang chủ
header('Location: /snackhaven');
exit; 
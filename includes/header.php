<?php
session_start();
require_once __DIR__ . '/url_config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnackHaven</title>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
</head>
<body>
<header>
    <div class="header-container">
        <div class="logo">
            <a href="<?php echo url(); ?>">
                <i class="fas fa-hamburger logo-icon"></i>
                <span>SnackHaven</span>
            </a>
        </div>
        
        <nav>
            <ul>
                <li><a href="<?php echo url(); ?>" class="active">Trang chủ</a></li>
                <li><a href="<?php echo url('menu'); ?>">Thực đơn</a></li>
                <li><a href="<?php echo url('about'); ?>">Giới thiệu</a></li>
                <li><a href="<?php echo url('contact'); ?>">Liên hệ</a></li>
            </ul>
        </nav>
        
        <div class="header-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo url('profile'); ?>">
                    <i class="fas fa-user"></i>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <a href="auth/logout.php" id="logout-btn" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            <?php else: ?>
                <a href="<?php echo url('auth/login.php'); ?>" class="login-btn">Đăng nhập</a>
                <a href="<?php echo url('auth/register.php'); ?>">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</header> 
<?php
session_start();
require_once __DIR__ . '/url_config.php';
?>
<header>
    <div class="header-container">
        <div class="logo">
            <a href="<?php echo url(); ?>">
                <img src="<?php echo url('assets/images/logo.png'); ?>" alt="Logo" class="logo-icon">
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
                <a href="<?php echo url('auth/logout.php'); ?>" class="login-btn">Đăng xuất</a>
            <?php else: ?>
                <a href="<?php echo url('auth/login.php'); ?>" class="login-btn">Đăng nhập</a>
                <a href="<?php echo url('auth/register.php'); ?>">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</header> 
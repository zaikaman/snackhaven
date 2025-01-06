<?php
session_start();
?>
<header>
    <div class="header-container">
        <div class="logo">
            <a href="/snackhaven">
                <i class="fas fa-hamburger logo-icon"></i>
                <span>SnackHaven</span>
            </a>
        </div>
        
        <nav>
            <ul>
                <li><a href="/snackhaven" class="active">Trang chủ</a></li>
                <li><a href="/snackhaven/menu">Thực đơn</a></li>
                <li><a href="/snackhaven/about">Giới thiệu</a></li>
                <li><a href="/snackhaven/contact">Liên hệ</a></li>
            </ul>
        </nav>
        
        <div class="header-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/snackhaven/profile">
                    <i class="fas fa-user"></i>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <a href="/snackhaven/auth/logout.php" class="login-btn">Đăng xuất</a>
            <?php else: ?>
                <a href="/snackhaven/auth/login.php" class="login-btn">Đăng nhập</a>
                <a href="/snackhaven/auth/register.php">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</header> 
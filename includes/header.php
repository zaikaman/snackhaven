<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>
<header>
    <div class="header-container">
        <div class="logo">
            <a href="index.php">
                <span class="logo-icon">🍔</span>
                <span class="logo-text">SnackHaven</span>
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php" <?php echo ($current_page == 'index.php') ? 'class="active"' : ''; ?>>Trang Chủ</a></li>
                <li><a href="menu.php" <?php echo ($current_page == 'menu.php') ? 'class="active"' : ''; ?>>Thực Đơn</a></li>
                <li><a href="deals.php" <?php echo ($current_page == 'deals.php') ? 'class="active"' : ''; ?>>Khuyến Mãi</a></li>
                <li><a href="about.php" <?php echo ($current_page == 'about.php') ? 'class="active"' : ''; ?>>Về Chúng Tôi</a></li>
                <li><a href="contact.php" <?php echo ($current_page == 'contact.php') ? 'class="active"' : ''; ?>>Liên Hệ</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <a href="#" class="search-icon"><i class="fas fa-search"></i></a>
            <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i></a>
            <a href="login.php" class="login-btn">Đăng Nhập</a>
        </div>
    </div>
</header> 
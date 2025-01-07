<?php
session_start();
require_once __DIR__ . '/url_config.php';

// Hàm kiểm tra trang hiện tại
function isCurrentPage($page) {
    $current_page = str_replace('.php', '', basename($_SERVER['PHP_SELF']));
    if ($page === '' && $current_page === 'index') {
        return true;
    }
    return $current_page === $page;
}
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
    <style>
        /* Cart Modal Styles */
        .cart-modal {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: white;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
            transition: right 0.3s ease-in-out;
            z-index: 1000;
            overflow-y: auto;
        }

        .cart-modal.active {
            right: 0;
        }

        .cart-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            z-index: 999;
        }

        .cart-overlay.active {
            display: block;
        }

        .cart-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .cart-items {
            padding: 20px;
        }

        .cart-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }

        .cart-item-details {
            flex-grow: 1;
        }

        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .quantity-btn {
            background: #ff6b6b;
            color: white;
            border: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
        }

        .cart-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            position: sticky;
            bottom: 0;
            background: white;
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .checkout-btn {
            width: 100%;
            padding: 12px;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .checkout-btn:hover {
            background: #ff5252;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff6b6b;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .cart-icon-btn {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            margin-right: 15px;
        }

        /* Cart Modal Styles */
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            color: #ff6b6b;
        }

        .search-container {
            flex: 1;
            max-width: 500px;
            margin: 0 2rem;
        }

        .search-form {
            display: flex;
            gap: 0.5rem;
        }

        .search-input {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 2px solid #eee;
            border-radius: 50px;
            transition: border-color 0.3s;
        }

        .search-input:focus {
            border-color: #ff6b6b;
            outline: none;
        }

        .search-btn {
            padding: 0.5rem 1.5rem;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-btn:hover {
            background: #ff5252;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        nav ul {
            display: flex;
            gap: 1.5rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.2rem;
            position: relative;
        }

        nav a i {
            font-size: 1.2rem;
        }

        nav a span {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        nav a:hover {
            color: #ff6b6b;
        }

        nav a.active {
            color: #ff6b6b;
        }

        /* Tooltip styles */
        nav a::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        nav a:hover::after {
            opacity: 1;
            visibility: visible;
            bottom: -25px;
        }

        .header-actions a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login-btn {
            padding: 0.5rem 1.5rem;
            background: #ff6b6b;
            color: white !important;
            border-radius: 50px;
            transition: background 0.3s;
        }

        .login-btn:hover {
            background: #ff5252;
        }
    </style>
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
                <li>
                    <a href="<?php echo url(); ?>" class="<?php echo isCurrentPage('') ? 'active' : ''; ?>" data-tooltip="Trang chủ">
                        <i class="fas fa-home"></i>
                        <span>Trang chủ</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('menu'); ?>" class="<?php echo isCurrentPage('menu') ? 'active' : ''; ?>" data-tooltip="Thực đơn">
                        <i class="fas fa-utensils"></i>
                        <span>Thực đơn</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('about'); ?>" class="<?php echo isCurrentPage('about') ? 'active' : ''; ?>" data-tooltip="Giới thiệu">
                        <i class="fas fa-info-circle"></i>
                        <span>Giới thiệu</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('contact'); ?>" class="<?php echo isCurrentPage('contact') ? 'active' : ''; ?>" data-tooltip="Liên hệ">
                        <i class="fas fa-envelope"></i>
                        <span>Liên hệ</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="search-container">
            <form action="<?php echo url('search'); ?>" method="GET" class="search-form">
                <input type="text" name="keyword" class="search-input" placeholder="Tìm kiếm sản phẩm..." value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <div class="header-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="cart-icon-btn" onclick="toggleCart()">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                    <span class="cart-badge" id="cartBadge">0</span>
                </button>
                <a href="<?php echo url('profile'); ?>" class="<?php echo isCurrentPage('profile') ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <a href="auth/logout.php" id="logout-btn" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php else: ?>
                <a href="<?php echo url('auth/login.php'); ?>" class="login-btn">Đăng nhập</a>
                <a href="<?php echo url('auth/register.php'); ?>">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Cart Modal -->
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-modal" id="cartModal">
    <div class="cart-header">
        <h3>Giỏ hàng của bạn</h3>
        <button class="cart-close" onclick="toggleCart()">&times;</button>
    </div>
    <div class="cart-items" id="cartItems">
        <!-- Cart items will be dynamically added here -->
    </div>
    <div class="cart-footer">
        <div class="cart-total">
            <span>Tổng cộng:</span>
            <span id="cartTotal">0₫</span>
        </div>
        <button class="checkout-btn" onclick="checkout()">Thanh toán</button>
    </div>
</div>

<script>
function toggleCart() {
    const modal = document.getElementById('cartModal');
    const overlay = document.getElementById('cartOverlay');
    modal.classList.toggle('active');
    overlay.classList.toggle('active');
}

function updateCartBadge(count) {
    document.getElementById('cartBadge').textContent = count;
}

function updateCartTotal(total) {
    document.getElementById('cartTotal').textContent = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(total);
}

function checkout() {
    <?php if (!isset($_SESSION['user_id'])): ?>
        Swal.fire({
            title: 'Thông báo',
            text: 'Vui lòng đăng nhập để thanh toán!',
            icon: 'warning',
            confirmButtonText: 'Đăng nhập',
            showCancelButton: true,
            cancelButtonText: 'Đóng'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?php echo url("auth/login.php"); ?>';
            }
        });
    <?php else: ?>
        window.location.href = '<?php echo url("checkout"); ?>';
    <?php endif; ?>
}

// Close cart when clicking outside
document.getElementById('cartOverlay').addEventListener('click', toggleCart);
</script> 
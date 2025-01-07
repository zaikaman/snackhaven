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
                <li><a href="<?php echo url(); ?>" class="active">Trang chủ</a></li>
                <li><a href="<?php echo url('menu'); ?>">Thực đơn</a></li>
                <li><a href="<?php echo url('about'); ?>">Giới thiệu</a></li>
                <li><a href="<?php echo url('contact'); ?>">Liên hệ</a></li>
            </ul>
        </nav>
        
        <div class="header-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="cart-icon-btn" onclick="toggleCart()">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                    <span class="cart-badge" id="cartBadge">0</span>
                </button>
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
    // Implement checkout logic here
    alert('Chức năng thanh toán đang được phát triển!');
}

// Close cart when clicking outside
document.getElementById('cartOverlay').addEventListener('click', toggleCart);
</script> 
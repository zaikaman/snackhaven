<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/url_config.php';

// Hàm kiểm tra trang hiện tại
if (!function_exists('isCurrentPage')) {
    function isCurrentPage($page) {
        $current_page = str_replace('.php', '', basename($_SERVER['PHP_SELF']));
        if ($page === '' && $current_page === 'index') {
            return true;
        }
        return $current_page === $page;
    }
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

        .cart-item-quantity button {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: none;
            background: #ff6b6b;
            color: white;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .cart-item-quantity button:hover {
            background: #ff5252;
            transform: scale(1.1);
        }

        /* Nút xóa */
        .cart-item-quantity button:last-child {
            background: #dc3545;
            font-size: 16px;
        }

        .cart-item-quantity button:last-child:hover {
            background: #c82333;
        }

        .cart-item-quantity span {
            min-width: 20px;
            text-align: center;
            font-weight: 500;
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
        .header-wrapper {
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        @media (max-width: 1440px) {
            .header-container {
                padding: 1rem;
            }
        }

        @media (max-width: 1200px) {
            .search-container {
                max-width: 400px;
            }
        }

        @media (max-width: 992px) {
            .search-container {
                max-width: 300px;
                margin: 0 1rem;
            }
            
            nav ul {
                gap: 1rem;
            }
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.6rem;
            font-weight: 600;
            text-decoration: none;
            color: #ff6b6b;
            transition: transform 0.2s;
            margin-left: -1rem;
        }

        @media (max-width: 1440px) {
            .logo {
                margin-left: -0.5rem;
            }
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .search-container {
            flex: 1;
            max-width: 600px;
            margin: 0 2rem;
        }

        .search-form {
            display: flex;
            gap: 0.5rem;
            position: relative;
        }

        .search-input {
            flex: 1;
            padding: 0.8rem 1.2rem;
            border: 2px solid #f0f0f0;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            background: #f8f8f8;
        }

        .search-input:focus {
            border-color: #ff6b6b;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(255,107,107,0.1);
            outline: none;
        }

        .search-btn {
            padding: 0.8rem 1.5rem;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .search-btn:hover {
            background: #ff5252;
            transform: translateY(-1px);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        nav ul {
            display: flex;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav a {
            text-decoration: none;
            color: #444;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
            position: relative;
            padding: 0.5rem;
        }

        nav a i {
            font-size: 1.3rem;
            transition: transform 0.2s;
        }

        nav a span {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        nav a:hover {
            color: #ff6b6b;
        }

        nav a:hover i {
            transform: translateY(-2px);
        }

        nav a.active {
            color: #ff6b6b;
        }

        nav a.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 2px;
            background: #ff6b6b;
            border-radius: 2px;
        }

        .header-actions a {
            text-decoration: none;
            color: #444;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .login-btn {
            padding: 0.8rem 1.8rem;
            background: #ff6b6b;
            color: white !important;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background: #ff5252;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255,107,107,0.2);
        }

        .cart-icon-btn {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            margin-right: 1rem;
            transition: transform 0.2s;
        }

        .cart-icon-btn:hover {
            transform: scale(1.1);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff6b6b;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
<header class="header-wrapper">
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

// Hàm cập nhật hiển thị giỏ hàng
function updateCartDisplay() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartBadge = document.getElementById('cartBadge');

    // Cập nhật số lượng trên badge
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartBadge.textContent = totalItems;

    // Cập nhật nội dung giỏ hàng
    cartItems.innerHTML = cart.map(item => `
        <div class="cart-item">
            <img src="${item.image_url}" alt="${item.name}">
            <div class="cart-item-details">
                <h4>${item.name}</h4>
                <p>${new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(item.price)}</p>
                <div class="cart-item-quantity">
                    <button onclick="updateQuantity(${item.id}, -1)">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="updateQuantity(${item.id}, 1)">+</button>
                    <button onclick="removeItem(${item.id})">&times;</button>
                </div>
            </div>
        </div>
    `).join('');

    // Cập nhật tổng tiền
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    cartTotal.textContent = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(total);
}

// Hàm cập nhật số lượng sản phẩm
function updateQuantity(productId, change) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            cart = cart.filter(item => item.id !== productId);
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartDisplay();
    }
}

// Hàm xóa sản phẩm khỏi giỏ hàng
function removeItem(productId) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
}

// Khởi tạo giỏ hàng khi trang được load
document.addEventListener('DOMContentLoaded', function() {
    updateCartDisplay();
});
</script> 
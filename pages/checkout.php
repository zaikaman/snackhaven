<?php
require_once __DIR__ . '/../includes/config.php';

// Kiểm tra đăng nhập
check_login();

// Lấy thông tin user
$user = get_logged_user();

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Cập nhật thông tin user
        $updateUserStmt = $pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, phone = ?, address = ?
            WHERE id = ?
        ");
        
        $updateUserStmt->execute([
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['phone'],
            $_POST['address'],
            $_SESSION['user_id']
        ]);

        // Tạo đơn hàng mới
        $pdo->beginTransaction();

        // Lấy giỏ hàng và tính tổng tiền
        $cart = json_decode($_POST['cart_data'], true);
        $totalPrice = 0;
        
        if (empty($cart)) {
            throw new Exception("Giỏ hàng trống");
        }

        // Tạo đơn hàng
        $createOrderStmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_price, status, created_at)
            VALUES (?, ?, 'pending', NOW())
        ");

        // Tính tổng tiền và chuẩn bị dữ liệu cho order_items
        $orderItems = [];
        foreach ($cart as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $totalPrice += $itemTotal;
            $orderItems[] = [
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }

        $createOrderStmt->execute([
            $_SESSION['user_id'],
            $totalPrice
        ]);

        $orderId = $pdo->lastInsertId();

        // Thêm các sản phẩm vào bảng order_items
        $createOrderItemStmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($orderItems as $item) {
            $createOrderItemStmt->execute([
                $orderId,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);
        }

        $pdo->commit();

        // Xóa giỏ hàng
        echo '<script>localStorage.removeItem("cart");</script>';

        // Chuyển hướng đến trang cảm ơn
        echo '<script>
            Swal.fire({
                title: "Đặt hàng thành công!",
                text: "Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất.",
                icon: "success"
            }).then(() => {
                window.location.href = "' . url() . '";
            });
        </script>';
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Có lỗi xảy ra: " . $e->getMessage();
    }
}
?>

<style>
.checkout-container {
    max-width: 1000px;
    margin: 100px auto 50px;
    padding: 0 20px;
}

.checkout-header {
    text-align: center;
    margin-bottom: 40px;
}

.checkout-header h1 {
    color: #333;
    font-size: 2.5rem;
    margin-bottom: 10px;
    font-weight: bold;
}

.checkout-header p {
    color: #666;
    font-size: 1.1rem;
}

.checkout-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
}

@media (max-width: 992px) {
    .checkout-content {
        grid-template-columns: 1fr;
    }
}

.form-section {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    padding: 30px;
    margin-bottom: 20px;
}

.form-section h2 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #ff6b6b;
    font-weight: 600;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #555;
    font-weight: 500;
    font-size: 0.95rem;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #ff6b6b;
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,107,107,0.1);
}

.form-group input:disabled {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

.form-group textarea {
    height: 120px;
    resize: vertical;
}

.order-summary {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item-details {
    display: flex;
    align-items: center;
    gap: 15px;
}

.order-item-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
}

.order-item-name {
    font-weight: 500;
    color: #333;
}

.order-item-quantity {
    color: #666;
    font-size: 0.9rem;
}

.order-item-price {
    font-weight: 600;
    color: #ff6b6b;
}

.order-total {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
}

.payment-method {
    margin-top: 20px;
}

.payment-method label {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.payment-method label:hover {
    border-color: #ff6b6b;
    background: #fff5f5;
}

.payment-method input[type="radio"]:checked + label {
    border-color: #ff6b6b;
    background: #fff5f5;
}

.place-order-btn {
    background: #ff6b6b;
    color: white;
    border: none;
    width: 100%;
    padding: 15px;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
    margin-top: 20px;
}

.place-order-btn:hover {
    background: #ff5252;
}

.required {
    color: #ff6b6b;
    margin-left: 3px;
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-danger {
    background-color: #fff5f5;
    color: #dc3545;
    border: 1px solid #ffcdd2;
}

.alert-success {
    background-color: #f0fff4;
    color: #28a745;
    border: 1px solid #c3e6cb;
}
</style>

<div class="checkout-container">
    <div class="checkout-header">
        <h1>Thanh toán</h1>
        <p>Vui lòng điền thông tin giao hàng của bạn</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="checkout-content">
        <div class="checkout-form">
            <form method="POST" id="checkoutForm">
                <div class="form-section">
                    <h2>Thông tin giao hàng</h2>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name">Họ<span class="required">*</span></label>
                                <input type="text" id="first_name" name="first_name" 
                                    value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name">Tên<span class="required">*</span></label>
                                <input type="text" id="last_name" name="last_name" 
                                    value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="phone">Số điện thoại<span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" 
                            value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Địa chỉ giao hàng<span class="required">*</span></label>
                        <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Phương thức thanh toán</h2>
                    <div class="payment-method">
                        <input type="radio" name="payment_method" id="cod" value="cod" checked hidden>
                        <label for="cod">
                            <i class="fas fa-money-bill-wave fa-lg"></i>
                            Thanh toán khi nhận hàng (COD)
                        </label>

                        <input type="radio" name="payment_method" id="bank" value="bank" hidden>
                        <label for="bank">
                            <i class="fas fa-university fa-lg"></i>
                            Chuyển khoản ngân hàng
                        </label>

                        <div id="bankInfo" class="bank-info" style="display: none; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="color: #333; margin-bottom: 10px;">Thông tin chuyển khoản:</h4>
                            <p style="margin-bottom: 5px;"><strong>Ngân hàng:</strong> MB Bank</p>
                            <p style="margin-bottom: 5px;"><strong>Số tài khoản:</strong> 0931816175</p>
                            <p style="margin-bottom: 5px;"><strong>Chủ tài khoản:</strong> DINH PHUC THINH</p>
                            <p style="margin-bottom: 5px;"><strong>Nội dung:</strong> <span id="orderCode"></span></p>
                            <p style="color: #dc3545; margin-top: 10px;">* Vui lòng chuyển khoản đúng số tiền và nội dung để đơn hàng được xử lý nhanh nhất</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="order-details">
            <div class="form-section">
                <h2>Đơn hàng của bạn</h2>
                <div class="order-summary" id="orderSummary">
                    <!-- Đơn hàng sẽ được điền bằng JavaScript -->
                </div>
                <button type="submit" form="checkoutForm" class="place-order-btn">
                    Đặt hàng ngay
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Hiển thị đơn hàng từ localStorage
function displayOrderSummary() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const orderSummary = document.getElementById('orderSummary');
    let html = '';
    let total = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        html += `
            <div class="order-item">
                <div class="order-item-details">
                    <img src="${item.image_url}" alt="${item.name}" class="order-item-image">
                    <div>
                        <div class="order-item-name">${item.name}</div>
                        <div class="order-item-quantity">Số lượng: ${item.quantity}</div>
                    </div>
                </div>
                <div class="order-item-price">
                    ${new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    }).format(itemTotal)}
                </div>
            </div>
        `;
    });

    html += `
        <div class="order-total">
            <span>Tổng cộng:</span>
            <span>${new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(total)}</span>
        </div>
    `;

    orderSummary.innerHTML = html;
}

// Kiểm tra giỏ hàng trống
function checkEmptyCart() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    if (cart.length === 0) {
        Swal.fire({
            title: 'Giỏ hàng trống',
            text: 'Vui lòng thêm sản phẩm vào giỏ hàng trước khi thanh toán',
            icon: 'warning'
        }).then(() => {
            window.location.href = '<?php echo url("menu"); ?>';
        });
        return false;
    }
    return true;
}

// Validate form trước khi submit
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!checkEmptyCart()) {
        return;
    }

    // Thêm cart data vào form trước khi submit
    const cart = localStorage.getItem('cart');
    const cartInput = document.createElement('input');
    cartInput.type = 'hidden';
    cartInput.name = 'cart_data';
    cartInput.value = cart;
    this.appendChild(cartInput);

    this.submit();
});

// Hiển thị đơn hàng khi trang được load
document.addEventListener('DOMContentLoaded', function() {
    displayOrderSummary();
    checkEmptyCart();
});

// Hiển thị thông tin chuyển khoản khi chọn phương thức thanh toán
document.querySelectorAll('input[name="payment_method"]').forEach(input => {
    input.addEventListener('change', function() {
        const bankInfo = document.getElementById('bankInfo');
        if (this.value === 'bank') {
            bankInfo.style.display = 'block';
            // Tạo mã đơn hàng tạm thời
            const orderCode = 'DH' + Date.now();
            document.getElementById('orderCode').textContent = orderCode;
        } else {
            bankInfo.style.display = 'none';
        }
    });
});
</script>

<?php
require_once __DIR__ . '/../includes/config.php';
require_once 'includes/vietnam_cities.php';

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
            INSERT INTO orders (
                user_id, 
                total_price, 
                status, 
                shipping_city,
                shipping_district,
                shipping_address,
                created_at
            )
            VALUES (?, ?, 'pending', ?, ?, ?, NOW())
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
            $totalPrice,
            $_POST['shipping_city'],
            $_POST['shipping_district'],
            $_POST['shipping_address']
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
                <input type="hidden" name="cart_data" id="cart_data">
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

                    <div class="shipping-info">
                        <h2>Thông tin giao hàng</h2>
                        
                        <?php if($user['user_city'] && $user['user_district'] && $user['user_address']): ?>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="address_option" id="use_account_address" value="account_address" checked>
                                <label class="form-check-label" for="use_account_address">
                                    Sử dụng địa chỉ tài khoản
                                </label>
                                <div class="existing-address mt-2 ps-4">
                                    <p class="mb-1">
                                        <?php echo htmlspecialchars($user['user_address']); ?><br>
                                        <?php echo htmlspecialchars($user['user_district']); ?>, <?php echo htmlspecialchars($user['user_city']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="address_option" id="use_new_address" value="new_address">
                                <label class="form-check-label" for="use_new_address">
                                    Sử dụng địa chỉ khác
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div id="new_address_form" <?php echo ($user['user_city'] && $user['user_district'] && $user['user_address']) ? 'style="display: none;"' : ''; ?>>
                            <div class="mb-3">
                                <label for="shipping_city" class="form-label">Thành phố</label>
                                <select class="form-select" id="shipping_city" name="shipping_city" required>
                                    <option value="">Chọn thành phố</option>
                                    <?php foreach(array_keys($vietnam_cities) as $city): ?>
                                        <option value="<?php echo $city; ?>" <?php echo ($user['user_city'] == $city) ? 'selected' : ''; ?>>
                                            <?php echo $city; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="shipping_district" class="form-label">Quận/Huyện</label>
                                <select class="form-select" id="shipping_district" name="shipping_district" required>
                                    <option value="">Chọn quận/huyện</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="shipping_address" class="form-label">Địa chỉ cụ thể</label>
                                <input type="text" class="form-control" id="shipping_address" name="shipping_address" value="<?php echo htmlspecialchars($user['user_address'] ?? ''); ?>">
                            </div>
                        </div>
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
    const cartDataInput = document.getElementById('cart_data');
    let html = '';
    let total = 0;

    if (cart.length === 0) {
        html = '<div class="alert alert-info">Giỏ hàng của bạn đang trống</div>';
        document.querySelector('.place-order-btn').disabled = true;
    } else {
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

        // Lưu dữ liệu giỏ hàng vào input hidden
        cartDataInput.value = JSON.stringify(cart);
    }

    orderSummary.innerHTML = html;
}

// Gọi hàm hiển thị khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    displayOrderSummary();
});

// Xử lý chọn địa chỉ
document.querySelectorAll('input[name="address_option"]').forEach(input => {
    input.addEventListener('change', function() {
        const newAddressForm = document.getElementById('new_address_form');
        if (this.value === 'new_address') {
            newAddressForm.style.display = 'block';
            // Reset form fields
            document.getElementById('shipping_city').value = '';
            document.getElementById('shipping_district').value = '';
            document.getElementById('shipping_address').value = '';
        } else {
            newAddressForm.style.display = 'none';
            // Fill form with account address
            document.getElementById('shipping_city').value = '<?php echo addslashes($user['user_city'] ?? ''); ?>';
            document.getElementById('shipping_district').value = '<?php echo addslashes($user['user_district'] ?? ''); ?>';
            document.getElementById('shipping_address').value = '<?php echo addslashes($user['user_address'] ?? ''); ?>';
        }
    });
});

// Xử lý chọn thành phố
document.getElementById('shipping_city').addEventListener('change', function() {
    const city = this.value;
    const districtSelect = document.getElementById('shipping_district');
    
    if (city) {
        // Lấy danh sách quận/huyện từ API
        fetch(`api/get_districts.php?city=${encodeURIComponent(city)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
                    data.districts.forEach(district => {
                        const selected = district === '<?php echo addslashes($user['user_district'] ?? ''); ?>' ? 'selected' : '';
                        districtSelect.innerHTML += `<option value="${district}" ${selected}>${district}</option>`;
                    });
                    districtSelect.disabled = false;
                }
            });
    } else {
        districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
        districtSelect.disabled = true;
    }
});

// Trigger city change event if city is pre-selected
if (document.getElementById('shipping_city').value) {
    document.getElementById('shipping_city').dispatchEvent(new Event('change'));
}

// Xử lý hiển thị thông tin ngân hàng
document.querySelectorAll('input[name="payment_method"]').forEach(input => {
    input.addEventListener('change', function() {
        const bankInfo = document.getElementById('bankInfo');
        if (this.value === 'bank') {
            bankInfo.style.display = 'block';
            // Hiển thị số tiền cần chuyển khoản
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            document.getElementById('orderCode').textContent = new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(total);
        } else {
            bankInfo.style.display = 'none';
        }
    });
});
</script>
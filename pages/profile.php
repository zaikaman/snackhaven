<?php
require_once __DIR__ . '/../includes/config.php';
require_once 'includes/vietnam_cities.php';

// Kiểm tra đăng nhập
check_login();

// Lấy thông tin user
$user = get_logged_user();

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $userCity = trim($_POST['user_city']);
    $userDistrict = trim($_POST['user_district']);
    $userAddress = trim($_POST['user_address']);
    
    try {
        $updateStmt = $pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, phone = ?, 
                user_city = ?, user_district = ?, user_address = ?
            WHERE id = ?
        ");
        
        $updateStmt->execute([
            $firstName, $lastName, $phone,
            $userCity, $userDistrict, $userAddress,
            $_SESSION['user_id']
        ]);
        
        // Hiển thị thông báo thành công
        $successMessage = "Cập nhật thông tin thành công!";
        
        // Cập nhật lại thông tin user
        $user = get_logged_user();
    } catch (PDOException $e) {
        $errorMessage = "Có lỗi xảy ra khi cập nhật thông tin: " . $e->getMessage();
    }
}

// Lấy danh sách đơn hàng
$orderStmt = $pdo->prepare("
    SELECT * FROM orders 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$orderStmt->execute([$_SESSION['user_id']]);
$orders = $orderStmt->fetchAll();

// Lấy chi tiết đơn hàng nếu có order_id được truyền vào
$orderDetails = null;
if (isset($_GET['order_id'])) {
    $orderDetailStmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name, p.image_url
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $orderDetailStmt->execute([$_GET['order_id']]);
    $orderDetails = $orderDetailStmt->fetchAll();

    // Lấy thông tin đơn hàng
    $orderInfoStmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $orderInfoStmt->execute([$_GET['order_id'], $_SESSION['user_id']]);
    $orderInfo = $orderInfoStmt->fetch();
}
?>

<style>
.profile-container {
    max-width: 800px;
    margin: 120px auto 50px;
    padding: 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.profile-header {
    text-align: center;
    margin-bottom: 30px;
}

.profile-header h1 {
    color: #333;
    font-size: 2rem;
    margin-bottom: 10px;
}

.profile-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #666;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.form-group input:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    height: 100px;
    resize: vertical;
}

.save-btn {
    background: #ff6b6b;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.3s;
}

.save-btn:hover {
    background: #ff5252;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.profile-tabs {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}

.profile-tab {
    padding: 10px 20px;
    border: none;
    background: none;
    font-size: 1.1rem;
    color: #666;
    cursor: pointer;
    position: relative;
}

.profile-tab.active {
    color: #ff6b6b;
    font-weight: 500;
}

.profile-tab.active::after {
    content: '';
    position: absolute;
    bottom: -11px;
    left: 0;
    width: 100%;
    height: 3px;
    background: #ff6b6b;
}

.profile-content > div {
    display: none;
}

.profile-content > div.active {
    display: block;
}

.orders-list {
    margin-top: 20px;
}

.order-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    cursor: pointer;
    transition: transform 0.2s;
}

.order-card:hover {
    transform: translateY(-2px);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.order-id {
    font-weight: 600;
    color: #333;
}

.order-date {
    color: #666;
    font-size: 0.9rem;
}

.order-status {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.9rem;
    font-weight: 500;
}

.order-status.pending {
    background: #fff3cd;
    color: #856404;
}

.order-status.confirmed {
    background: #cce5ff;
    color: #004085;
}

.order-status.delivered {
    background: #d4edda;
    color: #155724;
}

.order-status.cancelled {
    background: #f8d7da;
    color: #721c24;
}

.order-total {
    font-size: 1.1rem;
    font-weight: 600;
    color: #ff6b6b;
}

.order-details-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}

.order-details-content {
    position: relative;
    width: 90%;
    max-width: 800px;
    margin: 50px auto;
    background: white;
    border-radius: 10px;
    padding: 30px;
    max-height: 80vh;
    overflow-y: auto;
}

.close-modal {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 1.5rem;
    color: #666;
    cursor: pointer;
    border: none;
    background: none;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.order-item-details {
    flex-grow: 1;
}

.order-item-name {
    font-weight: 500;
    color: #333;
    margin-bottom: 5px;
}

.order-item-price {
    color: #ff6b6b;
    font-weight: 600;
}

.order-item-quantity {
    color: #666;
    font-size: 0.9rem;
}
</style>

<div class="profile-container">
    <div class="profile-header">
        <h1>Thông tin cá nhân</h1>
        <p>Quản lý thông tin và đơn hàng của bạn</p>
    </div>

    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>

    <div class="profile-tabs">
        <button class="profile-tab active" onclick="showTab('info')">Thông tin cá nhân</button>
        <button class="profile-tab" onclick="showTab('orders')">Lịch sử đơn hàng</button>
    </div>

    <div class="profile-content">
        <div id="info-tab" class="active">
            <form method="POST" class="profile-form">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="first_name">Họ</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="last_name">Tên</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="user_city">Thành phố</label>
                    <select class="form-select" id="user_city" name="user_city">
                        <option value="">Chọn thành phố</option>
                        <?php foreach(array_keys($vietnam_cities) as $city): ?>
                            <option value="<?php echo $city; ?>" <?php echo ($user['user_city'] == $city) ? 'selected' : ''; ?>>
                                <?php echo $city; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="user_district">Quận/Huyện</label>
                    <select class="form-select" id="user_district" name="user_district">
                        <option value="">Chọn quận/huyện</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="user_address">Địa chỉ cụ thể</label>
                    <input type="text" id="user_address" name="user_address" value="<?php echo htmlspecialchars($user['user_address'] ?? ''); ?>">
                </div>

                <div class="form-group full-width">
                    <button type="submit" name="update_profile" class="save-btn">Lưu thay đổi</button>
                </div>
            </form>
        </div>

        <div id="orders-tab">
            <div class="orders-list">
                <?php if (empty($orders)): ?>
                    <div class="text-center">
                        <p>Bạn chưa có đơn hàng nào.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card" onclick="showOrderDetails(<?php echo $order['id']; ?>)">
                            <div class="order-header">
                                <div class="order-id">Đơn hàng #<?php echo $order['id']; ?></div>
                                <div class="order-date">
                                    <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                </div>
                            </div>
                            <div class="order-info">
                                <div class="order-status <?php echo $order['status']; ?>">
                                    <?php 
                                        switch($order['status']) {
                                            case 'pending':
                                                echo 'Chưa xác nhận';
                                                break;
                                            case 'confirmed':
                                                echo 'Đã xác nhận';
                                                break;
                                            case 'delivered':
                                                echo 'Đã giao';
                                                break;
                                            case 'cancelled':
                                                echo 'Đã hủy';
                                                break;
                                        }
                                    ?>
                                </div>
                                <div class="order-total">
                                    <?php echo number_format($order['total_price'], 0, ',', '.'); ?>₫
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal chi tiết đơn hàng -->
<div id="orderDetailsModal" class="order-details-modal">
    <div class="order-details-content">
        <button class="close-modal" onclick="closeOrderDetails()">&times;</button>
        <h2>Chi tiết đơn hàng #<span id="modalOrderId"></span></h2>
        <div id="orderDetailsContent"></div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.profile-content > div').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.profile-tab').forEach(tab => {
        tab.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    event.currentTarget.classList.add('active');
}

function showOrderDetails(orderId) {
    console.log('Showing order details for order:', orderId);
    
    // Hiển thị modal trước để người dùng thấy có phản hồi
    const modal = document.getElementById('orderDetailsModal');
    const content = document.getElementById('orderDetailsContent');
    document.getElementById('modalOrderId').textContent = orderId;
    content.innerHTML = '<div class="text-center">Đang tải...</div>';
    modal.style.display = 'block';

    // Fetch order details using AJAX
    fetch('<?php echo url("api/get_order_details.php"); ?>?order_id=' + orderId)
        .then(response => {
            console.log('API Response:', response);
            return response.json();
        })
        .then(data => {
            console.log('Order details:', data);
            if (data.success) {
                let html = `
                    <div class="order-info">
                        <p>Ngày đặt: ${new Date(data.order.created_at).toLocaleString('vi-VN')}</p>
                        <p>Trạng thái: <span class="order-status ${data.order.status}">${data.order.status_text}</span></p>
                    </div>
                    <div class="order-items">
                `;

                data.items.forEach(item => {
                    html += `
                        <div class="order-item">
                            <img src="${item.image_url}" alt="${item.product_name}" class="order-item-image">
                            <div class="order-item-details">
                                <div class="order-item-name">${item.product_name}</div>
                                <div class="order-item-quantity">Số lượng: ${item.quantity}</div>
                                <div class="order-item-price">${new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(item.price)}</div>
                            </div>
                        </div>
                    `;
                });

                html += `
                    </div>
                    <div class="order-total">
                        Tổng cộng: ${new Intl.NumberFormat('vi-VN', {
                            style: 'currency',
                            currency: 'VND'
                        }).format(data.order.total_price)}
                    </div>
                `;

                content.innerHTML = html;
            } else {
                content.innerHTML = `<div class="alert alert-danger">${data.error || 'Có lỗi xảy ra khi tải chi tiết đơn hàng'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error fetching order details:', error);
            content.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra khi tải chi tiết đơn hàng</div>';
        });
}

function closeOrderDetails() {
    document.getElementById('orderDetailsModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('orderDetailsModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Show SweetAlert2 messages if they exist
<?php if (isset($successMessage)): ?>
Swal.fire({
    title: 'Thành công!',
    text: '<?php echo $successMessage; ?>',
    icon: 'success',
    timer: 2000,
    showConfirmButton: false
});
<?php endif; ?>

<?php if (isset($errorMessage)): ?>
Swal.fire({
    title: 'Lỗi!',
    text: '<?php echo $errorMessage; ?>',
    icon: 'error'
});
<?php endif; ?>

document.getElementById('user_city').addEventListener('change', function() {
    const city = this.value;
    const districtSelect = document.getElementById('user_district');
    
    if (city) {
        // Lấy danh sách quận/huyện từ API
        fetch(`api/get_districts.php?city=${encodeURIComponent(city)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
                    data.districts.forEach(district => {
                        districtSelect.innerHTML += `<option value="${district}">${district}</option>`;
                    });
                    districtSelect.disabled = false;
                }
            });
    } else {
        districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
        districtSelect.disabled = true;
    }
});
</script>

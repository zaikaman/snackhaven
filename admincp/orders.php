<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/vietnam_cities.php';

// Cấu hình múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Xử lý cập nhật trạng thái đơn hàng
if(isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    try {
        // Lấy trạng thái hiện tại của đơn hàng
        $stmt = $pdo->prepare("SELECT status FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        $current_status = $stmt->fetchColumn();
        
        // Kiểm tra thứ tự trạng thái
        $status_order = [
            'pending' => 1,
            'confirmed' => 2, 
            'delivered' => 3,
            'cancelled' => 4
        ];
        
        if (!isset($status_order[$new_status]) || !isset($status_order[$current_status])) {
            throw new Exception("Trạng thái không hợp lệ");
        }
        
        if ($status_order[$new_status] < $status_order[$current_status]) {
            throw new Exception("Không thể cập nhật ngược trạng thái đơn hàng");
        }

        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $success = "Đã cập nhật trạng thái đơn hàng";
    } catch(Exception $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// Xử lý các tham số lọc
$status = isset($_GET['status']) ? $_GET['status'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$district = isset($_GET['district']) ? $_GET['district'] : '';
$city = isset($_GET['city']) ? $_GET['city'] : '';

// Xây dựng câu truy vấn với điều kiện lọc
$where = [];
$params = [];

if (!empty($status)) {
    $where[] = 'o.status = :status';
    $params[':status'] = $status;
}

if (!empty($start_date)) {
    $where[] = 'DATE(o.created_at) >= :start_date';
    $params[':start_date'] = $start_date;
}

if (!empty($end_date)) {
    $where[] = 'DATE(o.created_at) <= :end_date';
    $params[':end_date'] = $end_date;
}

if (!empty($district)) {
    $where[] = 'o.shipping_district = :district';
    $params[':district'] = $district;
}

if (!empty($city)) {
    $where[] = 'o.shipping_city = :city';
    $params[':city'] = $city;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Lấy danh sách đơn hàng với điều kiện lọc
$query = "SELECT o.*, u.first_name, u.last_name, u.email, u.phone,
          CONCAT(u.first_name, ' ', u.last_name) as customer_name 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          $whereClause 
          ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$page_title = 'Quản lý Đơn hàng';
$current_page = 'orders';
require_once 'includes/header.php';
?>

    <div class="container-fluid py-4">
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản lý Đơn hàng</h2>
        </div>

        <!-- Form lọc đơn hàng -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái đơn hàng</label>
                        <select class="form-select" name="status">
                            <option value="">Tất cả</option>
                            <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Chưa xác nhận</option>
                            <option value="confirmed" <?php echo $status == 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                            <option value="delivered" <?php echo $status == 'delivered' ? 'selected' : ''; ?>>Đã giao</option>
                            <option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Thành phố</label>
                        <select class="form-select" name="city" id="filter_city">
                            <option value="">Tất cả</option>
                            <?php foreach(array_keys($vietnam_cities) as $city_name): ?>
                                <option value="<?php echo $city_name; ?>" <?php echo $city == $city_name ? 'selected' : ''; ?>>
                                    <?php echo $city_name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quận/Huyện</label>
                        <select class="form-select" name="district" id="filter_district" <?php echo empty($city) ? 'disabled' : ''; ?>>
                            <option value="">Tất cả</option>
                            <?php if (!empty($city) && isset($vietnam_cities[$city])): ?>
                                <?php foreach($vietnam_cities[$city] as $district_name): ?>
                                    <option value="<?php echo $district_name; ?>" <?php echo $district == $district_name ? 'selected' : ''; ?>>
                                        <?php echo $district_name; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Khách hàng</th>
                                <th>Liên hệ</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày đặt</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo $order['customer_name']; ?></td>
                                <td>
                                    Email: <?php echo $order['email']; ?><br>
                                    SĐT: <?php echo $order['phone']; ?>
                                </td>
                                <td><?php echo number_format($order['total_price']); ?>đ</td>
                                <td>
                                    <span class="badge <?php 
                                        switch($order['status']) {
                                            case 'pending':
                                                echo 'bg-warning';
                                                break;
                                            case 'confirmed':
                                                echo 'bg-info';
                                                break;
                                            case 'delivered':
                                                echo 'bg-success';
                                                break;
                                            case 'cancelled':
                                                echo 'bg-danger';
                                                break;
                                        }
                                    ?>">
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
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $orderDate = new DateTime($order['created_at']);
                                    echo $orderDate->format('d/m/Y H:i');
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info view-order" 
                                            data-id="<?php echo $order['id']; ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#orderDetailModal">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <?php if($order['status'] != 'delivered' && $order['status'] != 'cancelled'): ?>
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php if($order['status'] == 'pending'): ?>
                                            <li>
                                                <form method="POST" class="dropdown-item">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <input type="hidden" name="current_status" value="<?php echo $order['status']; ?>">
                                                    <input type="hidden" name="status" value="confirmed">
                                                    <button type="submit" name="update_status" class="btn btn-link p-0">
                                                        Xác nhận đơn hàng
                                                    </button>
                                                </form>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php if($order['status'] == 'confirmed'): ?>
                                            <li>
                                                <form method="POST" class="dropdown-item">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <input type="hidden" name="current_status" value="<?php echo $order['status']; ?>">
                                                    <input type="hidden" name="status" value="delivered">
                                                    <button type="submit" name="update_status" class="btn btn-link p-0">
                                                        Đánh dấu đã giao
                                                    </button>
                                                </form>
                                            </li>
                                            <?php endif; ?>

                                            <li>
                                                <form method="POST" class="dropdown-item">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <input type="hidden" name="current_status" value="<?php echo $order['status']; ?>">
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" name="update_status" class="btn btn-link p-0 text-danger"
                                                            onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                                        Hủy đơn hàng
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chi tiết đơn hàng -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="orderDetail">
                        <!-- Nội dung chi tiết đơn hàng sẽ được load bằng AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Xử lý khi click nút xem chi tiết
    document.querySelectorAll('.view-order').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.id;
            fetch(`get_order.php?id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    let html = `
                        <div class="mb-4">
                            <h6>Thông tin khách hàng:</h6>
                            <p>Tên: ${data.customer.name}<br>
                               Email: ${data.customer.email}<br>
                               SĐT: ${data.customer.phone}</p>
                        </div>
                        <div class="mb-4">
                            <h6>Địa chỉ giao hàng:</h6>
                            <p>
                                ${data.order.shipping_address}<br>
                                ${data.order.shipping_district}, ${data.order.shipping_city}
                            </p>
                        </div>
                        <div class="mb-4">
                            <h6>Chi tiết đơn hàng:</h6>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Đơn giá</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                    
                    data.items.forEach(item => {
                        html += `
                            <tr>
                                <td>${item.product_name}</td>
                                <td>${item.quantity}</td>
                                <td>${new Intl.NumberFormat('vi-VN').format(item.price)}đ</td>
                                <td>${new Intl.NumberFormat('vi-VN').format(item.price * item.quantity)}đ</td>
                            </tr>`;
                    });
                    
                    html += `
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                        <td><strong>${new Intl.NumberFormat('vi-VN').format(data.order.total_price)}đ</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>`;
                    
                    document.getElementById('orderDetail').innerHTML = html;
                });
        });
    });

    document.getElementById('filter_city').addEventListener('change', function() {
        const city = this.value;
        const districtSelect = document.getElementById('filter_district');
        
        if (city) {
            // Lấy danh sách quận/huyện từ API
            fetch(`../api/get_districts.php?city=${encodeURIComponent(city)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        districtSelect.innerHTML = '<option value="">Tất cả</option>';
                        data.districts.forEach(district => {
                            districtSelect.innerHTML += `<option value="${district}">${district}</option>`;
                        });
                        districtSelect.disabled = false;
                    }
                });
        } else {
            districtSelect.innerHTML = '<option value="">Tất cả</option>';
            districtSelect.disabled = true;
        }
    });
    </script>
</body>
</html> 
<?php
session_start();
require_once '../includes/config.php';

// Cấu hình múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Xử lý các tham số lọc
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : 'all';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'quantity';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';

// Lấy danh sách categories
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

// Lấy thống kê cơ bản
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$totalProducts = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
$pendingOrders = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM contact_messages WHERE status = 'new'");
$newMessages = $stmt->fetch()['total'];

// Xây dựng query cho thống kê sản phẩm bán chạy
$query = "SELECT p.id, p.name, p.price, c.name as category_name,
          COALESCE(SUM(CASE WHEN o.created_at BETWEEN :start_date AND :end_date AND o.status = 'delivered' THEN oi.quantity ELSE 0 END), 0) as total_quantity,
          COALESCE(SUM(CASE WHEN o.created_at BETWEEN :start_date AND :end_date AND o.status = 'delivered' THEN oi.quantity * oi.price ELSE 0 END), 0) as total_revenue
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          LEFT JOIN order_items oi ON p.id = oi.product_id
          LEFT JOIN orders o ON oi.order_id = o.id ";

if($category_id != 'all') {
    $query .= " WHERE p.category_id = :category_id";
}

$query .= " GROUP BY p.id, p.name, p.price, c.name
           ORDER BY " . ($sort_by == 'revenue' ? 'total_revenue' : 'total_quantity') . " $sort_order, p.name ASC
           LIMIT 10";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
if($category_id != 'all') {
    $stmt->bindParam(':category_id', $category_id);
}
$stmt->execute();
$topProducts = $stmt->fetchAll();

// Tính tổng doanh thu trong khoảng thời gian
$query = "SELECT COALESCE(SUM(oi.quantity * oi.price), 0) as total_revenue
          FROM orders o 
          LEFT JOIN order_items oi ON o.id = oi.order_id 
          WHERE o.created_at BETWEEN :start_date AND :end_date
          AND o.status = 'delivered'";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$totalRevenue = $stmt->fetch()['total_revenue'] ?? 0;

// Xây dựng query cho thống kê khách hàng mua nhiều
$query = "SELECT u.id, u.first_name, u.last_name, u.email, u.phone,
          COUNT(DISTINCT o.id) as total_orders,
          SUM(o.total_price) as total_spent
          FROM users u
          JOIN orders o ON u.id = o.user_id
          WHERE o.status = 'delivered'
          AND o.created_at BETWEEN :start_date AND :end_date
          GROUP BY u.id, u.first_name, u.last_name, u.email, u.phone
          ORDER BY total_spent DESC
          LIMIT 5";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$topCustomers = $stmt->fetchAll();

// Lấy chi tiết đơn hàng của mỗi khách hàng trong khoảng thời gian
$customerOrders = [];
foreach ($topCustomers as $customer) {
    $query = "SELECT o.id, o.total_price, o.status, o.created_at
              FROM orders o
              WHERE o.user_id = :user_id
              AND o.status = 'delivered'
              AND o.created_at BETWEEN :start_date AND :end_date
              ORDER BY o.created_at DESC";
              
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $customer['id']);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->execute();
    $customerOrders[$customer['id']] = $stmt->fetchAll();
}

$page_title = 'Dashboard';
$current_page = 'dashboard';
require_once 'includes/header.php';
?>

    <div class="container-fluid py-4">
        <!-- Thống kê tổng quan -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Tổng sản phẩm</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalProducts; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-box fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Đơn hàng chờ xử lý</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pendingOrders; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-cart fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Tổng người dùng</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalUsers; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-people fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Tin nhắn mới</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $newMessages; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-envelope fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form lọc thống kê -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thống kê khách hàng mua nhiều</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Lọc</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hiển thị tổng doanh thu -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Tổng doanh thu: <?php echo number_format($totalRevenue, 0, ',', '.'); ?> VNĐ</h5>
                <p class="card-text">Thời gian: <?php echo date('d/m/Y', strtotime($start_date)); ?> - <?php echo date('d/m/Y', strtotime($end_date)); ?></p>
            </div>
        </div>

        <!-- Bảng thống kê khách hàng -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top 5 khách hàng mua nhiều nhất</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($topCustomers)): ?>
                    <?php foreach ($topCustomers as $customer): ?>
                        <div class="customer-stats mb-4">
                            <h6 class="border-bottom pb-2">
                                <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>
                                <small class="text-muted">
                                    (<?php echo htmlspecialchars($customer['email']); ?> - 
                                    <?php echo htmlspecialchars($customer['phone']); ?>)
                                </small>
                            </h6>
                            <div class="row mb-2">
                                <div class="col">
                                    <strong>Tổng số đơn:</strong> <?php echo $customer['total_orders']; ?>
                                </div>
                                <div class="col">
                                    <strong>Tổng chi tiêu:</strong> <?php echo number_format($customer['total_spent'], 0, ',', '.'); ?> VNĐ
                                </div>
                            </div>
                            <?php if (!empty($customerOrders[$customer['id']])): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Mã đơn hàng</th>
                                                <th>Ngày đặt</th>
                                                <th>Tổng tiền</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($customerOrders[$customer['id']] as $order): ?>
                                                <tr>
                                                    <td>#<?php echo $order['id']; ?></td>
                                                    <td><?php 
                                                        $orderDate = new DateTime($order['created_at']);
                                                        echo $orderDate->format('d/m/Y H:i'); 
                                                    ?></td>
                                                    <td><?php echo number_format($order['total_price'], 0, ',', '.'); ?> VNĐ</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info view-order" 
                                                                data-id="<?php echo $order['id']; ?>"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#orderDetailModal">
                                                            <i class="bi bi-eye"></i> Xem chi tiết
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        Không có dữ liệu thống kê trong khoảng thời gian này
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Chi tiết đơn hàng -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1">
        <div class="modal-dialog">
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
    // Xử lý khi click nút xem chi tiết đơn hàng
    document.querySelectorAll('.view-order').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.id;
            fetch(`get_order.php?id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = `
                            <div class="mb-3">
                                <h6>Thông tin khách hàng:</h6>
                                <p>Họ tên: ${data.customer.name}<br>
                                   Email: ${data.customer.email}<br>
                                   SĐT: ${data.customer.phone}</p>
                            </div>
                            <div class="mb-3">
                                <h6>Địa chỉ giao hàng:</h6>
                                <p>${data.order.shipping_address ? data.order.shipping_address : 'Chưa có địa chỉ'}${
                                    data.order.shipping_district || data.order.shipping_city ? 
                                    '<br>' + 
                                    [
                                        data.order.shipping_district,
                                        data.order.shipping_city
                                    ].filter(Boolean).join(', ') 
                                    : ''
                                }</p>
                            </div>
                            <div class="mb-3">
                                <h6>Chi tiết đơn hàng:</h6>
                                <table class="table table-sm">
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
                                    <td>${new Intl.NumberFormat('vi-VN').format(item.quantity * item.price)}đ</td>
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
                    }
                });
        });
    });
    </script>
</body>
</html> 
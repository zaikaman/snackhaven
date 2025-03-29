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
          SUM(oi.quantity * oi.price) as total_spent
          FROM users u
          JOIN orders o ON u.id = o.user_id
          JOIN order_items oi ON o.id = oi.order_id
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
                        <input type="text" class="form-control" id="start_date_display" readonly
                               value="<?php echo date('d/m/Y', strtotime($start_date)); ?>">
                        <input type="hidden" name="start_date" id="start_date_hidden" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Đến ngày</label>
                        <input type="text" class="form-control" id="end_date_display" readonly
                               value="<?php echo date('d/m/Y', strtotime($end_date)); ?>">
                        <input type="hidden" name="end_date" id="end_date_hidden" value="<?php echo $end_date; ?>">
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
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Thông tin liên hệ</th>
                                    <th>Tổng số đơn</th>
                                    <th>Tổng chi tiêu</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topCustomers as $customer): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                                        <td>
                                            Email: <?php echo htmlspecialchars($customer['email']); ?><br>
                                            SĐT: <?php echo htmlspecialchars($customer['phone']); ?>
                                        </td>
                                        <td><?php echo $customer['total_orders']; ?></td>
                                        <td><?php echo number_format($customer['total_spent'], 0, ',', '.'); ?> VNĐ</td>
                                        <td>
                                            <button class="btn btn-sm btn-info view-customer-orders" 
                                                    data-id="<?php echo $customer['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#orderDetailModal">
                                                <i class="bi bi-eye"></i> Xem đơn hàng
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Danh sách đơn hàng của khách hàng: <span id="customerName"></span></h5>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <script>
    $(document).ready(function(){
        // Khởi tạo datepicker với định dạng dd/mm/yyyy
        $('#start_date_display, #end_date_display').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'vi'
        });

        // Xử lý khi ngày được chọn
        $('#start_date_display').on('changeDate', function(e) {
            const date = e.date;
            const formattedDate = date.getFullYear() + '-' + 
                                String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                                String(date.getDate()).padStart(2, '0');
            $('#start_date_hidden').val(formattedDate);
        });

        $('#end_date_display').on('changeDate', function(e) {
            const date = e.date;
            const formattedDate = date.getFullYear() + '-' + 
                                String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                                String(date.getDate()).padStart(2, '0');
            $('#end_date_hidden').val(formattedDate);
        });
    });

    // Xử lý khi click nút xem đơn hàng của khách hàng
    document.querySelectorAll('.view-customer-orders').forEach(button => {
        button.addEventListener('click', function() {
            const customerId = this.dataset.id;
            const customerName = this.dataset.name;
            document.getElementById('customerName').textContent = customerName;
            
            fetch(`get_customer_orders.php?user_id=${customerId}&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = `
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Mã đơn hàng</th>
                                            <th>Ngày đặt</th>
                                            <th>Địa chỉ giao hàng</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Chi tiết</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                        
                        data.orders.forEach(order => {
                            const status = {
                                'pending': 'Chưa xác nhận',
                                'confirmed': 'Đã xác nhận',
                                'delivered': 'Đã giao',
                                'cancelled': 'Đã hủy'
                            };
                            
                            // Chuyển đổi định dạng ngày tháng
                            const orderDate = new Date(order.created_at);
                            const formattedDate = orderDate.getDate().toString().padStart(2, '0') + '/' +
                                                (orderDate.getMonth() + 1).toString().padStart(2, '0') + '/' +
                                                orderDate.getFullYear() + ' ' +
                                                orderDate.getHours().toString().padStart(2, '0') + ':' +
                                                orderDate.getMinutes().toString().padStart(2, '0');
                            
                            html += `
                                <tr>
                                    <td>#${order.id}</td>
                                    <td>${formattedDate}</td>
                                    <td>${order.shipping_address}<br>${order.shipping_district}, ${order.shipping_city}</td>
                                    <td>${new Intl.NumberFormat('vi-VN').format(order.total_price)}đ</td>
                                    <td>${status[order.status]}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-order-items" 
                                                data-id="${order.id}">
                                            Chi tiết
                                        </button>
                                    </td>
                                </tr>`;
                        });
                        
                        html += `
                                    </tbody>
                                </table>
                            </div>`;
                        
                        document.getElementById('orderDetail').innerHTML = html;
                        
                        // Thêm event listener cho các nút xem chi tiết đơn hàng
                        document.querySelectorAll('.view-order-items').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const orderId = this.dataset.id;
                                fetch(`get_order.php?id=${orderId}`)
                                    .then(response => response.json())
                                    .then(orderData => {
                                        if (orderData.success) {
                                            let itemsHtml = `
                                                <h6 class="mt-4">Chi tiết đơn hàng #${orderId}:</h6>
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
                                            
                                            orderData.items.forEach(item => {
                                                itemsHtml += `
                                                    <tr>
                                                        <td>${item.product_name}</td>
                                                        <td>${item.quantity}</td>
                                                        <td>${new Intl.NumberFormat('vi-VN').format(item.price)}đ</td>
                                                        <td>${new Intl.NumberFormat('vi-VN').format(item.quantity * item.price)}đ</td>
                                                    </tr>`;
                                            });
                                            
                                            itemsHtml += `
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                                            <td><strong>${new Intl.NumberFormat('vi-VN').format(orderData.order.total_price)}đ</strong></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>`;
                                            
                                            // Thêm chi tiết đơn hàng vào dưới bảng đơn hàng
                                            const orderRow = this.closest('tr');
                                            const detailRow = orderRow.nextElementSibling;
                                            if (detailRow && detailRow.classList.contains('order-items-detail')) {
                                                detailRow.remove();
                                            } else {
                                                const newRow = document.createElement('tr');
                                                newRow.classList.add('order-items-detail');
                                                newRow.innerHTML = `<td colspan="6">${itemsHtml}</td>`;
                                                orderRow.parentNode.insertBefore(newRow, orderRow.nextSibling);
                                            }
                                        }
                                    });
                            });
                        });
                    }
                });
        });
    });
    </script>
</body>
</html> 
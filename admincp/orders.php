<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Xử lý cập nhật trạng thái đơn hàng
if(isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        $success = "Đã cập nhật trạng thái đơn hàng";
    } catch(PDOException $e) {
        $error = "Lỗi khi cập nhật trạng thái: " . $e->getMessage();
    }
}

// Lấy danh sách đơn hàng
$stmt = $pdo->query("SELECT o.*, u.first_name, u.last_name, u.email, u.phone,
                     CONCAT(u.first_name, ' ', u.last_name) as customer_name 
                     FROM orders o 
                     LEFT JOIN users u ON o.user_id = u.id 
                     ORDER BY o.created_at DESC");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đơn hàng - Snack Haven</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Snack Haven Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="orders.php">Đơn hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Người dùng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="messages.php">Tin nhắn</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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
                                    <span class="badge <?php echo $order['status'] == 'pending' ? 'bg-warning' : 'bg-success'; ?>">
                                        <?php echo $order['status'] == 'pending' ? 'Chờ xử lý' : 'Đã xử lý'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info view-order" 
                                            data-id="<?php echo $order['id']; ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#orderDetailModal">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if($order['status'] == 'pending'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <input type="hidden" name="status" value="processed">
                                        <button type="submit" name="update_status" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
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
    </script>
</body>
</html> 
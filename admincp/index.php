<?php
session_start();
require_once '../includes/config.php';

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
          COALESCE(SUM(CASE WHEN o.created_at BETWEEN :start_date AND :end_date AND o.status = 'processed' THEN oi.quantity ELSE 0 END), 0) as total_quantity,
          COALESCE(SUM(CASE WHEN o.created_at BETWEEN :start_date AND :end_date AND o.status = 'processed' THEN oi.quantity * oi.price ELSE 0 END), 0) as total_revenue
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
          AND o.status = 'processed'";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$totalRevenue = $stmt->fetch()['total_revenue'] ?? 0;

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
                <h5 class="mb-0">Thống kê doanh thu và sản phẩm</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Danh mục</label>
                        <select class="form-select" name="category_id">
                            <option value="all">Tất cả</option>
                            <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sắp xếp theo</label>
                        <select class="form-select" name="sort_by">
                            <option value="quantity" <?php echo $sort_by == 'quantity' ? 'selected' : ''; ?>>Số lượng bán</option>
                            <option value="revenue" <?php echo $sort_by == 'revenue' ? 'selected' : ''; ?>>Doanh thu</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Thứ tự</label>
                        <select class="form-select" name="sort_order">
                            <option value="DESC" <?php echo $sort_order == 'DESC' ? 'selected' : ''; ?>>Giảm dần</option>
                            <option value="ASC" <?php echo $sort_order == 'ASC' ? 'selected' : ''; ?>>Tăng dần</option>
                        </select>
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

        <!-- Bảng thống kê sản phẩm -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top sản phẩm bán chạy</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Số lượng bán</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($topProducts as $product): ?>
                            <tr>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['category_name']; ?></td>
                                <td><?php echo number_format($product['total_quantity']); ?></td>
                                <td><?php echo number_format($product['total_revenue'], 0, ',', '.'); ?> VNĐ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
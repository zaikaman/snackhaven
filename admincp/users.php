<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Xử lý khóa/mở khóa tài khoản
if(isset($_POST['toggle_status'])) {
    $user_id = $_POST['user_id'];
    $active = $_POST['active'];
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET active = ? WHERE id = ?");
        $stmt->execute([$active, $user_id]);
        $success = $active ? "Đã mở khóa tài khoản" : "Đã khóa tài khoản";
    } catch(PDOException $e) {
        $error = "Lỗi khi cập nhật trạng thái: " . $e->getMessage();
    }
}

// Lấy danh sách người dùng
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng - Snack Haven</title>
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
                        <a class="nav-link" href="orders.php">Đơn hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="users.php">Người dùng</a>
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
            <h2>Quản lý Người dùng</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên người dùng</th>
                                <th>Email</th>
                                <th>Thông tin</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    Họ tên: <?php echo $user['first_name'] . ' ' . $user['last_name']; ?><br>
                                    SĐT: <?php echo $user['phone']; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $user['role'] == 'admin' ? 'bg-danger' : 'bg-primary'; ?>">
                                        <?php echo $user['role'] == 'admin' ? 'Admin' : 'Khách hàng'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $user['verified'] ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo $user['verified'] ? 'Đã xác thực' : 'Chưa xác thực'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info view-user" 
                                            data-id="<?php echo $user['id']; ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#userDetailModal">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if($user['role'] != 'admin'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <input type="hidden" name="active" value="<?php echo $user['active'] ? '0' : '1'; ?>">
                                        <button type="submit" name="toggle_status" 
                                                class="btn btn-sm <?php echo $user['active'] ? 'btn-warning' : 'btn-success'; ?>"
                                                onclick="return confirm('Bạn có chắc muốn <?php echo $user['active'] ? 'khóa' : 'mở khóa'; ?> tài khoản này?')">
                                            <i class="bi <?php echo $user['active'] ? 'bi-lock' : 'bi-unlock'; ?>"></i>
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

    <!-- Modal Chi tiết người dùng -->
    <div class="modal fade" id="userDetailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="userDetail">
                        <!-- Nội dung chi tiết người dùng sẽ được load bằng AJAX -->
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
    document.querySelectorAll('.view-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            fetch(`get_user.php?id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    let html = `
                        <div class="mb-3">
                            <h6>Thông tin cá nhân:</h6>
                            <p>Họ tên: ${data.first_name} ${data.last_name}<br>
                               Username: ${data.username}<br>
                               Email: ${data.email}<br>
                               SĐT: ${data.phone || 'Chưa cập nhật'}<br>
                               Địa chỉ: ${data.address || 'Chưa cập nhật'}</p>
                        </div>
                        <div class="mb-3">
                            <h6>Thông tin tài khoản:</h6>
                            <p>Vai trò: ${data.role == 'admin' ? 'Admin' : 'Khách hàng'}<br>
                               Trạng thái: ${data.verified ? 'Đã xác thực' : 'Chưa xác thực'}<br>
                               Ngày tạo: ${new Date(data.created_at).toLocaleString('vi-VN')}</p>
                        </div>`;
                    
                    document.getElementById('userDetail').innerHTML = html;
                });
        });
    });
    </script>
</body>
</html> 
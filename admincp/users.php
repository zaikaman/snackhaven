<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Xử lý thêm/sửa người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user']) || isset($_POST['edit_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $role = $_POST['role'];
        
        try {
            if (isset($_POST['add_user'])) {
                // Kiểm tra username và email đã tồn tại chưa
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
                $checkStmt->execute([$username, $email]);
                if ($checkStmt->fetchColumn() > 0) {
                    throw new Exception("Username hoặc email đã tồn tại");
                }

                // Tạo mật khẩu mặc định
                $password = password_hash("123456", PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password, first_name, last_name, phone, address, role, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$username, $email, $password, $first_name, $last_name, $phone, $address, $role]);
                $success = "Đã thêm người dùng mới thành công! Mật khẩu mặc định là: 123456";
            } else {
                // Sửa người dùng
                $user_id = $_POST['user_id'];
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET username = ?, email = ?, first_name = ?, last_name = ?, 
                        phone = ?, address = ?, role = ?
                    WHERE id = ?
                ");
                $stmt->execute([$username, $email, $first_name, $last_name, $phone, $address, $role, $user_id]);
                $success = "Đã cập nhật thông tin người dùng thành công!";
            }
        } catch(PDOException $e) {
            $error = "Lỗi: " . $e->getMessage();
        } catch(Exception $e) {
            $error = $e->getMessage();
        }
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
}

// Lấy danh sách người dùng
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

$page_title = 'Quản lý Người dùng';
$current_page = 'users';
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
            <h2>Quản lý Người dùng</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userFormModal">
                <i class="bi bi-plus-lg"></i> Thêm người dùng mới
            </button>
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
                                    
                                    <button class="btn btn-sm btn-warning edit-user"
                                            data-id="<?php echo $user['id']; ?>"
                                            data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                            data-first-name="<?php echo htmlspecialchars($user['first_name']); ?>"
                                            data-last-name="<?php echo htmlspecialchars($user['last_name']); ?>"
                                            data-phone="<?php echo htmlspecialchars($user['phone']); ?>"
                                            data-address="<?php echo htmlspecialchars($user['address']); ?>"
                                            data-role="<?php echo $user['role']; ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#userFormModal">
                                        <i class="bi bi-pencil"></i>
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

    <!-- Modal Thêm/Sửa người dùng -->
    <div class="modal fade" id="userFormModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm người dùng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="userForm">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="userId">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">Họ</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Tên</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="customer">Khách hàng</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" name="add_user" id="submitBtn">Thêm</button>
                    </div>
                </form>
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

    // Xử lý khi click nút sửa
    document.querySelectorAll('.edit-user').forEach(button => {
        button.addEventListener('click', function() {
            const modal = document.getElementById('userFormModal');
            modal.querySelector('.modal-title').textContent = 'Sửa thông tin người dùng';
            
            // Điền thông tin người dùng vào form
            document.getElementById('userId').value = this.dataset.id;
            document.getElementById('username').value = this.dataset.username;
            document.getElementById('email').value = this.dataset.email;
            document.getElementById('first_name').value = this.dataset.firstName;
            document.getElementById('last_name').value = this.dataset.lastName;
            document.getElementById('phone').value = this.dataset.phone;
            document.getElementById('address').value = this.dataset.address;
            document.getElementById('role').value = this.dataset.role;

            // Đổi nút submit thành "Cập nhật"
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.textContent = 'Cập nhật';
            submitBtn.name = 'edit_user';
        });
    });

    // Reset form khi đóng modal
    document.getElementById('userFormModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('submitBtn').textContent = 'Thêm';
        document.getElementById('submitBtn').name = 'add_user';
        document.querySelector('#userFormModal .modal-title').textContent = 'Thêm người dùng mới';
    });
    </script>
</body>
</html> 
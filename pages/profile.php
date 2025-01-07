<?php
require_once __DIR__ . '/../includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('auth/login.php'));
    exit;
}

// Lấy thông tin user từ database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    try {
        $updateStmt = $pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, phone = ?, address = ?
            WHERE id = ?
        ");
        
        $updateStmt->execute([$firstName, $lastName, $phone, $address, $_SESSION['user_id']]);
        
        // Hiển thị thông báo thành công
        $successMessage = "Cập nhật thông tin thành công!";
        
        // Cập nhật lại thông tin user
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        $errorMessage = "Có lỗi xảy ra khi cập nhật thông tin: " . $e->getMessage();
    }
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
</style>

<div class="profile-container">
    <div class="profile-header">
        <h1>Thông tin cá nhân</h1>
        <p>Quản lý thông tin cá nhân của bạn</p>
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

        <div class="form-group full-width">
            <label for="address">Địa chỉ</label>
            <textarea id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
        </div>

        <div class="form-group full-width">
            <button type="submit" class="save-btn">Lưu thay đổi</button>
        </div>
    </form>
</div>

<script>
// Hiển thị thông báo thành công sử dụng SweetAlert2
<?php if (isset($successMessage)): ?>
Swal.fire({
    title: 'Thành công!',
    text: '<?php echo $successMessage; ?>',
    icon: 'success',
    timer: 2000,
    showConfirmButton: false
});
<?php endif; ?>

// Hiển thị thông báo lỗi sử dụng SweetAlert2
<?php if (isset($errorMessage)): ?>
Swal.fire({
    title: 'Lỗi!',
    text: '<?php echo $errorMessage; ?>',
    icon: 'error'
});
<?php endif; ?>
</script>

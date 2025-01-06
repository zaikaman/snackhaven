<?php
session_start();

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: /snackhaven');
    exit;
}

// Xử lý đăng nhập AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/config.php';
    
    $response = ['success' => false, 'message' => ''];
    
    try {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        // Kiểm tra email và password
        if (empty($email) || empty($password)) {
            throw new Exception('Vui lòng nhập đầy đủ thông tin');
        }

        // Tìm user theo email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception('Email hoặc mật khẩu không chính xác');
        }

        // Kiểm tra xác thực email
        if (!$user['verified']) {
            throw new Exception('Vui lòng xác thực email trước khi đăng nhập');
        }

        // Tạo session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        $response['success'] = true;
        $response['message'] = 'Đăng nhập thành công!';
        $response['redirect'] = '/snackhaven';
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Lấy thông báo từ URL nếu có
$status = $_GET['status'] ?? '';
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - SnackHaven</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="auth-container">
        <div class="auth-box">
            <div class="food-decoration">🍕</div>
            <div class="food-decoration">🍟</div>
            <div class="food-decoration">🥤</div>
            <div class="food-decoration">🌭</div>
            
            <div class="auth-header">
                <div class="logo">
                    <i class="fas fa-hamburger"></i>
                </div>
                <h1>Chào mừng trở lại!</h1>
                <p>Đăng nhập để tiếp tục khám phá thế giới ẩm thực tại SnackHaven</p>
            </div>

            <?php if ($status && $message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($status); ?>" style="display: block;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php else: ?>
                <div class="alert"></div>
            <?php endif; ?>

            <form id="loginForm">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email của bạn
                    </label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="example@email.com">
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Mật khẩu
                    </label>
                    <div class="password-toggle">
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Nhập mật khẩu">
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="fas fa-sign-in-alt"></i>
                    Đăng nhập
                </button>
            </form>

            <div class="auth-footer">
                <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                <p><a href="forgot-password.php">Quên mật khẩu?</a></p>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="auth.js"></script>
</body>
</html> 
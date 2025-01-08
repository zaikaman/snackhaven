<?php
session_start();
require_once '../includes/url_config.php';
require_once '../includes/config.php';
require_once '../includes/mail.php';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: ' . url());
    exit;
}

// Xử lý đăng ký AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    try {
        // Lấy và validate dữ liệu
        $username = trim(htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'));
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate username
        if (empty($username) || strlen($username) < 3) {
            throw new Exception('Username phải có ít nhất 3 ký tự');
        }

        // Kiểm tra password khớp nhau
        if ($password !== $confirm_password) {
            throw new Exception('Mật khẩu không khớp');
        }

        // Kiểm tra email hợp lệ
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email không hợp lệ');
        }

        // Kiểm tra username và email đã tồn tại
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            throw new Exception('Username hoặc email đã tồn tại');
        }

        // Tạo mã xác thực
        $verification_token = bin2hex(random_bytes(32));
        $token_expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Băm mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Bắt đầu transaction
        $pdo->beginTransaction();

        // Thêm user mới
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, verification_token) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password, $verification_token]);
        $user_id = $pdo->lastInsertId();

        // Thêm verification token
        $stmt = $pdo->prepare("INSERT INTO email_verifications (user_id, token, type, expires_at) VALUES (?, ?, 'registration', ?)");
        $stmt->execute([$user_id, $verification_token, $token_expiry]);

        // Gửi email xác thực
        $verification_link = "https://" . $_SERVER['HTTP_HOST'] . url('auth/verify.php') . "?token=" . $verification_token;
        $email_body = "
            <h2>Xác thực tài khoản SnackHaven</h2>
            <p>Cảm ơn bạn đã đăng ký tài khoản tại SnackHaven.</p>
            <p>Vui lòng click vào link bên dưới để xác thực email của bạn:</p>
            <p><a href='{$verification_link}'>{$verification_link}</a></p>
            <p>Link này sẽ hết hạn sau 24 giờ.</p>
        ";

        if (!sendMail($email, "Xác thực tài khoản SnackHaven", $email_body)) {
            throw new Exception('Không thể gửi email xác thực');
        }

        // Commit transaction
        $pdo->commit();

        $response['success'] = true;
        $response['message'] = 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.';
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $response['message'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - SnackHaven</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                <h1>Tham gia cùng chúng tôi!</h1>
                <p>Đăng ký để khám phá thế giới ẩm thực tuyệt vời tại SnackHaven</p>
            </div>

            <div class="alert"></div>

            <form id="registerForm">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        Tên đăng nhập
                    </label>
                    <input type="text" id="username" name="username" class="form-control" required minlength="3" placeholder="Nhập tên đăng nhập">
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="example@email.com">
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Mật khẩu
                    </label>
                    <div class="password-toggle">
                        <input type="password" id="password" name="password" class="form-control" required minlength="6" placeholder="Tối thiểu 6 ký tự">
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-shield-alt"></i>
                        Xác nhận mật khẩu
                    </label>
                    <div class="password-toggle">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Nhập lại mật khẩu">
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="fas fa-user-plus"></i>
                    Đăng ký ngay
                </button>
            </form>

            <div class="auth-footer">
                <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="auth.js"></script>
</body>
</html> 
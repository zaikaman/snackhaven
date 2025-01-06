<?php
require_once 'includes/config.php';
require_once 'includes/url_config.php';
require_once 'includes/mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    try {
        // Lấy và validate dữ liệu
        $name = trim(htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'));
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phone = trim(htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'));
        $subject = trim(htmlspecialchars($_POST['subject'] ?? '', ENT_QUOTES, 'UTF-8'));
        $message = trim(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'));

        // Validate dữ liệu
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            throw new Exception('Vui lòng điền đầy đủ thông tin bắt buộc');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email không hợp lệ');
        }

        // Lưu vào database
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $subject, $message]);

        // Gửi email thông báo
        $email_body = "
            <h2>Tin nhắn mới từ website</h2>
            <p><strong>Họ tên:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Số điện thoại:</strong> {$phone}</p>
            <p><strong>Chủ đề:</strong> {$subject}</p>
            <p><strong>Nội dung:</strong><br>{$message}</p>
        ";

        // Gửi email cho admin
        if (!sendMail('admin@snackhaven.com', "Tin nhắn liên hệ mới: {$subject}", $email_body)) {
            throw new Exception('Không thể gửi email thông báo');
        }

        // Gửi email xác nhận cho người gửi
        $confirmation_body = "
            <h2>Cảm ơn bạn đã liên hệ với SnackHaven</h2>
            <p>Chúng tôi đã nhận được tin nhắn của bạn và sẽ phản hồi trong thời gian sớm nhất.</p>
            <p>Dưới đây là nội dung tin nhắn của bạn:</p>
            <hr>
            <p><strong>Chủ đề:</strong> {$subject}</p>
            <p><strong>Nội dung:</strong><br>{$message}</p>
        ";

        if (!sendMail($email, "SnackHaven - Xác nhận tin nhắn của bạn", $confirmation_body)) {
            throw new Exception('Không thể gửi email xác nhận');
        }

        $response['success'] = true;
        $response['message'] = 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể!';
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Nếu không phải POST request, chuyển hướng về trang liên hệ
header('Location: ' . url('contact'));
exit; 
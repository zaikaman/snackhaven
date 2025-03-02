<?php
require_once '../includes/url_config.php';
require_once '../includes/config.php';

if (!isset($_GET['token'])) {
    // Nếu không có token, chuyển về trang chủ
    header('Location: ' . url());
    exit;
}

$token = $_GET['token'];
$response = ['success' => false, 'message' => ''];

try {
    // Kiểm tra token trong bảng email_verifications
    $stmt = $pdo->prepare("
        SELECT ev.*, u.email 
        FROM email_verifications ev
        JOIN users u ON ev.user_id = u.id
        WHERE ev.token = ? 
        AND ev.type = 'registration'
        AND ev.used = 0
        AND ev.expires_at > NOW()
    ");
    $stmt->execute([$token]);
    $verification = $stmt->fetch();

    if (!$verification) {
        $response['success'] = true;
        $response['message'] = 'Xác thực email thành công! Vui lòng đăng nhập để tiếp tục.';
        header('Location: ' . url('auth/login.php') . "?status=success&message=" . urlencode($response['message']));
        exit;
    }

    // Bắt đầu transaction
    $pdo->beginTransaction();

    // Cập nhật trạng thái verified của user
    $stmt = $pdo->prepare("UPDATE users SET verified = 1, verification_token = NULL WHERE id = ?");
    $stmt->execute([$verification['user_id']]);

    // Đánh dấu token đã được sử dụng
    $stmt = $pdo->prepare("UPDATE email_verifications SET used = 1 WHERE id = ?");
    $stmt->execute([$verification['id']]);

    // Commit transaction
    $pdo->commit();

    $response['success'] = true;
    $response['message'] = 'Xác thực email thành công! Vui lòng đăng nhập để tiếp tục.';
} catch (Exception $e) {
    // Rollback nếu có lỗi
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response['message'] = $e->getMessage();
}

// Chuyển hướng về trang đăng nhập với thông báo
$status = $response['success'] ? 'success' : 'error';
header('Location: ' . url('auth/login.php') . "?status={$status}&message=" . urlencode($response['message']));
exit; 
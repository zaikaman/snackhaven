<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if(isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user) {
            // Loại bỏ thông tin nhạy cảm
            unset($user['password']);
            unset($user['verification_token']);
            unset($user['reset_token']);
            unset($user['reset_token_expires']);
            
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user ID']);
} 
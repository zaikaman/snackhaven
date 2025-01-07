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
    $message_id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
        $stmt->execute([$message_id]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($message) {
            echo json_encode($message);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Message not found']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing message ID']);
} 
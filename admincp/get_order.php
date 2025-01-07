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
    $order_id = $_GET['id'];
    
    try {
        // Lấy thông tin đơn hàng
        $stmt = $pdo->prepare("SELECT o.*, u.first_name, u.last_name, u.email, u.phone,
                              CONCAT(u.first_name, ' ', u.last_name) as customer_name 
                              FROM orders o 
                              LEFT JOIN users u ON o.user_id = u.id 
                              WHERE o.id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($order) {
            // Lấy chi tiết đơn hàng
            $stmt = $pdo->prepare("SELECT oi.*, p.name as product_name 
                                 FROM order_items oi 
                                 LEFT JOIN products p ON oi.product_id = p.id 
                                 WHERE oi.order_id = ?");
            $stmt->execute([$order_id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response = [
                'order' => [
                    'id' => $order['id'],
                    'total_price' => $order['total_price'],
                    'status' => $order['status'],
                    'created_at' => $order['created_at']
                ],
                'customer' => [
                    'name' => $order['customer_name'],
                    'email' => $order['email'],
                    'phone' => $order['phone']
                ],
                'items' => $items
            ];
            
            echo json_encode($response);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing order ID']);
} 
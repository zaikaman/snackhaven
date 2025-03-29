<?php
session_start();
require_once '../includes/config.php';

// Cấu hình múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if(isset($_GET['user_id']) && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $user_id = $_GET['user_id'];
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    
    try {
        // Lấy danh sách đơn hàng của khách hàng
        $stmt = $pdo->prepare("
            SELECT o.*, 
                   CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                   u.email, u.phone
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.user_id = ?
            AND o.created_at BETWEEN ? AND ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$user_id, $start_date, $end_date]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if($orders) {
            // Định dạng lại ngày tháng và thêm thông tin bổ sung
            foreach($orders as &$order) {
                $orderDate = new DateTime($order['created_at']);
                $order['created_at'] = $orderDate->format('Y-m-d H:i:s');
            }
            
            $response = [
                'success' => true,
                'orders' => $orders
            ];
            
            echo json_encode($response);
        } else {
            echo json_encode([
                'success' => true,
                'orders' => []
            ]);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
} 
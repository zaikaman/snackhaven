<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized'
    ]);
    exit;
}

// Kiểm tra order_id
if (!isset($_GET['order_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing order_id'
    ]);
    exit;
}

try {
    // Lấy thông tin đơn hàng
    $orderStmt = $pdo->prepare("
        SELECT * FROM orders 
        WHERE id = ? AND user_id = ?
    ");
    $orderStmt->execute([$_GET['order_id'], $_SESSION['user_id']]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode([
            'success' => false,
            'error' => 'Order not found'
        ]);
        exit;
    }

    // Lấy chi tiết đơn hàng
    $itemsStmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name, p.image_url
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $itemsStmt->execute([$_GET['order_id']]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'order' => $order,
        'items' => $items
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 
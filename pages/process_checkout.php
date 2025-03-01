<?php

try {
    // Lấy thông tin địa chỉ giao hàng
    $shipping_city = '';
    $shipping_district = '';
    $shipping_address = '';

    if (isset($_POST['address_option']) && $_POST['address_option'] === 'account_address') {
        // Sử dụng địa chỉ từ tài khoản
        $stmt = $pdo->prepare("SELECT user_city, user_district, user_address FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_address = $stmt->fetch();
        
        $shipping_city = $user_address['user_city'];
        $shipping_district = $user_address['user_district'];
        $shipping_address = $user_address['user_address'];
    } else {
        // Sử dụng địa chỉ mới
        $shipping_city = $_POST['shipping_city'];
        $shipping_district = $_POST['shipping_district'];
        $shipping_address = $_POST['shipping_address'];
    }

    // Tạo đơn hàng mới
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_price, status, shipping_city, shipping_district, shipping_address, created_at)
        VALUES (?, ?, 'pending', ?, ?, ?, NOW())
    ");
    $stmt->execute([$user_id, $total_price, $shipping_city, $shipping_district, $shipping_address]);

    // ... existing code ...
} catch (Exception $e) {
    // ... existing code ...
} 
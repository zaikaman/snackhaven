<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

// Lấy product_id từ request
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Lấy thông tin sản phẩm và category
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Không tìm thấy sản phẩm'
        ]);
        exit;
    }

    // Lấy các sản phẩm liên quan (cùng category)
    $related_sql = "SELECT * FROM products 
                    WHERE category_id = :category_id 
                    AND id != :product_id 
                    ORDER BY RAND() 
                    LIMIT 4";
    $stmt = $pdo->prepare($related_sql);
    $stmt->execute([
        'category_id' => $product['category_id'],
        'product_id' => $product_id
    ]);
    $related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'product' => $product,
        'related_products' => $related_products
    ]);

} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi database: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    error_log("General error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi: ' . $e->getMessage()
    ]);
} 
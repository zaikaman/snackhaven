<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

// Lấy tham số từ request
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 9;

// Debug information
error_log("Category ID: " . $category_id);
error_log("Page: " . $page);
error_log("Per Page: " . $per_page);

try {
    // Tính offset cho phân trang
    $offset = ($page - 1) * $per_page;

    // Lấy tổng số sản phẩm của category
    $total_sql = "SELECT COUNT(*) as total FROM products WHERE category_id = :category_id AND active = 1";
    $stmt = $pdo->prepare($total_sql);
    $stmt->execute(['category_id' => $category_id]);
    $total_row = $stmt->fetch();
    $total_products = $total_row['total'];

    error_log("Total products: " . $total_products);

    // Tính tổng số trang
    $total_pages = ceil($total_products / $per_page);

    // Lấy sản phẩm theo phân trang
    $sql = "SELECT * FROM products WHERE category_id = :category_id AND active = 1 ORDER BY id LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $products = $stmt->fetchAll();

    error_log("Number of products fetched: " . count($products));

    // Trả về kết quả
    echo json_encode([
        'success' => true,
        'products' => $products,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'per_page' => $per_page,
        'total_products' => $total_products,
        'debug' => [
            'category_id' => $category_id,
            'offset' => $offset,
            'products_count' => count($products)
        ]
    ]);

} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    error_log("General error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'General error: ' . $e->getMessage()
    ]);
} 
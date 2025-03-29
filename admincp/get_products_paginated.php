<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Lấy tham số phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Lấy tham số tìm kiếm
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

try {
    // Xây dựng câu query với điều kiện tìm kiếm
    $where_clause = '';
    $params = [];

    if (!empty($keyword)) {
        $where_clause = "WHERE p.name LIKE :keyword";
        $params[':keyword'] = "%$keyword%";
    }

    // Đếm tổng số sản phẩm thỏa mãn điều kiện
    $count_query = "SELECT COUNT(*) as total FROM products p $where_clause";
    $stmt = $pdo->prepare($count_query);
    if (!empty($params)) {
        $stmt->bindValue(':keyword', $params[':keyword']);
    }
    $stmt->execute();
    $total = $stmt->fetch()['total'];
    $totalPages = ceil($total / $limit);

    // Lấy danh sách sản phẩm theo trang và điều kiện tìm kiếm
    $query = "
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $where_clause
        ORDER BY p.id DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $pdo->prepare($query);
    if (!empty($params)) {
        $stmt->bindValue(':keyword', $params[':keyword']);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format lại dữ liệu trước khi trả về
    foreach ($products as &$product) {
        $product['price'] = (int)$product['price'];
        $product['active'] = (bool)$product['active'];
        // Đảm bảo description không bị null
        $product['description'] = $product['description'] ?? '';
    }

    echo json_encode([
        'success' => true,
        'products' => $products,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $total,
            'items_per_page' => $limit
        ]
    ]);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 
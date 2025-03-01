<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/../includes/config.php';
    header('Content-Type: application/json');

    // Lấy tham số
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    $category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
    $priceRange = isset($_GET['priceRange']) ? $_GET['priceRange'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $itemsPerPage = 12;
    $offset = ($page - 1) * $itemsPerPage;

    // Xây dựng query
    $where = ['p.active = 1'];
    $params = [];

    if (!empty($keyword)) {
        $where[] = 'p.name LIKE :keyword';
        $params[':keyword'] = "%$keyword%";
    }

    if (!empty($category)) {
        $where[] = 'p.category_id = :category';
        $params[':category'] = $category;
    }

    if (!empty($priceRange)) {
        $prices = explode('-', $priceRange);
        if (count($prices) == 2) {
            $where[] = 'p.price BETWEEN :price_min AND :price_max';
            $params[':price_min'] = $prices[0];
            $params[':price_max'] = $prices[1];
        } elseif (count($prices) == 1) {
            $where[] = 'p.price >= :price_min';
            $params[':price_min'] = $prices[0];
        }
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Đếm tổng số sản phẩm
    $countQuery = "SELECT COUNT(*) as total FROM products p $whereClause";
    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    $totalPages = ceil($total / $itemsPerPage);

    // Lấy sản phẩm
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              $whereClause 
              ORDER BY p.name ASC 
              LIMIT $itemsPerPage OFFSET $offset";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tạo HTML cho sản phẩm
    $html = '';
    if (!empty($products)) {
        foreach ($products as $product) {
            $html .= '<div class="col-md-3 mb-4">';
            $html .= '<div class="card h-100">';
            $html .= '<img src="' . htmlspecialchars($product['image_url']) . '" class="card-img-top" alt="' . htmlspecialchars($product['name']) . '">';
            $html .= '<div class="card-body">';
            $html .= '<h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>';
            $html .= '<div class="category-name">' . htmlspecialchars($product['category_name']) . '</div>';
            $html .= '<div class="product-price">' . number_format($product['price'], 0, ',', '.') . 'đ</div>';
            $html .= '<a href="' . url('product?id=' . $product['id']) . '" class="btn btn-primary w-100">Xem chi tiết</a>';
            $html .= '</div></div></div>';
        }
    } else {
        $html = '<div class="col-12"><div class="alert alert-info">Không tìm thấy sản phẩm nào phù hợp.</div></div>';
    }

    // Tạo HTML cho phân trang
    $paginationHtml = '';
    if ($totalPages > 1) {
        $paginationHtml .= '<ul class="pagination">';
        
        // Nút Previous
        if ($page > 1) {
            $paginationHtml .= '<li class="page-item"><a class="page-link" href="#" data-page="' . ($page - 1) . '"><i class="fas fa-chevron-left"></i></a></li>';
        } else {
            $paginationHtml .= '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>';
        }

        // Hiển thị tối đa 5 trang
        $start = max(1, min($page - 2, $totalPages - 4));
        $end = min($totalPages, max(5, $page + 2));

        if ($start > 1) {
            $paginationHtml .= '<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>';
            if ($start > 2) {
                $paginationHtml .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $page) {
                $paginationHtml .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $paginationHtml .= '<li class="page-item"><a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
            }
        }

        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $paginationHtml .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $paginationHtml .= '<li class="page-item"><a class="page-link" href="#" data-page="' . $totalPages . '">' . $totalPages . '</a></li>';
        }

        // Nút Next
        if ($page < $totalPages) {
            $paginationHtml .= '<li class="page-item"><a class="page-link" href="#" data-page="' . ($page + 1) . '"><i class="fas fa-chevron-right"></i></a></li>';
        } else {
            $paginationHtml .= '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>';
        }

        $paginationHtml .= '</ul>';
    }

    // Trả về kết quả
    echo json_encode([
        'html' => $html,
        'pagination' => $paginationHtml,
        'total' => $total,
        'currentPage' => $page,
        'totalPages' => $totalPages
    ]);

} catch (Exception $e) {
    // Trả về lỗi dưới dạng JSON
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?> 